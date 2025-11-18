// Shared Cart Functionality for All Pages
// This script handles cart modal functionality across all pages

// Prevent duplicate execution - wrap in IIFE
(function() {
  'use strict';
  
  // Check if already loaded
  if (typeof window.cartFunctions !== 'undefined') {
    console.warn('Cart script already loaded, skipping duplicate initialization');
    return;
  }

  const BOOKING_TIMEOUT = 2 * 60 * 1000; // 2 minutes in milliseconds

// Format date for display
function formatDate(dateString) {
  const date = new Date(dateString);
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                  'July', 'August', 'September', 'October', 'November', 'December'];
  return {
    day: date.getDate(),
    month: months[date.getMonth()],
    year: date.getFullYear()
  };
}

// Format time for display
function formatTime(timeString) {
  if (!timeString) return '';
  const [hours, minutes] = timeString.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
  return `${displayHour}:${minutes} ${ampm}`;
}

// Get base path for relative URLs based on current page location
function getBasePath() {
  // Get the current page's directory path
  const currentPath = window.location.pathname;
  
  // Remove the filename to get the directory
  const lastSlash = currentPath.lastIndexOf('/');
  const directory = currentPath.substring(0, lastSlash + 1);
  
  // Count how many levels deep we are by counting '/' after filtering workspace folders
  const parts = directory.split('/').filter(p => p && p !== 'Frontend' && p !== 'User');
  
  // Calculate depth (number of directory levels)
  const depth = parts.length;
  
  // Generate the relative path to root
  // Examples:
  // - index.html directory: depth = 0, return ''
  // - pages/Events/ directory: depth = 2, return '../../'
  // - pages/EventDetails/ directory: depth = 2, return '../../'
  if (depth > 0) {
    return '../'.repeat(depth);
  }
  
  return '';
}

// Update cart count badge
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const cartCountElements = document.querySelectorAll('.cart-count');
  cartCountElements.forEach(cartCount => {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.style.display = totalItems > 0 ? 'block' : 'none';
  });
}

// Load cart modal dynamically
function loadCartModal() {
  const cartBody = document.getElementById('cartBody');
  if (!cartBody) return;

  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const basePath = getBasePath();
  
  if (cart.length === 0) {
    cartBody.innerHTML = `
      <div class="empty-cart-container text-center py-5">
        <div class="empty-cart-icon mb-4">
          <i class="fas fa-shopping-bag fa-4x text-muted" style="opacity: 0.3;"></i>
        </div>
        <h4 class="text-muted mb-3">Your cart is empty</h4>
        <p class="text-muted mb-4" style="font-size: 0.95rem;">
          Looks like you haven't added any tickets yet.<br>
          Explore our exciting events and find your perfect match!
        </p>
        <a href="${basePath}pages/Events/event.html" class="btn btn-primary btn-lg">
          <i class="fas fa-ticket-alt me-2"></i>Browse Events
        </a>
      </div>
    `;
    // Hide timer if it exists
    const timerDisplay = document.getElementById('cart-timer');
    if (timerDisplay) {
      timerDisplay.style.display = 'none';
    }
    return;
  }

  let html = `
    <div class="cart-section mb-3">
      <h6>Pending Event Orders</h6>
    </div>
  `;

  cart.forEach((item, index) => {
    const { day, month } = formatDate(item.eventDate);
    const imageSrc = item.eventImage || `${basePath}assets/img/img3.jpg`; // Fallback image
    html += `
      <div class="cart-item mb-3 p-3 border rounded" data-index="${index}">
        <div class="row align-items-center">
          <div class="col-md-3 mb-3 mb-md-0">
            <img src="${imageSrc}" alt="${item.eventTitle}" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;" onerror="this.src='${basePath}assets/img/img3.jpg'">
          </div>
          <div class="col-md-6">
            <h5>${item.eventTitle}</h5>
            <p class="mb-1 text-muted">
              <i class="fas fa-map-marker-alt me-1"></i>${item.eventLocation}
            </p>
            <p class="mb-1 text-muted">
              <i class="fas fa-calendar-alt me-1"></i>${month} ${day}
            </p>
            <p class="mb-0">
              <strong>Quantity: </strong>
              <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${index}, -1)">
                <i class="fas fa-minus"></i>
              </button>
              <span class="mx-2">${item.quantity}</span>
              <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${index}, 1)">
                <i class="fas fa-plus"></i>
              </button>
            </p>
          </div>
          <div class="col-md-3 text-end">
            <p class="mb-2"><strong>$${item.total.toFixed(2)}</strong></p>
            <button class="btn btn-sm btn-outline-danger" onclick="removeCartItem(${index})">
              <i class="fas fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>
    `;
  });

  const total = cart.reduce((sum, item) => sum + item.total, 0);
  html += `
    <div class="cart-total border-top pt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Total: $${total.toFixed(2)}</h5>
      </div>
      <div id="cart-timer" class="alert alert-warning mb-3" style="display: none;">
        <i class="fas fa-clock me-2"></i>
        <strong>Complete purchase within: <span id="cart-timer-display">2:00</span></strong>
      </div>
      <div class="cart-actions d-flex justify-content-end gap-2">
        <button onclick="window.location.href='${basePath}pages/Checkout/checkout.html'" class="btn btn-success">
          <i class="fas fa-credit-card"></i> Checkout
        </button>
      </div>
    </div>
  `;

  cartBody.innerHTML = html;
  
  // Start cart timer if items exist
  if (cart.length > 0) {
    startCartTimer();
  }
}

