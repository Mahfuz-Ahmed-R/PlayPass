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
    <title>Registration | playpass.live</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <link rel="stylesheet" href="registration.css" />
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link
      rel="stylesheet"
      href="../../components/Responsive_Navbar/responsive_navbar.css"
    />
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

            <a
              class="navbar-brand-custom d-none d-lg-block"
              href="../../index.php"
            >
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
              <li class="nav-item">
                <a class="nav-link" href="#">Activities</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="#">Merch</a></li>
              <li class="nav-item">
                <a class="nav-link" href="#">Contact us</a>
              </li>
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
            <li class="nav-item">
              <a class="nav-link" href="../Events/event.php">Events</a>
            </li>
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
      <!-- </div>
    </nav> -->

      <div class="container mt-2">
        <!-- Registration start -->
        <div class="registration-body">
          <div class="registration-container">
            <h1 class="registration-title">Create Your Account</h1>
            <p class="registration-subtitle">
              Sign up easily with your name, email, phone number, and password.
              For a faster option, use
              <span class="quick-signup-text">Quick Signup!</span> Enter your
              email to receive an OTP.
            </p>

            <form method="post" id="registrationForm">
              <div class="input-group">
                <i class="bi bi-person input-icon"></i>
                <input
                
                  type="text"
                  name="name"
                  class="form-control"
                  placeholder="Full Name"
                  required
                />
              </div>
              <div class="input-group">
                <i class="bi bi-envelope input-icon"></i>
                <input
                  type="email"
                  name="email"
                  class="form-control"
                  placeholder="Email Address"
                  required
                />
              </div>
              <div class="input-group">
                <i class="bi bi-telephone input-icon"></i>
                <input
                  type="tel"
                  name="phone"
                  class="form-control"
                  placeholder="Phone Number"
                  required
                />
              </div>

              <div class="input-group">
                <i class="bi bi-lock input-icon"></i>
                <input
                  type="password"
                  name="password"
                  class="form-control"
                  placeholder="Password"
                  required
                />
              </div>

              <div class="input-group">
                <i class="bi bi-lock input-icon"></i>
                <input
                  type="password"
                  name="repassword"
                  class="form-control"
                  placeholder="Re-type Password"
                  required
                />
              </div>

              <button type="submit" name="submit" class="btn-create">Create Account</button>

              <div class="login-link">
                Already have an account?
                <a href="../Login/login.php">Login here.</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Registration end -->
      </div>

      <!-- Success Modal -->
      <div
        class="modal fade"
        id="registrationSuccessModal"
        tabindex="-1"
        aria-labelledby="registrationSuccessModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-semibold text-success" id="registrationSuccessModalLabel">
                <i class="bi bi-check-circle-fill me-2"></i>Registration Successful
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <p class="mb-2 text-center">
                Your account has been created successfully.
              </p>
            </div>
            <div class="modal-footer border-0">
              <button
                type="button"
                class="btn btn-outline-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
              <a href="../Login/login.php" class="btn btn-success">
                Go to Login
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Modal -->
      <div
        class="modal fade"
        id="registrationErrorModal"
        tabindex="-1"
        aria-labelledby="registrationErrorModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-semibold text-danger" id="registrationErrorModalLabel">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Registration Error
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <p id="registrationErrorMessage" class="mb-0 text-center">
                Something went wrong while creating your account.
              </p>
            </div>
            <div class="modal-footer border-0">
              <button
                type="button"
                class="btn btn-outline-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Section Start -->
      <?php include '../../components/Footer/footer.php'; ?>
      <!-- Footer Section End -->
    </main>
    <?php

      include __DIR__ . '/../../../../Backend/PHP/reg-back.php';
    ?>
    <script src="script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Show success modal if backend set the flag
        if (window.registrationSuccess) {
          var successModalEl = document.getElementById("registrationSuccessModal");
          if (successModalEl && window.bootstrap) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
          }
        }

        // Show error modal if backend set an error message
        if (window.registrationError) {
          var errorModalEl = document.getElementById("registrationErrorModal");
          var errorMsgEl = document.getElementById("registrationErrorMessage");
          if (errorMsgEl) {
            errorMsgEl.textContent = window.registrationError;
          }
          if (errorModalEl && window.bootstrap) {
            var errorModal = new bootstrap.Modal(errorModalEl);
            errorModal.show();
          }
        }
      });
    </script>
  </body>
</html>
