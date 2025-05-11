<?php include("../includes/head.php"); ?>

<body>

    <?php include("../includes/navbar.php"); ?>

    <div class="container mt-4">
        <h1>Produkt hinzufügen</h1>
        <form id="product-form">
            <div class="mb-3">
                <label for="name" class="form-label">Produktname</label>
                <input type="text" class="form-control" id="name" name="name" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Beschreibung</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Preis</label>
                <input type="number" class="form-control" id="price" name="price" min= "1" step="0.01" required />
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Kategorie</label>
                <input type="text" class="form-control" id="category" name="category" required />
            </div>
            <div class="mb-3">
                <label for="image_path" class="form-label">Bild-URL</label>
                <input type="text" class="form-control" id="image_path" name="image_path" required />
            </div>
            <button type="submit" class="btn btn-primary">Produkt hinzufügen</button>
        </form>
    </div>

    <script src="../js/product_add.js"></script>
</body>