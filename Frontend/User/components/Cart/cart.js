(function () {
  'use strict';

  const BOOKING_TIMEOUT = 3 * 60 * 1000; 
  const API_URL = '../../../../Backend/PHP/cart-back.php';

  let cartCache = null;
  let cartLoadPromise = null;

  function cleanupExpiredCartItems() {
    try {
      const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
      if (!localCart || localCart.length === 0) return;

      const now = Date.now();
      let cartChanged = false;

      const validCart = localCart.filter(item => {
        const hasExpiredSeat = item.seats && item.seats.some(seat => {
          if (seat.expiresAt) {
            const expiryTime = new Date(seat.expiresAt).getTime();
            return expiryTime <= now;
          }
          if (item.addedAt) {
            const expiryTime = item.addedAt + BOOKING_TIMEOUT;
            return expiryTime <= now;
          }
          return false;
        });

        if (hasExpiredSeat) {
          cartChanged = true;
          return false; 
        }
        return true; 
      });

      if (cartChanged) {
        localStorage.setItem("cart", JSON.stringify(validCart));
        cartCache = null; 
        console.log('Cleaned up expired cart items');
        return true;
      }
      return false;
    } catch (error) {
      console.error('Error cleaning up expired cart items:', error);
      return false;
    }
  }

  async function getCart() {
    const userId = localStorage.getItem('user_id');
    if (!userId || userId === 'null' || userId === 'undefined' || userId === '') {
      localStorage.removeItem("cart");
      cartCache = [];
      return [];
    }

    cleanupExpiredCartItems();

    if (cartCache) {
      return cartCache;
    }

    if (cartLoadPromise) {
      return cartLoadPromise;
    }

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
          localStorage.setItem("cart", JSON.stringify(cartCache));
          return cartCache;
        } else {
          throw new Error(data.message || 'Failed to load cart');
        }
      })
      .catch(error => {
        console.error('Error loading cart from backend:', error);
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

  async function refreshCart() {
    cartCache = null;
    return await getCart();
  }

  async function updateCartCount() {
    try {
      const cart = await getCart();
      const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
      
      document.querySelectorAll(".cart-count").forEach(el => {
        el.textContent = totalItems;
        el.style.display = totalItems > 0 ? 'block' : 'none';
      });

      if (window.cartFunctions) {
        window.cartFunctions.updateCartCount = updateCartCount;
      } else {
        window.cartFunctions = { updateCartCount };
      }
    } catch (error) {
      console.error('Error updating cart count:', error);
    }
  }

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

  async function loadCartModal() {
    const body = document.getElementById("cartBody");
    if (!body) return;

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

  function showTimerWarningNotification() {
    const existingNotif = document.getElementById('cart-timer-warning');
    if (existingNotif) return; 

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
      
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 5000);
    }
  }

  function updateItemTimerDisplay(timerElement, seconds) {
    if (!timerElement) return;

    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    const display = timerElement.querySelector('.item-timer-display');
    
    if (display) {
      display.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
      
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

  function startTimer() {
    const timer = document.getElementById("cart-timer");
    if (!timer) return;

    let warningShown = false;
    let itemTimers = {}; 
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

        cart.forEach((item, itemIndex) => {
          const itemContainer = document.querySelector(`.cart-item-container[data-item-index="${itemIndex}"]`);
          if (!itemContainer) return;

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

            if (!earliestExpiry || itemEarliestExpiry < earliestExpiry) {
              earliestExpiry = itemEarliestExpiry;
            }
          }
        });

        if (earliestExpiry) {
          const now = Date.now();
          const timeLeft = Math.max(0, earliestExpiry - now);
          
          if (timeLeft <= 0) {
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

            timer.classList.remove('warning', 'danger');
            
            if (timeLeft <= 60000) {
              timer.classList.add('danger');
              if (!warningShown) {
                warningShown = true;
                showTimerWarningNotification();
                playTimerExpiredSound();
              }
            } else if (timeLeft <= 120000) {
              timer.classList.add('warning');
            }
          }
        }
      } catch (error) {
        console.error('Error updating cart timer:', error);
      }
    }, 1000);
  }

  window.checkout = async function () {
    try {
      const cart = await getCart();
      if (!cart || cart.length === 0) {
        alert('Your cart is empty');
        return;
      }

      window.location.href = './pages/Checkout/checkout.php';
    } catch (error) {
      console.error('Error during checkout:', error);
      alert('Error proceeding to checkout. Please try again.');
    }
  };

  function startCartCleanup() {
    setInterval(() => {
      const wasChanged = cleanupExpiredCartItems();
      if (wasChanged) {
        updateCartCount();
        if (document.getElementById("cartBody")) {
          loadCartModal();
        }
      }
    }, 10000); 
  }

  async function restoreCartOnLogin() {
    const userId = localStorage.getItem('user_id');
    if (!userId || userId === 'null' || userId === 'undefined' || userId === '') {
      localStorage.removeItem("cart");
      cartCache = null;
      return;
    }

    try {
      const localCart = JSON.parse(localStorage.getItem("cart") || "[]");
      
      cartCache = null; 
      const backendCart = await getCart();
      
      if (backendCart && backendCart.length > 0) {
        const backendSeatIds = new Set();
        backendCart.forEach(item => {
          item.seats.forEach(seat => {
            if (seat.holdId) backendSeatIds.add(seat.holdId);
          });
        });
        
        if (localCart.length === 0 || backendCart.length !== localCart.length) {
          localStorage.setItem("cart", JSON.stringify(backendCart));
          console.log('Cart restored from backend on login:', backendCart);
        }
      } else if (localCart.length > 0) {
        localStorage.removeItem("cart");
        console.log('Cleared local cart - no backend cart found');
      }
    } catch (error) {
      console.error('Error restoring cart on login:', error);
    }
  }

  document.addEventListener("DOMContentLoaded", async () => {
    cleanupExpiredCartItems();
    
    await restoreCartOnLogin();
    
    await updateCartCount();
    await loadCartModal();
    startTimer();
    startCartCleanup();
  });

  window.addEventListener('storage', function(e) {
    if (e.key === 'user_id' && e.newValue && e.newValue !== 'null' && e.newValue !== 'undefined') {
      restoreCartOnLogin().then(() => {
        updateCartCount();
        loadCartModal();
      });
    } else if (e.key === 'user_id' && (!e.newValue || e.newValue === 'null' || e.newValue === 'undefined')) {
      localStorage.removeItem("cart");
      cartCache = null;
      updateCartCount();
      loadCartModal();
    }
  });
  
  let lastUserId = localStorage.getItem('user_id');
  setInterval(() => {
    const currentUserId = localStorage.getItem('user_id');
    if (currentUserId !== lastUserId) {
      if (currentUserId && currentUserId !== 'null' && currentUserId !== 'undefined' && currentUserId !== '') {
        restoreCartOnLogin().then(() => {
          updateCartCount();
          loadCartModal();
        });
      } else {
        localStorage.removeItem("cart");
        cartCache = null;
        updateCartCount();
        loadCartModal();
      }
      lastUserId = currentUserId;
    }
  }, 500); 

  window.cartFunctions = {
    updateCartCount,
    refreshCart,
    loadCartModal,
    getCart,
    cleanupExpiredCartItems
  };

})();
