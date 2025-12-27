  function scrollSlider(value) {
    const slider = document.getElementById("categorySlider");
    slider.scrollBy({
      left: value,
      behavior: "smooth"
    });
  }

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

  function handleLogout(event) {
    event.preventDefault();
    event.stopPropagation();
    
    localStorage.removeItem("cart");
    Object.keys(localStorage).forEach(key => {
      if (key.startsWith('purchasedSeats_')) {
        localStorage.removeItem(key);
      }
    });
    
    if (window.cartFunctions && typeof window.cartFunctions.refreshCart === 'function') {
      if (window.cartFunctions.cartCache !== undefined) {
        window.cartFunctions.cartCache = null;
      }
    }
    
    localStorage.removeItem("user_id");
    localStorage.removeItem("user_email"); 
    localStorage.removeItem("user_name"); 
    
    updateAuthButton();
    
    if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
      window.cartFunctions.updateCartCount();
    }
    
    if (window.cartFunctions && typeof window.cartFunctions.loadCartModal === 'function') {
      window.cartFunctions.loadCartModal();
    }
    
    setTimeout(() => {
      window.location.href = "./index.php";
    }, 100);
  }

  window.handleLogout = handleLogout;

