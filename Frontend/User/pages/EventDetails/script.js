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

function getEventIdFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  return parseInt(urlParams.get("id")) || null;
}

function getMatchData() {
  if (window.matchData && window.matchData.match_id) {
    console.log('Using match data from database:', window.matchData);
    return {
      id: window.matchData.match_id,
      match_id: window.matchData.match_id,
      stadium_id: window.matchData.stadium_id,
      title: window.matchData.match_title,
      location: window.matchData.stadium_location,
      category: 'Football', // Default to Football, can be determined from match data if available
      image: window.matchData.poster_url,
      date: window.matchData.match_date,
      time: window.matchData.start_time,
      isLive: window.matchData.status && window.matchData.status.toLowerCase() === 'live',
      status: window.matchData.status
    };
  }
  
  const eventId = getEventIdFromURL();
  if (eventId) {
    return fetchEventById(eventId);
  }
  
  return null;
}

async function fetchEventById(eventId) {
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

function populateEventDetails(event) {
  if (!event) return;

  const { day, month, year } = formatDate(event.date);
  
  const matchImage = document.querySelector('.match-image');
  if (matchImage && event.image) {
    matchImage.src = event.image;
    matchImage.alt = event.title;
    matchImage.onerror = function() {
      this.src = '../../assets/img/img3.jpg';
    };
  }

  const categoryBadge = document.querySelector('.match-category-badge');
  if (categoryBadge) {
    categoryBadge.textContent = event.category;
  }

  const liveBadge = document.querySelector('.match-live-badge');
  if (liveBadge) {
    liveBadge.style.display = event.isLive ? 'block' : 'none';
  }

  const matchTitle = document.querySelector('.match-title');
  if (matchTitle) {
    matchTitle.textContent = event.title;
  }

  const stadiumLocation = document.querySelector('.match-stadium span');
  if (stadiumLocation) {
    stadiumLocation.textContent = event.location;
  }

  const dateElement = document.querySelector('.meta-item span');
  if (dateElement) {
    dateElement.textContent = `${month} ${day}, ${year}`;
  }

  const timeElements = document.querySelectorAll('.meta-item span');
  if (timeElements.length > 1) {
    timeElements[1].textContent = formatTime(event.time);
  }

  updateCategoryPrices(event);
}

function updateCategoryPrices(event) {
  
  if (window.ticketPrices && Object.keys(window.ticketPrices).length > 0) {
    return;
  }
  
  const prices = {
    VIP: event.price || 150,
    Regular: (event.price || 150) * 0.5,
    Economy: (event.price || 150) * 0.23
  };

  document.querySelectorAll('.category-btn').forEach(btn => {
    const category = btn.dataset.category;
    const small = btn.querySelector('small');
    if (small && prices[category]) {
      small.textContent = `$${prices[category].toFixed(2)}`;
    }
  });

  window.ticketPrices = prices;
}

function loadStadiumLayout(event) {
  console.log('Loading stadium layout for event:', event);
  
  const footballContainer = document.getElementById("stadium-layout");
  const cricketContainer = document.getElementById("cricket-stadium-layout");
  
  if (!footballContainer || !cricketContainer) {
    console.error('Stadium layout containers not found!');
    return;
  }

  const isCricket = event.category && event.category.toLowerCase() === 'cricket';
  const container = isCricket ? cricketContainer : footballContainer;
  const otherContainer = isCricket ? footballContainer : cricketContainer;
  
  console.log('Using layout:', isCricket ? 'cricket' : 'football');

  if (otherContainer) {
    otherContainer.style.display = 'none';
    otherContainer.innerHTML = '';
  }
  container.style.display = 'block';
  container.innerHTML = '';

  const layoutFile = isCricket 
    ? "../../components/Stadium/cricket-stadium-layout.php"
    : "../../components/Stadium/stadium-layout.php";
  
  const matchData = window.matchData || {};
  const urlParams = new URLSearchParams();
  
  const matchId = matchData.match_id || event.match_id || event.id || null;
  const stadiumId = matchData.stadium_id || event.stadium_id || null;
  
  if (matchId) {
    urlParams.append('match_id', matchId);
    console.log('Using match_id from database:', matchId);
  } else {
    console.warn('No match_id available!');
  }
  
  if (stadiumId) {
    urlParams.append('stadium_id', stadiumId);
    console.log('Using stadium_id from database:', stadiumId);
  } else {
    console.warn('No stadium_id available! Using default.');
    urlParams.append('stadium_id', '1');
  }
  
  const layoutUrl = layoutFile + (urlParams.toString() ? '?' + urlParams.toString() : '');
  
  console.log('Loading iframe from:', layoutUrl);

  const iframe = document.createElement("iframe");
  iframe.src = layoutUrl;
  iframe.style.width = "100%";
  iframe.style.height = "80vh";
  iframe.style.border = "none";
  iframe.style.overflow = "hidden";
  iframe.style.backgroundColor = "transparent";
  iframe.id = "stadium-iframe";
  
  iframe.onerror = function() {
    console.error('Error loading stadium layout iframe');
    container.innerHTML = '<div class="alert alert-danger">Failed to load stadium layout. Please refresh the page.</div>';
  };
  
  container.appendChild(iframe);

  iframe.onload = function () {
    console.log('Stadium layout iframe loaded successfully');
    
    try {
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

      const eventIdForSeats = event.match_id || event.id;
      loadPurchasedSeats(eventIdForSeats, iframe);
    } catch (error) {
      console.error('Error communicating with iframe:', error);
    }
  };
}

function loadPurchasedSeats(eventId, iframe) {
  const purchasedSeats = JSON.parse(localStorage.getItem(`purchasedSeats_${eventId}`) || "[]");
  const defaultSeats = DEFAULT_BOOKED_SEATS_BY_EVENT[eventId] || [];
  const combinedSeats = Array.from(new Set([...defaultSeats, ...purchasedSeats]));

  iframe.contentWindow.postMessage({
    type: "markPurchasedSeats",
    seats: combinedSeats,
    eventId: eventId
  }, "*");
}

const ticketPrices = {
  VIP: 150,
  Regular: 75,
  Economy: 35,
};

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
const MAX_TICKETS = 5; // Maximum tickets per cart addition
const SEAT_LOCK_DURATION = 180; // 3 minutes in seconds

function startBookingTimer() {
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

  let timeLeft = SEAT_LOCK_DURATION; // 3 minutes in seconds
  const startTime = Date.now();

  timerDisplay.style.display = 'block';
  updateTimerDisplay(timeLeft);

  timerInterval = setInterval(() => {
    const elapsed = Date.now() - startTime;
    timeLeft = Math.max(0, SEAT_LOCK_DURATION - Math.floor(elapsed / 1000));
    
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
    <p class="mb-4">Your 3-minute reservation window has ended. Please refresh the page to start a new booking session.</p>
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

function releaseAllSeats() {
  selectedSeats = {};
  updateUI();

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

document.addEventListener("DOMContentLoaded", async function () {
  let event = getMatchData();
  
  if (!event) {
    const eventId = getEventIdFromURL();
    if (eventId) {
      event = await fetchEventById(eventId);
    }
  }
  
  if (!event) {
    alert("Event not found. Redirecting to events page...");
    window.location.href = "../Events/event.php";
    return;
  }

  populateEventDetails(event);

  loadStadiumLayout(event);

  startBookingTimer();

  const categoryButtons = document.querySelectorAll(".category-btn");

  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      categoryButtons.forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      currentCategory = this.dataset.category;

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

  const addToCartBtn = document.getElementById("add-to-cart-btn");
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", async function () {
      if (Object.keys(selectedSeats).length === 0) {
        alert("Please select at least one seat");
        return;
      }

      const selectedCount = Object.keys(selectedSeats).length;
      if (selectedCount > MAX_TICKETS) {
        alert(`You can add a maximum of ${MAX_TICKETS} tickets at a time. Please select ${MAX_TICKETS} or fewer seats.`);
        return;
      }

      let cart = JSON.parse(localStorage.getItem("cart") || "[]");
      const totalTicketsInCart = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
      const totalAfterAdd = totalTicketsInCart + selectedCount;
      
      if (totalAfterAdd > MAX_TICKETS) {
        const remaining = MAX_TICKETS - totalTicketsInCart;
        if (remaining <= 0) {
          alert(`You have reached the maximum limit of ${MAX_TICKETS} tickets in your cart. Please checkout or remove items from cart.`);
        } else {
          alert(`You can only add ${remaining} more ticket(s) to your cart (maximum ${MAX_TICKETS} tickets total).`);
        }
        return;
      }

      addToCartBtn.disabled = true;
      addToCartBtn.textContent = "Processing...";

      const iframe = document.getElementById("stadium-iframe");
      if (iframe && iframe.contentWindow) {
        const seatIds = Object.keys(selectedSeats);
        let allHeld = true;
        const failedSeats = [];
        const holdResults = {};

        for (const seatId of seatIds) {
          try {
            const holdResult = await new Promise((resolve) => {
              const messageHandler = (event) => {
                if (event.data && event.data.type === 'holdSeatResult' && event.data.seatId === seatId) {
                  window.removeEventListener('message', messageHandler);
                  holdResults[seatId] = {
                    success: event.data.success,
                    message: event.data.message || null,
                    holdId: event.data.holdId,
                    expiresAt: event.data.expiresAt
                  };
                  resolve({
                    success: event.data.success,
                    message: event.data.message || null
                  });
                }
              };
              window.addEventListener('message', messageHandler);
              
              iframe.contentWindow.postMessage({
                type: 'holdSeat',
                seatId: seatId
              }, '*');

              setTimeout(() => {
                window.removeEventListener('message', messageHandler);
                if (!holdResults[seatId]) {
                  holdResults[seatId] = { success: false, message: 'Request timeout' };
                }
                resolve({ success: false, message: 'Request timeout' });
              }, 5000);
            });

            if (!holdResult.success) {
              allHeld = false;
              failedSeats.push({
                seatId: seatId,
                message: holdResult.message || 'Failed to hold seat'
              });
            } else {
              if (selectedSeats[seatId] && holdResults[seatId]) {
                selectedSeats[seatId].holdId = holdResults[seatId].holdId;
                selectedSeats[seatId].expiresAt = holdResults[seatId].expiresAt;
              }
            }
          } catch (error) {
            console.error('Error holding seat:', seatId, error);
            allHeld = false;
            failedSeats.push({
              seatId: seatId,
              message: 'Network error: ' + error.message
            });
          }
        }

        if (!allHeld) {
          addToCartBtn.disabled = false;
          addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
          
          const errorMessages = failedSeats.map(f => {
            const seatInfo = selectedSeats[f.seatId];
            const seatLabel = seatInfo ? `${seatInfo.section}${seatInfo.row}-${seatInfo.seatNumber}` : f.seatId;
            return `${seatLabel}: ${f.message}`;
          }).join('\n');
          
          alert(`Failed to add seats to cart:\n\n${errorMessages}\n\nPlease refresh the page and try again.`);
          return;
        }
      }

      const cartItem = {
        eventId: event.match_id || event.id, // Use match_id from database if available
        match_id: event.match_id || event.id,
        stadium_id: event.stadium_id,
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
          holdId: seat.holdId || null, // Now includes hold ID from permanent hold
          expiresAt: seat.expiresAt || null // Now includes expiration time
        })),
        quantity: selectedCount,
        total: calculateTotal(),
        addedAt: Date.now()
      };

      // Store in localStorage
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

      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage(
          {
            type: "clearSelections",
          },
          "*"
        );
      }

      startBookingTimer();

      addToCartBtn.disabled = false;
      addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
    });
  }

  window.addEventListener("message", function (event) {
    if (event.data && event.data.type === "seatSelection") {
      const { seatId, section, row, seatNumber, category, price, isSelected, holdId, expiresAt } =
        event.data;

      if (isSelected) {
        if (Object.keys(selectedSeats).length >= MAX_TICKETS) {
          alert(`You can select a maximum of ${MAX_TICKETS} tickets at a time. Please remove a seat or add current selection to cart.`);
          const iframe = document.getElementById("stadium-iframe");
          if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({
              type: "deselectSeat",
              seatId: seatId
            }, "*");
          }
          return;
        }

        selectedSeats[seatId] = {
          seatId,
          section,
          row,
          seatNumber,
          category,
          price: price || (window.ticketPrices && window.ticketPrices[category]) || ticketPrices[category],
          holdId: holdId || null, // Store hold ID for seat lock
          expiresAt: expiresAt || null // Store expiration time
        };
      } else {
        delete selectedSeats[seatId];
      }

      updateUI();
    }
  });
});

