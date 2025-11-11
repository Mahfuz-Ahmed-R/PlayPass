  // Function to load HTML components dynamically
function includeHTML(id, file) {
  fetch(file)
    .then(res => res.text())
    .then(data => {
      document.getElementById(id).innerHTML = data;
    })
    .catch(err => console.error('Error loading', file, err));
}

includeHTML('navbar', 'components/Navbar/navbar.html');
includeHTML('responsive_navbar', 'components/Responsive_Navbar/responsive_navbar.html');
includeHTML('card-component', 'components/Card/card.html');
includeHTML('footer', 'components/Footer/footer.html');