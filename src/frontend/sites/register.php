<?php include("../includes/head.php"); // Meta / CSS / JS-Header einbinden?> 

<body>
    <?php include("../includes/navbar.php"); // Navigationsleiste einbinden ?>

    <div class="container mt-4 text-center">
        <h1>Registrierung</h1>

        <div class="container text-center">
            <!-- Registrierungsformular -->
            <form id="form" class="p-3 formularbox mx-auto" style="max-width: 350px;">

                <!-- Anrede auswählen -->
                <div class="form-floating mb-2">
                    <select class="form-select" name="salutation" id="salutation" required>
                        <option selected hidden>Wählen Sie eine Anrede</option>
                        <option value="Herr">Herr</option>
                        <option value="Frau">Frau</option>
                    </select>
                    <label for="salutation">Anrede</label>
                </div>

                <!-- Vorname -->
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Vorname"
                        required>
                    <label for="firstname">Vorname</label>
                </div>

                <!-- Nachname -->
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Nachname"
                        required>
                    <label for="lastname">Nachname</label>
                </div>

                <!-- Adresse -->
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" name="address" id="address" placeholder="Adresse" required>
                    <label for="address">Adresse</label>
                </div>

                <!-- Postleitzahl -->
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" name="zipcode" id="zipcode" placeholder="PLZ" min="1010"
                        step="1" required>
                    <label for="zipcode">PLZ</label>
                </div>

                <!-- Stadt -->
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" name="city" id="city" placeholder="Ort" required>
                    <label for="city">Ort</label>
                </div>

                <!-- E-Mail-Adresse -->
                <div class="form-floating mb-2">
                    <input type="email" class="form-control" name="email" id="email" placeholder="E-Mail" required>
                    <label for="email">E-Mail</label>
                </div>

                <!-- Benutzername -->
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Benutzername"
                        required>
                    <label for="username">Benutzername</label>
                </div>

                <!-- Passwort (Mindestanforderungen im Pattern) -->
                <div class="form-floating mb-2">
                    <input type="password" class="form-control" name="password" id="password"
                        pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$" placeholder="Passwort" required>
                    <label for="password">Passwort</label>
                </div>

                <!-- Passwort bestätigen -->
                <div class="form-floating mb-2">
                    <input type="password" class="form-control" name="passwordconf" id="passwordconf"
                        pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$" placeholder="Passwort bestätigen"
                        required>
                    <label for="passwordconf">Passwort bestätigen</label>
                </div>

                <!-- Absende-Button -->
                <button type="submit" class="btn btn-success" style="margin-top: 10px;">Absenden</button>
            </form>
        </div>

        <!-- Einbindung der zugehörigen JS-Datei für das Verhalten beim Absenden -->
        <script src="../js/register.js"></script>
</body>

</html>