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
  alert("Proceeding to payment...");
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
