function includeHTML(id, file, callback) {
  fetch(file)
    .then(res => res.text())
    .then(data => {
      document.getElementById(id).innerHTML = data;
      if (callback) callback(); // run callback after HTML is inserted
    })
    .catch(err => console.error('Error loading', file, err));
}

function highlightActiveLink() {
  const currentPage = window.location.pathname.split("/").pop();
  document.querySelectorAll("#navbar a").forEach(link => {
    const linkPage = link.getAttribute("href").split("/").pop();
    if (linkPage === currentPage) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

// Load navbar and then highlight active link
includeHTML('navbar', '/components/Navbar/navbar.html', highlightActiveLink);