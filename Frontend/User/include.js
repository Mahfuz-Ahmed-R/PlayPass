if (window.location.protocol === 'file:') {
  console.error('⚠️ This page must be served from a web server (not file://). Please use a local server like Live Server, Python http.server, or similar.');
  alert('⚠️ This page must be served from a web server. Please use a local development server.');
}

const includeScriptRef =
  document.currentScript ||
  Array.from(document.getElementsByTagName("script")).find(script =>
    script.src.endsWith("/include.js") || script.src.includes("include.js")
  );

const includeBaseURL = includeScriptRef
  ? new URL("./", includeScriptRef.src).href
  : window.location.origin + "/";

function resolveIncludePath(file) {
  if (/^(?:[a-z]+:)?\/\//i.test(file)) {
    return file;
  }
  if (file.startsWith("/")) {
    return window.location.origin + file;
  }
  const currentDir = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
  return window.location.origin + currentDir + file;
}

function loadCSS(file) {
  const href = resolveIncludePath(file);
  
  console.log(`Loading CSS: ${href}`); 
  
  if (document.querySelector(`link[href="${href}"]`)) {
    return;
  }
  
  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = href;
  link.onerror = function() {
    console.error(`Failed to load CSS: ${href}`);
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
      
      if (id === 'card-component') {
        loadCardScript();
      }
      
      if (id === 'card-component') {
        loadCSS('components/Card/card.css');
      }
      
      if (id === 'footer') {
        loadCSS('components/Footer/footer.css');
      }
            if (id === 'navbar' || url.includes('navbar.html')) {
        loadCartScript();
      }
    })
    .catch(err => console.error("Error loading", url, err));
}

function loadCartScript() {
  if (document.querySelector('script[src*="components/Cart/cart.js"]') || window.cartFunctions) {
    return;
  }
  
  const script = document.createElement('script');
  script.src = resolveIncludePath('components/Cart/cart.js');
  script.async = true;
  document.body.appendChild(script);
}

function loadCardScript() {
  if (document.querySelector('script[src*="components/Card/script.js"]')) {
    return;
  }
  
  const script = document.createElement('script');
  script.src = resolveIncludePath('components/Card/script.js');
  script.async = true;
  document.body.appendChild(script);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    includeHTML('card-component', 'components/Card/card.html');
  });
} else {
  includeHTML('card-component', 'components/Card/card.html');
}

