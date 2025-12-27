document.addEventListener("DOMContentLoaded", function () {
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

  if (window.loginSuccess) {
    const successModal = new bootstrap.Modal(
      document.getElementById("loginSuccessModal")
    );
    successModal.show();

    if (window.restoreCartOnLogin && window.loginUserId) {
      restoreCartFromBackend(window.loginUserId);
    }

    setTimeout(function () {
      window.location.href = "../../index.php";
    }, 1000);
  }

  async function restoreCartFromBackend(userId) {
    try {
      const API_URL = '../../../../Backend/PHP/cart-back.php';
      const response = await fetch(`${API_URL}?action=getCart&user_id=${userId}`);
      
      if (response.ok) {
        const data = await response.json();
        if (data.success && data.cart && Array.isArray(data.cart)) {
          localStorage.removeItem("cart");
          if (data.cart.length > 0) {
            localStorage.setItem("cart", JSON.stringify(data.cart));
            console.log('Cart restored from backend after login:', data.cart);
          } else {
            console.log('No cart items found in backend after login');
          }
          
          if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
            window.cartFunctions.updateCartCount();
          }
          
          if (window.cartFunctions && typeof window.cartFunctions.loadCartModal === 'function') {
            window.cartFunctions.loadCartModal();
          }
        }
      }
    } catch (error) {
      console.error('Error restoring cart from backend:', error);
    }
  }

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
