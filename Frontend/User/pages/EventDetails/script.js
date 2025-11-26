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

// Function to handle Sign In / Account button visibility based on localStorage
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

// Run on page load
document.addEventListener("DOMContentLoaded", function () {
  updateAuthButton();
});

// Get event ID from URL
function getEventIdFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  return parseInt(urlParams.get("id")) || null;
}

// Fetch event data (using same dummy data structure as card component)
async function fetchEventById(eventId) {
  // Import the same dummy data
  const dummyEventsData = [
    {
      id: 1,
      title: "Real Madrid vs Barcelona",
      location: "Camp Nou, Barcelona, Spain",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-13",
      time: "20:00",
      isLive: true,
      price: 150,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 2,
      title: "Manchester United vs Liverpool",
      location: "Old Trafford, Manchester, UK",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-15",
      time: "18:30",
      isLive: false,
      price: 200,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 3,
      title: "India vs Australia",
      location: "Eden Gardens, Kolkata, India",
      category: "Cricket",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-17",
      time: "14:00",
      isLive: false,
      price: 120,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 4,
      title: "Lakers vs Warriors",
      location: "Staples Center, Los Angeles, USA",
      category: "Basketball",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-20",
      time: "19:00",
      isLive: false,
      price: 180,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 5,
      title: "PSG vs Bayern Munich",
      location: "Parc des Princes, Paris, France",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-22",
      time: "21:00",
      isLive: false,
      price: 220,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 6,
      title: "Federer vs Nadal Exhibition",
      location: "Wimbledon, London, UK",
      category: "Tennis",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-25",
      time: "16:00",
      isLive: false,
      price: 250,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 7,
      title: "Chelsea vs Arsenal",
      location: "Stamford Bridge, London, UK",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-11-28",
      time: "17:30",
      isLive: false,
      price: 190,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 8,
      title: "Bangladesh vs Sri Lanka",
      location: "Sher-e-Bangla Stadium, Dhaka, Bangladesh",
      category: "Cricket",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-01",
      time: "15:00",
      isLive: false,
      price: 100,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 9,
      title: "Barcelona vs Atletico Madrid",
      location: "Camp Nou, Barcelona, Spain",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-05",
      time: "20:00",
      isLive: false,
      price: 170,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 10,
      title: "Miami Heat vs Boston Celtics",
      location: "TD Garden, Boston, USA",
      category: "Basketball",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-08",
      time: "19:30",
      isLive: false,
      price: 210,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 11,
      title: "Manchester City vs Chelsea",
      location: "Etihad Stadium, Manchester, UK",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-10",
      time: "19:00",
      isLive: false,
      price: 195,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 12,
      title: "Pakistan vs India",
      location: "Eden Gardens, Kolkata, India",
      category: "Cricket",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-12",
      time: "14:30",
      isLive: false,
      price: 300,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 13,
      title: "Bulls vs Lakers",
      location: "United Center, Chicago, USA",
      category: "Basketball",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-15",
      time: "20:00",
      isLive: false,
      price: 175,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 14,
      title: "Arsenal vs Tottenham",
      location: "Emirates Stadium, London, UK",
      category: "Football",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-18",
      time: "17:00",
      isLive: false,
      price: 185,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    },
    {
      id: 15,
      title: "Australia vs England",
      location: "MCG, Melbourne, Australia",
      category: "Cricket",
      image: "../../assets/img/img3.jpg",
      date: "2024-12-20",
      time: "10:00",
      isLive: false,
      price: 160,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    }
  ];

  return dummyEventsData.find(event => event.id === eventId) || null;
}

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

// Populate event details on page
function populateEventDetails(event) {
  if (!event) return;

  const { day, month, year } = formatDate(event.date);
  
  // Update match image
  const matchImage = document.querySelector('.match-image');
  if (matchImage && event.image) {
    matchImage.src = event.image;
    matchImage.alt = event.title;
    // Add error handler for broken images
    matchImage.onerror = function() {
      this.src = '../../assets/img/img3.jpg';
    };
  }

  // Update category badge
  const categoryBadge = document.querySelector('.match-category-badge');
  if (categoryBadge) {
    categoryBadge.textContent = event.category;
  }

  // Update live badge
  const liveBadge = document.querySelector('.match-live-badge');
  if (liveBadge) {
    liveBadge.style.display = event.isLive ? 'block' : 'none';
  }

  // Update match title
  const matchTitle = document.querySelector('.match-title');
  if (matchTitle) {
    matchTitle.textContent = event.title;
  }

  // Update stadium location
  const stadiumLocation = document.querySelector('.match-stadium span');
  if (stadiumLocation) {
    stadiumLocation.textContent = event.location;
  }

  // Update date
  const dateElement = document.querySelector('.meta-item span');
  if (dateElement) {
    dateElement.textContent = `${month} ${day}, ${year}`;
  }

  // Update time
  const timeElements = document.querySelectorAll('.meta-item span');
  if (timeElements.length > 1) {
    timeElements[1].textContent = formatTime(event.time);
  }

  // Update category prices (if different prices per event)
  updateCategoryPrices(event);
}

