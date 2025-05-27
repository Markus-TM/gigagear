<?php include("../includes/head.php"); // Meta / CSS / JS-Header einbinden ?>

<body>
    <?php include("../includes/navbar.php"); // Navigation einfügen ?>

    <div class="container text-center">
        <!-- Login-Formular -->
        <form id="loginForm" class="p-3 formularbox mx-auto" style="max-width: 350px;">
            <h1>Login</h1>

            <!-- Benutzername -->
            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="username" id="username" placeholder="Benutzername"
                    required>
                <label for="username">Benutzername</label>
            </div>

            <!-- Passwort -->
            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" id="password" placeholder="Passwort"
                    required>
                <label for="password">Passwort</label>
            </div>

            <!-- Checkbox „Login merken“ & Link zur Registrierung -->
            <div class="d-flex justify-content-between align-items-center my-2">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" value="" id="remember">
                    <label class="form-check-label" for="remember">Login merken</label>
                </div>
                <a href="register.php">Hier registrieren</a>
            </div>

            <!-- Absende-Button -->
            <button type="submit" class="btn btn-success" style="margin-top: 10px;">Einloggen</button>
        </form>
    </div>

    <!-- JS für Login-Prozess (API-Kommunikation) -->
    <script src="../js/login.js"></script>
</body>

</html>