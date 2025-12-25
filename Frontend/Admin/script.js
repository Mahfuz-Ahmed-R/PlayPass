function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}

function closeSidebar() {
  document.getElementById("sidebar").classList.remove("active");
}


// Create Match button: on small screens, close the sidebar when tapped
const createBtn = document.querySelector(".create-match-btn");
if (createBtn) {
  createBtn.addEventListener("click", function () {
    if (window.innerWidth <= 768) closeSidebar();
  });
}

// Tab functionality
document.querySelectorAll(".order-tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    document
      .querySelectorAll(".order-tab")
      .forEach((t) => t.classList.remove("active"));
    this.classList.add("active");
  });
});

document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    this.classList.add("active");
  });
});

  function handleLogout(event) {
    event.preventDefault();
    event.stopPropagation();
    
    // Now remove user data
    localStorage.removeItem("user_id");
    localStorage.removeItem("user_email"); // Remove any other stored user data if present
    localStorage.removeItem("user_name"); // Remove any other stored user data if present
    
    updateAuthButton();
    
    // Small delay to ensure localStorage is cleared before redirecting
    setTimeout(() => {
      window.location.href = "./index.php";
    }, 100);
  }