// Update category prices
function updateCategoryPrices(event) {
  // You can customize prices per event if needed
  const prices = {
    VIP: event.price || 150,
    Regular: (event.price || 150) * 0.5,
    Economy: (event.price || 150) * 0.23
  };

  // Update price buttons
  document.querySelectorAll('.category-btn').forEach(btn => {
    const category = btn.dataset.category;
    const small = btn.querySelector('small');
    if (small) {
      small.textContent = `$${prices[category]}`;
    }
  });

  // Store prices globally
  window.ticketPrices = prices;
}

// Load stadium layout dynamically based on event category
function loadStadiumLayout(event) {
  const footballContainer = document.getElementById("stadium-layout");
  const cricketContainer = document.getElementById("cricket-stadium-layout");
  
  // Determine which layout to use
  const isCricket = event.category.toLowerCase() === 'cricket';
  const container = isCricket ? cricketContainer : footballContainer;
  const otherContainer = isCricket ? footballContainer : cricketContainer;
  
  if (!container) return;

  // Hide the other container and show the correct one
  if (otherContainer) {
    otherContainer.style.display = 'none';
    otherContainer.innerHTML = '';
  }
  container.style.display = 'block';
  container.innerHTML = '';

  // Determine layout file
  const layoutFile = isCricket 
    ? "../../components/Stadium/cricket-stadium-layout.html"
    : "../../components/Stadium/stadium-layout.html";

  // Create iframe
  const iframe = document.createElement("iframe");
  iframe.src = layoutFile;
  iframe.style.width = "100%";
  iframe.style.height = "80vh";
  iframe.style.border = "none";
  iframe.style.overflow = "hidden";
  iframe.id = "stadium-iframe";
  container.appendChild(iframe);

  // Wait for iframe to load
  iframe.onload = function () {
    // Send category pricing to iframe
    iframe.contentWindow.postMessage(
      {
        type: "init",
        prices: window.ticketPrices || {
          VIP: 150,
          Regular: 75,
          Economy: 35,
        },
      },
      "*"
    );

    // Load purchased seats for this event
    loadPurchasedSeats(event.id, iframe);
  };
}

// Load purchased seats from localStorage and mark them
function loadPurchasedSeats(eventId, iframe) {
  // Get purchased seats from cart items that have been checked out
  const purchasedSeats = JSON.parse(localStorage.getItem(`purchasedSeats_${eventId}`) || "[]");
  const defaultSeats = DEFAULT_BOOKED_SEATS_BY_EVENT[eventId] || [];
  const combinedSeats = Array.from(new Set([...defaultSeats, ...purchasedSeats]));

  iframe.contentWindow.postMessage({
    type: "markPurchasedSeats",
    seats: combinedSeats,
    eventId: eventId
  }, "*");
}

// Ticket Categories
const ticketPrices = {
  VIP: 150,
  Regular: 75,
  Economy: 35,
};

// Predefined booked seats per event (simulating data from backend)
const DEFAULT_BOOKED_SEATS_BY_EVENT = {
  1: ['A1-1', 'A1-2', 'B2-5'],
  2: ['C1-3', 'C1-4', 'D2-6'],
  3: ['N1-1', 'N1-2', 'N2-5'],
  4: ['V1-4', 'U2-7'],
  5: ['X1-3', 'Y2-2'],
};

let selectedSeats = {};
let currentCategory = "VIP";
let timerInterval = null;
let timerExpiredPopupShown = false;

// Start 2-minute booking timer
function startBookingTimer() {
  // Clear any existing timer
  if (timerInterval) {
    clearInterval(timerInterval);
  }

  timerExpiredPopupShown = false;
  const existingPopup = document.getElementById('timer-expired-popup');
  if (existingPopup) {
    existingPopup.remove();
  }

  const timerDisplay = document.getElementById('booking-timer');
  if (!timerDisplay) return;

  let timeLeft = 120; // 2 minutes in seconds
  const startTime = Date.now();

  timerDisplay.style.display = 'block';
  updateTimerDisplay(timeLeft);

  timerInterval = setInterval(() => {
    const elapsed = Date.now() - startTime;
    timeLeft = Math.max(0, 120 - Math.floor(elapsed / 1000));
    
    updateTimerDisplay(timeLeft);

    if (timeLeft === 0) {
      clearInterval(timerInterval);
      releaseAllSeats();
      timerDisplay.innerHTML = '<span class="text-danger">Time expired! Please refresh to book again.</span>';
      showTimerExpiredPopup();
    }
  }, 1000);
}

