<?php
include __DIR__ . '/../../../../Backend/PHP/connection.php';

$query = $_GET['query'] ?? '';
$results = null;

if ($query !== '') {
    $search = "%$query%";

    $sql = "SELECT 
                m.match_id AS match_id,
                s.name AS stadium_name,
                ht.name AS home_team,
                at.name AS away_team,
                m.match_date AS match_date,
                m.start_time,
                m.end_time,
                m.status
            FROM match_table m
            JOIN stadium s ON m.stadium_id = s.stadium_id
            JOIN team ht ON m.home_team_id = ht.team_id
            JOIN team at ON m.away_team_id = at.team_id
            WHERE s.name LIKE ?
               OR ht.name LIKE ?
               OR at.name LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>
