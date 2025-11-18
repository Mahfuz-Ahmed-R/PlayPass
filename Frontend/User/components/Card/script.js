// Card Component Script
// Dynamically loads event cards from backend (using dummy data for now)

(function() {
  'use strict';

  // Dummy data - Replace this with actual API call when backend is ready
  const dummyEventsData = [
    {
      id: 1,
      title: "Real Madrid vs Barcelona",
      location: "Camp Nou, Barcelona, Spain",
      category: "Football",
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
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
      image: "assets/img/img3.jpg",
      date: "2024-12-20",
      time: "10:00",
      isLive: false,
      price: 160,
      eventDetailsUrl: "pages/EventDetails/eventdetails.html"
    }
  ];

  /**
   * Fetch events from backend API
   * Currently returns dummy data, replace with actual API call
   */
  async function fetchEvents() {
    try {
      // TODO: Replace with actual API endpoint when backend is ready
      // const response = await fetch('/api/events');
      // if (!response.ok) throw new Error('Failed to fetch events');
      // const data = await response.json();
      // return data;

      // For now, return dummy data
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve(dummyEventsData);
        }, 100); // Simulate network delay
      });
    } catch (error) {
      console.error('Error fetching events:', error);
      // Return empty array on error
      return [];
    }
  }

  /**
   * Resolve relative path to work from both index.html and pages/Events/event.html
   * Works from both index.html and pages/Events/event.html
   */
  function resolveRelativePath(relativePath) {
    // If path already starts with / or http, return as is
    if (relativePath.startsWith('/') || relativePath.startsWith('http')) {
      return relativePath;
    }
    
    // Get current page path
    const currentPath = window.location.pathname;
    
    // If we're in a subdirectory (like pages/Events/), go up to root
    if (currentPath.includes('/pages/')) {
      // Count how many levels deep we are (excluding Frontend/User from workspace root)
      const pathParts = currentPath.split('/').filter(p => p && p !== 'Frontend' && p !== 'User');
      const depth = pathParts.length - 1; // -1 because we don't count the file itself
      const upPath = '../'.repeat(depth);
      return upPath + relativePath;
    }
    
    // If we're at root (index.html), use path as is
    return relativePath;
  }

  /**
   * Format date to display format (e.g., "13 Nov")
   */
  function formatDate(dateString) {
    const date = new Date(dateString);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const day = date.getDate();
    const month = months[date.getMonth()];
    return { day, month };
  }

  /**
   * Create a single card HTML element
   */
  function createCardHTML(event) {
    const { day, month } = formatDate(event.date);
    const liveBadge = event.isLive ? '<span class="live-badge">Live Now</span>' : '';
    const imagePath = resolveRelativePath(event.image);
    const eventUrl = resolveRelativePath(event.eventDetailsUrl);
    
    return `
      <div class="col-lg-4 col-md-6">
        <div class="card event-card">
          <div class="img-container">
            <img src="${imagePath}" class="card-img-top" alt="${event.category}" />
            <span class="event-category">${event.category}</span>
            ${liveBadge}
          </div>
          <div class="card-body">
            <h5 class="event-title">${event.title}</h5>
            <p class="event-location">
              <span class="location-icon">📍</span>
              ${event.location}
            </p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="date-badge">
                <span class="day">${day}</span>
                <span class="month">${month}</span>
              </div>
              <button onclick="window.location.href='${eventUrl}?id=${event.id}'" class="btn price-tag bg-success">Get Tickets</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Render cards to the container
   */
  function renderCards(events, limit = null) {
    const container = document.getElementById('cards-container');
    
    if (!container) {
      console.error('Cards container not found');
      return;
    }

    // Clear existing content
    container.innerHTML = '';

    // Limit events if specified (for index.html)
    const eventsToShow = limit ? events.slice(0, limit) : events;

    if (eventsToShow.length === 0) {
      container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No events available at the moment.</p></div>';
      return;
    }

    // Generate and append cards
    eventsToShow.forEach(event => {
      container.innerHTML += createCardHTML(event);
    });
  }

  /**
   * Check if current page is index.html
   */
  function isIndexPage() {
    const path = window.location.pathname;
    return path === '/' || path.endsWith('/index.html') || path.endsWith('/');
  }

  /**
   * Check if current page is event.html
   */
  function isEventPage() {
    const path = window.location.pathname;
    return path.includes('/event.html') || path.includes('/Events/event.html');
  }

  /**
   * Initialize card component
   */
  async function initCards() {
    try {
      const events = await fetchEvents();
      
      // Show first 6 cards on index.html, all cards on event.html
      if (isIndexPage()) {
        renderCards(events, 6);
      } else if (isEventPage()) {
        renderCards(events);
      } else {
        // Default: show all cards
        renderCards(events);
      }
    } catch (error) {
      console.error('Error initializing cards:', error);
      const container = document.getElementById('cards-container');
      if (container) {
        container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Failed to load events. Please try again later.</p></div>';
      }
    }
  }

  /**
   * Wait for the cards container to be available (in case card.html is loaded asynchronously)
   */
  function waitForContainer(callback, maxAttempts = 50, interval = 100) {
    let attempts = 0;
    const checkContainer = () => {
      const container = document.getElementById('cards-container');
      if (container) {
        callback();
      } else if (attempts < maxAttempts) {
        attempts++;
        setTimeout(checkContainer, interval);
      } else {
        console.warn('Cards container not found after maximum attempts');
      }
    };
    checkContainer();
  }

  // Initialize when DOM is ready and container is available
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      waitForContainer(initCards);
    });
  } else {
    // DOM is already ready, but container might not be (if loaded asynchronously)
    waitForContainer(initCards);
  }

  // Also export initCards function for manual initialization if needed
  window.initEventCards = initCards;
  
  // Export events data and functions for filtering/sorting
  window.getEventsData = async function() {
    return await fetchEvents();
  };
  
  window.renderEventCards = renderCards;

})();

