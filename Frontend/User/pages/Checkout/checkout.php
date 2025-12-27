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
    <title>Checkout | playpass</title>
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
    <link rel="stylesheet" href="../../style.css" />
    <link rel="stylesheet" href="checkout.css" />
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
      <div class="header mt-4">
          <div>
            <h1>Checkout</h1>
            <p>
              Please check all the information before making payment! 
            </p>
          </div>
        </div>
      </div>
      <div class="main-container">
        <div class="row">
          <div class="col-lg-7 mb-4 order-lg-1 order-2">
            <div class="ticket-card">
              <div class="ticket-header">
                <div class="d-flex align-items-center">
                    <span id="ticketTitle" class="ticket-title">Loading...</span>
                    <span id="ticketBadge" class="ticket-badge">0 Tickets</span>
                    <span id="ticketPrice" class="ticket-price">৳ 0 per ticket</span>
                </div>
                <i class="fas fa-chevron-down text-success"></i>
              </div>
              <div class="ticket-info">Click to view ticket info</div>
              <div class="attendee-info">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="attendee-name">
                      <i class="fas fa-user"></i>
                        <span id="attendeeName">--</span>
                    </div>
                    <div class="attendee-contact">
                      <i class="fas fa-envelope"></i>
                        <a id="attendeeEmail" href="mailto:--">--</a>
                    </div>
                    <div class="attendee-contact mt-1">
                      <i class="fas fa-phone"></i>
                        <span id="attendeePhone">--</span>
                    </div>
                  </div>
                    <button id="deleteAllBtn" class="delete-btn" title="Remove all">
                      <i class="fas fa-trash"></i>
                    </button>
                </div>
              </div>
            </div>
          </div>

          <div class="col mb-4 order-3">
            <div class="info-section">
              <h3>Important Information</h3>
              <ul>
                <li>
                  Your ticket/tickets will be sent to the following email
                  address/addresses provided during ticket addition.
                </li>
                <li>
                  Please double-check your ticket information before proceeding
                  to complete payment.
                </li>
                <li>
                  Tickets are non-refundable or subject to the organizer's
                  decision.
                </li>
                <li>
                  After successful payment, a confirmation email with your
                  ticket details will be sent to the provided email address(es).
                  Please check your inbox and spam folder.
                </li>
                <li>
                  Keep an eye on your email for any updates or changes to the
                  event details. The organizer will communicate any important
                  information through the provided email address(es).
                </li>
                <li>
                  If you encounter any issues with your tickets or have any
                  questions, please contact our customer support at
                  <a href="mailto:playpass.live@gmail.com"
                    >playpass.live@gmail.com</a
                  >.
                </li>
                <li>
                  You can also download your tickets from your account profile
                  on our website after the payment is confirmed.
                </li>
              </ul>
            </div>
          </div>

          <div class="col-lg-5 order-lg-2 order-1 mb-4">
            <div class="checkout-card">
              <div class="sub-total">
                <span>Sub Total:</span>
                <span id="subTotal">৳0</span>
              </div>

              <div class="promo-input">
                <input type="text" placeholder="Promo code" />
                <button class="redeem-btn">Redeem</button>
              </div>

              <div class="total-amount">
                <div class="total-amount-label">
                  Total Amount: <span id="totalAmount" class="total-amount-value">৳0</span>
                </div>
              </div>

              <div class="payment-methods">
                <div class="payment-method">
                  <img id="paymentImage"
                    src="https://sslcommerz.com/wp-content/uploads/2021/11/logo.png"
                    alt="SSLCommerez"
                  />
                </div>
              </div>

              <div class="terms-checkbox">
                <input type="checkbox" id="terms" checked />
                <label for="terms"
                  >I agree to the <a href="#">Terms & Conditions</a>,
                  <a href="#">Privacy Policy</a>, and
                  <a href="#">Refund Policy</a>.</label
                >
              </div>

              <button id="proceedBtn" class="proceed-btn">
                Proceed to Pay with SSLCommerez <i class="fas fa-arrow-right"></i>
              </button>

              <div class="warning-note">
                <i class="fas fa-exclamation-triangle"></i>
                Tickets are non-refundable or subject to the organizer's
                decision.
              </div>
            </div>
          </div>
        </div>
      </div>


      <?php include __DIR__ . "/../../components/Footer/footer.php"; ?>

    </main>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script src="../../include.js"></script>
    <script src="../../script.js"></script>
    <script src="script.js"></script>
  </body>
</html>
