<?php include("../includes/head.php"); // Head inkl. Meta, CSS, JS ?>

<body>
    <?php include("../includes/navbar.php"); // Navigation ?>

    <!-- Kontoübersicht -->
    <div class="container mt-5" style="max-width: 500px;">
        <h2>Mein Konto</h2>

        <!-- Anzeige der aktuellen Benutzerdaten (readonly) -->
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

            <!-- Button zum Umschalten auf Bearbeitungsmodus -->
            <button id="editBtn" class="btn btn-primary">Bearbeiten</button>
        </div>
    </div>

    <!-- Formular zur Bearbeitung der persönlichen Daten -->
    <div class="container mt-5" style="max-width: 1000px;">
        <form id="editForm" class="mt-4 d-none">
            <!-- Persönliche Datenfelder -->
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
            <!-- Passwort zur Bestätigung -->
            <div class="mb-3">
                <label class="form-label">Passwort (zur Bestätigung)</label>
                <input type="password" id="passwordConfirm" name="password" class="form-control" required>
            </div>

            <!-- Formular-Buttons -->
            <button type="submit" class="btn btn-success">Speichern</button>
            <button type="button" id="cancelBtn" class="btn btn-secondary">Abbrechen</button>
        </form>

        <!-- Tabelle der bisherigen Bestellungen -->
        <h3 class="mt-5">Meine Bestellungen</h3>
        <table class="table table-striped mt-3" id="order-table">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th>Rechnungsnummer</th>
                    <th>Bestelldatum</th>
                    <th>Gesamtpreis</th>
                    <th>Aktionen</th> <!-- z.B. Rechnung drucken -->
                </tr>
            </thead>
            <tbody id="order-list">
                <!-- Dynamisch per JS befüllt -->
            </tbody>
        </table>
    </div>

    <!-- jsPDF für Rechnungen, eigene JS-Dateien für Konto- und Bestellverwaltung -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="../js/account.js"></script>
    <script src="../js/account_orders.js"></script>
</body>