function showTimerExpiredPopup() {
  if (timerExpiredPopupShown) return;
  timerExpiredPopupShown = true;

  const popup = document.createElement('div');
  popup.id = 'timer-expired-popup';
  popup.style.position = 'fixed';
  popup.style.top = '0';
  popup.style.left = '0';
  popup.style.width = '100%';
  popup.style.height = '100%';
  popup.style.background = 'rgba(0,0,0,0.6)';
  popup.style.display = 'flex';
  popup.style.alignItems = 'center';
  popup.style.justifyContent = 'center';
  popup.style.zIndex = '9999';

  const content = document.createElement('div');
  content.style.background = '#fff';
  content.style.borderRadius = '12px';
  content.style.padding = '24px';
  content.style.maxWidth = '400px';
  content.style.textAlign = 'center';
  content.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';

  content.innerHTML = `
    <div class="mb-3">
      <i class="fas fa-hourglass-end fa-3x text-warning"></i>
    </div>
    <h4 class="mb-2">Session Expired</h4>
    <p class="mb-4">Your 2-minute reservation window has ended. Please refresh the page to start a new booking session.</p>
    <button class="btn btn-primary w-100" id="refresh-booking-btn">
      Refresh Page
    </button>
  `;

  popup.appendChild(content);
  document.body.appendChild(popup);

  const refreshBtn = document.getElementById('refresh-booking-btn');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', () => {
      window.location.reload();
    });
  }
}

// Update timer display
function updateTimerDisplay(seconds) {
  const timerDisplay = document.getElementById('booking-timer');
  if (!timerDisplay) return;

  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  timerDisplay.innerHTML = `
    <i class="fas fa-clock me-2"></i>
    <strong>Time remaining: ${mins}:${secs.toString().padStart(2, '0')}</strong>
  `;
}

// Release all selected seats when timer expires
function releaseAllSeats() {
  selectedSeats = {};
  updateUI();

  // Notify iframe to clear selections
  const iframe = document.getElementById("stadium-iframe");
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.postMessage(
      {
        type: "clearSelections",
      },
      "*"
    );
  }
}

// Category button handlers
document.addEventListener("DOMContentLoaded", async function () {
  // Get event ID from URL
  const eventId = getEventIdFromURL();
  
  if (!eventId) {
    alert("Invalid event ID. Redirecting to events page...");
    window.location.href = "../Events/event.html";
    return;
  }

  // Fetch event data
  const event = await fetchEventById(eventId);
  
  if (!event) {
    alert("Event not found. Redirecting to events page...");
    window.location.href = "../Events/event.html";
    return;
  }

  // Populate event details
  populateEventDetails(event);

  // Load appropriate stadium layout
  loadStadiumLayout(event);

  // Start booking timer
  startBookingTimer();

  // Category button handlers
  const categoryButtons = document.querySelectorAll(".category-btn");

  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      categoryButtons.forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      currentCategory = this.dataset.category;

      // Notify iframe about category change
      const iframe = document.getElementById("stadium-iframe");
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage(
          {
            type: "categoryChange",
            category: currentCategory,
          },
          "*"
        );
      }
    });
  });

  // Add to cart button
  const addToCartBtn = document.getElementById("add-to-cart-btn");
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", function () {
      if (Object.keys(selectedSeats).length === 0) {
        alert("Please select at least one seat");
        return;
      }

      // Prepare cart data
      const cartItem = {
        eventId: event.id,
        eventTitle: event.title,
        eventImage: event.image,
        eventLocation: event.location,
        eventDate: event.date,
        eventTime: event.time,
        seats: Object.values(selectedSeats).map((seat) => ({
          seatId: seat.seatId,
          section: seat.section,
          row: seat.row,
          seatNumber: seat.seatNumber,
          category: seat.category,
          price: seat.price,
        })),
        quantity: Object.keys(selectedSeats).length,
        total: calculateTotal(),
        addedAt: Date.now()
      };

      // Store in localStorage
      let cart = JSON.parse(localStorage.getItem("cart") || "[]");
      cart.push(cartItem);
      localStorage.setItem("cart", JSON.stringify(cart));

      // Show success popup
      showSuccessPopup(cartItem.quantity);

      // Update cart count using shared function
      if (window.cartFunctions) {
        window.cartFunctions.updateCartCount();
      } else {
        updateCartCount();
      }

      // Clear selections
      selectedSeats = {};
      updateUI();

      // Notify iframe to clear selections
      const iframe = document.getElementById("stadium-iframe");
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage(
          {
            type: "clearSelections",
          },
          "*"
        );
      }

      // Reset timer
      startBookingTimer();
    });
  }

  // Listen for messages from iframe
  window.addEventListener("message", function (event) {
    if (event.data && event.data.type === "seatSelection") {
      const { seatId, section, row, seatNumber, category, price, isSelected } =
        event.data;

      if (isSelected) {
        selectedSeats[seatId] = {
          seatId,
          section,
          row,
          seatNumber,
          category,
          price: price || (window.ticketPrices && window.ticketPrices[category]) || ticketPrices[category],
        };
      } else {
        delete selectedSeats[seatId];
      }

      updateUI();
    }
  });
});

