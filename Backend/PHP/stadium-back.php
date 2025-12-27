<?php
include __DIR__ . "/connection.php";

$response = array('success' => false, 'message' => '');

if (isset($_POST['submit'])) {

    $name = trim($_POST['stadiumName'] ?? '');
    $location = trim($_POST['locationName'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $contact_info = trim($_POST['contact'] ?? '');

    $check_sql = "SELECT * FROM stadium WHERE name = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if (!$check_stmt) {
        $response['message'] = "Database error (prepare failed)";
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, "s", $name);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $response['message'] = "Stadium name already exists.";
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_close($check_stmt);

    $sql = "INSERT INTO stadium (name, location, capacity, contact_info) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $response['message'] = "Insert prepare failed.";
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ssis", $name, $location, $capacity, $contact_info);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $response['success'] = true;
        $response['stadium_id'] = mysqli_insert_id($conn);
    }

    echo json_encode($response);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
