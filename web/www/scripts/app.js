console.log('app.js loaded');

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.getElementById('site-nav');
const mqMobile = window.matchMedia('(max-width: 680px)');

// JS object used to track dashboard summary counts
const dashboardStats = {
  applications: 0,
  oas: 0,
  interviews: 0
};

// Anonymous IIFE returning small validation helper module
const validationHelpers = (function () {
  function setError(elementOrId, message) {
    const el = typeof elementOrId === 'string'
      ? document.getElementById(elementOrId)
      : elementOrId;
    if (!el) return;
    el.textContent = message || '';
    el.style.display = message ? 'block' : 'none';
  }

  function clearError(elementOrId) {
    setError(elementOrId, '');
  }

  return {
    setError,
    clearError
  };
})();

// nav + layout helpers
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
  setNav(!mqMobile.matches);
}

if (navToggle && siteNav) {
  navToggle.addEventListener('click', () =>
    setNav(!(navToggle.getAttribute('aria-expanded') === 'true'))
  );
  mqMobile.addEventListener('change', initOrResize);
  window.addEventListener('DOMContentLoaded', initOrResize);
}

// Dashboard "focus mode" – toggles layout & card styling
const focusToggle = document.getElementById('toggle-focus');
if (focusToggle) {
  focusToggle.addEventListener('click', () => {
    const isFocus = document.body.classList.toggle('focus-mode');
    focusToggle.textContent = isFocus ? 'Exit focus mode' : 'Toggle focus mode';
    announce(isFocus ? 'Focus mode enabled' : 'Focus mode disabled');
  });
}

// Utility announcement for screen readers & subtle feedback
function announce(msg) {
  const lr = document.getElementById('live-region');
  if (lr) {
    lr.textContent = msg;
    setTimeout(() => {
      lr.textContent = '';
    }, 1000);
  }
}

function td(text) {
  const el = document.createElement('td');
  el.textContent = text;
  return el;
}
function th(text) {
  const el = document.createElement('th');
  el.scope = 'row';
  el.textContent = text;
  return el;
}

// Generic fetch-based helper for most API calls
async function api(action, opts = {}) {
  const method = opts.method || 'GET';
  const url = `controller.php?action=${encodeURIComponent(action)}`;
  const res = await fetch(url, {
    method,
    body: opts.body || undefined,
    credentials: 'same-origin'
  });
  return res.json();
}

function getCsrf(selector) {
  return document.querySelector(selector)?.value || '';
}

// Update dashboard cards from dashboardStats object
function updateDashboardCounts() {
  const appsEl = document.getElementById('stat-apps');
  const intsEl = document.getElementById('stat-interviews');
  if (appsEl) appsEl.textContent = dashboardStats.applications;
  if (intsEl) intsEl.textContent = dashboardStats.interviews;
}

// ----- Applications -----

const appForm = document.getElementById('app-form');
const appsBody = document.getElementById('apps-body');

function setAppRowStyles(tr, status) {
  if (!tr) return;
  tr.classList.toggle('row-offer', status === 'Offer');
  tr.classList.toggle('row-rejected', status === 'Rejected');
}

function renderAppRow(row) {
  const tr = document.createElement('tr');
  tr.dataset.id = row.id;

  const statusSelect = document.createElement('select');
  ['Submitted', 'In Review', 'Interview', 'Offer', 'Rejected'].forEach(s => {
    const opt = document.createElement('option');
    opt.value = s;
    opt.textContent = s;
    if (row.status === s) opt.selected = true;
    statusSelect.appendChild(opt);
  });

  statusSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('status', statusSelect.value);
    fd.append('csrf', getCsrf('#app-form input[name="csrf"]'));
    const json = await api('updateApp', { method: 'POST', body: fd });
    if (!json.success) {
      alert(json.message || 'Update failed');
      return;
    }
    setAppRowStyles(tr, statusSelect.value);
    announce('Application updated');
  });

  const delBtn = document.createElement('button');
  delBtn.type = 'button';
  delBtn.textContent = 'Delete';
  delBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('csrf', getCsrf('#app-form input[name="csrf"]'));
    const json = await api('deleteApp', { method: 'POST', body: fd });
    if (json.success) {
      tr.remove();
      dashboardStats.applications = Math.max(0, dashboardStats.applications - 1);
      updateDashboardCounts();
      announce('Deleted application');
    } else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td');
  delCell.appendChild(delBtn);
  const statusCell = document.createElement('td');
  statusCell.appendChild(statusSelect);

  tr.append(
    th(row.company || '—'),
    td(row.role || '—'),
    td(row.date_applied || '—'),
    statusCell,
    delCell
  );

  setAppRowStyles(tr, row.status || 'Submitted');
  return tr;
}

/**
 * loadApps
 * ----------
 * Uses a classic XMLHttpRequest (AJAX) call to retrieve the user's
 * applications as JSON from controller.php and then updates the DOM.
 * This explicitly satisfies the sprint requirement to use
 * "jQuery or XMLHTTPRequest" for an asynchronous AJAX query.
 */
