<?php include("../includes/head.php"); ?>

<body>
    <?php include("../includes/navbar.php"); ?>

    <div class="container mt-4">
        <h1>Warenkorb</h1>
        
        <div id="cart-container">
            <div id="cart-items">
                <!-- Warenkorb-Elemente werden hier per JavaScript eingefügt -->
            </div>
            
            <p id="empty-cart-message" style="display: none;">Dein Warenkorb ist leer.</p>
            
            <div id="cart-summary" class="mt-4">
                <div id="cart-total">
                    <h3>Gesamtpreis: <span id="cart-total-price">0.00</span> €</h3>
                </div>
                <button id="checkout-btn" class="btn btn-success mt-2">Zur Kasse</button>
            </div>
        </div>
    </div>
    <style>
      .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Exakt 3 Spalten */
        gap: 20px;
        margin-top: 20px;
        justify-items: center;
      }

      .product-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 16px;
        width: 100%;
        max-width: 300px;
        text-align: center;
        transition: transform 0.2s ease;
      }

      .product-card:hover {
        transform: translateY(-4px);
      }

      .product-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
      }

      /* Optional: für mobile Ansicht (responsive) */
      @media (max-width: 900px) {
        .product-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }

      @media (max-width: 600px) {
        .product-grid {
          grid-template-columns: repeat(1, 1fr);
        }
      }

      /* Styling für den Warenkorb-Vorschau-Bereich */
      .cart-drop-target {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
      }

      .cart-badge {
        margin-left: 5px;
      }
    </style>

    <script src="../js/cart.js"></script>
</body>
