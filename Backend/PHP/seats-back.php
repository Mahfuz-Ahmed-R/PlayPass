<?php
include __DIR__ . "/connection.php";
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getStadiumLayout':
        getStadiumLayout($conn);
        break;
    case 'selectSeat':
        selectSeat($conn);
        break;
    case 'releaseSeat':
        releaseSeat($conn);
        break;
    case 'cleanupExpiredHolds':
        cleanupExpiredHolds($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/* =====================================================
   GET STADIUM LAYOUT
===================================================== */
function getStadiumLayout($conn) {
    $stadiumId = $_GET['stadium_id'] ?? null;
    $matchId   = $_GET['match_id'] ?? null;

    if (!$stadiumId) {
        echo json_encode(['success' => false, 'message' => 'stadium_id required']);
        return;
    }

    /* -------- seats layout -------- */
    $layout = [];
    $rowsPerSection = [];
    $seatsPerRow = [];
    $allSections = [];

    $sql = "SELECT seat_id, section, row_number, seat_number
            FROM seat
            WHERE stadium_id=?
            ORDER BY section,row_number,seat_number";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stadiumId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($r = mysqli_fetch_assoc($res)) {
        $sec = $r['section'];
        $row = $r['row_number'];

        $layout[$sec][$row][] = $r;
        $rowsPerSection[$sec] = max($rowsPerSection[$sec] ?? 0, $row);
        $seatsPerRow[$sec.$row] = ($seatsPerRow[$sec.$row] ?? 0) + 1;

        if (!in_array($sec, $allSections)) {
            $allSections[] = $sec;
        }
    }
    mysqli_stmt_close($stmt);

    /* -------- prices -------- */
    $prices = [];
    $sql = "SELECT category_name, price FROM ticket_category WHERE stadium_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $stadiumId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $prices[$r['category_name']] = (float)$r['price'];
    }
    mysqli_stmt_close($stmt);

    /* -------- booked seats -------- */
    $bookedSeats = [];
    if ($matchId) {
        $sql = "SELECT s.section,s.row_number,s.seat_number
                FROM match_seat ms
                JOIN seat s ON s.seat_id=ms.seat_id
                WHERE ms.match_id=? AND ms.status='booked'";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $matchId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $bookedSeats[] = $r['section'].$r['row_number'].'-'.$r['seat_number'];
        }
        mysqli_stmt_close($stmt);
    }

    /* -------- held seats (SOURCE OF TRUTH) -------- */
    /* Fetch all seats where match_seat.status = 'held' OR 'booked'
       Include user_id and session_id to distinguish between current user's seats (yellow) 
       and other users' seats (red/occupied)
       
       Note: When user doesn't pay within 3 minutes, cleanupExpiredHolds() will
       update match_seat.status from 'held' to 'available', and these seats will
       no longer appear in heldSeats, causing them to turn green in the UI */
    $heldSeats = [];
    $userId = $_GET['user_id'] ?? null;
    $sessionId = $_GET['session_id'] ?? session_id();
    
    if ($matchId) {
        // Get ALL seats where match_seat.status = 'held' OR 'booked'
        // Include user_id and session_id to identify who holds each seat
        $sql = "SELECT s.section, s.row_number, s.seat_number, 
                       COALESCE(sh.hold_expires_at, NULL) as hold_expires_at,
                       ms.status as match_seat_status,
                       sh.user_id as hold_user_id,
                       sh.session_id as hold_session_id
                FROM match_seat ms
                INNER JOIN seat s ON s.seat_id = ms.seat_id
                LEFT JOIN seat_hold sh ON sh.match_id = ms.match_id 
                                      AND sh.seat_id = ms.seat_id
                                      AND sh.status = 'active'
                WHERE ms.match_id = ?
                  AND (ms.status = 'held' OR ms.status = 'booked')
                ORDER BY s.section, s.row_number, s.seat_number";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $matchId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $seatIdFormatted = trim($r['section']) . intval($r['row_number']) . '-' . intval($r['seat_number']);
            $heldSeats[] = [
                'seat_id_formatted' => $seatIdFormatted,
                'expires_at' => $r['hold_expires_at'],
                'status' => $r['match_seat_status'],
                'user_id' => $r['hold_user_id'],
                'session_id' => $r['hold_session_id']
            ];
        }
        mysqli_stmt_close($stmt);
    }

    echo json_encode([
        'success' => true,
        'sections' => $layout,
        'allSections' => $allSections,
        'rowsPerSection' => $rowsPerSection,
        'seatsPerRow' => $seatsPerRow,
        'prices' => $prices,
        'bookedSeats' => $bookedSeats,
        'heldSeats' => $heldSeats
    ]);
}

