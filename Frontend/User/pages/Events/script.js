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
      
      // Load card script after card HTML is loaded
      if (id === 'card-section') {
        loadCardScript();
      }
    })
    .catch((err) => {
      console.error("Error loading", file, err);
      // Don't throw - allow other includes to continue
    });
}

function loadCardScript() {
  // Check if script is already loaded
  if (document.querySelector('script[src*="components/Card/script.js"]')) {
    return;
  }
  
  const script = document.createElement('script');
  script.src = "../../components/Card/script.js";
  script.async = true;
  script.onload = () => {
    // Initialize event filtering after card script is loaded
    initEventFiltering();
  };
  document.body.appendChild(script);
}

includeHTML("card-section", "../../components/Card/card.html");
includeHTML("footer", "../../components/Footer/footer.html");

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

// Function to load CSS components dynamically
function loadCSS(file) {
  // Check if CSS is already loaded to avoid duplicates
  const href = new URL(file, window.location.href).href;
  if (document.querySelector(`link[href="${file}"]`) || 
      document.querySelector(`link[href*="${file.split('/').pop()}"]`)) {
    return;
  }
  
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

// CSS files are already loaded in HTML head, so we skip loading them here
// loadCSS("../../components/Navbar/navbar.css");
// loadCSS("../../components/Responsive_Navbar/responsive_navbar.css");
loadCSS("../../components/Card/card.css");
loadCSS("../../components/Footer/footer.css");

// Event Filtering, Sorting, Search, and Pagination System
let allEvents = [];
let filteredEvents = [];
let currentPage = 1;
const itemsPerPage = 6;
let currentSort = null;
let currentFilters = {
  status: 'all', // 'all', 'live', 'upcoming'
  sports: [],
  locations: []
};

// Wait for events data to be available
async function initEventFiltering() {
  try {
    // Wait a bit for the card script to initialize
    await new Promise(resolve => setTimeout(resolve, 500));
    
    if (window.getEventsData) {
      allEvents = await window.getEventsData();
      applyAllFilters();
    } else {
      // Retry after a short delay
      setTimeout(initEventFiltering, 500);
    }
  } catch (error) {
    console.error('Error initializing event filtering:', error);
  }
}

// Filter Panel Controls
const filterBtn = document.getElementById('filterBtn');
const filterPanel = document.getElementById('filterPanel');
const closeFilter = document.getElementById('closeFilter');
const overlay = document.getElementById('overlay');
const applyFilters = document.getElementById('applyFilters');

if (filterBtn) {
  filterBtn.addEventListener('click', () => {
    filterPanel.classList.add('active');
    overlay.classList.add('active');
    if (sortPanel) sortPanel.classList.remove('active');
  });
}

if (closeFilter) {
  closeFilter.addEventListener('click', () => {
    filterPanel.classList.remove('active');
    overlay.classList.remove('active');
  });
}

if (applyFilters) {
  applyFilters.addEventListener('click', () => {
    const selectedSports = [];
    const selectedLocations = [];
    
    document.querySelectorAll('#filterPanel input[type="checkbox"]:checked').forEach(cb => {
      if (['football', 'cricket', 'basketball'].includes(cb.value)) {
        selectedSports.push(cb.value);
      } else {
        selectedLocations.push(cb.value);
      }
    });
    
    currentFilters.sports = selectedSports;
    currentFilters.locations = selectedLocations;
    
    filterPanel.classList.remove('active');
    overlay.classList.remove('active');
    
    currentPage = 1; // Reset to first page
    applyAllFilters();
  });
}

// Sort Panel
const sortBtn = document.getElementById('sortBtn');
const sortPanel = document.getElementById('sortPanel');
const closeSort = document.getElementById('closeSort');

if (sortBtn) {
  sortBtn.addEventListener('click', () => {
    sortPanel.classList.add('active');
    overlay.classList.add('active');
    if (filterPanel) filterPanel.classList.remove('active');
  });
}

if (closeSort) {
  closeSort.addEventListener('click', () => {
    sortPanel.classList.remove('active');
    overlay.classList.remove('active');
  });
}

// Sort Options
document.querySelectorAll('.sort-option').forEach(option => {
  option.addEventListener('click', function() {
    document.querySelectorAll('.sort-option').forEach(o => o.classList.remove('active'));
    this.classList.add('active');
    currentSort = this.dataset.sort;
    sortPanel.classList.remove('active');
    overlay.classList.remove('active');
    
    currentPage = 1; // Reset to first page
    applyAllFilters();
  });
});

// Close panels when clicking overlay
if (overlay) {
  overlay.addEventListener('click', () => {
    if (filterPanel) filterPanel.classList.remove('active');
    if (sortPanel) sortPanel.classList.remove('active');
    overlay.classList.remove('active');
  });
}

// Radio button selection (All, Live, Upcoming)
document.querySelectorAll('input[name="event-filter"]').forEach(radio => {
  radio.addEventListener('change', function() {
    currentFilters.status = this.id; // 'all', 'live', or 'upcoming'
    currentPage = 1; // Reset to first page
    applyAllFilters();
  });
});

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
  let searchTimeout;
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      currentPage = 1; // Reset to first page
      applyAllFilters();
    }, 300); // Debounce search
  });
}

