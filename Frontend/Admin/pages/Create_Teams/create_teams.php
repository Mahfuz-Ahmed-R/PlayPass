<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Unitip</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="teams.css">
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
                    <a href="../../pages/Matches/Matches.php" class="nav-link">
                        <i class="fas fa-futbol"></i>
                        <span>Matches</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="teams.php" class="nav-link active">
                        <i class="fas fa-users"></i>
                        <span>Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../pages/Stadium/stadium.php" class="nav-link">
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
                <h1>Add Team</h1>
                <span class="emoji">👥</span>
            </div>
            
            <div class="form-container">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    Team Information
                </div>
                
                <form method="post" id="teamForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-shield-alt"></i>
                                Team Name
                            </label>
                            <input type="text" class="form-control" id="teamName" name="teamName" placeholder="Enter team name" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-flag"></i>
                                Country
                            </label>
                            <select class="form-select" id="country" name="country" required>
                                <option value="" selected>Select Country</option>
                                <option value="england">England</option>
                                <option value="spain">Spain</option>
                                <option value="germany">Germany</option>
                                <option value="italy">Italy</option>
                                <option value="france">France</option>
                                <option value="portugal">Portugal</option>
                                <option value="netherlands">Netherlands</option>
                                <option value="belgium">Belgium</option>
                                <option value="brazil">Brazil</option>
                                <option value="argentina">Argentina</option>
                                <option value="usa">United States</option>
                                <option value="mexico">Mexico</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label class="form-label">
                                <i class="fas fa-user-tie"></i>
                                Coach Name
                            </label>
                            <input type="text" class="form-control" id="coachName" name="coachName" placeholder="Enter coach's full name" required>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <i class="fas fa-lightbulb"></i>
                        <div class="info-card-content">
                            <h4>Pro Tip</h4>
                            <p>Make sure to enter complete and accurate team information. This data will be used across all match scheduling and statistics.</p>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" name="submit" class="btn-primary-custom">
                            <i class="fas fa-save"></i>
                            Save Team
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

        <?php

      include __DIR__ . '/../../../../Backend/PHP/team-back.php';
    ?>
    
    <script src="script.js"></script>
</body>
</html>