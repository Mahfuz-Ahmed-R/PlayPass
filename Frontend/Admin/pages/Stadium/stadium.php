<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadiums - Unitip</title>
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
        
        /* Stadium Grid */
        .stadium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }
        
        .stadium-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stadium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .stadium-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .stadium-image i {
            font-size: 64px;
            color: rgba(255,255,255,0.9);
        }
        
        .stadium-capacity-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #6366f1;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .stadium-info {
            padding: 24px;
        }
        
        .stadium-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }
        
        .stadium-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .stadium-detail i {
            width: 16px;
            color: #6366f1;
        }
        
        .stadium-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
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
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .empty-state i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .empty-state p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
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
            
            .stadium-grid {
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
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active">
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
                        <input type="text" placeholder="Search stadiums...">
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
                    <h1>Stadiums</h1>
                    <span style="font-size: 32px;">🏟️</span>
                </div>
                <a href="#" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add Stadium
                </a>
            </div>
            
            <!-- Stadium Grid -->
            <div class="stadium-grid">
                <div class="stadium-card">
                    <div class="stadium-image">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            75,000
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Wembley Stadium</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>London, United Kingdom</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>contact@wembley.com</span>
                        </div>
                        <div class="stadium-actions">
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
                
                <div class="stadium-card">
                    <div class="stadium-image" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            99,354
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Camp Nou</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Barcelona, Spain</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>info@campnou.com</span>
                        </div>
                        <div class="stadium-actions">
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
                
                <div class="stadium-card">
                    <div class="stadium-image" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            81,044
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Santiago Bernabéu</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Madrid, Spain</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>contact@bernabeu.com</span>
                        </div>
                        <div class="stadium-actions">
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
                
                <div class="stadium-card">
                    <div class="stadium-image" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            74,879
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Old Trafford</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Manchester, United Kingdom</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>info@oldtrafford.com</span>
                        </div>
                        <div class="stadium-actions">
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
                
                <div class="stadium-card">
                    <div class="stadium-image" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            75,024
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Allianz Arena</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Munich, Germany</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>contact@allianz-arena.com</span>
                        </div>
                        <div class="stadium-actions">
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
                
                <div class="stadium-card">
                    <div class="stadium-image" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <i class="fas fa-landmark"></i>
                        <div class="stadium-capacity-badge">
                            <i class="fas fa-users"></i>
                            54,074
                        </div>
                    </div>
                    <div class="stadium-info">
                        <h3 class="stadium-name">Anfield</h3>
                        <div class="stadium-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Liverpool, United Kingdom</span>
                        </div>
                        <div class="stadium-detail">
                            <i class="fas fa-envelope"></i>
                            <span>info@anfield.com</span>
                        </div>
                        <div class="stadium-actions">
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