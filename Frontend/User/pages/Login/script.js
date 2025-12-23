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

    // Restore cart from backend if user just logged in
    if (window.restoreCartOnLogin && window.loginUserId) {
      restoreCartFromBackend(window.loginUserId);
    }

    // Auto-redirect to home page after 2 seconds
    setTimeout(function () {
      window.location.href = "../../index.php";
    }, 1000);
  }

  // Function to restore cart from backend after login
  async function restoreCartFromBackend(userId) {
    try {
      const API_URL = '../../../../Backend/PHP/cart-back.php';
      const response = await fetch(`${API_URL}?action=getCart&user_id=${userId}`);
      
      if (response.ok) {
        const data = await response.json();
        if (data.success && data.cart && Array.isArray(data.cart)) {
          // Clear existing cart and restore from backend
          localStorage.removeItem("cart");
          if (data.cart.length > 0) {
            localStorage.setItem("cart", JSON.stringify(data.cart));
            console.log('Cart restored from backend after login:', data.cart);
          } else {
            console.log('No cart items found in backend after login');
          }
          
          // Update cart count if cart functions are available
          if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
            window.cartFunctions.updateCartCount();
          }
          
          // Also trigger cart modal refresh if available
          if (window.cartFunctions && typeof window.cartFunctions.loadCartModal === 'function') {
            window.cartFunctions.loadCartModal();
          }
        }
      }
    } catch (error) {
      console.error('Error restoring cart from backend:', error);
    }
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
