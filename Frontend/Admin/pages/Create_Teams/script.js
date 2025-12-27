function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
  }
  
  function closeSidebar() {
    document.getElementById("sidebar").classList.remove("active");
  }
  
  document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (!href || href === "#" || href.startsWith("#")) {
        e.preventDefault();
        document
          .querySelectorAll(".nav-link")
          .forEach((l) => l.classList.remove("active"));
        this.classList.add("active");
        if (window.innerWidth <= 768) closeSidebar();
      } else {
        document
          .querySelectorAll(".nav-link")
          .forEach((l) => l.classList.remove("active"));
        this.classList.add("active");
      }
    });
  });
  
  document.querySelectorAll(".order-tab").forEach((tab) => {
    tab.addEventListener("click", function () {
      document
        .querySelectorAll(".order-tab")
        .forEach((t) => t.classList.remove("active"));
      this.classList.add("active");
    });
  });
  
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      document
        .querySelectorAll(".tab-btn")
        .forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
    });
  });
  
document.getElementById('teamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../../Backend/PHP/team-back.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response:', data);
        
        if (data.success) {
            alert('✓ ' + data.message);
            resetForm();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('Error: Unable to connect to server. Please check your connection and try again.');
    });
});