<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams - Unitip</title>
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
        
        .add-btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        
        /* Team Grid */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
        
        .team-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .team-header {
            padding: 24px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            text-align: center;
            position: relative;
        }
        
        .team-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .team-logo i {
            font-size: 36px;
            color: #6366f1;
        }
        
        .team-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .team-country {
            font-size: 13px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .team-info {
            padding: 24px;
        }
        
        .team-detail {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .detail-icon {
            width: 36px;
            height: 36px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 2px;
        }
        
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .team-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 16px 0;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #6366f1;
        }
        
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .team-actions {
            display: flex;
            gap: 8px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        
        .action-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .action-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #f9fafb;
        }
        
        .action-btn.delete {
            color: #ef4444;
            border-color: #fee2e2;
        }
        
        .action-btn.delete:hover {
            border-color: #ef4444;
            background: #fef2f2;
        }
        
        /* Team Colors */
        .team-card:nth-child(2) .team-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .team-card:nth-child(2) .team-logo i,
        .team-card:nth-child(2) .stat-number {
            color: #3b82f6;
        }
        
        .team-card:nth-child(2) .detail-icon {
            color: #3b82f6;
        }
        
        .team-card:nth-child(3) .team-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .team-card:nth-child(3) .team-logo i,
        .team-card:nth-child(3) .stat-number {
            color: #10b981;
        }
        
        .team-card:nth-child(3) .detail-icon {
            color: #10b981;
        }
        
        .team-card:nth-child(4) .team-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .team-card:nth-child(4) .team-logo i,
        .team-card:nth-child(4) .stat-number {
            color: #f59e0b;
        }
        
        .team-card:nth-child(4) .detail-icon {
            color: #f59e0b;
        }
        
        .team-card:nth-child(5) .team-header {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        }
        
        .team-card:nth-child(5) .team-logo i,
        .team-card:nth-child(5) .stat-number {
            color: #ec4899;
        }
        
        .team-card:nth-child(5) .detail-icon {
            color: #ec4899;
        }
        
        .team-card:nth-child(6) .team-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        .team-card:nth-child(6) .team-logo i,
        .team-card:nth-child(6) .stat-number {
            color: #8b5cf6;
        }
        
        .team-card:nth-child(6) .detail-icon {
            color: #8b5cf6;
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
            
            .team-grid {
                grid-template-columns: 1fr;
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
                    <a href="#" class="nav-link active">
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
                        <input type="text" placeholder="Search teams...">
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
                    <h1>Teams</h1>
                    <span style="font-size: 32px;">👥</span>
                </div>
                <a href="#" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add Team
                </a>
            </div>
            
            <!-- Team Grid -->
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Manchester United</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            England
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Erik ten Hag</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">12</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">8</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">28</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>
                        
                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Liverpool</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            England
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Jürgen Klopp</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">12</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">32</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>
                        
                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Barcelona</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            Spain
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Xavi Hernández</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">11</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">9</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">30</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>
                        
                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Real Madrid</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            Spain
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Carlo Ancelotti</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">11</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">35</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>
                        
                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Bayern Munich</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            Germany
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Thomas Tuchel</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">9</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">31</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>

                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-header">
                        <div class="team-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="team-name">Paris Saint-Germain</h3>
                        <div class="team-country">
                            <i class="fas fa-flag"></i>
                            France
                        </div>
                    </div>
                    <div class="team-info">
                        <div class="team-detail">
                            <div class="detail-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Coach</div>
                                <div class="detail-value">Luis Enrique</div>
                            </div>
                        </div>
                        
                        <div class="team-stats">
                            <div class="stat-item">
                                <div class="stat-number">10</div>
                                <div class="stat-label">Matches</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">8</div>
                                <div class="stat-label">Wins</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">27</div>
                                <div class="stat-label">Goals</div>
                            </div>
                        </div>

                        <div class="team-actions">
                            <button class="action-btn">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>   
                            <button class="action-btn delete">
                                <i class="fas fa-trash"></i>
                            </button>
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
