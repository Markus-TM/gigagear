<?php include("../includes/head.php"); ?>

<body>
    <?php include("../includes/navbar.php"); ?>

    <div class="container mt-4">
    <h1>Produkte</h1>
    
    <div class="filter-container">
        <!-- Kategorie-Dropdown zuerst -->
        <div class="category-filter mb-3">
            <select id="category-select" class="form-select">
                <option value="all">Alle Kategorien</option>
            </select>
        </div>
        
        <!-- Suchleiste darunter -->
        <div class="search-filter mb-3">
            <div class="input-group">
                <input type="text" id="product-search" class="form-control" placeholder="Produkte suchen...">
                    <i class="fas fa-search"></i>
            </div>
        </div>
    </div>
    
    <!-- Produkt-Grid -->
    <div class="container">
  <div id="products" class="product-grid"></div>


    <div id="products" class="product-grid"></div>
        
        <!-- Warenkorb-Vorschau -->
        <div id="cart-preview" class="cart-drop-target mt-4">
            <h3>Warenkorb <span id="cart-count" class="badge bg-primary">0</span></h3>
            <div id="cart-items-preview"></div>
            <div id="cart-total">Gesamtpreis: <span id="cart-total-price">0.00</span> € (<span id="cart-items-count">0</span> Artikel)</div>
            <a href="cart.php" class="btn btn-primary mt-2">Zum Warenkorb</a>
        </div>
    </div>
    </div>
    <script src="../js/products.js"></script>


</body>
</html>