// Show success popup
function showSuccessPopup(quantity) {
  // Create popup element
  const popup = document.createElement('div');
  popup.className = 'success-popup';
  popup.innerHTML = `
    <div class="success-popup-content">
      <div class="success-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h4>Success!</h4>
      <p>You have successfully added ${quantity} ticket(s) to your cart.</p>
      <p class="text-muted small">You have 2 minutes to complete your purchase.</p>
      <button class="btn btn-primary" onclick="this.closest('.success-popup').remove()">OK</button>
    </div>
  `;
  document.body.appendChild(popup);

  // Auto remove after 3 seconds
  setTimeout(() => {
    if (popup.parentNode) {
      popup.remove();
    }
  }, 3000);
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

function calculateTotal() {
  return Object.values(selectedSeats).reduce(
    (sum, seat) => sum + seat.price,
    0
  );
}

function updateUI() {
  // Update selected seats list
  const seatsList = document.getElementById("selected-seats-list");
  const addToCartBtn = document.getElementById("add-to-cart-btn");

  if (Object.keys(selectedSeats).length === 0) {
    seatsList.innerHTML =
      '<p class="text-muted text-center py-4">No seats selected</p>';
    addToCartBtn.disabled = true;
  } else {
    seatsList.innerHTML = Object.values(selectedSeats)
      .sort((a, b) => {
        if (a.section !== b.section) return a.section.localeCompare(b.section);
        if (a.row !== b.row) return a.row - b.row;
        return a.seatNumber - b.seatNumber;
      })
      .map(
        (seat) => `
        <div class="selected-seat-item">
          <div class="seat-info">
            <div class="seat-number">${seat.section}${seat.row}-${
          seat.seatNumber
        }</div>
            <div class="seat-category ${seat.category.toLowerCase()}">${
          seat.category
        }</div>
          </div>
          <div class="seat-price">$${seat.price}</div>
        </div>
      `
      )
      .join("");
    addToCartBtn.disabled = false;
  }

  // Update price summary
  const vipSeats = Object.values(selectedSeats).filter(
    (s) => s.category === "VIP"
  );
  const regularSeats = Object.values(selectedSeats).filter(
    (s) => s.category === "Regular"
  );
  const economySeats = Object.values(selectedSeats).filter(
    (s) => s.category === "Economy"
  );

  document.getElementById("vip-count").textContent = vipSeats.length;
  document.getElementById("vip-total").textContent = `$${vipSeats.reduce(
    (sum, s) => sum + s.price,
    0
  )}`;

  document.getElementById("regular-count").textContent = regularSeats.length;
  document.getElementById(
    "regular-total"
  ).textContent = `$${regularSeats.reduce((sum, s) => sum + s.price, 0)}`;

  document.getElementById("economy-count").textContent = economySeats.length;
  document.getElementById(
    "economy-total"
  ).textContent = `$${economySeats.reduce((sum, s) => sum + s.price, 0)}`;

  document.getElementById("total-price").textContent = `$${calculateTotal()}`;
}

// Initialize cart count on page load
document.addEventListener("DOMContentLoaded", function() {
  // Use shared cart functions if available, otherwise wait for them
  if (window.cartFunctions) {
    window.cartFunctions.updateCartCount();
  } else {
    // Wait for cart script to load
    const checkCartScript = setInterval(() => {
      if (window.cartFunctions) {
        window.cartFunctions.updateCartCount();
        clearInterval(checkCartScript);
      }
    }, 100);
  }
});

// Update cart count function (local fallback)
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const cartCountElements = document.querySelectorAll('.cart-count');
  cartCountElements.forEach(cartCount => {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.style.display = totalItems > 0 ? 'block' : 'none';
  });
}
