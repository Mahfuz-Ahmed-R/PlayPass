<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="shortcut icon"
      href="../../assets/img/pp.png"
      type="image/x-icon"
    />

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="event.css" />
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link rel="stylesheet" href="../../components/Cart/cart.css" />
    <title>Events</title>
  </head>
  <body>
    <main class="d-flex flex-column min-vh-100">
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
                  <a class="nav-link active" href="event.php">Events</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Activities</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Merch</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact us</a></li>
              </ul>
            </div>

            <div class="d-flex align-items-center">
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
                <div id="cart-timer" class="alert alert-warning mb-4" style="display: none;">
                  <i class="fas fa-clock me-2"></i>
                  <strong>Complete purchase within: <span id="cart-timer-display">3:00</span></strong>
                </div>
                <div id="cartBody">
                  <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

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
              <li class="nav-item"><a class="nav-link active" href="event.php">Events</a></li>
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
        
        <section class="mt-4">
          <div class="container-fluid">
            <div class="filter-section">
              <div class="filter-container">
                <div class="radio-group">
                  <div class="radio-item">
                    <input type="radio" id="all" name="event-filter" checked>
                    <label for="all">All</label>
                  </div>
                  <div class="radio-item">
                    <input type="radio" id="live" name="event-filter">
                    <label for="live">Live</label>
                  </div>
                  <div class="radio-item">
                    <input type="radio" id="upcoming" name="event-filter">
                    <label for="upcoming">Upcoming</label>
                  </div>
                </div>
        
                <button class="filter-btn" id="filterBtn">
                  <i class="fas fa-sliders-h"></i>
                  Filters
                </button>
        
                <button class="sort-btn" id="sortBtn">
                  <i class="fas fa-sort"></i>
                  Sort
                </button>
        
                <div class="search-container">
                  <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search by match or stadium name..">
                    <i class="fas fa-search search-icon"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <div class="overlay" id="overlay"></div>

        <div class="side-panel" id="filterPanel">
          <div class="side-panel-header">
            <h3>Filters</h3>
            <button class="close-panel" id="closeFilter">&times;</button>
          </div>
          <div class="side-panel-content">
            <div class="filter-group">
              <h4>Sport</h4>
              <div class="checkbox-item">
                <input type="checkbox" id="football" value="football">
                <label for="football">Football</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="cricket" value="cricket">
                <label for="cricket">Cricket</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="basketball" value="basketball">
                <label for="basketball">Basketball</label>
              </div>
            </div>

            <div class="filter-group">
              <h4>Location</h4>
              <div class="checkbox-item">
                <input type="checkbox" id="dhaka" value="dhaka">
                <label for="dhaka">Dhaka</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="chittagong" value="chittagong">
                <label for="chittagong">Chittagong</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="sylhet" value="sylhet">
                <label for="sylhet">Sylhet</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="khulna" value="khulna">
                <label for="khulna">Khulna</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="rajshahi" value="rajshahi">
                <label for="rajshahi">Rajshahi</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="barisal" value="barisal">
                <label for="barisal">Barisal</label>
              </div>
            </div>
            <button class="apply-filters-btn" id="applyFilters">Apply Filters</button>
          </div>
        </div>

        <div class="sort-panel" id="sortPanel">
          <div class="side-panel-header">
            <h3>Sort By</h3>
            <button class="close-panel" id="closeSort">&times;</button>
          </div>
          <div class="side-panel-content" style="padding: 0;">
            <div class="sort-option" data-sort="low-to-high">
              <i class="fas fa-arrow-up"></i>
              <span>Price: Low to High</span>
            </div>
            <div class="sort-option" data-sort="high-to-low">
              <i class="fas fa-arrow-down"></i>
              <span>Price: High to Low</span>
            </div>
            <div class="sort-option" data-sort="date-asc">
              <i class="fas fa-calendar-alt"></i>
              <span>Date: Earliest First</span>
            </div>
            <div class="sort-option" data-sort="date-desc">
              <i class="fas fa-calendar-alt"></i>
              <span>Date: Latest First</span>
            </div>
          </div>
        </div>

        <div class="container"><?php include '../../components/Card/card.php';?></div>

        

      <section class="pagination-section m-auto">
        <nav class="mt-5" aria-label="Page navigation">
          <ul class="pagination" id="pagination">
          </ul>
        </nav>
      </section>

      <?php include '../../components/Footer/footer.php';?>

    </main>
      <script src="script.js"></script>
      <script src="../../components/Cart/cart.js"></script>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
  ></script>
  </body>

</html>