function showSuccessPopup(quantity) {
  const popup = document.createElement('div');
  popup.className = 'success-popup';
  popup.innerHTML = `
    <div class="success-popup-content">
      <div class="success-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h4>Success!</h4>
      <p>You have successfully added ${quantity} ticket(s) to your cart.</p>
      <p class="text-muted small">Seats are locked for 3 minutes. Please complete your purchase within this time.</p>
      <button class="btn btn-primary" onclick="this.closest('.success-popup').remove()">OK</button>
    </div>
  `;
  document.body.appendChild(popup);

  setTimeout(() => {
    if (popup.parentNode) {
      popup.remove();
    }
  }, 3000);
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

function calculateTotal() {
  return Object.values(selectedSeats).reduce(
    (sum, seat) => sum + seat.price,
    0
  );
}

function updateUI() {
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

  const categoryNames = window.ticketCategories 
    ? window.ticketCategories.map(cat => cat.category_name)
    : Array.from(document.querySelectorAll('.summary-row[data-category]')).map(row => row.dataset.category);
  
  categoryNames.forEach(categoryName => {
    const categorySeats = Object.values(selectedSeats).filter(
      (s) => s.category === categoryName
    );
    
    const countElement = document.querySelector(`.category-count[data-category="${categoryName}"]`);
    const totalElement = document.querySelector(`.category-total[data-category="${categoryName}"]`);
    
    if (countElement) {
      countElement.textContent = categorySeats.length;
    }
    if (totalElement) {
      const categoryTotal = categorySeats.reduce((sum, s) => sum + s.price, 0);
      totalElement.textContent = `$${categoryTotal.toFixed(2)}`;
    }
  });

  document.getElementById("total-price").textContent = `$${calculateTotal().toFixed(2)}`;
}

document.addEventListener("DOMContentLoaded", function() {
  if (window.cartFunctions) {
    window.cartFunctions.updateCartCount();
  } else {
    const checkCartScript = setInterval(() => {
      if (window.cartFunctions) {
        window.cartFunctions.updateCartCount();
        clearInterval(checkCartScript);
      }
    }, 100);
  }
});

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  const cartCountElements = document.querySelectorAll('.cart-count');
  cartCountElements.forEach(cartCount => {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.style.display = totalItems > 0 ? 'block' : 'none';
  });
}
