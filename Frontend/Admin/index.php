<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../Frontend/User/assets/img/pp.png" type="image/x-icon">
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e8f4f8 0%, #f0e8f4 100%);
            min-height: 100vh;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: white;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            padding-left: 10px;
        }

        .logo i {
            font-size: 20px;
            color: #6366f1;
        }

        .logo span {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: #1a1a1a;
        }

        .nav-link.active {
            background: #7ed321;
            color: white;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .upgrade-box {
            margin-top: auto;
            padding: 20px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            text-align: center;
            margin-top: 40px;
        }

        .upgrade-box p {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            color: #1a1a1a;
        }

        .upgrade-box a {
            color: #6366f1;
            text-decoration: none;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 240px;
            padding: 30px 40px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .date-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .date-badge i {
            color: #6366f1;
        }

        .date-badge span {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 40px 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 300px;
            font-size: 14px;
            background: white;
        }

        .search-box i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 600;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        /* Dashboard Header */
        .dashboard-header {
            margin-bottom: 30px;
        }

        .welcome-text {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .welcome-subtext {
            font-size: 14px;
            color: #6b7280;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #ddd6fe 0%, #e0e7ff 100%);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #d1fae5 0%, #dcfce7 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #fed7aa 0%, #fde68a 100%);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.purple {
            background: #8b5cf6;
            color: white;
        }

        .stat-icon.blue {
            background: #3b82f6;
            color: white;
        }

        .stat-icon.green {
            background: #10b981;
            color: white;
        }

        .stat-icon.orange {
            background: #f59e0b;
            color: white;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            margin-top: 8px;
        }

        .stat-change.positive {
            background: #d1fae5;
            color: #065f46;
        }

        .stat-change.negative {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }

        /* Recent Matches */
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .view-all {
            color: #6366f1;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        .match-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border-radius: 8px;
            background: #f9fafb;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .match-item:hover {
            background: #f3f4f6;
        }

        .match-teams {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .team-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .match-vs {
            color: #9ca3af;
            font-size: 12px;
            font-weight: 500;
        }

        .match-score {
            font-size: 16px;
            font-weight: 700;
            color: #6366f1;
            margin: 0 16px;
        }

        .match-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .match-status.live {
            background: #fee2e2;
            color: #991b1b;
        }

        .match-status.upcoming {
            background: #dbeafe;
            color: #1e40af;
        }

        .match-status.ended {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .action-btn.tertiary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .action-btn i {
            font-size: 18px;
        }

        /* Recent Activity */
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #6366f1;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .activity-time {
            font-size: 12px;
            color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                padding: 30px 10px;
            }

            .main-content {
                margin-left: 80px;
                padding: 30px 20px;
            }

            .logo span,
            .nav-link span,
            .upgrade-box {
                display: none;
            }

            .logo {
                justify-content: center;
                padding-left: 0;
            }

            .nav-link {
                justify-content: center;
                padding: 12px 8px;
            }

            .nav-link i {
                margin: 0;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 1000;
                width: 240px;
                padding: 30px 20px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar.active .logo span,
            .sidebar.active .nav-link span,
            .sidebar.active .upgrade-box {
                display: block;
            }

            .sidebar.active .logo {
                justify-content: flex-start;
                padding-left: 10px;
            }

            .sidebar.active .nav-link {
                justify-content: flex-start;
                padding: 12px 16px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
                width: 100%;
            }

            .search-box input {
                width: 150px;
            }

            .header-right {
                gap: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .search-box {
                display: none;
            }

            .date-badge span {
                font-size: 12px;
            }

            .welcome-text {
                font-size: 22px;
            }

            .stat-value {
                font-size: 24px;
            }
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .mobile-exit-btn {
            display: none;
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .mobile-exit-btn {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <i class="fas fa-futbol"></i>
                <span>PlayPass</span>
                <button class="mobile-exit-btn mx-5" onclick="window.location.href='index.php'">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./pages/Matches/matches.php" class="nav-link">
                        <i class="fas fa-futbol"></i>
                        <span>Matches</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./pages/Teams/teams.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./pages/Stadium/stadium.php" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Stadiums</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./pages/Statistic/statistic.php" class="nav-link">
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
                    <span>November 30, 2025</span>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </div>
                    <div class="profile-pic">
                        <i class="fas fa-user"></i>
                    </div>


                </div>
            </div>

            <div class="dashboard-header">
                <h1 class="welcome-text">Welcome back, Admin! 👋</h1>
                <p class="welcome-subtext">Here's what's happening with your matches today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card purple">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">24</div>
                            <div class="stat-label">Total Matches</div>
                            <span class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12%
                            </span>
                        </div>
                        <div class="stat-icon purple">
                            <i class="fas fa-futbol"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card blue">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">16</div>
                            <div class="stat-label">Active Teams</div>
                            <span class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 8%
                            </span>
                        </div>
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">8</div>
                            <div class="stat-label">Stadiums</div>
                            <span class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 4%
                            </span>
                        </div>
                        <div class="stat-icon green">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">5</div>
                            <div class="stat-label">Live Matches</div>
                            <span class="stat-change negative">
                                <i class="fas fa-arrow-down"></i> 2%
                            </span>
                        </div>
                        <div class="stat-icon orange">
                            <i class="fas fa-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Matches -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Matches</h3>
                        <a href="./pages/Matches/matches.php" class="view-all">View All →</a>
                    </div>

                    <div class="match-item">
                        <div class="match-teams">
                            <span class="team-name">Manchester United</span>
                            <span class="match-vs">vs</span>
                            <span class="team-name">Liverpool</span>
                        </div>
                        <span class="match-score">2 - 1</span>
                        <span class="match-status live">
                            <i class="fas fa-circle"></i> Live
                        </span>
                    </div>

                    <div class="match-item">
                        <div class="match-teams">
                            <span class="team-name">Barcelona</span>
                            <span class="match-vs">vs</span>
                            <span class="team-name">Real Madrid</span>
                        </div>
                        <span class="match-score">3 - 3</span>
                        <span class="match-status ended">
                            Ended
                        </span>
                    </div>

                    <div class="match-item">
                        <div class="match-teams">
                            <span class="team-name">Chelsea</span>
                            <span class="match-vs">vs</span>
                            <span class="team-name">Arsenal</span>
                        </div>
                        <span class="match-score">18:00</span>
                        <span class="match-status upcoming">
                            Upcoming
                        </span>
                    </div>

                    <div class="match-item">
                        <div class="match-teams">
                            <span class="team-name">Manchester City</span>
                            <span class="match-vs">vs</span>
                            <span class="team-name">Tottenham</span>
                        </div>
                        <span class="match-score">1 - 0</span>
                        <span class="match-status live">
                            <i class="fas fa-circle"></i> Live
                        </span>
                    </div>

                    <div class="match-item">
                        <div class="match-teams">
                            <span class="team-name">Bayern Munich</span>
                            <span class="match-vs">vs</span>
                            <span class="team-name">Borussia Dortmund</span>
                        </div>
                        <span class="match-score">20:30</span>
                        <span class="match-status upcoming">
                            Upcoming
                        </span>
                    </div>
                </div>

                <!-- Sidebar Content -->
                <div>
                    <!-- Quick Actions -->
                    <div class="content-card" style="margin-bottom: 24px;">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>

                        <div class="quick-actions">
                            <a href="./pages/Create_Match/create_match.php" class="action-btn">
                                <i class="fas fa-plus-circle"></i>
                                Create New Match
                            </a>
                            <a href="./pages/Create_Teams/create_teams.php" class="action-btn secondary">
                                <i class="fas fa-users"></i>
                                Add New Team
                            </a>
                            <a href="./pages/Create_Stadium/create_stadium.php" class="action-btn tertiary">
                                <i class="fas fa-building"></i>
                                Add Stadium
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activity</h3>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-futbol"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">New match created: Arsenal vs Chelsea</p>
                                <p class="activity-time">2 hours ago</p>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">Team "Inter Milan" was added</p>
                                <p class="activity-time">5 hours ago</p>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-stadium"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">Stadium "Camp Nou" updated</p>
                                <p class="activity-time">1 day ago</p>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">Match schedule modified</p>
                                <p class="activity-time">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>

</html>