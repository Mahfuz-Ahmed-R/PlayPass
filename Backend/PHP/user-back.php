<?php
include __DIR__ . "/connection.php";
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;

if ($action !== 'getUser') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

if (!$userId || !is_numeric($userId)) {
    echo json_encode(['success' => false, 'message' => 'Invalid user_id']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT user_id, name, email, phone FROM users WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Sanitize output
unset($user['password']);

echo json_encode(['success' => true, 'user' => $user]);

?>
