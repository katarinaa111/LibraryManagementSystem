const routes = {
  '/dashboard': 'views/dashboard.html',
  '/login': 'views/login.html',
  '/register': 'views/register.html'
};

async function loadPage() {
  const path = location.hash.replace('#', '') || '/dashboard';
  const page = routes[path] || routes['/dashboard'];

  try {
    const res = await fetch(page);
    const html = await res.text();
    document.getElementById('app').innerHTML = html;
    attachInternalLinks(); // Attach SPA navigation to internal links
  } catch (e) {
    document.getElementById('app').innerHTML = `<div class="alert alert-danger">Page not found.</div>`;
  }
}

// Intercept internal link clicks for SPA navigation
function attachInternalLinks() {
  const app = document.getElementById('app');
  if (!app) return;
  const links = app.querySelectorAll('a[href^="#"], a[href^="/"]');
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = link.getAttribute('href');
      if (routes[href]) {
        e.preventDefault();
        location.hash = href;
      }
    });
  });
}

// Load page on hash change & first load
window.addEventListener('hashchange', loadPage);
window.addEventListener('DOMContentLoaded', loadPage);