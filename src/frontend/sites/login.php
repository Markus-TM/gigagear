<?php include("../includes/head.php"); ?>

<body>
    <?php include("../includes/navbar.php"); ?>

    <div class="container text-center">
        <form id="loginForm" class="p-3 formularbox mx-auto" style="max-width: 350px;">
            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="username" id="username" placeholder="Benutzername" required>
                <label for="username">Benutzername</label>
            </div>

            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" id="password" placeholder="Passwort" required>
                <label for="password">Passwort</label>
            </div>

            <div class="form-check text-start my-2">
                <input class="form-check-input" type="checkbox" value="" id="remember">
                <label class="form-check-label" for="remember">
                    Login merken
                </label>
            </div>

            <button type="submit" class="btn btn-success" style="margin-top: 10px;">Einloggen</button>
        </form>
    </div>

    <script src="../js/login.js"></script>
</body>

</html>
