<?php
include __DIR__ . "/connection.php";
$config = include __DIR__ . "/sslcommerz-config.php";

$tran_id = $_GET['tran_id'] ?? $_POST['tran_id'] ?? null;
$val_id = $_GET['val_id'] ?? $_POST['val_id'] ?? null;

if (!$tran_id) {
    echo "Missing tran_id";
    exit;
}

$createOrders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    tran_id VARCHAR(100) UNIQUE,
    amount DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'pending',
    details TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createOrders);

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE tran_id = ? LIMIT 1");
if (!$stmt) {
    echo 'DB prepare error: ' . mysqli_error($conn);
    exit;
}
mysqli_stmt_bind_param($stmt, "s", $tran_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$order) {
    echo "Order not found for tran_id: " . htmlspecialchars($tran_id);
    exit;
}

$is_valid = false;
$validation_response = null;
if ($val_id && !empty($config['sandbox_validation_api'])) {
    $store_id = $config['sandbox_store_id'] ?? '';
    $store_passwd = $config['sandbox_store_passwd'] ?? '';
    $validation_url = $config['sandbox_validation_api'] . "?val_id=" . urlencode($val_id) . "&store_id=" . urlencode($store_id) . "&store_passwd=" . urlencode($store_passwd) . "&format=json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $validation_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $validation_raw = curl_exec($ch);
    if (!curl_errno($ch)) {
        $validation_response = json_decode($validation_raw, true);
        if (!empty($validation_response) && (isset($validation_response['status']) && in_array(strtoupper($validation_response['status']), ['VALID','VALIDATED','VALIDATED'])) ) {
            $is_valid = true;
        }
    }
    curl_close($ch);
}

if ($val_id && !$is_valid) {
    echo "Transaction validation failed.";
    exit;
}

$user_id = $order['user_id'];
$details = [];
if (!empty($order['details'])) {
    $details = json_decode($order['details'], true) ?: [];
}

foreach ($details as $item) {
    $match_id = $item['match_id'] ?? ($item['eventId'] ?? null);
    $booking_stmt = mysqli_prepare($conn, "INSERT INTO booking (user_id, match_id, total_amount, payment_status, booking_date) VALUES (?, ?, ?, 'paid', NOW())");
    $total_amount = floatval($item['total'] ?? 0);
    mysqli_stmt_bind_param($booking_stmt, "iid", $user_id, $match_id, $total_amount);
    mysqli_stmt_execute($booking_stmt);
    $booking_id = mysqli_insert_id($conn);
    mysqli_stmt_close($booking_stmt);

    if (!empty($item['seats']) && is_array($item['seats'])) {
        foreach ($item['seats'] as $seat) {
            $holdId = $seat['holdId'] ?? null;
            if (!$holdId) continue;

            $u = mysqli_prepare($conn, "UPDATE seat_hold SET status = 'confirmed' WHERE hold_id = ? LIMIT 1");
            mysqli_stmt_bind_param($u, "i", $holdId);
            mysqli_stmt_execute($u);
            mysqli_stmt_close($u);

            $up = mysqli_prepare($conn, "UPDATE match_seat ms JOIN seat_hold sh ON sh.match_id = ms.match_id AND sh.seat_id = ms.seat_id SET ms.status = 'booked' WHERE sh.hold_id = ?");
            mysqli_stmt_bind_param($up, "i", $holdId);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);

            $q = mysqli_prepare($conn, "SELECT ms.match_seat_id, ms.match_id, ms.seat_id FROM match_seat ms JOIN seat_hold sh ON sh.match_id = ms.match_id AND sh.seat_id = ms.seat_id WHERE sh.hold_id = ? LIMIT 1");
            mysqli_stmt_bind_param($q, "i", $holdId);
            mysqli_stmt_execute($q);
            $r = mysqli_stmt_get_result($q);
            $ms = mysqli_fetch_assoc($r);
            mysqli_stmt_close($q);

            if ($ms) {
                $match_seat_id = $ms['match_seat_id'];
                $category_name = $seat['category'] ?? null;
                $category_id = null;
                if ($category_name && !empty($item['stadium_id'])) {
                    $c = mysqli_prepare($conn, "SELECT category_id FROM ticket_category WHERE stadium_id = ? AND category_name = ? LIMIT 1");
                    mysqli_stmt_bind_param($c, "is", $item['stadium_id'], $category_name);
                    mysqli_stmt_execute($c);
                    $cres = mysqli_stmt_get_result($c);
                    $crow = mysqli_fetch_assoc($cres);
                    mysqli_stmt_close($c);
                    if ($crow) $category_id = $crow['category_id'];
                }

                $t = mysqli_prepare($conn, "INSERT INTO ticket (match_seat_id, category_id, booking_id, issued_at) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($t, "iii", $match_seat_id, $category_id, $booking_id);
                mysqli_stmt_execute($t);
                mysqli_stmt_close($t);
            }
        }
    }
}

$transaction_identifier = $_REQUEST['bank_tran_id'] ?? $_REQUEST['val_id'] ?? $tran_id;
$pay = mysqli_prepare($conn, "INSERT INTO payment (user_id, amount, payment_method, transaction_id, payment_date, status) VALUES (?, ?, 'sslcommerz', ?, NOW(), 'success')");
mysqli_stmt_bind_param($pay, "ids", $user_id, $order['amount'], $transaction_identifier);
mysqli_stmt_execute($pay);
mysqli_stmt_close($pay);

$u2 = mysqli_prepare($conn, "UPDATE orders SET status = 'completed', updated_at = NOW() WHERE tran_id = ? LIMIT 1");
mysqli_stmt_bind_param($u2, "s", $tran_id);
mysqli_stmt_execute($u2);
mysqli_stmt_close($u2);

?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payment Success</title></head><body>
<h2>Payment Successful</h2>
<p>Your transaction id: <?php echo htmlspecialchars($transaction_identifier); ?></p>
<p>Your seats have been confirmed. You can close this window or return to the site.</p>

<script>
    (function(){
        try {
            localStorage.removeItem('cart');
            for (var key in localStorage) {
                if (key && key.indexOf && key.indexOf('purchasedSeats_') === 0) {
                    localStorage.removeItem(key);
                }
            }

            function notify(win){
                try {
                    if (win && win.cartFunctions && typeof win.cartFunctions.updateCartCount === 'function') {
                        win.cartFunctions.updateCartCount();
                    }
                } catch(e){}
            }
            notify(window);
            if (window.opener) notify(window.opener);

        } catch(e) {
            console.error('Could not clear cart after payment', e);
        }
    })();
</script>
</body></html>