// Main filtering function
function applyAllFilters() {
  if (allEvents.length === 0) {
    // Wait for events to load
    setTimeout(applyAllFilters, 500);
    return;
  }

  // Start with all events
  filteredEvents = [...allEvents];

  // Apply status filter (All, Live, Upcoming)
  if (currentFilters.status === 'live') {
    filteredEvents = filteredEvents.filter(event => event.isLive === true);
  } else if (currentFilters.status === 'upcoming') {
    const now = new Date();
    filteredEvents = filteredEvents.filter(event => {
      const eventDate = new Date(event.date + ' ' + event.time);
      return eventDate > now && !event.isLive;
    });
  }
  // 'all' shows everything, no filter needed

  // Apply sport filter
  if (currentFilters.sports.length > 0) {
    filteredEvents = filteredEvents.filter(event => {
      const eventSport = event.category.toLowerCase();
      return currentFilters.sports.some(sport => 
        eventSport.includes(sport.toLowerCase())
      );
    });
  }

  // Apply location filter
  if (currentFilters.locations.length > 0) {
    filteredEvents = filteredEvents.filter(event => {
      const eventLocation = event.location.toLowerCase();
      return currentFilters.locations.some(location => 
        eventLocation.includes(location.toLowerCase())
      );
    });
  }

  // Apply search filter (match name or stadium name)
  const searchQuery = searchInput ? searchInput.value.trim().toLowerCase() : '';
  if (searchQuery) {
    filteredEvents = filteredEvents.filter(event => {
      const matchName = event.title.toLowerCase();
      const stadiumName = event.location.toLowerCase();
      return matchName.includes(searchQuery) || stadiumName.includes(searchQuery);
    });
  }

  // Apply sorting
  if (currentSort) {
    applySorting();
  }

  // Render paginated results
  renderPaginatedCards();
}

// Sorting function
function applySorting() {
  if (!currentSort) return;

  switch (currentSort) {
    case 'low-to-high':
      filteredEvents.sort((a, b) => (a.price || 0) - (b.price || 0));
      break;
    case 'high-to-low':
      filteredEvents.sort((a, b) => (b.price || 0) - (a.price || 0));
      break;
    case 'date-asc':
      filteredEvents.sort((a, b) => {
        const dateA = new Date(a.date + ' ' + a.time);
        const dateB = new Date(b.date + ' ' + b.time);
        return dateA - dateB;
      });
      break;
    case 'date-desc':
      filteredEvents.sort((a, b) => {
        const dateA = new Date(a.date + ' ' + a.time);
        const dateB = new Date(b.date + ' ' + b.time);
        return dateB - dateA;
      });
      break;
  }
}

// Render paginated cards
function renderPaginatedCards() {
  if (!window.renderEventCards) {
    // Wait for render function to be available
    setTimeout(renderPaginatedCards, 500);
    return;
  }

  const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const eventsToShow = filteredEvents.slice(startIndex, endIndex);

  // Render cards
  window.renderEventCards(eventsToShow);

  // Render pagination
  renderPagination(totalPages);
}

// Pagination rendering
function renderPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  if (!pagination) return;

  pagination.innerHTML = "";

  if (totalPages <= 1) {
    return; // Don't show pagination if only one page or no results
  }

  const maxVisible = 7;
  let startPage = 1;
  let endPage = Math.min(maxVisible, totalPages);

  if (currentPage > Math.floor(maxVisible / 2)) {
    startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) {
      startPage = Math.max(1, endPage - maxVisible + 1);
    }
  }

  // Previous button
  const prevLi = document.createElement("li");
  prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
  prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>`;
  if (currentPage !== 1) {
    prevLi.querySelector(".page-link").onclick = (e) => {
      e.preventDefault();
      currentPage--;
      renderPaginatedCards();
    };
  } else {
    prevLi.querySelector(".page-link").onclick = (e) => e.preventDefault();
  }
  pagination.appendChild(prevLi);

  // Page numbers
  for (let i = startPage; i <= endPage; i++) {
    const li = document.createElement("li");
    li.className = `page-item ${i === currentPage ? "active" : ""}`;
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    li.querySelector(".page-link").onclick = (e) => {
      e.preventDefault();
      currentPage = i;
      renderPaginatedCards();
    };
    pagination.appendChild(li);
  }

  // Next button
  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
  nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>`;
  if (currentPage !== totalPages) {
    nextLi.querySelector(".page-link").onclick = (e) => {
      e.preventDefault();
      currentPage++;
      renderPaginatedCards();
    };
  } else {
    nextLi.querySelector(".page-link").onclick = (e) => e.preventDefault();
  }
  pagination.appendChild(nextLi);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    // initEventFiltering will be called after card script loads
  });
} else {
  // DOM is already ready
}
