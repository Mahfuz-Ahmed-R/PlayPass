function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}

function closeSidebar() {
  document.getElementById("sidebar").classList.remove("active");
}


const createBtn = document.querySelector(".create-match-btn");
if (createBtn) {
  createBtn.addEventListener("click", function () {
    if (window.innerWidth <= 768) closeSidebar();
  });
}

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
    
    localStorage.removeItem("user_id");
    localStorage.removeItem("user_email"); 
    localStorage.removeItem("user_name"); 
    
    updateAuthButton();
    
    setTimeout(() => {
      window.location.href = "./index.php";
    }, 100);
  }
