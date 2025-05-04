<?php include("../includes/head.php"); ?>

<body>
    <?php include("../includes/navbar.php"); ?>

    <div class="container mt-5" style="max-width: 500px;">
        <h2>Mein Konto</h2>
        <div id="accountInfo" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Benutzername</label>
                <input type="text" id="username" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" id="email" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" id="address" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">PLZ</label>
                <input type="text" id="zipcode" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Ort</label>
                <input type="text" id="city" class="form-control" disabled>
            </div>
            <button id="editBtn" class="btn btn-primary">Bearbeiten</button>
        </div>

        <form id="editForm" class="mt-4 d-none">
            <div class="mb-3">
                <label class="form-label">Vorname</label>
                <input type="text" id="firstnameEdit" name="firstname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nachname</label>
                <input type="text" id="lastnameEdit" name="lastname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" id="addressEdit" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">PLZ</label>
                <input type="text" id="zipcodeEdit" name="zipcode" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ort</label>
                <input type="text" id="cityEdit" name="city" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Passwort (zur Bestätigung)</label>
                <input type="password" id="passwordConfirm" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Speichern</button>
            <button type="button" id="cancelBtn" class="btn btn-secondary">Abbrechen</button>
        </form>
    </div>

    <script src="../js/account.js"></script>
</body>