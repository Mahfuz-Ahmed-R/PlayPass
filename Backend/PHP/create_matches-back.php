<?php
include __DIR__ . "/connection.php";

$response = array('success' => false, 'message' => '');

if (isset($_POST['submit'])) {

    $match_id = trim($_POST['match_id'] ?? '');  
    $stadium_id = trim($_POST['stadium'] ?? '');
    $home_team_id = trim($_POST['homeTeam'] ?? '');
    $away_team_id = trim($_POST['awayTeam'] ?? '');
    $match_date = trim($_POST['match_date'] ?? '');
    $start_time = trim($_POST['startTime'] ?? '');
    $end_time = trim($_POST['endTime'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($stadium_id === '' || $home_team_id === '' || $away_team_id === '' || 
        $match_date === '' || $start_time === '' || $end_time === '' || $status === '') {

        $response['message'] = "All fields are required.";
        echo json_encode($response);
        exit;
    }

    if ($home_team_id === $away_team_id) {
        $response['message'] = "Home and away team cannot be the same.";
        echo json_encode($response);
        exit;
    }

    if ($start_time === $end_time) {
        $response['message'] = "Start and end time cannot be the same.";
        echo json_encode($response);
        exit;
    }

    if (!empty($match_id)) {

        $sql = "UPDATE `match_table` 
                SET `stadium_id` = ?, 
                    `home_team_id` = ?, 
                    `away_team_id` = ?, 
                    `match_date` = ?, 
                    `start_time` = ?, 
                    `end_time` = ?, 
                    `status` = ?
                WHERE `match_id` = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            $response['message'] = "Database prepare failed: " . mysqli_error($conn);
            echo json_encode($response);
            exit;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssssssi",
            $stadium_id,
            $home_team_id,
            $away_team_id,
            $match_date,
            $start_time,
            $end_time,
            $status,
            $match_id
        );

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $response['success'] = true;
            $response['message'] = "Match updated successfully!";
        } else {
            $response['message'] = "Update failed: " . mysqli_error($conn);
        }

    } else {

        $sql = "INSERT INTO `match_table` 
                (`stadium_id`, `home_team_id`, `away_team_id`, `match_date`, `start_time`, `end_time`, `status`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            $response['message'] = "Database prepare failed: " . mysqli_error($conn);
            echo json_encode($response);
            exit;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssssss",
            $stadium_id,
            $home_team_id,
            $away_team_id,
            $match_date,
            $start_time,
            $end_time,
            $status
        );

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $response['success'] = true;
            $response['match_id'] = mysqli_insert_id($conn);
            $response['message'] = "Match added successfully!";
        } else {
            $response['message'] = "Insert failed: " . mysqli_error($conn);
        }
    }

    echo json_encode($response);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
