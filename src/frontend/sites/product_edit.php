<?php include("../includes/head.php"); // Meta / CSS / JS-Header einbinden?>

<body>
    <?php include("../includes/navbar.php"); // Navigation ?>

    <div class="container mt-4">

        <!-- Überschrift + Button für neues Produkt -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Produkte verwalten</h1>
            <button class="btn btn-success" id="add-product-btn">
                neues Produkt
            </button>
        </div>

        <!-- Produkttabelle -->
        <table class="table table-bordered align-middle text-center" id="product-table">
            <thead class="table-light">
                <tr>
                    <th>Bild</th>
                    <th>Name</th>
                    <th>Preis</th>
                    <th>Kategorie</th>
                    <th>Aktionen</th> <!-- Bearbeiten/Löschen -->
                </tr>
            </thead>
            <tbody id="product-list">
                <!-- Produktzeilen werden durch JS eingefügt -->
            </tbody>
        </table>

        <!-- Formular für Neues Produkt / Bearbeiten -->
        <div id="productFormContainer" class="mt-4 d-none">
            <h3 id="formTitle">Neues Produkt</h3>

            <form id="productForm">
                <!-- Hidden ID-Feld für Bearbeitungsmodus -->
                <input type="hidden" id="product-id" name="id" />

                <!-- Produktname -->
                <div class="mb-3">
                    <label for="name" class="form-label">Produktname</label>
                    <input type="text" class="form-control" id="name" name="name" required />
                </div>

                <!-- Beschreibung -->
                <div class="mb-3">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>

                <!-- Preis -->
                <div class="mb-3">
                    <label for="price" class="form-label">Preis</label>
                    <input type="number" class="form-control" id="price" name="price" min="1" step="0.01" required />
                </div>

                <!-- Kategorie -->
                <div class="mb-3">
                    <label for="category" class="form-label">Kategorie</label>
                    <input type="text" class="form-control" id="category" name="category" required />
                </div>

                <!-- Bild-URL -->
                <div class="mb-3">
                    <label for="image_path" class="form-label">Bild-URL</label>
                    <input type="text" class="form-control" id="image_path" name="image_path" required />
                </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-primary">Speichern</button>
                <button type="button" id="cancelProductFormBtn" class="btn btn-secondary">Abbrechen</button>
            </form>
        </div>
    </div>

    <!-- Logik für Produktliste, Bearbeiten, Löschen, Formularsteuerung -->
    <script src="../js/product_edit.js"></script>
</body>