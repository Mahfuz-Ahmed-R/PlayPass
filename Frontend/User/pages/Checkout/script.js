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

// Payment method selection
const paymentMethods = document.querySelectorAll(".payment-method");
paymentMethods.forEach((method) => {
  method.addEventListener("click", function () {
    paymentMethods.forEach((m) => m.classList.remove("selected"));
    this.classList.add("selected");
  });
});

// Add more ticket button
document
  .querySelector(".add-ticket-btn")
  .addEventListener("click", function () {
    alert("Add more ticket functionality would be implemented here");
  });

// Delete ticket button
document.querySelector(".delete-btn").addEventListener("click", function () {
  if (confirm("Are you sure you want to delete this ticket?")) {
    alert("Ticket deleted");
  }
});

// Proceed to pay button
document.querySelector(".proceed-btn").addEventListener("click", function () {
  const termsChecked = document.getElementById("terms").checked;
  if (!termsChecked) {
    alert("Please agree to the Terms & Conditions");
    return;
  }

  const cart = JSON.parse(localStorage.getItem("cart") || "[]");
  if (cart.length === 0) {
    alert("Your cart is empty. Please add tickets before proceeding to pay.");
    return;
  }

  const purchaseSummary = [];

  cart.forEach((item) => {
    if (!item || !item.eventId) return;
    const seatIds = Array.isArray(item.seats)
      ? item.seats.map((seat) => seat.seatId).filter(Boolean)
      : [];

    if (seatIds.length === 0) return;

    const storageKey = `purchasedSeats_${item.eventId}`;
    const existingSeats = JSON.parse(localStorage.getItem(storageKey) || "[]");
    const mergedSeats = Array.from(new Set([...existingSeats, ...seatIds]));
    localStorage.setItem(storageKey, JSON.stringify(mergedSeats));

    purchaseSummary.push({
      title: item.eventTitle,
      count: seatIds.length,
    });
  });

  localStorage.removeItem("cart");
  if (window.cartFunctions && typeof window.cartFunctions.updateCartCount === 'function') {
    window.cartFunctions.updateCartCount();
  }

  let confirmationMessage = "Payment successful! Your seats have been confirmed.";
  if (purchaseSummary.length > 0) {
    confirmationMessage += "\n\nConfirmed tickets:\n";
    confirmationMessage += purchaseSummary
      .map((entry) => `${entry.title}: ${entry.count} seat(s)`)
      .join("\n");
  }
  confirmationMessage += "\n\nYou can refresh the event page to see your booked seats.";

  alert(confirmationMessage);
  window.location.href = "../Events/event.html";
});

// Redeem promo code
document.querySelector(".redeem-btn").addEventListener("click", function () {
  const promoCode = document.querySelector(".promo-input input").value;
  if (promoCode) {
    alert("Validating promo code: " + promoCode);
  } else {
    alert("Please enter a promo code");
  }
});

// Function to load HTML components dynamically
function includeHTML(id, file) {
  return fetch(file)
    .then((res) => res.text())
    .then((data) => {
      document.getElementById(id).innerHTML = data;
    })
    .catch((err) => console.error("Error loading", file, err));
}

includeHTML("navbar", "../../components/Navbar/navbar.html").then(() => {
  // Load cart script after navbar is loaded
  if (!document.querySelector('script[src*="components/Cart/cart.js"]') && !window.cartFunctions) {
    const script = document.createElement('script');
    script.src = '../../components/Cart/cart.js';
    script.async = true;
    document.body.appendChild(script);
  }
});
includeHTML(
  "responsive_navbar",
  "../../components/Responsive_Navbar/responsive_navbar.html"
);
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
