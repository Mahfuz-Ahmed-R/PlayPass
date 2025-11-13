// Function to load HTML components dynamically
function includeHTML(id, file) {
  fetch(file)
    .then((res) => res.text())
    .then((data) => {
      document.getElementById(id).innerHTML = data;
    })
    .catch((err) => console.error("Error loading", file, err));
}

includeHTML("navbar", "../../components/Navbar/navbar.html");
includeHTML(
  "responsive_navbar",
  "../../components/Responsive_Navbar/responsive_navbar.html"
);
includeHTML("card-section", "../../components/Card/card.html");
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
loadCSS("../../components/Card/card.css");
loadCSS("../../components/Footer/footer.css");


const filterBtn = document.getElementById('filterBtn');
const filterPanel = document.getElementById('filterPanel');
const closeFilter = document.getElementById('closeFilter');
const overlay = document.getElementById('overlay');
const applyFilters = document.getElementById('applyFilters');

filterBtn.addEventListener('click', () => {
    filterPanel.classList.add('active');
    overlay.classList.add('active');
    sortPanel.classList.remove('active');
});

closeFilter.addEventListener('click', () => {
    filterPanel.classList.remove('active');
    overlay.classList.remove('active');
});

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
    
    console.log('Selected sports:', selectedSports);
    console.log('Selected locations:', selectedLocations);
    filterPanel.classList.remove('active');
    overlay.classList.remove('active');
    // Apply filters logic here
});

// Sort Panel
const sortBtn = document.getElementById('sortBtn');
const sortPanel = document.getElementById('sortPanel');
const closeSort = document.getElementById('closeSort');

sortBtn.addEventListener('click', () => {
    sortPanel.classList.add('active');
    overlay.classList.add('active');
    filterPanel.classList.remove('active');
});

closeSort.addEventListener('click', () => {
    sortPanel.classList.remove('active');
    overlay.classList.remove('active');
});

// Sort Options
document.querySelectorAll('.sort-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.sort-option').forEach(o => o.classList.remove('active'));
        this.classList.add('active');
        const sortType = this.dataset.sort;
        console.log('Sort by:', sortType);
        sortPanel.classList.remove('active');
        overlay.classList.remove('active');
        // Apply sort logic here
    });
});

// Close panels when clicking overlay
overlay.addEventListener('click', () => {
    filterPanel.classList.remove('active');
    sortPanel.classList.remove('active');
    overlay.classList.remove('active');
});

// Radio button selection
document.querySelectorAll('input[name="event-filter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        console.log('Selected filter:', this.id);
        // Apply radio filter logic here
    });
});

// Search functionality
const searchInput = document.querySelector('.search-box input');
searchInput.addEventListener('input', function() {
    console.log('Search query:', this.value);
    // Apply search logic here
});




let currentPage = 1;
const totalPages = 20;
const maxVisible = 7;

function updatePage(page) {
  currentPage = page;
  renderPagination();
}

function renderPagination() {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  // Calculate start and end page numbers to show
  let startPage = 1;
  let endPage = maxVisible;

  if (currentPage > maxVisible) {
    startPage = currentPage - maxVisible + 1;
    endPage = currentPage;
  }

  if (endPage > totalPages) {
    endPage = totalPages;
    startPage = Math.max(1, totalPages - maxVisible + 1);
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
      updatePage(currentPage - 1);
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
      updatePage(i);
    };
    pagination.appendChild(li);
  }

  // Next button
  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${
    currentPage === totalPages ? "disabled" : ""
  }`;
  nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>`;
  if (currentPage !== totalPages) {
    nextLi.querySelector(".page-link").onclick = (e) => {
      e.preventDefault();
      updatePage(currentPage + 1);
    };
  } else {
    nextLi.querySelector(".page-link").onclick = (e) => e.preventDefault();
  }
  pagination.appendChild(nextLi);
}

// Initialize pagination
renderPagination();