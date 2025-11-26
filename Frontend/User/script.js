  function scrollSlider(value) {
    const slider = document.getElementById("categorySlider");
    slider.scrollBy({
      left: value,
      behavior: "smooth"
    });
  }

  // Function to handle Sign In / Account button visibility based on localStorage
  function updateAuthButton() {
    const userId = localStorage.getItem("user_id");
    const signInBtn = document.getElementById("signInBtn");
    const accountBtn = document.getElementById("accountBtn");

    // Debug: Check if buttons exist
    if (!signInBtn && !accountBtn) {
      console.warn("Auth buttons not found in DOM");
      return;
    }

    if (userId) {
      // User is logged in - show Account button
      if (signInBtn) {
        signInBtn.style.display = "none";
      }
      if (accountBtn) {
        accountBtn.style.display = "block";
      }
    } else {
      // User is not logged in - show Sign In button
      if (signInBtn) {
        signInBtn.style.display = "block";
      }
      if (accountBtn) {
        accountBtn.style.display = "none";
      }
    }
  }

  // Run immediately if DOM is already loaded, otherwise wait for DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", function () {
      updateAuthButton();
      // Run multiple times to catch any timing issues
      setTimeout(updateAuthButton, 100);
      setTimeout(updateAuthButton, 500);
      setTimeout(updateAuthButton, 1000);
    });
  } else {
    // DOM is already loaded
    updateAuthButton();
    // Run multiple times to catch any timing issues
    setTimeout(updateAuthButton, 100);
    setTimeout(updateAuthButton, 500);
    setTimeout(updateAuthButton, 1000);
  }

  // Listen for storage changes (when localStorage is updated from another tab/window)
  window.addEventListener('storage', function(e) {
    if (e.key === 'user_id') {
      updateAuthButton();
    }
  });

  // Also update when page becomes visible (handles back/forward navigation)
  document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
      updateAuthButton();
    }
  });

  // Update when window gains focus (handles navigation back to page)
  window.addEventListener('focus', function() {
    updateAuthButton();
  });

  // Make updateAuthButton globally available so it can be called from other scripts
  window.updateAuthButton = updateAuthButton;

