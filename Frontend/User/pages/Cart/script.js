// Function to load HTML components dynamically
function includeHTML(id, file) {
  return fetch(file)
    .then((res) => {
      if (!res.ok) {
        throw new Error(`Failed to fetch ${file}: ${res.status}`);
      }
      return res.text();
    })
    .then((data) => {
      const element = document.getElementById(id);
      if (!element) {
        console.warn(`Element with id "${id}" not found. Skipping load of ${file}`);
        return;
      }
      element.innerHTML = data;
    })
    .catch((err) => {
      console.error("Error loading", file, err);
    });
}

includeHTML("footer", "../../components/Footer/footer.html");

// Function to load CSS components dynamically
function loadCSS(file) {
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

loadCSS("../../components/Navbar/navbar.css");
loadCSS("../../components/Responsive_Navbar/responsive_navbar.css");
loadCSS("../../components/Footer/footer.css");

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
  const [hours, minutes] = timeString.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
  return `${displayHour}:${minutes} ${ampm}`;
}

const BOOKING_TIMEOUT = 2 * 60 * 1000; // 2 minutes

// Load cart items
function loadCartItems() {
  const container = document.getElementById('cart-items-container');
  if (!container) return;

  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  
  if (cart.length === 0) {
    container.innerHTML = `
      <div class="empty-cart-container text-center py-5">
        <div class="empty-cart-icon mb-4">
          <i class="fas fa-shopping-bag fa-5x text-muted" style="opacity: 0.3;"></i>
        </div>
        <h3 class="text-muted mb-3">Your cart is empty</h3>
        <p class="text-muted mb-4" style="font-size: 1.1rem; max-width: 500px; margin: 0 auto;">
          Looks like you haven't added any tickets yet.<br>
          Explore our exciting events and find your perfect match!
        </p>
        <a href="../Events/event.html" class="btn btn-primary btn-lg mt-3">
          <i class="fas fa-ticket-alt me-2"></i>Browse Events
        </a>
      </div>
    `;
    // Hide timer
    const timerDisplay = document.getElementById('cart-timer');
    if (timerDisplay) {
      timerDisplay.style.display = 'none';
    }
    return;
  }

  let html = '';

  cart.forEach((item, index) => {
    const { day, month, year } = formatDate(item.eventDate);
    const imageSrc = item.eventImage || '../../assets/img/img3.jpg'; // Fallback image
    html += `
      <div class="cart-item mb-4 p-4 border rounded shadow-sm" data-index="${index}">
        <div class="row align-items-center">
          <div class="col-md-3 mb-3 mb-md-0">
            <img src="${imageSrc}" alt="${item.eventTitle}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;" onerror="this.src='../../assets/img/img3.jpg'">
          </div>
          <div class="col-md-6">
            <h4 class="mb-2">${item.eventTitle}</h4>
            <p class="mb-1 text-muted">
              <i class="fas fa-map-marker-alt me-1"></i><strong>Stadium:</strong> ${item.eventLocation}
            </p>
            <p class="mb-1 text-muted">
              <i class="fas fa-calendar-alt me-1"></i><strong>Date:</strong> ${month} ${day}, ${year}
            </p>
            <p class="mb-1 text-muted">
              <i class="fas fa-clock me-1"></i><strong>Time:</strong> ${formatTime(item.eventTime)}
            </p>
            <div class="mt-3">
              <strong>Quantity: </strong>
              <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${index}, -1)">
                <i class="fas fa-minus"></i>
              </button>
              <span class="mx-2 fw-bold">${item.quantity}</span>
              <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${index}, 1)">
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
          <div class="col-md-3 text-end">
            <h5 class="mb-3">$${item.total.toFixed(2)}</h5>
            <button class="btn btn-outline-danger" onclick="removeCartItem(${index})">
              <i class="fas fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>
    `;
  });

  const total = cart.reduce((sum, item) => sum + item.total, 0);
  html += `
    <div class="cart-total border-top pt-4 mt-4">
      <div class="d-flex justify-content-between mb-4">
        <h3>Total: $${total.toFixed(2)}</h3>
      </div>
      <div class="cart-actions d-flex justify-content-end gap-2">
        <a href="../Events/event.html" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left"></i> Continue Shopping
        </a>
        <button onclick="window.location.href='../Checkout/checkout.html'" class="btn btn-success btn-lg">
          <i class="fas fa-credit-card"></i> Proceed to Checkout
        </button>
      </div>
    </div>
  `;

  container.innerHTML = html;
  
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
  loadCartItems();
  updateCartCount();
};

// Remove cart item
window.removeCartItem = function(index) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (index < 0 || index >= cart.length) return;

  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCartItems();
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
    loadCartItems();
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
      loadCartItems();
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

// Update cart count
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const cartCount = document.querySelector('.cart-count');
  if (cartCount) {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.style.display = totalItems > 0 ? 'block' : 'none';
  }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function() {
  loadCartItems();
  updateCartCount();
});

