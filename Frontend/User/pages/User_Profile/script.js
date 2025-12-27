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

loadCSS("../../components/Footer/footer.css");

  function updateAuthButton() {
    const userId = localStorage.getItem("user_id");
    const signInBtn = document.getElementById("signInBtn");
    const accountBtn = document.getElementById("accountBtn");

    if (!signInBtn && !accountBtn) {
      console.warn("Auth buttons not found in DOM");
      return;
    }

    if (userId) {
      if (signInBtn) {
        signInBtn.style.display = "none";
      }
      if (accountBtn) {
        accountBtn.style.display = "block";
      }
    } else {
      if (signInBtn) {
        signInBtn.style.display = "block";
      }
      if (accountBtn) {
        accountBtn.style.display = "none";
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", function () {
      updateAuthButton();
      setTimeout(updateAuthButton, 100);
      setTimeout(updateAuthButton, 500);
      setTimeout(updateAuthButton, 1000);
    });
  } else {
    updateAuthButton();
    setTimeout(updateAuthButton, 100);
    setTimeout(updateAuthButton, 500);
    setTimeout(updateAuthButton, 1000);
  }

  window.addEventListener('storage', function(e) {
    if (e.key === 'user_id') {
      updateAuthButton();
    }
  });

  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      updateAuthButton();
    }
  });

  window.addEventListener('focus', function() {
    updateAuthButton();
  });

  window.updateAuthButton = updateAuthButton;