<?php
// SSLCommerz initiate endpoint - creates a payment session and returns redirect URL
include __DIR__ . "/connection.php";
header('Content-Type: application/json');

$config = include __DIR__ . "/sslcommerz-config.php";

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$user_id = $data['user_id'] ?? null;
$cart = $data['cart'] ?? [];

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Missing user_id']);
    exit;
}

// Calculate total amount
$amount = 0.0;
foreach ($cart as $item) {
    $amount += floatval($item['total'] ?? 0);
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

// Choose credentials and endpoint (use config values when present)
if (!empty($config['is_sandbox'])) {
    $store_id = $config['sandbox_store_id'];
    $store_passwd = $config['sandbox_store_passwd'];
    $api_url = $config['sandbox_api'] ?? 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php';
    $embed_script = $config['sandbox_embed_script'] ?? 'https://sandbox.sslcommerz.com/embed.min.js';
} else {
    $store_id = $config['live_store_id'];
    $store_passwd = $config['live_store_passwd'];
    $api_url = $config['live_api'] ?? 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
    $embed_script = $config['live_embed_script'] ?? 'https://seamless-epay.sslcommerz.com/embed.min.js';
}

if (empty($store_id) || empty($store_passwd)) {
    echo json_encode(['success' => false, 'message' => 'SSLCommerz credentials not set in sslcommerz-config.php']);
    exit;
}

// Create a unique transaction id
$tran_id = 'PP' . time() . rand(1000,9999);

// Basic post data required by SSLCommerz
$post_data = [
    'store_id' => $store_id,
    'store_passwd' => $store_passwd,
    'total_amount' => number_format($amount, 2, '.', ''),
    'currency' => 'BDT',
    'tran_id' => $tran_id,
    'success_url' => (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/ssl_success.php',
    'fail_url' => (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/ssl_fail.php',
    'cancel_url' => (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/ssl_cancel.php',
    'emi_option' => 0,
    'cus_name' => '',
    'cus_email' => '',
    'cus_phone' => '',
    'cus_add1' => '',
    'cus_city' => '',
    'cus_country' => 'Bangladesh',
    'shipping_method' => 'NO',
    'product_name' => 'Event Tickets',
    'product_category' => 'Tickets',
    'product_profile' => 'general'
];

// Fill customer info from users table if available
$stmt = mysqli_prepare($conn, "SELECT name, email, phone FROM users WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
if ($user) {
    $post_data['cus_name'] = $user['name'] ?? '';
    $post_data['cus_email'] = $user['email'] ?? '';
    $post_data['cus_phone'] = $user['phone'] ?? '';
}

// Ensure orders table has a details column to store cart JSON
@mysqli_query($conn, "ALTER TABLE orders ADD COLUMN IF NOT EXISTS details TEXT NULL");

// Record a pending order with details (cart JSON)
$details_json = json_encode($cart);
$order_sql = "INSERT INTO orders (user_id, tran_id, amount, status, details, created_at) VALUES (?, ?, ?, 'pending', ?, NOW())";
$order_stmt = mysqli_prepare($conn, $order_sql);
if ($order_stmt) {
    mysqli_stmt_bind_param($order_stmt, "isds", $user_id, $tran_id, $amount, $details_json);
    mysqli_stmt_execute($order_stmt);
    mysqli_stmt_close($order_stmt);
}

// Send request to SSLCommerz
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    $err = curl_error($ch);
    curl_close($ch);
    echo json_encode(['success' => false, 'message' => 'cURL error: ' . $err]);
    exit;
}
curl_close($ch);

$result = json_decode($response, true);
if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Invalid response from gateway', 'raw' => $response]);
    exit;
}

// Gateway returns 'GatewayPageURL' on success
if (!empty($result['GatewayPageURL'])) {
    echo json_encode(['success' => true, 'redirect_url' => $result['GatewayPageURL'], 'data' => $result]);
    exit;
}

// Otherwise return error
echo json_encode(['success' => false, 'message' => 'Gateway error', 'response' => $result]);

?>
