<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../../assets/img/pp.png" type="image/x-icon" />
    <title>Registration | playpass.live</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="registration.css" />
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link rel="stylesheet" href="../../components/Responsive_Navbar/responsive_navbar.css" />
    <link rel="stylesheet" href="../../components/Cart/cart.css" />
</head>
<body>
<main class="d-flex flex-column min-vh-100">

    <?php
        $navbarPath = '../../components/Navbar/navbar.php';
        if (file_exists($navbarPath)) {
            include $navbarPath;
        } else {
            echo '<p style="color:red;">Error: Navbar file could not be loaded.</p>';
        }
    ?>

    <div class="container mt-2">
        <div class="registration-body">
            <div class="registration-container">
                <h1 class="registration-title">Create Your Account</h1>
                <p class="registration-subtitle">
                    Sign up easily with your name, email, phone number, and password.
                    For a faster option, use <span class="quick-signup-text">Quick Signup!</span> Enter your email to receive an OTP.
                </p>

                <form method="post" id="registrationForm">
                    <div class="input-group">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required />
                    </div>
                    <div class="input-group">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required />
                    </div>
                    <div class="input-group">
                        <i class="bi bi-telephone input-icon"></i>
                        <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required />
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" placeholder="Password" required />
                    </div>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="repassword" class="form-control" placeholder="Re-type Password" required />
                    </div>
                    <button type="submit" name="submit" class="btn-create">Create Account</button>

                    <div class="login-link">
                        Already have an account? <a href="../Login/login.php">Login here.</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">
                        <i class="fas fa-shopping-cart me-2"></i> Cart Items
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <div class="modal fade" id="registrationSuccessModal" tabindex="-1" aria-labelledby="registrationSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-success" id="registrationSuccessModalLabel">
                        <i class="bi bi-check-circle-fill me-2"></i>Registration Successful
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 text-center">Your account has been created successfully.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="../Login/login.php" class="btn btn-success">Go to Login</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registrationErrorModal" tabindex="-1" aria-labelledby="registrationErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-danger" id="registrationErrorModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Registration Error
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="registrationErrorMessage" class="mb-0 text-center">
                        Something went wrong while creating your account.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
        $footerPath = '../../components/Footer/footer.php';
        if (file_exists($footerPath)) {
            include $footerPath;
        } else {
            echo '<p style="color:red;">Error: Footer file could not be loaded.</p>';
        }
    ?>

</main>

<?php
    $regBackPath = __DIR__ . '/../../../../Backend/PHP/reg-back.php';
    if (file_exists($regBackPath)) {
        include $regBackPath;
    } else {
        echo '<p style="color:red;">Error: Registration backend not found.</p>';
    }
?>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.registrationSuccess) {
        var successModalEl = document.getElementById("registrationSuccessModal");
        if (successModalEl && bootstrap) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }
    }

    if (window.registrationError) {
        var errorModalEl = document.getElementById("registrationErrorModal");
        var errorMsgEl = document.getElementById("registrationErrorMessage");
        if (errorMsgEl) {
            errorMsgEl.textContent = window.registrationError;
        }
        if (errorModalEl && bootstrap) {
            var errorModal = new bootstrap.Modal(errorModalEl);
            errorModal.show();
        }
    }
});
</script>
</body>
</html>
