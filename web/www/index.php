<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PLWeb – SWE Job Search + Interview Prep Tracker</title>
  <meta name="description" content="Track coding prep, applications, OAs, and interviews in one place.">
  <link rel="stylesheet" href="styles/style.css">
  <script defer src="scripts/app.js?v=2"></script>
</head>
<body>
  <a class="skip-link" href="#main">Skip to main content</a>

  <header class="site-header">
    <div class="brand">
      <span class="logo" aria-hidden="true">💼</span>
      <p class="tagline">SWE Job Search + Interview Prep Tracker</p>
    </div>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
      <span class="sr-only">Toggle navigation</span> ☰
    </button>

    <nav id="site-nav" class="site-nav" aria-label="Primary">
      <ul>
        <li><a class="nav-link" href="#dashboard" data-view="dashboard" aria-current="page">Dashboard</a></li>
        <li><a class="nav-link" href="#leetcode" data-view="leetcode">LeetCode Tracker</a></li>
        <li><a class="nav-link" href="#applications" data-view="applications">Applications</a></li>
        <li><a class="nav-link" href="#oas" data-view="oas">OA Tracker</a></li>
        <li><a class="nav-link" href="#interviews" data-view="interviews">Interviews</a></li>
        <li class="auth-links"><a class="nav-link" href="#login" data-view="login">Login</a></li>
        <li class="auth-links"><a class="nav-link" href="#signup" data-view="signup">Sign up</a></li>
      </ul>
    </nav>
  </header>

  <main id="main" tabindex="0">
    <h1 class="sr-only">PLWeb — SWE Job Search + Interview Prep Tracker</h1>

    <section id="dashboard" class="view" aria-labelledby="h-dashboard">
      <header class="view-header">
        <h2 id="h-dashboard">Dashboard</h2>
        <p class="view-desc">At-a-glance prep and job hunt metrics (dummy data for Sprint 2).</p>
      </header>

      <div class="cards">
        <article class="card" aria-labelledby="c1-h">
          <h3 id="c1-h">Problems solved</h3>
          <p class="stat"><strong aria-live="polite" id="stat-problems">32</strong></p>
          <p class="muted">By difficulty: 14 Easy · 14 Medium · 4 Hard</p>
        </article>
        <article class="card" aria-labelledby="c2-h">
          <h3 id="c2-h">Applications submitted</h3>
          <p class="stat"><strong aria-live="polite" id="stat-apps">18</strong></p>
          <p class="muted">5 in progress · 9 rejections · 4 pending</p>
        </article>
        <article class="card" aria-labelledby="c3-h">
          <h3 id="c3-h">Interviews completed</h3>
          <p class="stat"><strong aria-live="polite" id="stat-interviews">7</strong></p>
          <p class="muted">3 onsites · 4 phone screens</p>
        </article>
        <article class="card" aria-labelledby="c4-h">
          <h3 id="c4-h">Offers</h3>
          <p class="stat"><strong aria-live="polite" id="stat-offers">1</strong></p>
          <p class="muted">Congrats! 🎉 (dummy)</p>
        </article>
      </div>

      <section aria-labelledby="h-trends" class="panel">
        <h3 id="h-trends">Recent activity</h3>
        <ul class="timeline">
          <li><span class="when">Yesterday</span> Finished “Binary Search Trees” (Medium)</li>
          <li><span class="when">Oct 7</span> Submitted application to Databricks – New Grad SWE</li>
          <li><span class="when">Oct 5</span> OA completed for Parafin (dummy)</li>
        </ul>
      </section>
    </section>

    <section id="leetcode" class="view" aria-labelledby="h-leetcode">
      <header class="view-header">
        <h2 id="h-leetcode">LeetCode / Prep Tracker</h2>
        <p class="view-desc">Track Blind 75 / NeetCode progress; mark status and add notes.</p>
      </header>

      <form class="toolbar" aria-label="Problem filters">
        <label for="filter-text">Search</label>
        <input id="filter-text" name="filter-text" type="search" placeholder="e.g., Two Sum">
        <label for="filter-difficulty">Difficulty</label>
        <select id="filter-difficulty" name="filter-difficulty">
          <option value="">All</option>
          <option>Easy</option>
          <option>Medium</option>
          <option>Hard</option>
        </select>
        <button type="button" id="btn-filter">Apply</button>
      </form>

      <table class="table" aria-describedby="p-help">
        <caption>Practice problems (sample)</caption>
        <thead>
          <tr>
            <th scope="col">Problem</th>
            <th scope="col">Topic</th>
            <th scope="col">Difficulty</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="problems-body">
          <tr>
            <th scope="row">Two Sum</th>
            <td>Arrays · HashMap</td>
            <td><span class="badge easy">Easy</span></td>
            <td>
              <fieldset>
                <legend class="sr-only">Status</legend>
                <label><input type="radio" name="p1" value="unsolved" checked> Unsolved</label>
                <label><input type="radio" name="p1" value="solved"> Solved</label>
                <label><input type="radio" name="p1" value="review"> Review</label>
              </fieldset>
              <details>
                <summary>Notes</summary>
                <textarea rows="3" aria-label="Notes for Two Sum" placeholder="Write approach or pitfalls…"></textarea>
              </details>
            </td>
          </tr>
          <tr>
            <th scope="row">Binary Search Tree Iterator</th>
            <td>Trees · Stacks</td>
            <td><span class="badge medium">Medium</span></td>
            <td>
              <fieldset>
                <legend class="sr-only">Status</legend>
                <label><input type="radio" name="p2" value="unsolved"> Unsolved</label>
                <label><input type="radio" name="p2" value="solved" checked> Solved</label>
                <label><input type="radio" name="p2" value="review"> Review</label>
              </fieldset>
              <details>
                <summary>Notes</summary>
                <textarea rows="3" aria-label="Notes for BST Iterator" placeholder="Time/space, variants…"></textarea>
              </details>
            </td>
          </tr>
        </tbody>
      </table>
      <p id="p-help" class="muted">Use search/difficulty filters. Status changes are local only in Sprint 2.</p>
    </section>

    <section id="applications" class="view" aria-labelledby="h-apps">
      <header class="view-header">
        <h2 id="h-apps">Application Tracker</h2>
        <p class="view-desc">Log companies, roles, dates, and status. (No backend yet.)</p>
      </header>

      <form class="grid-form" id="app-form" aria-labelledby="h-add-app">
        <h3 id="h-add-app">Add application</h3>

        <label for="app-company">Company <span aria-hidden="true">*</span></label>
        <input id="app-company" name="company" required maxlength="80" placeholder="e.g., Databricks">

        <label for="app-role">Role <span aria-hidden="true">*</span></label>
        <input id="app-role" name="role" required maxlength="80" placeholder="e.g., New Grad SWE">

        <label for="app-date">Date applied</label>
        <input id="app-date" name="date" type="date">

        <label for="app-status">Status</label>
        <select id="app-status" name="status">
          <option>Submitted</option>
          <option>In Review</option>
          <option>Interview</option>
          <option>Offer</option>
          <option>Rejected</option>
        </select>

        <button type="submit">Add</button>
        <p class="muted">Required fields are marked *</p>
      </form>

      <table class="table">
        <caption>Applications (sample)</caption>
        <thead>
          <tr>
            <th scope="col">Company</th>
            <th scope="col">Role</th>
            <th scope="col">Applied</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="apps-body">
          <tr>
            <th scope="row">Parafin</th>
            <td>Software Engineer</td>
            <td>2025-10-05</td>
            <td><span class="badge inprogress">Interview</span></td>
          </tr>
          <tr>
            <th scope="row">Databricks</th>
            <td>New Grad SWE</td>
            <td>2025-10-07</td>
            <td><span class="badge submitted">Submitted</span></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section id="oas" class="view" aria-labelledby="h-oas">
      <header class="view-header">
        <h2 id="h-oas">Online Assessment (OA) Tracker</h2>
        <p class="view-desc">Record OA invitations and outcomes.</p>
      </header>

      <form class="grid-form" id="oa-form" aria-labelledby="h-add-oa">
        <h3 id="h-add-oa">Add OA</h3>

        <label for="oa-company">Company <span aria-hidden="true">*</span></label>
        <input id="oa-company" name="company" required placeholder="e.g., Parafin">

        <label for="oa-received">Received on</label>
        <input id="oa-received" name="received" type="date">

        <label for="oa-status">Status</label>
        <select id="oa-status" name="status">
          <option>Pending</option>
          <option>Completed – Pass</option>
          <option>Completed – Fail</option>
        </select>

        <button type="submit">Add</button>
      </form>

      <table class="table">
        <caption>OA list (sample)</caption>
        <thead>
          <tr>
            <th scope="col">Company</th>
            <th scope="col">Received</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="oas-body">
          <tr>
            <th scope="row">Parafin</th>
            <td>2025-10-03</td>
            <td><span class="badge pass">Completed – Pass</span></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section id="interviews" class="view" aria-labelledby="h-interviews">
      <header class="view-header">
        <h2 id="h-interviews">Interview Tracker</h2>
        <p class="view-desc">Track stages across companies.</p>
      </header>

      <form class="grid-form" id="int-form" aria-labelledby="h-add-int">
        <h3 id="h-add-int">Add interview</h3>

        <label for="int-company">Company <span aria-hidden="true">*</span></label>
        <input id="int-company" name="company" required placeholder="e.g., Databricks">

        <label for="int-stage">Stage</label>
        <select id="int-stage" name="stage">
          <option>Phone Screen</option>
          <option>Technical Round</option>
          <option>Onsite</option>
        </select>

        <label for="int-date">Date</label>
        <input id="int-date" name="date" type="date">

        <label for="int-result">Result</label>
        <select id="int-result" name="result">
          <option>Pending</option>
          <option>Pass</option>
          <option>Fail</option>
        </select>

        <button type="submit">Add</button>
      </form>

      <table class="table">
        <caption>Interviews (sample)</caption>
        <thead>
          <tr>
            <th scope="col">Company</th>
            <th scope="col">Stage</th>
            <th scope="col">Date</th>
            <th scope="col">Result</th>
          </tr>
        </thead>
        <tbody id="ints-body">
          <tr>
            <th scope="row">Parafin</th>
            <td>Onsite</td>
            <td>2025-10-08</td>
            <td><span class="badge pass">Pass</span></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section id="login" class="view auth" aria-labelledby="h-login">
      <header class="view-header">
        <h2 id="h-login">Log in</h2>
        <p class="view-desc">Validate email format and minimum password length (client-side only).</p>
      </header>

      <form class="auth-form">
        <div class="form-field">
          <label for="login-email">Email</label>
          <input id="login-email" name="email" type="email" autocomplete="email" required
                 placeholder="you@virginia.edu">
        </div>
        <div class="form-field">
          <label for="login-password">Password</label>
          <input id="login-password" name="password" type="password" minlength="8" required
                 placeholder="At least 8 characters">
        </div>
        <button type="submit">Log in</button>
        <p class="muted">For Sprint 2 this is a static mockup.</p>
      </form>
    </section>

    <section id="signup" class="view auth" aria-labelledby="h-signup">
      <header class="view-header">
        <h2 id="h-signup">Sign up</h2>
        <p class="view-desc">Front-end validation; pattern enforces a strong password example.</p>
      </header>

      <form class="auth-form">
        <div class="form-field">
          <label for="signup-name">Name</label>
          <input id="signup-name" name="name" autocomplete="name" required placeholder="First Last">
        </div>

        <div class="form-field">
          <label for="signup-email">Email</label>
          <input id="signup-email" name="email" type="email" autocomplete="email" required
                 placeholder="you@virginia.edu">
        </div>

        <div class="form-field">
          <label for="signup-password">Password</label>
          <input id="signup-password" name="password" type="password" required
                 pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()_\-=+]{8,}"
                 title="Min 8 chars, include letters & numbers"
                 placeholder="Min 8 chars, letters & numbers">
        </div>

        <button type="submit">Create account</button>
      </form>
    </section>
  </main>

  <footer class="site-footer">
    <p><strong>PLWeb</strong> · A CS 4640 project mockup for Sprint 2.</p>
    <p><a href="#login">Login</a> · <a href="#signup">Sign up</a> · <a href="#dashboard">Back to dashboard</a></p>
  </footer>

  <div aria-live="polite" aria-atomic="true" class="sr-only" id="live-region"></div>
</body>
</html>
