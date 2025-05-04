<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Gigagear</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../sites/imprint.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../sites/products.php">Produkte</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../sites/cart.php">Warenkorb</a>
        </li>
        <li class="nav-item d-none" id="nav-account">
          <a class="nav-link active" aria-current="page" href="../sites/account.php">Mein Konto</a>
        </li>
        <!-- LOGIN/REGISTER Dropdown -->
        <li class="nav-item Anmeldung" id="nav-auth">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Konto
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../sites/login.php">Login</a></li>
            <li><a class="dropdown-item" href="../sites/register.php">Registrierung</a></li>
          </ul>
        </li>
      </ul>

      <!-- RIGHT: Username + Logout -->
      <ul class="navbar-nav ms-auto">
        <!-- Eingeloggt-Anzeige -->
        <!-- Eingeloggt-Anzeige -->
        <li class="nav-item d-none" id="nav-username">
          <span class="nav-link disabled">
            Eingeloggt als <strong id="username-display"></strong>
          </span>
        </li>

        <!-- LOGOUT (nur wenn eingeloggt) -->
        <li class="nav-item d-none" id="nav-logout">
          <a class="nav-link" href="#" id="logout-link">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    const username = localStorage.getItem('username');

    const navAuth = document.getElementById('nav-auth');
    const navLogout = document.getElementById('nav-logout');
    const navUsername = document.getElementById('nav-username');
    const navAccount = document.getElementById('nav-account');
    const usernameDisplay = document.getElementById('username-display');

    if (token) {
      // Hide login/register dropdown
      if (navAuth) navAuth.style.display = 'none';
      // Show logout + username
      if (navLogout) navLogout.classList.remove('d-none');
      if (navUsername) navUsername.classList.remove('d-none');
      if (navAccount) navAccount.classList.remove('d-none');
      if (usernameDisplay && username) usernameDisplay.textContent = username;
    } else {
      // Show login/register dropdown
      if (navAuth) navAuth.style.display = '';
      // Hide logout + username
      if (navLogout) navLogout.classList.add('d-none');
      if (navUsername) navUsername.classList.add('d-none');
      if (navAccount) navAccount.classList.add('d-none'); 
    }

    // Logout click
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
      logoutLink.addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('token');
        localStorage.removeItem('username');
        localStorage.removeItem('role');
        document.cookie = 'remember_token=; Max-Age=0; path=/;';
        location.reload(); // Reload page after logout
      });
    }
  });
</script>