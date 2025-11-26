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


function loadCSS(file) {
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

// Load CSS for navbar and footer
loadCSS("../../components/Footer/footer.css");

// Function to handle Sign In / Account button visibility based on localStorage
function updateAuthButton() {
  const userId = localStorage.getItem("user_id");
  const signInBtn = document.getElementById("signInBtn");
  const accountBtn = document.getElementById("accountBtn");

  if (userId) {
    if (signInBtn) signInBtn.style.display = "none";
    if (accountBtn) accountBtn.style.display = "block";
  } else {
    if (signInBtn) signInBtn.style.display = "block";
    if (accountBtn) accountBtn.style.display = "none";
  }
}

// Run on page load
document.addEventListener("DOMContentLoaded", function () {
  updateAuthButton();
});