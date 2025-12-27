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

document.addEventListener("DOMContentLoaded", function () {
  updateAuthButton();
});