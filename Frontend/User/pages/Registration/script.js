document
  .getElementById("registrationForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const passwords = document.querySelectorAll('input[type="password"]');
    if (passwords[0].value !== passwords[1].value) {
      alert("Passwords do not match!");
      return;
    }

    alert("Account created successfully!");
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

// load navbar
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

function loadCSS(file) {
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = file;
  document.head.appendChild(link);
}

// Load CSS for navbar and footer
loadCSS("../../components/Navbar/navbar.css");
loadCSS("../../components/Responsive_Navbar/responsive_navbar.css");
loadCSS("../../components/Footer/footer.css");
