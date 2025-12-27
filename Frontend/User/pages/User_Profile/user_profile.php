<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="../../components/Navbar/navbar.css" />
    <link rel="stylesheet" href="../../components/Responsive_Navbar/responsive_navbar.css" />
]    <link rel="stylesheet" href="../../components/Cart/cart.css" />
  </head>
<body>
    <div class="profile-container">
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

            <a class="navbar-brand-custom d-none d-lg-block" href="index.php">
              <span>p</span>lay<span>p</span>ass
            </a>
          </div>

            <a
            class="navbar-brand-custom navbar-mobile-center-brand d-lg-none mx-auto"
            href="index.php"
          >
            <span>p</span>lay<span>p</span>ass
          </a>

          <div class="navbar-collapse-desktop d-none d-lg-flex">
            <ul class="navbar-nav-custom d-flex flex-row mb-0">
              <li class="nav-item">
                <a class="nav-link active" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pages/Events/event.html">Events</a>
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
              onclick="location.href='pages/Login/login.php'"
              class="btn-signin-custom btn btn-dark px-3"
            >
              Sign In
            </button>
            <button
              id="accountBtn"
              onclick="location.href='./pages/User_Profile/user_profile.php'"
              class="btn-signin-custom btn btn-dark px-3"
              style="display: none;"
            >
              Account
            </button>
          </div>
        </div>
      </div>
      </nav>

    <div class="container mt-2">
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
             <a class="nav-link active" href="index.php">Home</a>
            </li>
             <li class="nav-item"><a class="nav-link" href="pages/Events/event.php">Events</a></li>
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


        <div class="row">
            <div class="col-lg-9">
                <div class="profile-section">
                    <h2 class="section-title">
                        <i class="fas fa-id-card"></i>
                        Personal Information
                    </h2>

                    <div class="profile-picture-section">
                        <div class="profile-picture-wrapper">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&size=150&background=667eea&color=fff&bold=true" 
                                 alt="Profile Picture" 
                                 class="profile-picture" 
                                 id="profilePic">
                            <label class="upload-badge" for="profileUpload">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="profileUpload" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                        <div class="profile-name" id="displayName">John Doe</div>
                        <div class="profile-email" id="displayEmail">john.doe@example.com</div>
                    </div>

                    <form id="profileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">
                                    <i class="fas fa-user"></i> First Name
                                </label>
                                <input type="text" class="form-control" id="firstName" value="John" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">
                                    <i class="fas fa-user"></i> Last Name
                                </label>
                                <input type="text" class="form-control" id="lastName" value="Doe" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input type="email" class="form-control" id="email" value="john.doe@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="tel" class="form-control" id="phone" value="+880 1234567890">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dateOfBirth" class="form-label">
                                    <i class="fas fa-calendar"></i> Date of Birth
                                </label>
                                <input type="date" class="form-control" id="dateOfBirth" value="1990-01-15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select class="form-select" id="gender">
                                    <option value="male" selected>Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                    <option value="prefer-not">Prefer not to say</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Address
                            </label>
                            <input type="text" class="form-control" id="address" value="123 Main Street, Dhaka">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">
                                    <i class="fas fa-city"></i> City
                                </label>
                                <input type="text" class="form-control" id="city" value="Dhaka">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">
                                    <i class="fas fa-globe"></i> Country
                                </label>
                                <select class="form-select" id="country">
                                    <option value="BD" selected>Bangladesh</option>
                                    <option value="IN">India</option>
                                    <option value="PK">Pakistan</option>
                                    <option value="US">United States</option>
                                    <option value="UK">United Kingdom</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-success d-none" id="successAlert">
                            <i class="fas fa-check-circle"></i> Profile updated successfully!
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="password-section">
                    <div class="password-icon-wrapper">
                        <div class="password-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h2 class="section-title" style="border: none; padding: 0; margin: 0; justify-content: center;">
                            Security
                        </h2>
                    </div>

                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" placeholder="••••••••" required>
                                <span class="input-group-text" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye" id="currentPasswordIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" placeholder="••••••••" required oninput="checkPasswordStrength()">
                                <span class="input-group-text" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye" id="newPasswordIcon"></i>
                                </span>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-progress" id="strengthProgress" style="width: 0%; background: #dc3545;"></div>
                                </div>
                                <div class="strength-text" id="strengthText">Enter password</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" placeholder="••••••••" required>
                                <span class="input-group-text" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="alert alert-warning" style="font-size: 12px; padding: 10px;">
                            <i class="fas fa-info-circle"></i> Password must be at least 8 characters with uppercase, lowercase, and numbers.
                        </div>

                        <div class="alert alert-success d-none" id="passwordSuccessAlert">
                            <i class="fas fa-check-circle"></i> Password changed successfully!
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <section class="footer-section">
      <div id="footer"></div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            
            document.getElementById('displayName').textContent = `${firstName} ${lastName}`;
            document.getElementById('displayEmail').textContent = email;
            
            const alert = document.getElementById('successAlert');
            alert.classList.remove('d-none');
            
            setTimeout(() => {
                alert.classList.add('d-none');
            }, 3000);
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('New password and confirm password do not match!');
                return;
            }
            
            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long!');
                return;
            }
            
            const alert = document.getElementById('passwordSuccessAlert');
            alert.classList.remove('d-none');
            
            document.getElementById('passwordForm').reset();
            document.getElementById('strengthProgress').style.width = '0%';
            document.getElementById('strengthText').textContent = 'Enter password';
            
            setTimeout(() => {
                alert.classList.add('d-none');
            }, 3000);
        });

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('newPassword').value;
            const progress = document.getElementById('strengthProgress');
            const text = document.getElementById('strengthText');
            
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            
            progress.style.width = strength + '%';
            
            if (strength === 0) {
                progress.style.background = '#dc3545';
                text.textContent = 'Enter password';
                text.style.color = '#6c757d';
            } else if (strength <= 25) {
                progress.style.background = '#dc3545';
                text.textContent = 'Weak';
                text.style.color = '#dc3545';
            } else if (strength <= 50) {
                progress.style.background = '#ffc107';
                text.textContent = 'Fair';
                text.style.color = '#ffc107';
            } else if (strength <= 75) {
                progress.style.background = '#17a2b8';
                text.textContent = 'Good';
                text.style.color = '#17a2b8';
            } else {
                progress.style.background = '#28a745';
                text.textContent = 'Strong';
                text.style.color = '#28a745';
            }
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePic').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function resetForm() {
            document.getElementById('profileForm').reset();
            document.getElementById('displayName').textContent = 'John Doe';
            document.getElementById('displayEmail').textContent = 'john.doe@example.com';
        }
    </script>
</body>
</html>