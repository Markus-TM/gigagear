<?php include("../includes/head.php"); ?>

<body>
    <?php include("../includes/navbar.php"); ?>
<body>
    <div class="container mt-4">
        <h1>Kundenübersicht</h1>
        <table class="table" id="customer-table">
            <thead>
                <tr>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Email</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="customer-list">
                <!-- Kunden werden hier dynamisch geladen -->
            </tbody>
        </table>

        <!-- Modal für Bestellübersicht -->
        <div class="modal" id="order-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bestellungen des Kunden</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="order-details">
                            <!-- Bestellinformationen werden hier geladen -->
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="deactivate-customer-btn">Kunde deaktivieren</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/customer_edit.js"></script>
</body>
</html>