/* =====================================================
   SELECT SEAT (3 MIN HOLD)
===================================================== */
function selectSeat($conn) {
    $matchId = $_POST['match_id'];
    $section = $_POST['section'];
    $row     = $_POST['row'];
    $seatNum = $_POST['seat_number'];
    $userId  = $_POST['user_id'] ?: null;
    $session = $_POST['session_id'] ?? session_id();

    if (!$matchId) {
        echo json_encode(['success'=>false,'message'=>'Match ID is required']);
        return;
    }

    mysqli_begin_transaction($conn);

    /* find seat */
    $sql = "SELECT seat_id FROM seat
            WHERE section=? AND row_number=? AND seat_number=?
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $section, $row, $seatNum);
    mysqli_stmt_execute($stmt);
    $seat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$seat) {
        mysqli_rollback($conn);
        echo json_encode(['success'=>false,'message'=>'Seat not found']);
        return;
    }

    $seatId = $seat['seat_id'];

    /* CRITICAL: Check if match_seat record exists for this exact (match_id, seat_id) combination */
    $sql = "SELECT match_id, status FROM match_seat
            WHERE match_id=? AND seat_id=?
            FOR UPDATE";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $matchId, $seatId);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    // If match_seat record exists for this (match_id, seat_id), verify match_id matches
    if ($r) {
        // CRITICAL: Verify match_id matches - this should always be true since we queried with match_id
        // But double-check to be absolutely sure
        if ($r['match_id'] != $matchId) {
            mysqli_rollback($conn);
            echo json_encode([
                'success'=>false,
                'message'=>'Seat is not available for this match. Match ID mismatch with match_seat table. Please refresh the page and try again.'
            ]);
            return;
        }

        // If match_id matches, check if seat is already held or booked
        $currentStatus = $r['status'] ?? null;
        if ($currentStatus && $currentStatus !== 'available' && $currentStatus !== null) {
            mysqli_rollback($conn);
            echo json_encode(['success'=>false,'message'=>'Seat unavailable (status: ' . $currentStatus . ')']);
            return;
        }
    }
    // If row doesn't exist ($r is null), it means the seat is available for this match, proceed
    // At this point, we've verified:
    // 1. If match_seat record exists for (match_id, seat_id), the match_id matches
    // 2. If it exists, the status is 'available' or NULL
    // 3. If it doesn't exist, the seat is available for this match

    /* mark seat held - this will INSERT if row doesn't exist, or UPDATE if it does */
    /* CRITICAL: Only proceed if match_id verification passed above */
    $sql = "INSERT INTO match_seat (match_id,seat_id,status)
            VALUES (?,?, 'held')
            ON DUPLICATE KEY UPDATE status='held'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $matchId, $seatId);
    $result = mysqli_stmt_execute($stmt);
    
    // Check if the insert/update was successful
    if (!$result) {
        $error = mysqli_error($conn);
        mysqli_rollback($conn);
        echo json_encode(['success'=>false,'message'=>'Failed to hold seat: ' . $error]);
        mysqli_stmt_close($stmt);
        return;
    }
    
    mysqli_stmt_close($stmt);

    /* insert hold timer */
    $sql = "INSERT INTO seat_hold
            (match_id,seat_id,user_id,session_id,hold_expires_at,status)
            VALUES (?,?,?,?,DATE_ADD(NOW(),INTERVAL 3 MINUTE),'active')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $matchId, $seatId, $userId, $session);
    mysqli_stmt_execute($stmt);
    $holdId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);

    echo json_encode([
        'success'=>true,
        'hold_id'=>$holdId,
        'expires_at'=>date('Y-m-d H:i:s',strtotime('+3 minutes'))
    ]);
}

