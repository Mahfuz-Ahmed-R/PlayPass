<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Unitip</title>
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
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
            background: #6366f1;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
        
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }
        
        .filter-buttons {
            display: flex;
            gap: 12px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn.active {
            background: #6366f1;
            color: white;
            border-color: #6366f1;
        }
        
        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .stat-box.highlight {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }
        
        .stat-box.highlight .stat-label {
            color: rgba(255,255,255,0.9);
        }
        
        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .chart-card.full {
            grid-column: 1 / -1;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .chart-placeholder {
            height: 250px;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 14px;
        }
        
        /* Team Performance Table */
        .performance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .performance-table th {
            text-align: left;
            padding: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .performance-table td {
            padding: 16px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        
        .performance-table tr:hover {
            background: #f9fafb;
        }
        
        .team-rank {
            font-weight: 700;
            color: #6366f1;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #f3f4f6;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 4px;
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
            
            .charts-grid {
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
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
        
        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            background: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
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
                    <a href="#" class="nav-link">
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
                        <i class="fas fa-stadium"></i>
                        <span>Stadiums</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
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
            
            <div class="upgrade-box">
                <svg width="120" height="100" viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="50" r="25" fill="#FFB6C1"/>
                    <circle cx="55" cy="45" r="3" fill="#000"/>
                    <circle cx="65" cy="45" r="3" fill="#000"/>
                    <path d="M 50 55 Q 60 60 70 55" stroke="#000" stroke-width="2" fill="none"/>
                    <path d="M 40 35 Q 45 30 50 35" stroke="#000" stroke-width="2" fill="none"/>
                    <path d="M 70 35 Q 75 30 80 35" stroke="#000" stroke-width="2" fill="none"/>
                </svg>
                <p>Upgrade<br>your plan <a href="#">→</a></p>
            </div>
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
                        <input type="text" placeholder="Search statistics...">
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
            
            <div class="page-header">
                <div class="page-title">
                    <h1>Statistics</h1>
                    <span style="font-size: 32px;">📊</span>
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn">Today</button>
                    <button class="filter-btn">Week</button>
                    <button class="filter-btn active">Month</button>
                    <button class="filter-btn">Year</button>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="stats-overview">
                <div class="stat-box highlight">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Total Goals</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Matches Played</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">89%</div>
                    <div class="stat-label">Win Rate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">32K</div>
                    <div class="stat-label">Total Viewers</div>
                </div>
            </div>
            
            <!-- Charts Grid -->
            <div class="charts-grid">
                <div class="chart-card">
                    <h3 class="chart-title">Match Results Overview</h3>
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-pie" style="font-size: 48px;"></i>
                    </div>
                </div>
                
                <div class="chart-card">
                    <h3 class="chart-title">Goals per Match</h3>
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-line" style="font-size: 48px;"></i>
                    </div>
                </div>
                
                <div class="chart-card full">
                    <h3 class="chart-title">Team Performance Rankings</h3>
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Team</th>
                                <th>Matches</th>
                                <th>Wins</th>
                                <th>Goals</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="team-rank">1</td>
                                <td><strong>Manchester City</strong></td>
                                <td>12</td>
                                <td>10</td>
                                <td>34</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 95%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="team-rank">2</td>
                                <td><strong>Liverpool</strong></td>
                                <td>12</td>
                                <td>9</td>
                                <td>28</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 85%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="team-rank">3</td>
                                <td><strong>Arsenal</strong></td>
                                <td>12</td>
                                <td>8</td>
                                <td>26</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 80%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="team-rank">4</td>
                                <td><strong>Chelsea</strong></td>
                                <td>12</td>
                                <td>7</td>
                                <td>22</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 70%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="team-rank">5</td>
                                <td><strong>Manchester United</strong></td>
                                <td>12</td>
                                <td>6</td>
                                <td>19</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 65%"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        // Filter button functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>