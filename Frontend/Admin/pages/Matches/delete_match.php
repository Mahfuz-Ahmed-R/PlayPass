<?php
include __DIR__ . '/../../../../Backend/PHP/connection.php';
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$match_id = intval($_GET['id']);

$sql = "DELETE FROM match_table WHERE match_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $match_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: Matches.php");
    exit;
} else {
    echo "Delete failed: " . mysqli_error($conn);
}
?>