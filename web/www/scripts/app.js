console.log('app.js loaded')

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.getElementById('site-nav');
const mqMobile = window.matchMedia('(max-width: 680px)');

function setNav(open) {
  if (!navToggle || !siteNav) return;
  navToggle.setAttribute('aria-expanded', String(open));
  siteNav.classList.toggle('open', open);

  if (open) {
    siteNav.removeAttribute('hidden');
    siteNav.removeAttribute('inert');
  } else {
    siteNav.setAttribute('hidden', '');
    siteNav.setAttribute('inert', '');
  }
}

function initOrResize() {
  if (!navToggle || !siteNav) return;
  setNav(!mqMobile.matches ? true : false);
}

if (navToggle && siteNav) {
  navToggle.addEventListener('click', () => {
    const open = navToggle.getAttribute('aria-expanded') === 'true';
    setNav(!open);
  });

  mqMobile.addEventListener('change', initOrResize);
  window.addEventListener('DOMContentLoaded', initOrResize);
}


const links = Array.from(document.querySelectorAll('a.nav-link'));
const views = document.querySelectorAll('.view');

function setAriaCurrentFor(id) {
  links.forEach(a => a.removeAttribute('aria-current'));

  const active = links.find(a => a.getAttribute('href') === `#${id}`);
  if (active) active.setAttribute('aria-current', 'page');
}

function showView(idFromHash) {
  const id = (idFromHash || 'dashboard').replace('#', '');

  views.forEach(v => v.id === id ? v.removeAttribute('hidden') : v.setAttribute('hidden', ''));

  setAriaCurrentFor(id);

  const main = document.getElementById('main');
  if (main) main.focus({ preventScroll: true });
}

window.addEventListener('hashchange', () => showView(location.hash));
window.addEventListener('DOMContentLoaded', () => {
  if (!location.hash) location.hash = '#dashboard';
  showView(location.hash);
});

function wireAddRow(formId, tbodyId, builder) {
  const form = document.getElementById(formId);
  const tbody = document.getElementById(tbodyId);
  if (!form || !tbody) return;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(form).entries());
    const tr = builder(data);
    tbody.appendChild(tr);
    form.reset();
    announce('Added!');
  });
}
function td(text) { const el = document.createElement('td'); el.textContent = text; return el; }
function th(text) { const el = document.createElement('th'); el.scope = 'row'; el.textContent = text; return el; }
function badge(text, cls) {
  const span = document.createElement('span');
  span.className = `badge ${cls || ''}`.trim();
  span.textContent = text;
  const cell = document.createElement('td'); cell.appendChild(span);
  return cell;
}
function announce(msg) {
  const lr = document.getElementById('live-region');
  if (lr) {
    lr.textContent = msg;
    setTimeout(() => lr.textContent = '', 1000);
  }
}

wireAddRow('app-form', 'apps-body', (d) => {
  const tr = document.createElement('tr');
  tr.append(
    th(d.company || '—'),
    td(d.role || '—'),
    td(d.date || '—'),
    badge(d.status || 'Submitted', (d.status || '').toLowerCase().replace(/\s+/g, ''))
  );
  return tr;
});

wireAddRow('oa-form', 'oas-body', (d) => {
  const tr = document.createElement('tr');
  tr.append(
    th(d.company || '—'),
    td(d.received || '—'),
    badge(d.status || 'Pending', (d.status || '').toLowerCase().split('–')[0].trim())
  );
  return tr;
});

wireAddRow('int-form', 'ints-body', (d) => {
  const tr = document.createElement('tr');
  tr.append(
    th(d.company || '—'),
    td(d.stage || '—'),
    td(d.date || '—'),
    badge(d.result || 'Pending', (d.result || '').toLowerCase())
  );
  return tr;
});

const filterBtn = document.getElementById('btn-filter');
if (filterBtn) {
  filterBtn.addEventListener('click', () => {
    const q = (document.getElementById('filter-text').value || '').toLowerCase();
    const diff = (document.getElementById('filter-difficulty').value || '').toLowerCase();
    document.querySelectorAll('#problems-body tr').forEach((tr) => {
      const name = tr.querySelector('th')?.textContent.toLowerCase() || '';
      const d = tr.querySelector('.badge')?.textContent.toLowerCase() || '';
      const match = (!q || name.includes(q)) && (!diff || d.includes(diff));
      tr.style.display = match ? '' : 'none';
    });
    announce('Filter applied');
  });
}


// handle user signups
const signupForm = document.getElementById('signup-form');
const signupError = document.getElementById('signup-error');

if (signupForm) {
signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(signupForm);
    const res = await fetch('controller.php?action=signup', { method: 'POST', body: formData });
    const json = await res.json();

    if (json.success) {
    announce('Signup successful!');
    // console.log('Before hash change:', location.hash);
    location.hash = '#dashboard';
    // console.log('After hash change:', location.hash);
    } else {
    signupError.textContent = json.message || 'Signup failed.';
    signupError.style.display = 'block';
    }
});
}


// handl user logins
const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');

if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    // dont do normal form behavior (redirect with fields in url)
    e.preventDefault();

    const formData = new FormData(loginForm);
    const res = await fetch('controller.php?action=login', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();

    if (json.success) {
      announce('Login successful!');
      location.hash = '#dashboard';  // redrect
      loginError.style.display = 'none';
    } else {
      loginError.textContent = json.message || 'Login failed.';
      loginError.style.display = 'block';
    }
  });
}

