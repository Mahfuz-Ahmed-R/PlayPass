// Check if running from file:// protocol (won't work with fetch)
if (window.location.protocol === 'file:') {
  console.error('⚠️ This page must be served from a web server (not file://). Please use a local server like Live Server, Python http.server, or similar.');
  alert('⚠️ This page must be served from a web server. Please use a local development server.');
}

// Function to load HTML components dynamically
const includeScriptRef =
  document.currentScript ||
  Array.from(document.getElementsByTagName("script")).find(script =>
    script.src.endsWith("/include.js") || script.src.includes("include.js")
  );

const includeBaseURL = includeScriptRef
  ? new URL("./", includeScriptRef.src).href
  : window.location.origin + "/";

function resolveIncludePath(file) {
  // If already absolute URL, return as is
  if (/^(?:[a-z]+:)?\/\//i.test(file)) {
    return file;
  }
  // If starts with /, make it absolute from origin
  if (file.startsWith("/")) {
    return window.location.origin + file;
  }
  // Otherwise, make it relative to current page location
  const currentDir = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
  return window.location.origin + currentDir + file;
}

// Function to load CSS files
function loadCSS(file) {
  const href = resolveIncludePath(file);
  
  console.log(`Loading CSS: ${href}`); // Debug log
  
  // Check if CSS is already loaded
  if (document.querySelector(`link[href="${href}"]`)) {
    return;
  }
  
  // Load CSS directly - browser will handle MIME type validation
  // Some servers don't handle HEAD requests well, so we load directly
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = href;
  link.onerror = function() {
    console.error(`Failed to load CSS: ${href}`);
    // Try with absolute path from origin as fallback
    const fallbackHref = window.location.origin + '/' + file;
    if (fallbackHref !== href) {
      console.log(`Trying fallback path: ${fallbackHref}`);
      const fallbackLink = document.createElement("link");
      fallbackLink.rel = "stylesheet";
      fallbackLink.href = fallbackHref;
      document.head.appendChild(fallbackLink);
    }
  };
  link.onload = function() {
    console.log(`CSS loaded successfully: ${href}`);
  };
  document.head.appendChild(link);
}

function includeHTML(id, file) {
  const target = document.getElementById(id);
  if (!target) {
    console.warn(`Target element with id "${id}" not found`);
    return;
  }

  const url = resolveIncludePath(file);

  fetch(url)
    .then(res => {
      if (!res.ok) {
        throw new Error(`Failed to fetch ${url}: ${res.status}`);
      }
      return res.text();
    })
    .then(data => {
      target.innerHTML = data;
      
      // Load card script after card HTML is loaded
      if (id === 'card-component') {
        loadCardScript();
      }
      
      // Load card CSS after card HTML is loaded
      if (id === 'card-component') {
        loadCSS('components/Card/card.css');
      }
      
      // Load footer CSS after footer HTML is loaded
      if (id === 'footer') {
        loadCSS('components/Footer/footer.css');
      }
      
      // Load cart script after navbar HTML is loaded
      if (id === 'navbar' || url.includes('navbar.html')) {
        loadCartScript();
      }
    })
    .catch(err => console.error("Error loading", url, err));
}

function loadCartScript() {
  // Check if script is already loaded
  if (document.querySelector('script[src*="components/Cart/cart.js"]') || window.cartFunctions) {
    return;
  }
  
  const script = document.createElement('script');
  script.src = resolveIncludePath('components/Cart/cart.js');
  script.async = true;
  document.body.appendChild(script);
}

function loadCardScript() {
  // Check if script is already loaded
  if (document.querySelector('script[src*="components/Card/script.js"]')) {
    return;
  }
  
  const script = document.createElement('script');
  script.src = resolveIncludePath('components/Card/script.js');
  script.async = true;
  document.body.appendChild(script);
}

// Wait for DOM to be ready before loading components
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    includeHTML('card-component', 'components/Card/card.html');
    includeHTML('footer', 'components/Footer/footer.html');
  });
} else {
  // DOM is already ready
  includeHTML('card-component', 'components/Card/card.html');
  includeHTML('footer', 'components/Footer/footer.html');
}

