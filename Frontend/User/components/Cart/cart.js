(function () {
  'use strict';

  const BOOKING_TIMEOUT = 3 * 60 * 1000; // 3 minutes (matching seat hold duration)
  const API_URL = '../../../../Backend/PHP/cart-back.php';

  // Cache for cart data
  let cartCache = null;
  let cartLoadPromise = null;

  // ===============================
  // CLEANUP EXPIRED CART ITEMS
  // ===============================
  function cleanupExpiredCartItems() {
    try {
      const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
      if (!localCart || localCart.length === 0) return;

      const now = Date.now();
      let cartChanged = false;

      // Filter out expired items
      const validCart = localCart.filter(item => {
        // Check if any seat in this item has expired
        const hasExpiredSeat = item.seats && item.seats.some(seat => {
          if (seat.expiresAt) {
            const expiryTime = new Date(seat.expiresAt).getTime();
            return expiryTime <= now;
          }
          // If no expiresAt, check addedAt + 3 minutes
          if (item.addedAt) {
            const expiryTime = item.addedAt + BOOKING_TIMEOUT;
            return expiryTime <= now;
          }
          return false;
        });

        if (hasExpiredSeat) {
          cartChanged = true;
          return false; // Remove this item
        }
        return true; // Keep this item
      });

      if (cartChanged) {
        localStorage.setItem("cart", JSON.stringify(validCart));
        cartCache = null; // Invalidate cache
        console.log('Cleaned up expired cart items');
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error cleaning up expired cart items:', error);
      return false;
    }
  }

  // ===============================
  // GET CART FROM BACKEND
  // ===============================
  async function getCart() {
    // Check if user is logged in - if not, return empty cart and clear localStorage
    const userId = localStorage.getItem('user_id');
    if (!userId || userId === 'null' || userId === 'undefined' || userId === '') {
      // No user logged in - return empty cart and clear localStorage
      localStorage.removeItem("cart");
      cartCache = [];
      return [];
    }

    // Clean up expired items from localStorage first
    cleanupExpiredCartItems();

    // Return cache if available and fresh
    if (cartCache) {
      return cartCache;
    }

    // If already loading, return the existing promise
    if (cartLoadPromise) {
      return cartLoadPromise;
    }

    // Load from backend - include user_id to ensure we only get this user's cart
    const url = `${API_URL}?action=getCart&user_id=${encodeURIComponent(userId)}`;
    cartLoadPromise = fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to load cart');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          cartCache = data.cart || [];
          // Also update localStorage as backup (backend already filters expired holds)
          localStorage.setItem("cart", JSON.stringify(cartCache));
          return cartCache;
        } else {
          throw new Error(data.message || 'Failed to load cart');
        }
      })
      .catch(error => {
        console.error('Error loading cart from backend:', error);
        // Fallback to localStorage (after cleanup)
        cleanupExpiredCartItems();
        const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
        cartCache = localCart;
        return localCart;
      })
      .finally(() => {
        cartLoadPromise = null;
      });

    return cartLoadPromise;
  }

  // ===============================
  // REFRESH CART (RELOAD FROM BACKEND)
  // ===============================
  async function refreshCart() {
    cartCache = null;
    return await getCart();
  }

  // ===============================
  // UPDATE CART COUNT
  // ===============================
  async function updateCartCount() {
    try {
      const cart = await getCart();
      const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
      
      document.querySelectorAll(".cart-count").forEach(el => {
        el.textContent = totalItems;
        el.style.display = totalItems > 0 ? 'block' : 'none';
      });

      // Make it available globally
      if (window.cartFunctions) {
        window.cartFunctions.updateCartCount = updateCartCount;
      } else {
        window.cartFunctions = { updateCartCount };
      }
    } catch (error) {
      console.error('Error updating cart count:', error);
    }
  }

  // ===============================
  // REMOVE ITEM FROM CART
  // ===============================
  window.removeCartItem = async function (holdId) {
    if (!holdId) {
      console.error('Hold ID required to remove item');
      return;
    }

    try {
      const formData = new FormData();
      formData.append('action', 'removeFromCart');
      formData.append('hold_id', holdId);

      const response = await fetch(API_URL, {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        // Invalidate cache and reload
        cartCache = null;
        await refreshCart();
        await loadCartModal();
        await updateCartCount();
      } else {
        alert(data.message || 'Failed to remove item from cart');
      }
    } catch (error) {
      console.error('Error removing item from cart:', error);
      alert('Error removing item. Please try again.');
    }
  };

  // ===============================
  // LOAD CART MODAL
  // ===============================
  async function loadCartModal() {
    const body = document.getElementById("cartBody");
    if (!body) return;

    // Show loading
    body.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;

    try {
      const cart = await getCart();

      if (!cart || cart.length === 0) {
        body.innerHTML = `
          <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
            <p class="text-muted">Your cart is empty</p>
          </div>
        `;
        return;
      }

      let html = "";

      cart.forEach((item, itemIndex) => {
        // Format date and time
        const eventDate = new Date(item.eventDate).toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        });
        const eventTime = item.eventTime ? new Date('2000-01-01 ' + item.eventTime).toLocaleTimeString('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        }) : '';

        html += `
          <div class="border rounded p-3 mb-3 cart-item-container" data-item-index="${itemIndex}">
            <div class="d-flex align-items-start mb-2">
              ${item.eventImage ? `<img src="${item.eventImage}" alt="${item.eventTitle}" class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">` : ''}
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h6 class="mb-1">${item.eventTitle || 'Event'}</h6>
                    <p class="text-muted small mb-1">
                      <i class="fas fa-map-marker-alt me-1"></i>${item.eventLocation || ''}
                    </p>
                    <p class="text-muted small mb-0">
                      <i class="fas fa-calendar-alt me-1"></i>${eventDate} 
                      ${eventTime ? `<i class="fas fa-clock ms-2 me-1"></i>${eventTime}` : ''}
                    </p>
                  </div>
                  <!-- Item-specific timer -->
                  <div class="item-timer" data-item-index="${itemIndex}" style="text-align: right;">
                    <small class="text-muted d-block mb-1">Expires in</small>
                    <span class="item-timer-display badge bg-warning text-dark" style="font-size: 0.9rem; font-family: 'Courier New', monospace;">
                      3:00
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-2">
              <strong>Seats:</strong>
              <ul class="list-unstyled mb-2 mt-1">
        `;

        item.seats.forEach((seat, seatIndex) => {
          html += `
            <li class="d-flex justify-content-between align-items-center py-1">
              <span>
                <strong>${seat.section}${seat.row}-${seat.seatNumber}</strong>
                <span class="badge bg-secondary ms-2">${seat.category}</span>
              </span>
              <span>
                <strong>$${parseFloat(seat.price).toFixed(2)}</strong>
                <button class="btn btn-sm btn-link text-danger ms-2 p-0" 
                        onclick="removeCartItem(${seat.holdId})" 
                        title="Remove seat">
                  <i class="fas fa-times"></i>
                </button>
              </span>
            </li>
          `;
        });

        html += `
              </ul>
            </div>
            
            <div class="d-flex justify-content-between align-items-center border-top pt-2">
              <span><strong>Subtotal:</strong></span>
              <span><strong>$${parseFloat(item.total).toFixed(2)}</strong></span>
            </div>
          </div>
        `;
      });

      const grandTotal = cart.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);
      const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);

      html += `
        <hr>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong>Total Items: ${totalItems}</strong><br>
            <strong class="text-primary">Grand Total: $${grandTotal.toFixed(2)}</strong>
          </div>
        </div>
        <button class="btn btn-success w-100" onclick="checkout()">
          <i class="fas fa-shopping-cart me-2"></i>Proceed to Checkout
        </button>
      `;

      body.innerHTML = html;
    } catch (error) {
      console.error('Error loading cart modal:', error);
      body.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle me-2"></i>
          Error loading cart. Please refresh the page.
        </div>
      `;
    }
  }

  // ===============================
  // PLAY TIMER EXPIRATION SOUND
  // ===============================
  function playTimerExpiredSound() {
    try {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);

      const now = audioContext.currentTime;
      oscillator.frequency.setValueAtTime(800, now);
      oscillator.frequency.setValueAtTime(600, now + 0.1);
      
      gainNode.gain.setValueAtTime(0.3, now);
      gainNode.gain.exponentialRampToValueAtTime(0.01, now + 0.5);

      oscillator.start(now);
      oscillator.stop(now + 0.5);
    } catch (error) {
      console.log('Could not play timer sound:', error);
    }
  }

  // ===============================
  // SHOW TIMER WARNING NOTIFICATION
  // ===============================
  function showTimerWarningNotification() {
    const existingNotif = document.getElementById('cart-timer-warning');
    if (existingNotif) return; // Already shown

    const notification = document.createElement('div');
    notification.id = 'cart-timer-warning';
    notification.className = 'alert alert-warning alert-dismissible fade show';
    notification.style.marginBottom = '1rem';
    notification.innerHTML = `
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Hurry!</strong> You have 1 minute remaining to complete your purchase before your cart expires.
    `;
    
    const cartBody = document.getElementById('cartBody');
    if (cartBody && cartBody.parentElement) {
      cartBody.parentElement.insertBefore(notification, cartBody);
      
      // Auto-remove after 5 seconds
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 5000);
    }
  }

  // ===============================
  // UPDATE INDIVIDUAL ITEM TIMER
  // ===============================
  function updateItemTimerDisplay(timerElement, seconds) {
    if (!timerElement) return;

    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    const display = timerElement.querySelector('.item-timer-display');
    
    if (display) {
      display.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
      
      // Update visual state based on remaining time
      display.classList.remove('bg-warning', 'bg-warning-orange', 'bg-danger');
      
      if (seconds <= 60) {
        display.classList.add('bg-danger');
        display.classList.remove('text-dark');
        display.classList.add('text-white');
      } else if (seconds <= 120) {
        display.classList.add('bg-warning-orange');
        display.classList.add('text-dark');
      } else {
        display.classList.add('bg-warning');
        display.classList.add('text-dark');
      }
    }
  }

  // ===============================
  // TIMER (for cart expiration)
  // ===============================
  function startTimer() {
    const timer = document.getElementById("cart-timer");
    if (!timer) return;

    let warningShown = false;
    let itemTimers = {}; // Store interval IDs for each item

    setInterval(async () => {
      try {
        const cart = await getCart();
        if (!cart || cart.length === 0) {
          timer.style.display = 'none';
          timer.classList.remove('warning', 'danger');
          warningShown = false;
          return;
        }

        let earliestExpiry = null;

        // Update individual item timers and find earliest expiry
        cart.forEach((item, itemIndex) => {
          const itemContainer = document.querySelector(`.cart-item-container[data-item-index="${itemIndex}"]`);
          if (!itemContainer) return;

          // Find the earliest expiration time for this item's seats
          let itemEarliestExpiry = null;
          item.seats.forEach(seat => {
            if (seat.expiresAt) {
              const expiryTime = new Date(seat.expiresAt).getTime();
              if (!itemEarliestExpiry || expiryTime < itemEarliestExpiry) {
                itemEarliestExpiry = expiryTime;
              }
            }
          });

          if (itemEarliestExpiry) {
            const now = Date.now();
            const timeLeft = Math.max(0, itemEarliestExpiry - now);
            const timerElement = itemContainer.querySelector('.item-timer');
            
            if (timerElement) {
              if (timeLeft <= 0) {
                updateItemTimerDisplay(timerElement, 0);
              } else {
                const secondsLeft = Math.floor(timeLeft / 1000);
                updateItemTimerDisplay(timerElement, secondsLeft);
              }
            }

            // Track earliest expiry across all items
            if (!earliestExpiry || itemEarliestExpiry < earliestExpiry) {
              earliestExpiry = itemEarliestExpiry;
            }
          }
        });

        // Update main cart timer based on earliest item expiry
        if (earliestExpiry) {
          const now = Date.now();
          const timeLeft = Math.max(0, earliestExpiry - now);
          
          if (timeLeft <= 0) {
            // Cart expired, refresh
            cartCache = null;
            await refreshCart();
            await loadCartModal();
            await updateCartCount();
            playTimerExpiredSound();
            timer.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i><span class="text-danger">Time expired! Cart has been cleared.</span>';
            timer.classList.remove('warning');
            timer.classList.add('danger');
            timer.style.display = 'block'; 
          } else {
            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);
            const timeDisplay = `<span class="cart-timer-display">${minutes}:${seconds.toString().padStart(2, '0')}</span>`;
            timer.innerHTML = `<i class="fas fa-clock me-2"></i><strong>Complete purchase within remaining time!</strong>`;
            timer.style.display = 'block';

            // Update visual warning levels based on remaining time
            timer.classList.remove('warning', 'danger');
            
            if (timeLeft <= 60000) {
              // Less than 1 minute - danger state
              timer.classList.add('danger');
              // Show warning notification only once
              if (!warningShown) {
                warningShown = true;
                showTimerWarningNotification();
                playTimerExpiredSound();
              }
            } else if (timeLeft <= 120000) {
              // 1-2 minutes - warning state
              timer.classList.add('warning');
            }
          }
        }
      } catch (error) {
        console.error('Error updating cart timer:', error);
      }
    }, 1000);
  }

  // ===============================
  // CHECKOUT
  // ===============================
  window.checkout = async function () {
    try {
      const cart = await getCart();
      if (!cart || cart.length === 0) {
        alert('Your cart is empty');
        return;
      }

      // Redirect to checkout page with cart data
      // The checkout page will load the cart from backend
      window.location.href = './pages/Checkout/checkout.php';
    } catch (error) {
      console.error('Error during checkout:', error);
      alert('Error proceeding to checkout. Please try again.');
    }
  };

  // ===============================
  // PERIODIC CART CLEANUP
  // ===============================
  function startCartCleanup() {
    // Clean up expired cart items every 10 seconds (matching seat hold cleanup interval)
    setInterval(() => {
      const wasChanged = cleanupExpiredCartItems();
      if (wasChanged) {
        // Refresh cart display and count if items were removed
        updateCartCount();
        if (document.getElementById("cartBody")) {
          loadCartModal();
        }
      }
    }, 10000); // Every 10 seconds
  }

  // ===============================
  // RESTORE CART ON LOGIN
  // ===============================
  async function restoreCartOnLogin() {
    const userId = localStorage.getItem('user_id');
    if (!userId || userId === 'null' || userId === 'undefined' || userId === '') {
      // No user logged in - ensure cart is cleared
      localStorage.removeItem("cart");
      cartCache = null;
      return;
    }

    try {
      // Check if cart needs to be restored (empty or outdated)
      const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
      
      // Get cart from backend
      cartCache = null; // Clear cache to force fresh fetch
      const backendCart = await getCart();
      
      // If backend has cart items and local doesn't, or if they differ, restore from backend
      if (backendCart && backendCart.length > 0) {
        // Merge: keep backend as source of truth, but preserve any local additions
        const backendSeatIds = new Set();
        backendCart.forEach(item => {
          item.seats.forEach(seat => {
            if (seat.holdId) backendSeatIds.add(seat.holdId);
          });
        });
        
        // Only restore if backend cart is different or local is empty
        if (localCart.length === 0 || backendCart.length !== localCart.length) {
          localStorage.setItem("cart", JSON.stringify(backendCart));
          console.log('Cart restored from backend on login:', backendCart);
        }
      } else if (localCart.length > 0) {
        // If backend has no cart but local does, clear local (user logged out and back in)
        localStorage.removeItem("cart");
        console.log('Cleared local cart - no backend cart found');
      }
    } catch (error) {
      console.error('Error restoring cart on login:', error);
    }
  }

  // ===============================
  // INITIALIZE
  // ===============================
  document.addEventListener("DOMContentLoaded", async () => {
    // Clean up expired items on page load
    cleanupExpiredCartItems();
    
    // Restore cart from backend if user is logged in
    await restoreCartOnLogin();
    
    await updateCartCount();
    await loadCartModal();
    startTimer();
    startCartCleanup();
  });
  
  // Also restore cart when user_id changes (login event)
  // Note: storage event only fires in other tabs/windows, not the current one
  // So we also check on page load and after a short delay
  window.addEventListener('storage', function(e) {
    if (e.key === 'user_id' && e.newValue && e.newValue !== 'null' && e.newValue !== 'undefined') {
      // User logged in - restore cart
      restoreCartOnLogin().then(() => {
        updateCartCount();
        loadCartModal();
      });
    } else if (e.key === 'user_id' && (!e.newValue || e.newValue === 'null' || e.newValue === 'undefined')) {
      // User logged out - clear cart immediately
      localStorage.removeItem("cart");
      cartCache = null;
      updateCartCount();
      loadCartModal();
    }
  });
  
  // Also check for user_id changes periodically (for same-tab login/logout)
  let lastUserId = localStorage.getItem('user_id');
  setInterval(() => {
    const currentUserId = localStorage.getItem('user_id');
    if (currentUserId !== lastUserId) {
      if (currentUserId && currentUserId !== 'null' && currentUserId !== 'undefined' && currentUserId !== '') {
        // User logged in - restore cart
        restoreCartOnLogin().then(() => {
          updateCartCount();
          loadCartModal();
        });
      } else {
        // User logged out - clear cart immediately
        localStorage.removeItem("cart");
        cartCache = null;
        updateCartCount();
        loadCartModal();
      }
      lastUserId = currentUserId;
    }
  }, 500); // Check every 500ms for faster response

  // Expose functions globally
  window.cartFunctions = {
    updateCartCount,
    refreshCart,
    loadCartModal,
    getCart,
    cleanupExpiredCartItems
  };

})();
