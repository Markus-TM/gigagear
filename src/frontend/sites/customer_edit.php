<?php include("../includes/head.php"); // Standard-Header einbinden ?>

<body>
    <?php include("../includes/navbar.php"); // Navigationsleiste ?>

    <div class="container mt-4">
        <h1>Kundenübersicht</h1>

        <!-- Kundenliste (wird durch JS gefüllt) -->
        <table class="table" id="customer-table">
            <thead>
                <tr>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Email</th>
                    <th>Bestellungen</th> <!-- Button: Details anzeigen -->
                    <th>Status</th> <!-- Button: Aktivieren / Deaktivieren -->
                </tr>
            </thead>
            <tbody id="customer-list">
                <!-- Dynamischer JS-Content -->
            </tbody>
        </table>

        <!-- Bestellübersicht für ausgewählten Kunden -->
        <div id="customer-orders-section" class="d-none mt-5">
            <h3>Bestellungen von <span id="customer-name-title"></span></h3>

            <!-- Tabelle mit Kundenbestellungen -->
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nr.</th>
                        <th>Rechnungsnummer</th>
                        <th>Datum</th>
                        <th>Gesamtpreis</th>
                        <th>Details</th> <!-- Toggle: Details ein-/ausblenden -->
                        <th>Aktion</th> <!-- Bearbeiten / Löschen -->
                    </tr>
                </thead>
                <tbody id="customer-order-list">
                    <!-- JS füllt die Bestellungen -->
                </tbody>
            </table>

            <!-- Zurück zur Kundenliste -->
            <button class="btn btn-secondary" id="back-to-customers">Zurück zur Übersicht</button>
        </div>
    </div>

    <!-- Formular zur Bearbeitung einer Bestellung (Datum, Rechnungsnr.) -->
    <div id="orderEditFormContainer" class="mt-4 d-none d-flex justify-content-center">
        <div>
            <h4 id="orderEditTitle">Bestellung bearbeiten</h4>
            <form id="orderEditForm" style="max-width: 600px;">
                <!-- Hidden ID für die zu bearbeitende Bestellung -->
                <input type="hidden" id="edit-order-id" />

                <!-- Rechnungsnummer -->
                <div class="mb-3">
                    <label class="form-label">Rechnungsnummer</label>
                    <input type="text" class="form-control" id="edit-invoice-number" required>
                </div>

                <!-- Datum -->
                <div class="mb-3">
                    <label class="form-label">Bestelldatum</label>
                    <input type="datetime-local" class="form-control" id="edit-order-date" required>
                </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-primary">Speichern</button>
                <button type="button" class="btn btn-secondary" id="cancelEditOrderBtn">Abbrechen</button>
            </form>
        </div>
    </div>

    <!-- JS für Kundendaten, Statusumschaltung, Bestelldetails, Formulare -->
    <script src="../js/customer_edit.js"></script>
</body>

</html>