<?php
include __DIR__ . "/../../../../Backend/PHP/connection.php";

$editMode = false;
$matchData = null;

if (isset($_GET['id'])) {
    $match_id = intval($_GET['id']);
    $editMode = true;

    $sql = "SELECT * FROM match_table WHERE match_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $match_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $matchData = mysqli_fetch_assoc($result);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? "Edit Match" : "Create New Match"; ?>
    </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <i class="fas fa-futbol"></i>
                <span>unitip</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <i class="fas fa-futbol"></i>
                        <span>Matches</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
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
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedule</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
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

        <main class="main-content">
            <div class="header">
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="date-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <span>November 30, 2025</span>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search matches, teams...">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="profile-pic">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <div class="match-header">
                <h1>Create Match</h1>
                <span class="emoji">⚽</span>
            </div>

            <div class="match-form-container">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    <?php echo $editMode ? "Edit Match" : "Create New Match"; ?>

                </div>



                <form method="post" id="matchForm">

                    <?php if ($editMode): ?>
                        <input type="hidden" name="match_id" value="<?= $matchData['match_id'] ?>">
                    <?php endif; ?>

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Stadium</label>
                            <select class="form-select" name="stadium" required>
                                <option value="">Select Stadium</option>

                                <?php
                                $stadiums = mysqli_query($conn, "SELECT stadium_id, name FROM stadium");
                                while ($row = mysqli_fetch_assoc($stadiums)) {
                                    $selected = ($editMode && $matchData['stadium_id'] == $row['stadium_id']) ? "selected" : "";
                                    echo "<option value='{$row['stadium_id']}' $selected>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Match Date</label>
                            <input type="date"
                                name="match_date"
                                class="form-control"
                                value="<?= $editMode ? $matchData['match_date'] : '' ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Home Team</label>
                            <select class="form-select" name="homeTeam" required>
                                <option value="">Select Home Team</option>

                                <?php
                                $teams = mysqli_query($conn, "SELECT team_id, name FROM team");
                                while ($t = mysqli_fetch_assoc($teams)) {
                                    $selected = ($editMode && $matchData['home_team_id'] == $t['team_id']) ? "selected" : "";
                                    echo "<option value='{$t['team_id']}' $selected>{$t['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Away Team</label>
                            <select class="form-select" name="awayTeam" required>
                                <option value="">Select Away Team</option>

                                <?php
                                $teams2 = mysqli_query($conn, "SELECT team_id, name FROM team");
                                while ($t = mysqli_fetch_assoc($teams2)) {
                                    $selected = ($editMode && $matchData['away_team_id'] == $t['team_id']) ? "selected" : "";
                                    echo "<option value='{$t['team_id']}' $selected>{$t['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time"
                                name="startTime"
                                class="form-control"
                                value="<?= $editMode ? $matchData['start_time'] : '' ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time"
                                name="endTime"
                                class="form-control"
                                value="<?= $editMode ? $matchData['end_time'] : '' ?>"
                                required>
                        </div>

                        <!-- Match Status -->
                        <div class="form-group full-width">
                            <label>Match Status</label>

                            <?php $status = $editMode ? $matchData['status'] : "upcoming"; ?>

                            <div class="status-buttons">
                                <button type="button" class="status-btn live <?= $status === 'live' ? 'active' : '' ?>" onclick="selectStatus('live')">
                                    Live
                                </button>
                                <button type="button" class="status-btn upcoming <?= $status === 'upcoming' ? 'active' : '' ?>" onclick="selectStatus('upcoming')">
                                    Upcoming
                                </button>
                                <button type="button" class="status-btn ended <?= $status === 'ended' ? 'active' : '' ?>" onclick="selectStatus('ended')">
                                    Ended
                                </button>
                            </div>

                            <input type="hidden" name="status" id="matchStatus" value="<?= $status ?>">
                        </div>

                    </div>

                    <div class="action-buttons">
                        <button onclick="window.location.reload()" type="submit" name="submit" class="btn-primary-custom">
                            <i class="fas fa-save"></i>
                            <?= $editMode ? "Update Match" : "Save Match" ?>
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>


    <?php

    include __DIR__ . '/../../../../Backend/PHP/create_matches-back.php';
    ?>

    <script>
        let selectedStatus = 'upcoming';

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function selectStatus(status) {
            document.getElementById("matchStatus").value = status;

            document.querySelectorAll(".status-btn").forEach(btn => btn.classList.remove("active"));

            document.querySelector(".status-btn." + status).classList.add("active");
        }

        function resetForm() {
            document.getElementById('matchForm').reset();
            selectStatus('upcoming');
            document.querySelector('.status-btn.upcoming').classList.add('active');
        }

        // Set today's date as default
        document.getElementById('matchDate').valueAsDate = new Date();


        // --- Additional site-wide navigation behaviour (copied from main script.js) ---
        // Navigation link functionality: only prevent default for placeholder/hash links
        document.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (!href || href === '#' || href.indexOf('#') === 0) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-link').forEach(function(l) {
                        l.classList.remove('active');
                    });
                    this.classList.add('active');
                    // close sidebar on mobile
                    if (window.innerWidth <= 768) toggleSidebar();
                } else {
                    // For real links, briefly mark active for feedback
                    document.querySelectorAll('.nav-link').forEach(function(l) {
                        l.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });

        // Create Match button: on small screens, close the sidebar when tapped
        var createBtn = document.querySelector('.create-match-btn');
        if (createBtn) {
            createBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) toggleSidebar();
            });
        }
    </script>
</body>

</html>

<?php mysqli_close($conn); ?>