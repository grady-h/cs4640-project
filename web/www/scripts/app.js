console.log('app.js loaded');

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.getElementById('site-nav');
const mqMobile = window.matchMedia('(max-width: 680px)');

function setNav(open) {
  if (!navToggle || !siteNav) return;
  navToggle.setAttribute('aria-expanded', String(open));
  siteNav.classList.toggle('open', open);
  if (open) {
    siteNav.removeAttribute('hidden'); siteNav.removeAttribute('inert');
  } else {
    siteNav.setAttribute('hidden',''); siteNav.setAttribute('inert','');
  }
}
function initOrResize() { if (!navToggle || !siteNav) return; setNav(!mqMobile.matches); }
if (navToggle && siteNav) {
  navToggle.addEventListener('click', () => setNav(!(navToggle.getAttribute('aria-expanded') === 'true')));
  mqMobile.addEventListener('change', initOrResize);
  window.addEventListener('DOMContentLoaded', initOrResize);
}

function announce(msg) {
  const lr = document.getElementById('live-region');
  if (lr) { lr.textContent = msg; setTimeout(() => lr.textContent = '', 1000); }
}
function td(text){ const el=document.createElement('td'); el.textContent=text; return el; }
function th(text){ const el=document.createElement('th'); el.scope='row'; el.textContent=text; return el; }

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

const appForm = document.getElementById('app-form');
const appsBody = document.getElementById('apps-body');

function renderAppRow(row) {
  const tr = document.createElement('tr');
  tr.dataset.id = row.id;

  const statusSelect = document.createElement('select');
  ['Submitted','In Review','Interview','Offer','Rejected'].forEach(s => {
    const opt = document.createElement('option');
    opt.value = s; opt.textContent = s;
    if (row.status === s) opt.selected = true;
    statusSelect.appendChild(opt);
  });
  statusSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('status', statusSelect.value);
    fd.append('csrf', getCsrf('#app-form input[name="csrf"]'));
    const json = await api('updateApp', { method: 'POST', body: fd });
    if (!json.success) { alert(json.message || 'Update failed'); return; }
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
    if (json.success) { tr.remove(); announce('Deleted application'); }
    else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td'); delCell.appendChild(delBtn);
  const statusCell = document.createElement('td'); statusCell.appendChild(statusSelect);

  tr.append(
    th(row.company || '—'),
    td(row.role || '—'),
    td(row.date_applied || '—'),
    statusCell,
    delCell
  );
  return tr;
}

async function loadApps() {
  if (!appsBody) return;
  const json = await api('listApps');
  if (!json.success) return;
  appsBody.innerHTML = '';
  (json.rows || []).forEach(r => appsBody.appendChild(renderAppRow(r)));
}

if (appForm && appsBody) {
  appForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(appForm);
    const json = await api('addApp', { method: 'POST', body: fd });
    if (json.success) {
      appsBody.prepend(renderAppRow(json.row));
      appForm.reset();
      announce('Application added');
    } else alert(json.message || 'Add failed');
  });
}

const oaForm = document.getElementById('oa-form');
const oasBody = document.getElementById('oas-body');

function renderOARow(row) {
  const tr = document.createElement('tr'); tr.dataset.id = row.id;

  const statusSelect = document.createElement('select');
  ['Pending','Completed – Pass','Completed – Fail'].forEach(s => {
    const opt = document.createElement('option');
    opt.value = s; opt.textContent = s;
    const display = (row.status === 'Passed' && s.includes('Pass')) ||
                    (row.status === 'Failed' && s.includes('Fail')) ||
                    (row.status === 'Pending' && s === 'Pending');
    if (display) opt.selected = true;
    statusSelect.appendChild(opt);
  });
  statusSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('status', statusSelect.value);
    fd.append('csrf', getCsrf('#oa-form input[name="csrf"]'));
    const json = await api('updateOA', { method: 'POST', body: fd });
    if (!json.success) { alert(json.message || 'Update failed'); return; }
    announce('OA updated');
  });

  const delBtn = document.createElement('button');
  delBtn.type = 'button'; delBtn.textContent = 'Delete';
  delBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('csrf', getCsrf('#oa-form input[name="csrf"]'));
    const json = await api('deleteOA', { method: 'POST', body: fd });
    if (json.success) { tr.remove(); announce('Deleted OA'); }
    else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td'); delCell.appendChild(delBtn);
  const statusCell = document.createElement('td'); statusCell.appendChild(statusSelect);

  tr.append(th(row.company||'—'), td(row.date_received||'—'), statusCell, delCell);
  return tr;
}

