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

function loadCSS(file) {
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

loadCSS("../../components/Navbar/navbar.css");
loadCSS("../../components/Responsive_Navbar/responsive_navbar.css");
loadCSS("../../components/Footer/footer.css");

function updateAuthButton() {
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
}

document.addEventListener("DOMContentLoaded", function () {
  updateAuthButton();
});

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

function formatTime(timeString) {
  const [hours, minutes] = timeString.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
  return `${displayHour}:${minutes} ${ampm}`;
}

const BOOKING_TIMEOUT = 3 * 60 * 1000; // 3 minutes

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
        <a href="../Events/event.php" class="btn btn-primary btn-lg mt-3">
          <i class="fas fa-ticket-alt me-2"></i>Browse Events
        </a>
      </div>
    `;
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
        <a href="../Events/event.php" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left"></i> Continue Shopping
        </a>
        <button onclick="window.location.href='../Checkout/checkout.php'" class="btn btn-success btn-lg">
          <i class="fas fa-credit-card"></i> Proceed to Checkout
        </button>
      </div>
    </div>
  `;

  container.innerHTML = html;
  
  if (cart.length > 0) {
    startCartTimer();
  }
}

window.updateCartQuantity = function(index, change) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (index < 0 || index >= cart.length) return;

  const item = cart[index];
  const newQuantity = item.quantity + change;
  
  if (newQuantity <= 0) {
    removeCartItem(index);
    return;
  }

  const pricePerSeat = item.total / item.quantity;
  item.quantity = newQuantity;
  item.total = pricePerSeat * newQuantity;
  
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCartItems();
  updateCartCount();
};

window.removeCartItem = function(index) {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (index < 0 || index >= cart.length) return;

  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCartItems();
  updateCartCount();
};

function cleanupExpiredCartItems() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (cart.length === 0) return false;

  const now = Date.now();
  let cartChanged = false;

  const validCart = cart.filter(item => {
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
      return false; // Remove this item
    }
    return true; // Keep this item
  });

  if (cartChanged) {
    localStorage.setItem("cart", JSON.stringify(validCart));
    return true;
  }
  return false;
}

function startCartTimer() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (cart.length === 0) return;

  const wasChanged = cleanupExpiredCartItems();
  if (wasChanged) {
    loadCartItems();
    updateCartCount();
    const updatedCart = JSON.parse(localStorage.getItem("cart") || "[]");
    if (updatedCart.length === 0) return;
  }

  let earliestExpiry = null;
  let oldestAddedAt = null;
  
  cart.forEach(item => {
    if (item.addedAt && (!oldestAddedAt || item.addedAt < oldestAddedAt)) {
      oldestAddedAt = item.addedAt;
    }
    if (item.seats) {
      item.seats.forEach(seat => {
        if (seat.expiresAt) {
          const expiryTime = new Date(seat.expiresAt).getTime();
          if (!earliestExpiry || expiryTime < earliestExpiry) {
            earliestExpiry = expiryTime;
          }
        }
      });
    }
  });

  const expiryTime = earliestExpiry || (oldestAddedAt ? oldestAddedAt + BOOKING_TIMEOUT : null);
  if (!expiryTime) return;

  const timerDisplay = document.getElementById('cart-timer');
  const timerText = document.getElementById('cart-timer-display');
  if (!timerDisplay || !timerText) return;

  timerDisplay.style.display = 'block';
  
  const now = Date.now();
  const timeLeft = Math.max(0, expiryTime - now);
  
  if (timeLeft <= 0) {
    cleanupExpiredCartItems();
    loadCartItems();
    updateCartCount();
    timerDisplay.innerHTML = '<span class="text-danger">Time expired! Cart has been cleared.</span>';
    return;
  }

  let secondsLeft = Math.floor(timeLeft / 1000);
  updateCartTimerDisplay(secondsLeft);

  const cartTimerInterval = setInterval(() => {
    const now = Date.now();
    secondsLeft = Math.max(0, Math.floor((expiryTime - now) / 1000));
    
    updateCartTimerDisplay(secondsLeft);

    if (secondsLeft === 0) {
      clearInterval(cartTimerInterval);
      cleanupExpiredCartItems();
      loadCartItems();
      updateCartCount();
      timerDisplay.innerHTML = '<span class="text-danger">Time expired! Cart has been cleared.</span>';
    }
  }, 1000);
}

function updateCartTimerDisplay(seconds) {
  const timerText = document.getElementById('cart-timer-display');
  if (!timerText) return;

  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  timerText.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const cartCount = document.querySelector('.cart-count');
  if (cartCount) {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.style.display = totalItems > 0 ? 'block' : 'none';
  }
}

document.addEventListener("DOMContentLoaded", function() {
  loadCartItems();
  updateCartCount();
});

