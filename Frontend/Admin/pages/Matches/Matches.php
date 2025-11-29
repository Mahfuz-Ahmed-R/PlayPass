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
                    <span>October 19, 2021</span>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search by name, name or ID...">
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
            
            <div class="orders-header">
                <h1>Matches</h1>
                <div class="tab-buttons">
                    <button class="tab-btn active">Daily</button>
                    <button class="tab-btn">Monthly</button>
                </div>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card new">
                    <div class="stat-label">New Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">245</span>
                        <span class="stat-divider">|</span>
                        <span class="stat-impression">Impression · 20%</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-label">Pending Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">123</span>
                        <span class="stat-divider">|</span>
                        <span class="stat-impression">Impression · 11%</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
                <div class="stat-card delivered">
                    <div class="stat-label">Delivered Matches</div>
                    <div class="stat-value">
                        <span class="stat-number">150</span>
                        <span class="stat-divider">|</span>
                        <span class="stat-impression">Impression · 18%</span>
                        <i class="fas fa-info-circle stat-info"></i>
                    </div>
                </div>
            </div>
            
                <div class="order-tabs-row">
                    <div class="order-tabs" id="orderTabs">
                        <div class="order-tab active">All Matches</div>
                        <div class="order-tab">Pending Matches</div>
                        <div class="order-tab">Delivered Matches</div>
                        <div class="order-tab">Booked Matches</div>
                        <div class="order-tab">Cancelled Matches</div>
                    </div>

                    <a href="../Create_Match/create_match.php" class="create-match-btn">
                        <i class="fas fa-plus"></i>
                        <span>Create Match</span>
                    </a>
                </div>
            
            <div class="orders-table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-list"></i> Order ID</th>
                            <th><i class="fas fa-calendar"></i> Ordered Date</th>
                            <th><i class="fas fa-box"></i> Product Name</th>
                            <th><i class="fas fa-dollar-sign"></i> Product Price</th>
                            <th><i class="fas fa-chart-line"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="order-id">#123245</td>
                            <td>14-12-2020</td>
                            <td>Decorative box</td>
                            <td>125 USD</td>
                            <td><span class="status-badge delivered"><i class="fas fa-check-circle"></i> Delivered</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#678457</td>
                            <td>13-12-2020</td>
                            <td>Plantation box</td>
                            <td>120 USD</td>
                            <td><span class="status-badge cancelled"><i class="fas fa-times-circle"></i> Cancelled</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#123245</td>
                            <td>12-12-2020</td>
                            <td>Camera film</td>
                            <td>155 USD</td>
                            <td><span class="status-badge delivered"><i class="fas fa-check-circle"></i> Delivered</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#123245</td>
                            <td>12-12-2020</td>
                            <td>Camera film</td>
                            <td>156 USD</td>
                            <td><span class="status-badge delivered"><i class="fas fa-check-circle"></i> Delivered</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#87245</td>
                            <td>10-12-2020</td>
                            <td>Visual lace</td>
                            <td>125 USD</td>
                            <td><span class="status-badge delivered"><i class="fas fa-check-circle"></i> Delivered</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#273245</td>
                            <td>11-11-2020</td>
                            <td>Decorative box</td>
                            <td>180 USD</td>
                            <td><span class="status-badge pending"><i class="fas fa-clock"></i> Pending</span></td>
                        </tr>
                        <tr>
                            <td class="order-id">#789245</td>
                            <td>10-11-2020</td>
                            <td>Decorative box</td>
                            <td>190 USD</td>
                            <td><span class="status-badge delivered"><i class="fas fa-check-circle"></i> Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>