function loadApps() {
  if (!appsBody) return;

  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'controller.php?action=listApps', true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState !== 4) return;

    if (xhr.status === 200) {
      try {
        const json = JSON.parse(xhr.responseText);
        if (!json.success) return;

        appsBody.innerHTML = '';
        (json.rows || []).forEach(r => appsBody.appendChild(renderAppRow(r)));
        dashboardStats.applications = (json.rows || []).length;
        updateDashboardCounts();
      } catch (err) {
        console.error('Error parsing listApps JSON', err);
      }
    } else {
      console.error('listApps XHR failed with status', xhr.status);
    }
  };
  xhr.send();
}

if (appForm && appsBody) {
  appForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // client-side validation for application form
    if (!appForm.reportValidity()) return;
    const company = appForm.company.value.trim();
    const role = appForm.role.value.trim();
    if (!company || !role) {
      validationHelpers.setError('app-error', 'Company and role are required.');
      return;
    }
    validationHelpers.clearError('app-error');

    const fd = new FormData(appForm);
    const json = await api('addApp', { method: 'POST', body: fd });
    if (json.success) {
      appsBody.prepend(renderAppRow(json.row));
      appForm.reset();
      dashboardStats.applications += 1;
      updateDashboardCounts();
      announce('Application added');
    } else {
      validationHelpers.setError('app-error', json.message || 'Add failed');
    }
  });
}

// ----- OAs -----

const oaForm = document.getElementById('oa-form');
const oasBody = document.getElementById('oas-body');

function renderOARow(row) {
  const tr = document.createElement('tr');
  tr.dataset.id = row.id;

  const statusSelect = document.createElement('select');

  const statusOptions = [
    { value: 'Pending', label: 'Pending' },
    { value: 'Passed', label: 'Completed – Pass' },
    { value: 'Failed', label: 'Completed – Fail' }
  ];

  statusOptions.forEach(optData => {
    const opt = document.createElement('option');
    opt.value = optData.value;
    opt.textContent = optData.label;
    if (row.status === optData.value) opt.selected = true;
    statusSelect.appendChild(opt);
  });

  statusSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('status', statusSelect.value);
    fd.append('csrf', getCsrf('#oa-form input[name="csrf"]'));
    const json = await api('updateOA', { method: 'POST', body: fd });
    if (!json.success) {
      alert(json.message || 'Update failed');
      return;
    }
    announce('OA updated');
  });

  const delBtn = document.createElement('button');
  delBtn.type = 'button';
  delBtn.textContent = 'Delete';
  delBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('csrf', getCsrf('#oa-form input[name="csrf"]'));
    const json = await api('deleteOA', { method: 'POST', body: fd });
    if (json.success) {
      tr.remove();
      dashboardStats.oas = Math.max(0, dashboardStats.oas - 1);
      announce('Deleted OA');
    } else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td');
  delCell.appendChild(delBtn);
  const statusCell = document.createElement('td');
  statusCell.appendChild(statusSelect);

  tr.append(th(row.company || '—'), td(row.date_received || '—'), statusCell, delCell);
  return tr;
}

async function loadOAs() {
  if (!oasBody) return;
  const json = await api('listOAs');
  if (!json.success) return;
  oasBody.innerHTML = '';
  (json.rows || []).forEach(r => oasBody.appendChild(renderOARow(r)));
  dashboardStats.oas = (json.rows || []).length;
}

if (oaForm && oasBody) {
  oaForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!oaForm.reportValidity()) return;
    const company = oaForm.company.value.trim();
    if (!company) {
      validationHelpers.setError('oa-error', 'Company is required.');
      return;
    }
    validationHelpers.clearError('oa-error');

    const fd = new FormData(oaForm);
    const json = await api('addOA', { method: 'POST', body: fd });
    if (json.success) {
      oasBody.prepend(renderOARow(json.row));
      oaForm.reset();
      dashboardStats.oas += 1;
      announce('OA added');
    } else {
      validationHelpers.setError('oa-error', json.message || 'Add failed');
    }
  });
}

// ----- Interviews -----

const intForm = document.getElementById('int-form');
const intsBody = document.getElementById('ints-body');

function renderIntRow(row) {
  const tr = document.createElement('tr');
  tr.dataset.id = row.id;

  const resultSelect = document.createElement('select');
  ['Pending', 'Pass', 'Fail'].forEach(r => {
    const opt = document.createElement('option');
    opt.value = r;
    opt.textContent = r;
    if (row.result === r) opt.selected = true;
    resultSelect.appendChild(opt);
  });

  resultSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('result', resultSelect.value);
    fd.append('csrf', getCsrf('#int-form input[name="csrf"]'));
    const json = await api('updateInterview', { method: 'POST', body: fd });
    if (!json.success) {
      alert(json.message || 'Update failed');
      return;
    }
    announce('Interview updated');
  });

  const delBtn = document.createElement('button');
  delBtn.type = 'button';
  delBtn.textContent = 'Delete';
  delBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('csrf', getCsrf('#int-form input[name="csrf"]'));
    const json = await api('deleteInterview', { method: 'POST', body: fd });
    if (json.success) {
      tr.remove();
      dashboardStats.interviews = Math.max(0, dashboardStats.interviews - 1);
      updateDashboardCounts();
      announce('Deleted interview');
    } else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td');
  delCell.appendChild(delBtn);
  const resultCell = document.createElement('td');
  resultCell.appendChild(resultSelect);

  tr.append(
    th(row.company || '—'),
    td(row.stage || '—'),
    td(row.date || '—'),
    resultCell,
    delCell
  );
  return tr;
}

