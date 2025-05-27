<?php include("../includes/head.php"); // Meta / CSS / JS-Header einbinden ?>

<body>
    <?php include("../includes/navbar.php"); // Navigationsleiste einbinden ?>

    <div class="container mt-4">
        <h1>Produkte</h1>

        <div class="filter-container">
            <!-- Kategorie-Filter (Dropdown) -->
            <div class="category-filter mb-3">
                <select id="category-select" class="form-select">
                    <option value="all">Alle Kategorien</option> <!-- Wird dynamisch ergänzt -->
                </select>
            </div>

            <!-- Produktsuche (Textfeld + Icon) -->
            <div class="search-filter mb-3">
                <div class="input-group">
                    <input type="text" id="product-search" class="form-control" placeholder="Produkte suchen...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>

        <!-- Produktanzeige-Container -->
        <div class="container">
            <div id="products" class="product-grid"></div> <!-- Grid wird via JS befüllt -->

            <!-- Warenkorb-Vorschau (Sticky Bereich unten) -->
            <div id="cart-preview" class="cart-drop-target mt-4">
                <h3>Warenkorb <span id="cart-count" class="badge bg-primary">0</span></h3>

                <!-- Artikelübersicht (Mini-Ansicht) -->
                <div id="cart-items-preview"></div>

                <!-- Gesamtpreis und Anzahl -->
                <div id="cart-total">
                    Gesamtpreis: <span id="cart-total-price">0.00</span> €
                    (<span id="cart-items-count">0</span> Artikel)
                </div>

                <!-- Button zur Warenkorb-Seite -->
                <a href="cart.php" class="btn btn-primary mt-2">Zum Warenkorb</a>
            </div>
        </div>

        <!-- Logik: Kategorie-Filter, Suche, Produkt-Rendering, Drag&Drop etc. -->
        <script src="../js/products.js"></script>
</body>

</html>