// Tab switching functionality
const tabButtons = document.querySelectorAll(".tab-btn");
tabButtons.forEach((btn) => {
  btn.addEventListener("click", function () {
    tabButtons.forEach((b) => b.classList.remove("active"));
    this.classList.add("active");
  });
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