async function loadOAs() {
  if (!oasBody) return;
  const json = await api('listOAs');
  if (!json.success) return;
  oasBody.innerHTML = '';
  (json.rows || []).forEach(r => oasBody.appendChild(renderOARow(r)));
}

if (oaForm && oasBody) {
  oaForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(oaForm);
    const json = await api('addOA', { method: 'POST', body: fd });
    if (json.success) {
      oasBody.prepend(renderOARow(json.row));
      oaForm.reset();
      announce('OA added');
    } else alert(json.message || 'Add failed');
  });
}

const intForm = document.getElementById('int-form');
const intsBody = document.getElementById('ints-body');

function renderIntRow(row) {
  const tr = document.createElement('tr'); tr.dataset.id = row.id;

  const resultSelect = document.createElement('select');
  ['Pending','Pass','Fail'].forEach(r => {
    const opt = document.createElement('option');
    opt.value = r; opt.textContent = r;
    if (row.result === r) opt.selected = true;
    resultSelect.appendChild(opt);
  });
  resultSelect.addEventListener('change', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('result', resultSelect.value);
    fd.append('csrf', getCsrf('#int-form input[name="csrf"]'));
    const json = await api('updateInterview', { method: 'POST', body: fd });
    if (!json.success) { alert(json.message || 'Update failed'); return; }
    announce('Interview updated');
  });

  const delBtn = document.createElement('button');
  delBtn.type = 'button'; delBtn.textContent = 'Delete';
  delBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('id', row.id);
    fd.append('csrf', getCsrf('#int-form input[name="csrf"]'));
    const json = await api('deleteInterview', { method: 'POST', body: fd });
    if (json.success) { tr.remove(); announce('Deleted interview'); }
    else alert(json.message || 'Delete failed');
  });

  const delCell = document.createElement('td'); delCell.appendChild(delBtn);
  const resultCell = document.createElement('td'); resultCell.appendChild(resultSelect);

  tr.append(
    th(row.company||'—'),
    td(row.stage||'—'),
    td(row.date||'—'),
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
}

if (intForm && intsBody) {
  intForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(intForm);
    const json = await api('addInterview', { method: 'POST', body: fd });
    if (json.success) {
      intsBody.prepend(renderIntRow(json.row));
      intForm.reset();
      announce('Interview added');
    } else alert(json.message || 'Add failed');
  });
}

const signupForm = document.getElementById('signup-form');
const signupError = document.getElementById('signup-error');
if (signupForm) {
  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(signupForm);
    const json = await api('signup', { method: 'POST', body: formData });
    if (json.success) {
      announce('Signup successful!');
      location.hash = '#dashboard';
      if (signupError) signupError.style.display = 'none';
      loadApps(); loadOAs(); loadInterviews();
    } else {
      if (signupError) {
        signupError.textContent = json.message || 'Signup failed.';
        signupError.style.display = 'block';
      } else alert(json.message || 'Signup failed.');
    }
  });
}
const loginForm = document.getElementById('login-form');
const loginError = document.getElementById('login-error');
if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(loginForm);
    const json = await api('login', { method: 'POST', body: formData });
    if (json.success) {
      announce('Login successful!');
      location.hash = '#dashboard';
      if (loginError) loginError.style.display = 'none';
      loadApps(); loadOAs(); loadInterviews();
    } else {
      if (loginError) {
        loginError.textContent = json.message || 'Login failed.';
        loginError.style.display = 'block';
      } else alert(json.message || 'Login failed.');
    }
  });
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
