<?php include("../includes/head.php"); ?>

<body>
    <?php
    include("../includes/navbar.php");
    ?>

    <div class="container text-center">
        <form id="form" class="p-3 formularbox mx-auto" style="max-width: 350px;">

            <div class="form-floating mb-2">
                <select class="form-select" name="salutation" id="salutation" required>
                    <option selected hidden>Wählen Sie eine Anrede</option>
                    <option value="Herr">Herr</option>
                    <option value="Frau">Frau</option>
                </select>
                <label for="salutation">Anrede</label>
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Vorname" required>
                <label for="firstname">Vorname</label>
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Nachname" required>
                <label for="lastname">Nachname</label>
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="address" id="address" placeholder="Adresse" required>
                <label for="address">Adresse</label>
            </div>

            <div class="form-floating mb-2">
                <input type="number" class="form-control" name="zipcode" id="zipcode" placeholder="PLZ" min="1010"
                    step="1" required>
                <label for="zipcode">PLZ</label>
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="city" id="city" placeholder="Ort" required>
                <label for="city">Ort</label>
            </div>

            <div class="form-floating mb-2">
                <input type="email" class="form-control" name="email" id="email" placeholder="E-Mail" required>
                <label for="email">E-Mail</label>
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="username" id="username" placeholder="Benutzername"
                    required>
                <label for="username">Benutzername</label>
            </div>

            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" id="password"
                    pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$" placeholder="Passwort" required>
                <label for="password">Passwort</label>
            </div>

            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="passwordconf" id="passwordconf"
                    pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$" placeholder="Passwort bestätigen" required>
                <label for="passwordconf">Passwort bestätigen</label>
            </div>

            <button type="submit" class="btn btn-success" style="margin-top: 10px;">Absenden</button>
        </form>
    </div>

    <script src="../js/register.js"></script>
</body>

</html>