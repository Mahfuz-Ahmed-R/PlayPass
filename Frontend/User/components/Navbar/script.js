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

  // Handle logout - remove user_id and cart from localStorage
  function handleLogout(event) {
    event.preventDefault();
    event.stopPropagation();
    
    // Clear cart FIRST before removing user_id
    localStorage.removeItem("cart");
    // Also clear any purchased seats data
    Object.keys(localStorage).forEach(key => {
      if (key.startsWith('purchasedSeats_')) {
        localStorage.removeItem(key);
      }
    });
    
    // Clear cart cache if cart functions are available
    if (window.cartFunctions && typeof window.cartFunctions.refreshCart === 'function') {
      // Invalidate cart cache
      if (window.cartFunctions.cartCache !== undefined) {
        window.cartFunctions.cartCache = null;
      }
    }
    
    // Now remove user data
    localStorage.removeItem("user_id");
    localStorage.removeItem("user_email"); // Remove any other stored user data if present
    localStorage.removeItem("user_name"); // Remove any other stored user data if present
    
    updateAuthButton();
    
    // Update cart count if cart functions are available
    if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
      window.cartFunctions.updateCartCount();
    }
    
    // Also clear cart modal if available
    if (window.cartFunctions && typeof window.cartFunctions.loadCartModal === 'function') {
      window.cartFunctions.loadCartModal();
    }
    
    // Small delay to ensure localStorage is cleared before redirecting
    setTimeout(() => {
      window.location.href = "./index.php";
    }, 100);
  }

  // Make handleLogout globally available
  window.handleLogout = handleLogout;

