// Function to load HTML components dynamically
function includeHTML(id, file) {
  return fetch(file)
    .then((res) => res.text())
    .then((data) => {
      document.getElementById(id).innerHTML = data;
    })
    .catch((err) => console.error("Error loading", file, err));
}

includeHTML("footer", "../../components/Footer/footer.html");

// Function to load CSS components dynamically
function loadCSS(file) {
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

loadCSS("../../components/Footer/footer.css");

// Handle login success/error modals
document.addEventListener("DOMContentLoaded", function () {
  // Check localStorage for user_id and show appropriate button
  const userId = localStorage.getItem("user_id");
  const signInBtn = document.getElementById("signInBtn");
  const accountBtn = document.getElementById("accountBtn");

  if (userId) {
    // User is logged in - show Account button
    if (signInBtn) signInBtn.style.display = "none";
    if (accountBtn) accountBtn.style.display = "block";
  } else {
    // User is not logged in - show Sign In button
    if (signInBtn) signInBtn.style.display = "block";
    if (accountBtn) accountBtn.style.display = "none";
  }

  // Check for login success
  if (window.loginSuccess) {
    const successModal = new bootstrap.Modal(
      document.getElementById("loginSuccessModal")
    );
    successModal.show();

    // Store user_id in localStorage (you'll need to get this from the backend)
    // For now, we'll set a placeholder - update this when you implement session management
    // localStorage.setItem('user_id', userIdFromBackend);

    // Auto-redirect to home page after 2 seconds
    setTimeout(function () {
      window.location.href = "../../index.php";
    }, 1000);
  }

  // Check for login error
  if (window.loginError) {
    const errorModal = new bootstrap.Modal(
      document.getElementById("loginErrorModal")
    );
    const errorMessage = document.getElementById("loginErrorMessage");
    if (errorMessage) {
      errorMessage.textContent = window.loginError;
    }
    errorModal.show();
  }
});