// Update cart quantity
window.updateCartQuantity = function(index, change) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (index < 0 || index >= cart.length) return;

  const item = cart[index];
  const newQuantity = item.quantity + change;
  
  if (newQuantity <= 0) {
    removeCartItem(index);
    return;
  }

  // Update quantity - calculate price per seat first, then update
  const pricePerSeat = item.total / item.quantity;
  item.quantity = newQuantity;
  item.total = pricePerSeat * newQuantity;
  
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCartModal();
  updateCartCount();
};

// Remove cart item
window.removeCartItem = function(index) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (index < 0 || index >= cart.length) return;

  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCartModal();
  updateCartCount();
};

// Start cart timer (2 minutes from when first item was added)
function startCartTimer() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (cart.length === 0) return;

  // Find the oldest item
  const oldestItem = cart.reduce((oldest, item) => 
    (!oldest || item.addedAt < oldest.addedAt) ? item : oldest
  , null);

  if (!oldestItem) return;

  const timerDisplay = document.getElementById('cart-timer');
  const timerText = document.getElementById('cart-timer-display');
  if (!timerDisplay || !timerText) return;

  timerDisplay.style.display = 'block';
  
  const elapsed = Date.now() - oldestItem.addedAt;
  const timeLeft = Math.max(0, BOOKING_TIMEOUT - elapsed);
  
  if (timeLeft <= 0) {
    // Timer expired - clear cart
    localStorage.removeItem("cart");
    loadCartModal();
    updateCartCount();
    timerDisplay.innerHTML = '<span class="text-danger">Time expired! Cart has been cleared.</span>';
    return;
  }

  let secondsLeft = Math.floor(timeLeft / 1000);
  updateCartTimerDisplay(secondsLeft);

  const cartTimerInterval = setInterval(() => {
    const elapsed = Date.now() - oldestItem.addedAt;
    secondsLeft = Math.max(0, Math.floor((BOOKING_TIMEOUT - elapsed) / 1000));
    
    updateCartTimerDisplay(secondsLeft);

    if (secondsLeft === 0) {
      clearInterval(cartTimerInterval);
      localStorage.removeItem("cart");
      loadCartModal();
      updateCartCount();
      timerDisplay.innerHTML = '<span class="text-danger">Time expired! Cart has been cleared.</span>';
    }
  }, 1000);
}

// Update cart timer display
function updateCartTimerDisplay(seconds) {
  const timerText = document.getElementById('cart-timer-display');
  if (!timerText) return;

  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  timerText.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Initialize cart functionality when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Update cart count on page load
  updateCartCount();
  
  // Listen for cart modal open event - load content when modal is about to show
  const cartModal = document.getElementById('cartModal');
  if (cartModal) {
    // Use 'show.bs.modal' event which fires before modal is shown
    // This ensures content is loaded before Bootstrap sets aria-hidden to false
    cartModal.addEventListener('show.bs.modal', function(event) {
      // Load cart content before modal is displayed
      // Use requestAnimationFrame to ensure DOM is ready
      requestAnimationFrame(() => {
        loadCartModal();
      });
    });
  }
});

  // Export functions for use in other scripts if needed
  window.cartFunctions = {
    loadCartModal,
    updateCartCount,
    updateCartQuantity: window.updateCartQuantity,
    removeCartItem: window.removeCartItem
  };
})();

