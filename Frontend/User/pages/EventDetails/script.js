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
includeHTML("footer", "../../components/Footer/footer.html");

// Load stadium layout in iframe since it's a complete HTML document
const stadiumLayoutContainer =
  document.getElementById("stadium-layout") ||
  document.getElementById("cricket-stadium-layout");
if (stadiumLayoutContainer) {
  const iframe = document.createElement("iframe");
  // Determine which stadium layout to load based on which container exists
  if (document.getElementById("cricket-stadium-layout")) {
    iframe.src = "../../components/Stadium/cricket-stadium-layout.html";
  } else {
    iframe.src = "../../components/Stadium/stadium-layout.html";
  }
  iframe.style.width = "100%";
  iframe.style.height = "80vh";
  iframe.style.border = "none";
  iframe.style.overflow = "hidden";
  iframe.id = "stadium-iframe";
  stadiumLayoutContainer.appendChild(iframe);

  // Wait for iframe to load
  iframe.onload = function () {
    // Send category pricing to iframe
    iframe.contentWindow.postMessage(
      {
        type: "init",
        prices: {
          VIP: 150,
          Regular: 75,
          Economy: 35,
        },
      },
      "*"
    );
  };
}

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

// Ticket Categories
const ticketPrices = {
  VIP: 150,
  Regular: 75,
  Economy: 35,
};

let selectedSeats = {};
let currentCategory = "VIP";

// Category button handlers
document.addEventListener("DOMContentLoaded", function () {
  const categoryButtons = document.querySelectorAll(".category-btn");

  categoryButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from all buttons
      categoryButtons.forEach((b) => b.classList.remove("active"));
      // Add active class to clicked button
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
      const cartData = {
        seats: Object.values(selectedSeats).map((seat) => ({
          seatId: seat.seatId,
          category: seat.category,
          price: seat.price,
        })),
        total: calculateTotal(),
      };

      // Store in localStorage or send to backend
      let cart = JSON.parse(localStorage.getItem("cart") || "[]");
      cart.push(cartData);
      localStorage.setItem("cart", JSON.stringify(cart));

      alert(
        `Successfully added ${
          Object.keys(selectedSeats).length
        } seat(s) to cart!`
      );

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
          price,
        };
      } else {
        delete selectedSeats[seatId];
      }

      updateUI();
    }
  });
});

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
