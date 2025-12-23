<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include __DIR__ . '/../../../../Backend/PHP/connection.php';

// Get match_id from URL parameter
$match_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch match data from database
$match = null;
if ($match_id > 0) {
    $sql = "SELECT m.*, 
                   h.name AS home_team_name, 
                   a.name AS away_team_name, 
                   s.name AS stadium_name,
                   s.location AS stadium_location
            FROM match_table m
            LEFT JOIN team h ON m.home_team_id = h.team_id
            LEFT JOIN team a ON m.away_team_id = a.team_id
            LEFT JOIN stadium s ON m.stadium_id = s.stadium_id
            WHERE m.match_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $match_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $match = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// If match not found, redirect or show error
if (!$match) {
    // You can redirect or show an error message
    // For now, we'll set default values
    $match = [
        'match_id' => 0,
        'poster_url' => '../../assets/img/img3.jpg',
        'home_team_name' => 'Team A',
        'away_team_name' => 'Team B',
        'stadium_name' => 'Stadium',
        'stadium_location' => '',
        'match_date' => date('Y-m-d'),
        'start_time' => '20:00:00',
        'status' => 'upcoming'
    ];
}

// Format date and time
$match_date = $match['match_date'];
$start_time = $match['start_time'];
$formatted_date = date('F j, Y', strtotime($match_date));
$formatted_time = date('g:i A', strtotime($start_time));

// Determine if match is live
$is_live = strtolower($match['status']) === 'live';
$status_lower = strtolower($match['status'] ?? 'upcoming');

// Build match title
$match_title = ($match['home_team_name'] ?? 'Team A') . ' vs ' . ($match['away_team_name'] ?? 'Team B');

// Build stadium location string
$stadium_location = $match['stadium_name'] ?? 'Stadium';
if (!empty($match['stadium_location'])) {
    $stadium_location .= ', ' . $match['stadium_location'];
}

// Set poster URL with fallback
$poster_url = !empty($match['poster_url']) ? $match['poster_url'] : '../../assets/img/img3.jpg';

// Fetch ticket categories for this stadium
$ticket_categories = [];
$stadium_id = $match['stadium_id'] ?? 0;

if ($stadium_id > 0) {
    $category_sql = "SELECT category_id, category_name, price, status 
                     FROM ticket_category 
                     WHERE stadium_id = ? AND (status = 'active' OR status IS NULL)
                     ORDER BY price DESC";
    
    $category_stmt = mysqli_prepare($conn, $category_sql);
    if ($category_stmt) {
        mysqli_stmt_bind_param($category_stmt, "i", $stadium_id);
        mysqli_stmt_execute($category_stmt);
        $category_result = mysqli_stmt_get_result($category_stmt);
        
        while ($category_row = mysqli_fetch_assoc($category_result)) {
            $ticket_categories[] = $category_row;
        }
        mysqli_stmt_close($category_stmt);
    }
}

// Function to get icon for category
function getCategoryIcon($category_name) {
    $category_lower = strtolower($category_name);
    if (strpos($category_lower, 'vip') !== false) {
        return 'fas fa-crown';
    } elseif (strpos($category_lower, 'regular') !== false || strpos($category_lower, 'standard') !== false) {
        return 'fas fa-star';
    } elseif (strpos($category_lower, 'economy') !== false || strpos($category_lower, 'budget') !== false) {
        return 'fas fa-ticket-alt';
    } else {
        return 'fas fa-ticket-alt'; // Default icon
    }
}

// Function to get CSS class for category row
function getCategoryRowClass($category_name) {
    $category_lower = strtolower($category_name);
    if (strpos($category_lower, 'vip') !== false) {
        return 'vip-row';
    } elseif (strpos($category_lower, 'regular') !== false || strpos($category_lower, 'standard') !== false) {
        return 'regular-row';
    } elseif (strpos($category_lower, 'economy') !== false || strpos($category_lower, 'budget') !== false) {
        return 'economy-row';
    } else {
        return 'regular-row'; // Default class
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../../assets/img/pp.png" type="image/x-icon" />
    <title>Event Details | playpass.live</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
    <!-- Font Awesome CDN -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="eventdetails.css" />
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link rel="stylesheet" href="../../components/Responsive_Navbar/responsive_navbar.css" />
    <!-- Cart Component CSS -->
    <link rel="stylesheet" href="../../components/Cart/cart.css" />
  </head>
  <body>
    <main class="d-flex flex-column min-vh-100">
      <!-- Navbar start -->
      <nav class="navbar sticky-top bg-white shadow-sm">
          <div class="container-fluid">
            <div class="d-flex align-items-center">
              <button
                class="navbar-toggler me-2 d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar"
                aria-label="Toggle navigation"
              >
                <span class="navbar-toggler-icon"></span>
              </button>

              <a class="navbar-brand-custom d-none d-lg-block" href="../../index.php">
                <span>p</span>lay<span>p</span>ass
              </a>
            </div>

            <a
              class="navbar-brand-custom navbar-mobile-center-brand d-lg-none mx-auto"
              href="../../index.php"
            >
              <span>p</span>lay<span>p</span>ass
            </a>

            <div class="navbar-collapse-desktop d-none d-lg-flex">
              <ul class="navbar-nav-custom d-flex flex-row mb-0">
                <li class="nav-item">
                  <a class="nav-link" href="../../index.php">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="../Events/event.php">Events</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Activities</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Merch</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact us</a></li>
              </ul>
            </div>

            <div class="d-flex align-items-center">
              <!-- Cart Button -->
              <button
                class="cart-btn position-relative btn btn-outline-light me-3"
                data-bs-toggle="modal"
                data-bs-target="#cartModal"
              >
                <i class="fa-solid fa-bag-shopping fs-5 text-dark"></i>
                <span
                  class="cart-count position-absolute top-0 translate-middle badge rounded-pill"
                  >0</span
                >
              </button>

              <!-- Sign In / Account Button (conditional based on localStorage) -->
              <button
                id="signInBtn"
                onclick="location.href='../Login/login.php'"
                class="btn-signin-custom btn btn-dark px-3"
                style="display: none;"
              >
                Sign In
              </button>
              <button
                id="accountBtn"
                onclick="location.href='#'"
                class="btn-signin-custom btn btn-dark px-3"
                style="display: none;"
              >
                Account
              </button>
            </div>
          </div>
        </nav>

        <!-- Cart Modal -->
        <div
          class="modal fade"
          id="cartModal"
          tabindex="-1"
          aria-labelledby="cartModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">
                  <i class="fas fa-shopping-cart me-2"></i> Cart Items
                </h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                ></button>
              </div>
              <div class="modal-body">
                <!-- Cart Timer -->
                <div id="cart-timer" class="alert alert-warning mb-4" style="display: none;">
                  <i class="fas fa-clock me-2"></i>
                  <strong>Complete purchase within: <span id="cart-timer-display">3:00</span></strong>
                </div>
                <div id="cartBody">
                  <!-- Cart items will be loaded dynamically here -->
                  <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Responsive Navbar -->
        <div
          class="offcanvas offcanvas-start d-lg-none responsive_navbar"
          tabindex="-1"
          id="offcanvasNavbar"
          aria-labelledby="offcanvasNavbarLabel"
        >
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="offcanvas"
              aria-label="Close"
            ></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav-custom flex-column">
              <li class="nav-item">
                <a class="nav-link" href="../../index.php">Home</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="../Events/event.php">Events</a></li>
              <li class="nav-item">
                <a class="nav-link" href="#">Activities</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="#">Merch</a></li>
              <li class="nav-item">
                <a class="nav-link" href="#">Contact us</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <div class="container mt-2">
      <!-- Event Details start -->
      <div class="container mt-4">
        <!-- Booking Details Header -->
        <div class="booking-header mb-3">
          <h2 class="booking-title">
            <i class="fas fa-ticket-alt me-2"></i>
            Booking Details
          </h2>
        </div>

        <!-- Match Details Section -->
        <div class="match-details-section mb-4">
          <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="match-image-container">
                <img src="<?= htmlspecialchars($poster_url) ?>" alt="Match Image" class="match-image" />
                <span class="match-category-badge">Football</span>
                <?php if ($is_live): ?>
                <span class="match-live-badge">Live Now</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-8">
              <div class="match-info">
                <h1 class="match-title"><?= htmlspecialchars($match_title) ?></h1>
                <div class="match-stadium">
                  <i class="fas fa-map-marker-alt me-2"></i>
                  <span><?= htmlspecialchars($stadium_location) ?></span>
                </div>
                <div class="match-meta">
                  <div class="meta-item">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span><?= htmlspecialchars($formatted_date) ?></span>
                  </div>
                  <div class="meta-item">
                    <i class="fas fa-clock me-2"></i>
                    <span><?= htmlspecialchars($formatted_time) ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Timer -->
        <div class="booking-timer-container mb-3">
          <div id="booking-timer" class="alert alert-warning mb-0" style="display: none;">
            <i class="fas fa-clock me-2"></i>
            <strong>Time remaining: 3:00</strong>
          </div>
        </div>

        <!-- Legend Buttons -->
        <div class="legend-toolbar mb-4">
          <div class="legend-button">
            <span class="legend-token available"></span>
            <span>Available</span>
          </div>
          <div class="legend-button">
            <span class="legend-token selected"></span>
            <span>Selected</span>
          </div>
          <div class="legend-button">
            <span class="legend-token booked"></span>
            <span>Booked</span>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="layout-container">
              <div id="stadium-layout"></div>
              <div id="cricket-stadium-layout"></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="details-container">
              <h2 class="mb-4">Ticket Selection</h2>
              
              <!-- Ticket Categories -->
              <div class="ticket-categories mb-4">
                <h5 class="mb-3">Select Category</h5>
                <div class="category-buttons">
                  <?php if (empty($ticket_categories)): ?>
                    <p class="text-muted text-center py-3">No ticket categories available for this stadium.</p>
                  <?php else: ?>
                    <?php foreach ($ticket_categories as $index => $category): ?>
                      <button class="category-btn <?= $index === 0 ? 'active' : '' ?>" 
                              data-category="<?= htmlspecialchars($category['category_name']) ?>"
                              data-category-id="<?= htmlspecialchars($category['category_id']) ?>"
                              data-price="<?= htmlspecialchars($category['price']) ?>">
                        <i class="<?= getCategoryIcon($category['category_name']) ?>"></i>
                        <span><?= htmlspecialchars($category['category_name']) ?></span>
                        <small>$<?= number_format($category['price'], 2) ?></small>
                      </button>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Selected Seats -->
              <div class="selected-seats-section">
                <h5 class="mb-3">Selected Seats</h5>
                <div id="selected-seats-list" class="selected-seats-list">
                  <p class="text-muted text-center py-4">No seats selected</p>
                </div>
              </div>

              <!-- Price Summary -->
              <div class="price-summary mt-4">
                <?php if (empty($ticket_categories)): ?>
                  <p class="text-muted text-center py-3">No pricing information available.</p>
                <?php else: ?>
                  <?php foreach ($ticket_categories as $category): ?>
                    <div class="summary-row <?= getCategoryRowClass($category['category_name']) ?>" 
                         data-category="<?= htmlspecialchars($category['category_name']) ?>">
                      <span><?= htmlspecialchars($category['category_name']) ?> Seats:</span>
                      <span class="category-count" data-category="<?= htmlspecialchars($category['category_name']) ?>">0</span>
                      <span class="category-total" data-category="<?= htmlspecialchars($category['category_name']) ?>">$0</span>
                    </div>
                  <?php endforeach; ?>
                  <hr>
                  <div class="summary-row total-row">
                    <strong>Total:</strong>
                    <strong id="total-price">$0</strong>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Add to Cart Button -->
              <button class="btn btn-primary btn-lg w-100 mt-4" id="add-to-cart-btn" disabled>
                <i class="fas fa-shopping-cart me-2"></i>
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Event Details end -->
    </div>

    <!-- Footer Section Start -->
    <section class="footer-section mt-5">
      <div id="footer"></div>
    </section>
    <!-- Footer Section End -->
    </main>

    <!-- Bootstrap JS Bundle -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    
    <!-- Pass ticket categories and match data to JavaScript -->
    <script>
      // Make ticket categories available to JavaScript
      window.ticketCategories = <?= json_encode($ticket_categories) ?>;
      window.ticketPrices = {};
      <?php foreach ($ticket_categories as $category): ?>
      window.ticketPrices['<?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8') ?>'] = <?= $category['price'] ?>;
      <?php endforeach; ?>
      
      // Pass match data from PHP to JavaScript
      window.matchData = {
        match_id: <?= json_encode($match['match_id'] ?? null) ?>,
        stadium_id: <?= json_encode($match['stadium_id'] ?? null) ?>,
        match_title: <?= json_encode($match_title) ?>,
        stadium_name: <?= json_encode($match['stadium_name'] ?? '') ?>,
        stadium_location: <?= json_encode($stadium_location) ?>,
        match_date: <?= json_encode($match_date) ?>,
        start_time: <?= json_encode($start_time) ?>,
        poster_url: <?= json_encode($poster_url) ?>,
        status: <?= json_encode($match['status'] ?? 'upcoming') ?>
      };
    </script>
    
    <script src="script.js"></script>
    <!-- Cart Functionality Script -->
    <script src="../../components/Cart/cart.js"></script>
  </body>
</html>
