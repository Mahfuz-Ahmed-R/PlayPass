<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../../User/assets/img/pp.png" type="image/x-icon" />
    <title>Matches</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="matches.css">
</head>

<body>
    <div class="main-container">
        <!-- Sidebar -->


        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-content">
                    <i class="fas fa-futbol"></i>
                    <span>PlayPass</span>
                </div>
                <button class="close-sidebar-btn" onclick="closeSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../../index.php" class="nav-link">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Matches.php" class="nav-link active">
                        <i class="fas fa-futbol"></i>
                        <span>Matches</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Teams/teams.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Stadium/stadium.php" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Stadiums</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="date-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <span>October 19, 2021</span>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <form method="GET">
                            <input type="text" name="query" placeholder="Search by Team, Stadium..." required>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="profile-pic">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <div class="orders-header">
                <h1>Matches</h1>
                <div class="tab-buttons">
                    <button class="tab-btn active">Daily</button>
                    <button class="tab-btn">Monthly</button>
                </div>
            </div>

            <div class="stats-cards">
                <div class="stat-card new">
                    <div class="stat-label">Live Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">245</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-label">Upcoming Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">123</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
                <div class="stat-card delivered">
                    <div class="stat-label">Ended Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">150</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
            </div>

            <div class="order-tabs-row">
                <div class="order-tabs" id="orderTabs">
                    <div class="order-tab active">Live Matches</div>
                    <div class="order-tab">Upcoming Matches</div>
                    <div class="order-tab">Ended Matches</div>
                    <div class="order-tab">Cancelled Matches</div>
                </div>

                <a href="../Create_Match/create_match.php" class="create-match-btn">
                    <i class="fas fa-plus"></i>
                    <span>Create Match</span>
                </a>
            </div>
    <?php include __DIR__ . '/search.php'; ?>

            <div class="orders-table-container">
                <?php
                include __DIR__ . '/../../../../Backend/PHP/connection.php';

                // If search query exists
                if (!empty($query)) {
                    $searchSql = "SELECT m.*, h.name AS home_team_name, a.name AS away_team_name, s.name AS stadium_name
                      FROM match_table m
                      LEFT JOIN team h ON m.home_team_id = h.team_id
                      LEFT JOIN team a ON m.away_team_id = a.team_id
                      LEFT JOIN stadium s ON m.stadium_id = s.stadium_id
                      WHERE h.name LIKE ? OR a.name LIKE ? OR s.name LIKE ?
                      LIMIT 10";

                    $stmt = $conn->prepare($searchSql);
                    $likeQuery = "%$query%";
                    $stmt->bind_param("sss", $likeQuery, $likeQuery, $likeQuery);
                    $stmt->execute();
                    $results = $stmt->get_result();

                    if ($results->num_rows > 0) {
                        echo '<table class="orders-table">';
                        echo '<thead>
                    <tr>
                        <th>Home Team</th>
                        <th></th>
                        <th>Away Team</th>
                        <th>Stadium</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Match Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                  </thead><tbody>';

                        while ($row = $results->fetch_assoc()) {
                            $status = strtolower($row['status']);
                            $badgeClass = match ($status) {
                                'live' => 'bg-danger text-white',
                                'upcoming' => 'bg-primary text-white',
                                'ended' => 'bg-secondary text-white',
                                default => 'bg-light text-dark',
                            };

                            echo '<tr>
                        <td>' . htmlspecialchars($row['home_team_name']) . '</td>
                        <td class="fw-bold text-center">Vs</td>
                        <td>' . htmlspecialchars($row['away_team_name']) . '</td>
                        <td>' . htmlspecialchars($row['stadium_name']) . '</td>
                        <td>' . htmlspecialchars($row['start_time']) . '</td>
                        <td>' . htmlspecialchars($row['end_time']) . '</td>
                        <td>' . htmlspecialchars($row['match_date']) . '</td>
                        <td><span class="badge ' . $badgeClass . '">' . strtoupper($row['status']) . '</span></td>
                        <td>
                            <a href="../Create_Match/create_match.php?id=' . $row['match_id'] . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                            <a href="delete_match.php?id=' . $row['match_id'] . '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                        </td>
                      </tr>';
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<p>No matches found for your search.</p>';
                    }

                    $stmt->close();
                }
                else{

                // Always show latest matches
                $defaultSql = "SELECT m.*, h.name AS home_team_name, a.name AS away_team_name, s.name AS stadium_name
                   FROM match_table m
                   LEFT JOIN team h ON m.home_team_id = h.team_id
                   LEFT JOIN team a ON m.away_team_id = a.team_id
                   LEFT JOIN stadium s ON m.stadium_id = s.stadium_id
                   ORDER BY m.match_date DESC
                   LIMIT 10";

                $result = mysqli_query($conn, $defaultSql);
                echo '<table class="orders-table">';
                echo '<thead>
            <tr>
                <th>Home Team</th>
                <th></th>
                <th>Away Team</th>
                <th>Stadium</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Match Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
          </thead><tbody>';

                while ($row = mysqli_fetch_assoc($result)) {
                    $status = strtolower($row['status']);
                    $badgeClass = match ($status) {
                        'live' => 'bg-danger text-white',
                        'upcoming' => 'bg-primary text-white',
                        'ended' => 'bg-secondary text-white',
                        default => 'bg-light text-dark',
                    };

                    echo '<tr>
                <td>' . htmlspecialchars($row['home_team_name']) . '</td>
                <td class="fw-bold text-center">Vs</td>
                <td>' . htmlspecialchars($row['away_team_name']) . '</td>
                <td>' . htmlspecialchars($row['stadium_name']) . '</td>
                <td>' . htmlspecialchars($row['start_time']) . '</td>
                <td>' . htmlspecialchars($row['end_time']) . '</td>
                <td>' . htmlspecialchars($row['match_date']) . '</td>
                <td><span class="badge ' . $badgeClass . '">' . strtoupper($row['status']) . '</span></td>
                <td>
                    <a href="../Create_Match/create_match.php?id=' . $row['match_id'] . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                    <a href="delete_match.php?id=' . $row['match_id'] . '" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                </td>
              </tr>';
                }

                echo '</tbody></table>';
            }

                mysqli_close($conn);
                ?>
                
            </div>
            

        </main>
        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>