/* =====================================================
   RELEASE SEAT
===================================================== */
function releaseSeat($conn) {
    $holdId = $_POST['hold_id'] ?? null;
    $matchId = $_POST['match_id'] ?? null;

    if (!$holdId) {
        echo json_encode(['success'=>false, 'message'=>'Hold ID required']);
        return;
    }

    if (!$matchId) {
        echo json_encode(['success'=>false, 'message'=>'Match ID required']);
        return;
    }

    // First, verify that the hold exists and get its match_id
    $sql = "SELECT hold_id, match_id, seat_id, status 
            FROM seat_hold 
            WHERE hold_id = ? AND status = 'active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $holdId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $hold = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$hold) {
        echo json_encode(['success'=>false, 'message'=>'Seat hold not found or already released']);
        return;
    }

    // Verify match_id matches the hold's match_id
    if ($hold['match_id'] != $matchId) {
        echo json_encode([
            'success'=>false, 
            'message'=>'Seat is not released yet. Match ID mismatch. Please refresh the page.'
        ]);
        return;
    }

    // Verify match_id matches the match_seat table before updating
    $sql = "SELECT match_id, status FROM match_seat 
            WHERE match_id = ? AND seat_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $matchId, $hold['seat_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $matchSeat = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$matchSeat) {
        echo json_encode([
            'success'=>false, 
            'message'=>'Seat is not released yet. Match-seat record not found. Please refresh the page.'
        ]);
        return;
    }

    // Verify match_id matches
    if ($matchSeat['match_id'] != $matchId) {
        echo json_encode([
            'success'=>false, 
            'message'=>'Seat is not released yet. Match ID mismatch with match_seat table. Please refresh the page.'
        ]);
        return;
    }

    /* release hold */
    $sql = "UPDATE seat_hold SET status='expired' WHERE hold_id=? AND match_id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"ii",$holdId, $matchId);
    $result = mysqli_stmt_execute($stmt);
    
    if (!$result) {
        mysqli_stmt_close($stmt);
        echo json_encode(['success'=>false, 'message'=>'Failed to release seat hold']);
        return;
    }
    mysqli_stmt_close($stmt);

    /* free match_seat - verify match_id matches before updating */
    $sql = "UPDATE match_seat ms
            JOIN seat_hold sh ON sh.match_id=ms.match_id AND sh.seat_id=ms.seat_id
            SET ms.status='available'
            WHERE sh.hold_id=? AND ms.match_id=? AND sh.match_id=?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,"iii",$holdId, $matchId, $matchId);
    $result = mysqli_stmt_execute($stmt);
    
    if (!$result) {
        mysqli_stmt_close($stmt);
        echo json_encode(['success'=>false, 'message'=>'Failed to update seat status']);
        return;
    }
    mysqli_stmt_close($stmt);

    echo json_encode(['success'=>true, 'message'=>'Seat released successfully']);
}

/* =====================================================
   CLEANUP EXPIRED HOLDS
===================================================== */
function cleanupExpiredHolds($conn) {
    // First, expire all holds that have passed their expiration time
    mysqli_query($conn,
        "UPDATE seat_hold
         SET status='expired'
         WHERE status='active'
           AND hold_expires_at < NOW()"
    );

    // Then, update match_seat.status to 'available' for all seats that:
    // 1. Have status = 'held' (not 'booked' - booked seats are paid and should stay booked)
    // 2. Have no active seat_hold records (either expired or never existed)
    // This ensures seats return to available after 3 minutes if user doesn't pay
    // Note: Only 'held' seats are updated. 'booked' seats remain booked (they were paid for)
    mysqli_query($conn,
        "UPDATE match_seat ms
         LEFT JOIN seat_hold sh ON sh.match_id = ms.match_id 
                                AND sh.seat_id = ms.seat_id 
                                AND sh.status = 'active'
                                AND sh.hold_expires_at > NOW()
         SET ms.status = 'available'
         WHERE ms.status = 'held'
           AND (sh.hold_id IS NULL OR sh.status = 'expired' OR sh.hold_expires_at <= NOW())"
    );

    echo json_encode(['success'=>true]);
}
