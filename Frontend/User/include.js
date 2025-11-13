  // Function to load HTML components dynamically
const includeScriptRef =
  document.currentScript ||
  Array.from(document.getElementsByTagName("script")).find(script =>
    script.src.endsWith("/include.js")
  );

const includeBaseURL = includeScriptRef
  ? new URL("./", includeScriptRef.src).href
  : window.location.origin + "/";

function resolveIncludePath(file) {
  if (/^(?:[a-z]+:)?\/\//i.test(file) || file.startsWith("/")) {
    return file;
  }
  return new URL(file, includeBaseURL).href;
}

function includeHTML(id, file) {
  const target = document.getElementById(id);
  if (!target) {
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
    })
    .catch(err => console.error("Error loading", url, err));
}

includeHTML('navbar', 'components/Navbar/navbar.html');
includeHTML('responsive_navbar', 'components/Responsive_Navbar/responsive_navbar.html');
includeHTML('card-component', 'components/Card/card.html');
includeHTML('footer', 'components/Footer/footer.html');

