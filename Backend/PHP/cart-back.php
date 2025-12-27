<?php
include __DIR__ . "/connection.php";
header('Content-Type: application/json');

session_start();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = $_GET['user_id'] ?? $_SESSION['user_id'] ?? $_POST['user_id'] ?? null;
$sessionId = session_id();

switch ($action) {
    case 'getCart':
        getCart($conn, $userId, $sessionId);
        break;
    case 'addToCart':
        addToCart($conn, $userId, $sessionId);
        break;
    case 'removeFromCart':
        removeFromCart($conn, $userId, $sessionId);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getCart($conn, $userId, $sessionId) {
    $cart = [];
    
    if (!$userId && isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];
    }
    
    if ($userId && (!is_numeric($userId) || $userId <= 0)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user_id',
            'cart' => []
        ]);
        return;
    }
    
    $sql = "SELECT sh.hold_id, sh.match_id, sh.seat_id, sh.hold_expires_at,
                   s.section, s.row_number, s.seat_number,
                   m.match_date, m.start_time, m.stadium_id,
                   st.name AS stadium_name, st.location AS stadium_location,
                   h.name AS home_team_name, a.name AS away_team_name,
                   m.poster_url, m.status AS match_status
            FROM seat_hold sh
            JOIN seat s ON s.seat_id = sh.seat_id
            JOIN match_table m ON m.match_id = sh.match_id
            JOIN stadium st ON st.stadium_id = m.stadium_id
            LEFT JOIN team h ON m.home_team_id = h.team_id
            LEFT JOIN team a ON m.away_team_id = a.team_id
            WHERE sh.status = 'active' 
            AND sh.hold_expires_at > NOW()";
    
    if ($userId) {
        $sql .= " AND sh.user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . mysqli_error($conn),
                'cart' => []
            ]);
            return;
        }
        mysqli_stmt_bind_param($stmt, "i", $userId);
    } else {
        // Only use session_id if no user_id is provided
        $sql .= " AND sh.session_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . mysqli_error($conn),
                'cart' => []
            ]);
            return;
        }
        mysqli_stmt_bind_param($stmt, "s", $sessionId);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($conn),
            'cart' => []
        ]);
        mysqli_stmt_close($stmt);
        return;
    }
    
    $itemsByMatch = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $matchId = $row['match_id'];
        
        if (!isset($itemsByMatch[$matchId])) {
            $stadiumId = $row['stadium_id'];
            
            $categories = [];
            if ($stadiumId) {
                $catSql = "SELECT category_id, category_name, price FROM ticket_category WHERE stadium_id = ? ORDER BY price DESC";
                $catStmt = mysqli_prepare($conn, $catSql);
                mysqli_stmt_bind_param($catStmt, "i", $stadiumId);
                mysqli_stmt_execute($catStmt);
                $catResult = mysqli_stmt_get_result($catStmt);
                while ($catRow = mysqli_fetch_assoc($catResult)) {
                    $categories[$catRow['category_name']] = [
                        'category_id' => $catRow['category_id'],
                        'price' => (float)$catRow['price']
                    ];
                }
                mysqli_stmt_close($catStmt);
            }
            
            $itemsByMatch[$matchId] = [
                'match_id' => $matchId,
                'eventId' => $matchId,
                'stadium_id' => $stadiumId,
                'eventTitle' => ($row['home_team_name'] ?? 'Team A') . ' vs ' . ($row['away_team_name'] ?? 'Team B'),
                'eventImage' => $row['poster_url'] ?? '',
                'eventLocation' => ($row['stadium_name'] ?? '') . ($row['stadium_location'] ? ', ' . $row['stadium_location'] : ''),
                'eventDate' => $row['match_date'],
                'eventTime' => $row['start_time'],
                'match_status' => $row['match_status'],
                'categories' => $categories,
                'seats' => [],
                'quantity' => 0,
                'total' => 0,
                'addedAt' => strtotime($row['hold_expires_at']) - 180 
            ];
        }
        
        // VIP = rows 1-2, Regular = rows 3-4, Economy = rows 5+
        $section = $row['section'];
        $rowNum = (int)$row['row_number'];
        $categoryName = 'Regular';
        $price = 0;
        
        // Determine category based on row number
        if ($rowNum <= 2) {
            $categoryName = 'VIP';
        } elseif ($rowNum <= 4) {
            $categoryName = 'Regular';
        } else {
            $categoryName = 'Economy';
        }
        
        // Get price from categories array
        if (isset($itemsByMatch[$matchId]['categories'][$categoryName])) {
            $price = $itemsByMatch[$matchId]['categories'][$categoryName]['price'];
        } elseif (!empty($itemsByMatch[$matchId]['categories'])) {
            $firstCat = reset($itemsByMatch[$matchId]['categories']);
            $categoryName = key($itemsByMatch[$matchId]['categories']);
            $price = $firstCat['price'];
        } else {
            if ($categoryName === 'VIP') {
                $price = 150;
            } elseif ($categoryName === 'Regular') {
                $price = 75;
            } else {
                $price = 35;
            }
        }
        
        $seatIdFormatted = $section . $rowNum . '-' . $row['seat_number'];
        
        $itemsByMatch[$matchId]['seats'][] = [
            'seatId' => $seatIdFormatted,
            'section' => $section,
            'row' => $rowNum,
            'seatNumber' => $row['seat_number'],
            'category' => $categoryName,
            'price' => $price,
            'holdId' => $row['hold_id'],
            'expiresAt' => $row['hold_expires_at']
        ];
        
        $itemsByMatch[$matchId]['quantity']++;
        $itemsByMatch[$matchId]['total'] += $price;
    }
    
    mysqli_stmt_close($stmt);
    
    $cart = array_values($itemsByMatch);
    
    echo json_encode([
        'success' => true,
        'cart' => $cart,
        'totalItems' => array_sum(array_column($cart, 'quantity'))
    ]);
}

function addToCart($conn, $userId, $sessionId) {
    echo json_encode([
        'success' => true,
        'message' => 'Use selectSeat action to add items to cart'
    ]);
}

function removeFromCart($conn, $userId, $sessionId) {
    $holdId = $_POST['hold_id'] ?? null;
    
    if (!$holdId) {
        echo json_encode(['success' => false, 'message' => 'Hold ID required']);
        return;
    }
    
    if ($userId) {
        $sql = "SELECT hold_id FROM seat_hold 
                WHERE hold_id = ? AND status = 'active' 
                AND (user_id = ? OR session_id = ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $holdId, $userId, $sessionId);
    } else {
        $sql = "SELECT hold_id FROM seat_hold 
                WHERE hold_id = ? AND status = 'active' 
                AND session_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $holdId, $sessionId);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => false, 'message' => 'Hold not found or already released']);
        return;
    }
    
    $releaseSql = "UPDATE seat_hold SET status = 'expired' WHERE hold_id = ?";
    $releaseStmt = mysqli_prepare($conn, $releaseSql);
    mysqli_stmt_bind_param($releaseStmt, "i", $holdId);
    mysqli_stmt_execute($releaseStmt);
    mysqli_stmt_close($releaseStmt);
    
    $updateSeatSql = "UPDATE match_seat ms
                      JOIN seat_hold sh ON sh.match_id = ms.match_id AND sh.seat_id = ms.seat_id
                      SET ms.status = 'available'
                      WHERE sh.hold_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateSeatSql);
    mysqli_stmt_bind_param($updateStmt, "i", $holdId);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    
    mysqli_stmt_close($stmt);
    
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
}
