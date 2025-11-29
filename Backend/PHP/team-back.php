<?php 
    include __DIR__ . "/connection.php";

    $response = array('success' => false, 'message' => '');

    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode($response);
        exit;
    }

    // Get and validate form data
    $name = isset($_POST['teamName']) ? trim($_POST['teamName']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $coach_name = isset($_POST['coachName']) ? trim($_POST['coachName']) : '';

    // Validate inputs
    if (empty($name) || empty($country) || empty($coach_name)) {
        echo json_encode($response);
        exit;
    }

    // Validate input lengths
    if (strlen($name) > 100 || strlen($coach_name) > 100) {
        echo json_encode($response);
        exit;
    }

    // Check if team name already exists
    $check_sql = "SELECT * FROM `team` WHERE name = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    
    if (!$stmt) {
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode($response);
        exit;
    }

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO `team` (name, country, coach_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sss", $name, $country, $coach_name);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $team_id = mysqli_insert_id($conn);
        $response['success'] = true;
        $response['message'] = 'Team added successfully!';
        $response['team_id'] = $team_id;
    }

    echo json_encode($response);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
?>