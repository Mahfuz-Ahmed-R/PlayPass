<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../../assets/img/pp.png" type="image/x-icon" />
    <title>Login | playpass.live</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="login.css" />
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link rel="stylesheet" href="../../components/Responsive_Navbar/responsive_navbar.css" />
    <link rel="stylesheet" href="../../components/Cart/cart.css" />
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
                  <a class="nav-link" href="../Events/event.php">Events</a>
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
                onclick="location.href='login.php'"
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
      <div class="login-body">
        <div class="main-container">
          <div class="welcome-header">
            <h1>Welcome to PlayPass</h1>
            <p>
              Log in with your username or email and password. For quicker
              access, use our passwordless sign-in.
            </p>
          </div>

          <div class="login-wrapper">
            <div class="left-section">
              <button class="social-btn">
                <svg class="google-icon" viewBox="0 0 24 24">
                  <path
                    fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  />
                  <path
                    fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  />
                  <path
                    fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  />
                  <path
                    fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  />
                </svg>
                Login with Google
              </button>

              <button class="social-btn">
                <i class="fab fa-facebook facebook-icon"></i>
                Login with Facebook
              </button>

              <div class="create-account">
                Don't have an account?
                <a href="../Registration/registration.php"
                  >Create an account.</a
                >
              </div>
            </div>

            <div class="right-section">
              <div class="tab-buttons">
                <h3>Login</h3>
              </div>

              <form method="post" id="loginForm">
                <div class="form-group input-icon">
                  <i class="far fa-envelope"></i>
                  <input
                    type="text"
                    class="form-control"
                    placeholder=" Email Address"
                    name="email"
                    required
                  />
                </div>

                <div class="form-group input-icon">
                  <i class="fas fa-lock"></i>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="Password"
                    name="password"
                    required
                  />
                </div>

                <div class="remember-forgot">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="remember"
                    />
                    <label class="form-check-label" for="remember">
                      Remember me
                    </label>
                  </div>
                  <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" name="submit" class="login-btn" id="loginButton">Login Now!</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        id="loginSuccessModal"
        tabindex="-1"
        aria-labelledby="loginSuccessModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-semibold text-success" id="loginSuccessModalLabel">
                <i class="bi bi-check-circle-fill me-2"></i>Login Successful
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
                You have been logged in successfully.
              </p>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        id="loginErrorModal"
        tabindex="-1"
        aria-labelledby="loginErrorModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-semibold text-danger" id="loginErrorModalLabel">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Login Error
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <p id="loginErrorMessage" class="mb-0 text-center">
                Invalid email or password.
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
    </div>


    </main>
    <?php include '../../components/Footer/footer.php'; ?>



    <?php
      include __DIR__ . '/../../../../Backend/PHP/login-back.php';
    ?>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script src="script.js"></script>
  </body>
</html>
