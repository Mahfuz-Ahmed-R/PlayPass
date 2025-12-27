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

function getStadiumLayout($conn) {
    $stadiumId = $_GET['stadium_id'] ?? null;
    $matchId   = $_GET['match_id'] ?? null;

    if (!$stadiumId) {
        echo json_encode(['success' => false, 'message' => 'stadium_id required']);
        return;
    }

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

    $heldSeats = [];
    $userId = $_GET['user_id'] ?? null;
    $sessionId = $_GET['session_id'] ?? session_id();
    
    if ($matchId) {

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

    $sql = "SELECT match_id, status FROM match_seat
            WHERE match_id=? AND seat_id=?
            FOR UPDATE";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $matchId, $seatId);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($r) {
        if ($r['match_id'] != $matchId) {
            mysqli_rollback($conn);
            echo json_encode([
                'success'=>false,
                'message'=>'Seat is not available for this match. Match ID mismatch with match_seat table. Please refresh the page and try again.'
            ]);
            return;
        }

        $currentStatus = $r['status'] ?? null;
        if ($currentStatus && $currentStatus !== 'available' && $currentStatus !== null) {
            mysqli_rollback($conn);
            echo json_encode(['success'=>false,'message'=>'Seat unavailable (status: ' . $currentStatus . ')']);
            return;
        }
    }
    $sql = "INSERT INTO match_seat (match_id,seat_id,status)
            VALUES (?,?, 'held')
            ON DUPLICATE KEY UPDATE status='held'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $matchId, $seatId);
    $result = mysqli_stmt_execute($stmt);
    
    if (!$result) {
        $error = mysqli_error($conn);
        mysqli_rollback($conn);
        echo json_encode(['success'=>false,'message'=>'Failed to hold seat: ' . $error]);
        mysqli_stmt_close($stmt);
        return;
    }
    
    mysqli_stmt_close($stmt);

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

    if ($hold['match_id'] != $matchId) {
        echo json_encode([
            'success'=>false, 
            'message'=>'Seat is not released yet. Match ID mismatch. Please refresh the page.'
        ]);
        return;
    }

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

    if ($matchSeat['match_id'] != $matchId) {
        echo json_encode([
            'success'=>false, 
            'message'=>'Seat is not released yet. Match ID mismatch with match_seat table. Please refresh the page.'
        ]);
        return;
    }

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

function cleanupExpiredHolds($conn) {
    mysqli_query($conn,
        "UPDATE seat_hold
         SET status='expired'
         WHERE status='active'
           AND hold_expires_at < NOW()"
    );

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