async function loadInterviews() {
  if (!intsBody) return;
  const json = await api('listInterviews');
  if (!json.success) return;
  intsBody.innerHTML = '';
  (json.rows || []).forEach(r => intsBody.appendChild(renderIntRow(r)));
  dashboardStats.interviews = (json.rows || []).length;
  updateDashboardCounts();
}

if (intForm && intsBody) {
  intForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!intForm.reportValidity()) return;
    const company = intForm.company.value.trim();
    if (!company) {
      validationHelpers.setError('int-error', 'Company is required.');
      return;
    }
    validationHelpers.clearError('int-error');

    const fd = new FormData(intForm);
    const json = await api('addInterview', { method: 'POST', body: fd });
    if (json.success) {
      intsBody.prepend(renderIntRow(json.row));
      intForm.reset();
      dashboardStats.interviews += 1;
      updateDashboardCounts();
      announce('Interview added');
    } else {
      validationHelpers.setError('int-error', json.message || 'Add failed');
    }
  });
}

// ----- Auth (signup / login) -----

const signupForm = document.getElementById('signup-form');
const signupError = document.getElementById('signup-error');
const signupPassword = document.getElementById('signup-password');
const passwordHint = document.getElementById('password-hint');

if (signupPassword && passwordHint) {
  signupPassword.addEventListener('input', () => {
    const val = signupPassword.value;
    let msg = '';
    if (val.length === 0) {
      msg = '';
    } else if (val.length < 8) {
      msg = 'Too short – aim for at least 8 characters.';
    } else if (!/[0-9]/.test(val) || !/[A-Za-z]/.test(val)) {
      msg = 'Add both letters and numbers for a stronger password.';
    } else {
      msg = 'Nice! This password meets the requirements.';
    }
    passwordHint.textContent = msg;
  });
}

if (signupForm) {
  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!signupForm.reportValidity()) return;

    const emailVal = signupForm.email.value.trim();
    if (!emailVal.endsWith('@virginia.edu')) {
      validationHelpers.setError('signup-error', 'Please sign up with your @virginia.edu email address.');
      signupForm.email.focus();
      return;
    }

    validationHelpers.clearError('signup-error');

    const formData = new FormData(signupForm);
    const json = await api('signup', { method: 'POST', body: formData });
    if (json.success) {
      announce('Signup successful!');
      location.hash = '#dashboard';
      if (signupError) {
        validationHelpers.clearError('signup-error');
      }
      loadApps();
      loadOAs();
      loadInterviews();
    } else {
      if (signupError) {
        validationHelpers.setError('signup-error', json.message || 'Signup failed.');
      } else alert(json.message || 'Signup failed.');
    }
  });
}

const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');

if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!loginForm.reportValidity()) return;

    const emailVal = loginForm.email.value.trim();
    const passwordVal = loginForm.password.value;

    if (!emailVal.endsWith('@virginia.edu')) {
      validationHelpers.setError('login-error', 'Please use your @virginia.edu email address.');
      loginForm.email.focus();
      return;
    }
    if (passwordVal.length < 8) {
      validationHelpers.setError('login-error', 'Password must be at least 8 characters.');
      loginForm.password.focus();
      return;
    }

    validationHelpers.clearError('login-error');

    const formData = new FormData(loginForm);
    const json = await api('login', { method: 'POST', body: formData });
    if (json.success) {
      announce('Login successful!');
      location.hash = '#dashboard';
      if (loginError) {
        validationHelpers.clearError('login-error');
      }
      loadApps();
      loadOAs();
      loadInterviews();
    } else {
      if (loginError) {
        validationHelpers.setError('login-error', json.message || 'Login failed.');
      } else alert(json.message || 'Login failed.');
    }
  });
}

// ----- Navigation between views -----

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
  document.getElementById('main')?.focus({ preventScroll: true });

  if (id === 'applications') loadApps();
  if (id === 'oas') loadOAs();
  if (id === 'interviews') loadInterviews();

  document.cookie = `lastTab=${encodeURIComponent(id)}; path=/; SameSite=Lax`;
}

window.addEventListener('hashchange', () => showView(location.hash));
window.addEventListener('DOMContentLoaded', () => {
  if (!location.hash) {
    const m = document.cookie.match(/(?:^|; )lastTab=([^;]+)/);
    if (m) location.hash = `#${decodeURIComponent(m[1])}`;
    else location.hash = '#dashboard';
  }
  showView(location.hash);
});