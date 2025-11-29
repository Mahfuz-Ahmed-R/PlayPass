<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Management - Unitip</title>
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
            color:#7ed321;
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
            background:#7ed321;
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
        
        .upgrade-box img {
            width: 120px;
            margin-bottom: 10px;
        }
        
        .upgrade-box p {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            color: #1a1a1a;
        }
        
        .upgrade-box a {
            color:#7ed321;
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
            color:#7ed321;
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
        
        /* Match Header */
        .match-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }
        
        .match-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }
        
        .match-header .emoji {
            font-size: 32px;
        }
        
        /* Match Form Container */
        .match-form-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 40px;
            max-width: 900px;
        }
        
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-section-title i {
            color:#7ed321;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .form-label i {
            color: #7ed321;
            font-size: 12px;
        }
        
        .form-control, .form-select {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f9fafb;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color:#7ed321;
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
            cursor: pointer;
        }
        
        .time-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .status-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .status-btn {
            padding: 12px 24px;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-btn:hover {
            border-color:#7ed321;
            background: white;
        }
        
        .status-btn.active {
            background:#7ed321;
            color: white;
            border-color:#7ed321;
        }
        
        .status-btn.live.active {
            background: #ef4444;
            border-color: #ef4444;
        }
        
        .status-btn.upcoming.active {
            background: #3b82f6;
            border-color: #3b82f6;
        }
        
        .status-btn.ended.active {
            background: #6b7280;
            border-color: #6b7280;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #f3f4f6;
        }
        
        .btn-primary-custom {
            padding: 14px 32px;
            background: #7ed321;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary-custom:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        
        .btn-secondary-custom {
            padding: 14px 32px;
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-secondary-custom:hover {
            border-color:#7ed321;
            color:#7ed321;
        }
        
        /* Info Card */
        .info-card {
            background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
            padding: 20px;
            border-radius: 12px;
            margin-top: 24px;
            display: flex;
            align-items: start;
            gap: 12px;
        }
        
        .info-card i {
            color: #3b82f6;
            font-size: 20px;
            margin-top: 2px;
        }
        
        .info-card-content h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        
        .info-card-content p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .match-form-container {
                padding: 24px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .search-box {
                display: none;
            }
            
            .date-badge span {
                font-size: 12px;
            }
            
            .match-header h1 {
                font-size: 24px;
            }
            
            .status-buttons {
                flex-direction: column;
            }
            
            .status-btn {
                width: 100%;
                justify-content: center;
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
                    Match Details
                </div>
                
                <form id="matchForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-building"></i>
                                Stadium
                            </label>
                            <select class="form-select" id="stadium" required>
                                <option value="" selected>Select Stadium</option>
                                <option value="wembley">Wembley Stadium</option>
                                <option value="camp-nou">Camp Nou</option>
                                <option value="santiago">Santiago Bernabéu</option>
                                <option value="old-trafford">Old Trafford</option>
                                <option value="allianz">Allianz Arena</option>
                                <option value="anfield">Anfield</option>
                                <option value="emirates">Emirates Stadium</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-day"></i>
                                Match Date
                            </label>
                            <input type="date" class="form-control" id="matchDate" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-home"></i>
                                Home Team
                            </label>
                            <select class="form-select" id="homeTeam" required>
                                <option value="" selected>Select Home Team</option>
                                <option value="manchester-united">Manchester United</option>
                                <option value="liverpool">Liverpool</option>
                                <option value="chelsea">Chelsea</option>
                                <option value="arsenal">Arsenal</option>
                                <option value="manchester-city">Manchester City</option>
                                <option value="tottenham">Tottenham Hotspur</option>
                                <option value="barcelona">Barcelona</option>
                                <option value="real-madrid">Real Madrid</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-plane"></i>
                                Away Team
                            </label>
                            <select class="form-select" id="awayTeam" required>
                                <option value="" selected>Select Away Team</option>
                                <option value="manchester-united">Manchester United</option>
                                <option value="liverpool">Liverpool</option>
                                <option value="chelsea">Chelsea</option>
                                <option value="arsenal">Arsenal</option>
                                <option value="manchester-city">Manchester City</option>
                                <option value="tottenham">Tottenham Hotspur</option>
                                <option value="barcelona">Barcelona</option>
                                <option value="real-madrid">Real Madrid</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i>
                                Start Time
                            </label>
                            <input type="time" class="form-control" id="startTime" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-hourglass-end"></i>
                                End Time
                            </label>
                            <input type="time" class="form-control" id="endTime" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">
                                <i class="fas fa-signal"></i>
                                Match Status
                            </label>
                            <div class="status-buttons">
                                <button type="button" class="status-btn live" onclick="selectStatus('live')">
                                    <i class="fas fa-circle"></i>
                                    Live
                                </button>
                                <button type="button" class="status-btn upcoming active" onclick="selectStatus('upcoming')">
                                    <i class="fas fa-clock"></i>
                                    Upcoming
                                </button>
                                <button type="button" class="status-btn ended" onclick="selectStatus('ended')">
                                    <i class="fas fa-check-circle"></i>
                                    Ended
                                </button>
                            </div>
                            <input type="hidden" id="matchStatus" value="upcoming">
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-lightbulb"></i>
                        <div class="info-card-content">
                            <h4>Pro Tip</h4>
                            <p>Make sure to verify all match details before saving. You can always edit the match information later from the matches dashboard.</p>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-save"></i>
                            Save Match
                        </button>
                        <button type="button" class="btn-secondary-custom" onclick="resetForm()">
                            <i class="fas fa-redo"></i>
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        let selectedStatus = 'upcoming';
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
        
        function selectStatus(status) {
            selectedStatus = status;
            document.getElementById('matchStatus').value = status;
            
            // Remove active class from all buttons
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to selected button
            event.target.closest('.status-btn').classList.add('active');
        }
        
        function resetForm() {
            document.getElementById('matchForm').reset();
            selectStatus('upcoming');
            document.querySelector('.status-btn.upcoming').classList.add('active');
        }
        
        // Set today's date as default
        document.getElementById('matchDate').valueAsDate = new Date();
        
        // Form submission
        document.getElementById('matchForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                stadium: document.getElementById('stadium').value,
                matchDate: document.getElementById('matchDate').value,
                homeTeam: document.getElementById('homeTeam').value,
                awayTeam: document.getElementById('awayTeam').value,
                startTime: document.getElementById('startTime').value,
                endTime: document.getElementById('endTime').value,
                status: selectedStatus
            };

            console.log('Match Created:', formData);
            alert('Match created successfully! Check console for details.');
        });

        // --- Additional site-wide navigation behaviour (copied from main script.js) ---
        // Navigation link functionality: only prevent default for placeholder/hash links
        document.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (!href || href === '#' || href.indexOf('#') === 0) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-link').forEach(function(l) { l.classList.remove('active'); });
                    this.classList.add('active');
                    // close sidebar on mobile
                    if (window.innerWidth <= 768) toggleSidebar();
                } else {
                    // For real links, briefly mark active for feedback
                    document.querySelectorAll('.nav-link').forEach(function(l) { l.classList.remove('active'); });
                    this.classList.add('active');
                }
            });
        });

        // Create Match button: on small screens, close the sidebar when tapped
        var createBtn = document.querySelector('.create-match-btn');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                if (window.innerWidth <= 768) toggleSidebar();
            });
        }
    </script>
</body>
</html>