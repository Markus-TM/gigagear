// Session-ID für Warenkorb-Tracking laden oder neu erstellen
const sessionId = getOrCreateSessionId();

// Warten, bis das gesamte HTML-Dokument geladen ist
document.addEventListener("DOMContentLoaded", () => {
  loadCategories();     // Kategorien aus Backend laden
  loadProducts();       // Alle Produkte laden
  loadCartCount();      // Anzahl Produkte im Warenkorb anzeigen
  setupDragAndDrop();   // Drag-&-Drop-Funktion für Warenkorb aktivieren
  setupSearch();        // Produktsuchleiste initialisieren
});

// Erstellt (falls nötig) eine eindeutige Session-ID und speichert sie im localStorage
function getOrCreateSessionId() {
  let id = localStorage.getItem("session_id"); // ID aus localStorage holen
  if (!id) {
    id = crypto.randomUUID();                  // Neue UUID erzeugen (browserseitig)
    localStorage.setItem("session_id", id);    // In localStorage speichern
  }
  return id;                                    // ID zurückgeben
}

// Aktiviert die Produktsuche (Live-Suche, Button, Enter-Taste)
function setupSearch() {
  const searchInput = document.getElementById("product-search");
  const searchButton = document.querySelector(".search-button");

  if (searchInput) {
    // Live-Suche mit Verzögerung (debounce)
    searchInput.addEventListener("input", debounce(function (e) {
      performSearch(e.target.value);
    }, 300));

    // Suche per Klick
    if (searchButton) {
      searchButton.addEventListener("click", function () {
        performSearch(searchInput.value);
      });
    }

    // Suche per Enter-Taste
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        performSearch(searchInput.value);
      }
    });
  }
}

// debounce: Verzögert den Aufruf einer Funktion (z. B. bei schnellem Tippen)
function debounce(func, wait) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout); // alten Timeout abbrechen
    timeout = setTimeout(() => func.apply(this, args), wait); // neuen setzen
  };
}

// Entscheidet, welche Such- oder Filterfunktion ausgeführt werden soll
function performSearch(searchTerm) {
  searchTerm = searchTerm.trim().toLowerCase(); // Eingabe bereinigen
  const categorySelect = document.getElementById("category-select");
  const selectedCategory = categorySelect ? categorySelect.value : "all";

  if (searchTerm === "") {
    // Wenn kein Suchbegriff: Produkte laden (ggf. nach Kategorie)
    selectedCategory === "all" ? loadProducts() : loadProductsByCategory(selectedCategory);
  } else {
    // Suche nach Suchbegriff und ggf. Kategorie
    searchProducts(searchTerm, selectedCategory);
  }
}

// Sendet eine Suchanfrage ans Backend und ruft die Darstellung auf
function searchProducts(searchTerm, categoryId = "all") {
  let url = `../../backend/api/get_products.php?search=${encodeURIComponent(searchTerm)}`;
  if (categoryId !== "all") {
    url += `&category_id=${categoryId}`;
  }

  fetch(url)
    .then(res => res.json())
    .then(products => renderProducts(products, searchTerm)) // Darstellung aktualisieren
    .catch(error => {
      console.error("Fehler bei der Produktsuche:", error);
      document.getElementById("products").innerHTML =
        "<p class='text-center mt-4'>Fehler bei der Suche. Bitte versuche es erneut.</p>";
    });
}

// Lädt alle Produkte ohne Filter
function loadProducts() {
  fetch("../../backend/api/get_products.php")
    .then(res => res.json())
    .then(products => renderProducts(products))
    .catch(error => {
      console.error("Fehler beim Laden der Produkte:", error);
      document.getElementById("products").innerHTML =
        "<p>Fehler beim Laden der Produkte. Bitte überprüfe die Konsole.</p>";
    });
}

// Lädt Produkte gefiltert nach Kategorie
function loadProductsByCategory(categoryId) {
  fetch(`../../backend/api/get_products.php?category_id=${categoryId}`)
    .then(res => res.json())
    .then(products => renderProducts(products))
    .catch(error => {
      console.error("Fehler beim Laden der Produkte:", error);
      document.getElementById("products").innerHTML =
        "<p>Fehler beim Laden der Produkte. Bitte überprüfe die Konsole.</p>";
    });
}

// Erzeugt HTML-Darstellung für eine Liste von Produkten
function renderProducts(products, searchTerm = "") {
  const container = document.getElementById("products");
  container.innerHTML = ""; // vorherigen Inhalt leeren

  // Wenn keine Produkte gefunden wurden
  if (products.length === 0) {
    container.innerHTML = `<p class='text-center mt-4'>Keine Produkte gefunden${searchTerm ? ` für "${searchTerm}"` : ""}.</p>`;
    return;
  }

  // Container für Grid-Layout erstellen
  const productGrid = document.createElement("div");
  productGrid.className = "product-grid";
  container.appendChild(productGrid);

  // Für jedes Produkt eine Karte erzeugen
  products.forEach(p => {
    const card = document.createElement("div");
    card.className = "product-card";
    card.setAttribute("draggable", "true"); // Drag aktivieren
    card.setAttribute("data-product-id", p.id); // ID für Drag-Event

    const imagePath = "/gigagear/src/frontend/res/images/" + p.image_path;
    const ratingStars = createRatingStars(p.rating || 0); // Bewertung (Sterne)

    // HTML für die Karte
    card.innerHTML = `
      <div class="product-image">
        <img src="${imagePath}" alt="${p.name}" width="150">
      </div>
      <div class="product-info">
        <h3>${p.name}</h3>
        <p class="product-description">${p.description || ''}</p>
        <div class="product-rating">${ratingStars}</div>
        <p class="product-price">${(Number(p.price) || 0).toFixed(2)} €</p>
        <button class="add-to-cart-btn" onclick="addToCart(${p.id})">In den Warenkorb</button>
      </div>
    `;

    productGrid.appendChild(card);
    card.addEventListener("dragstart", handleDragStart); // Drag-Start registrieren
  });
}

// Sterne-Darstellung für Produktbewertung erzeugen
function createRatingStars(rating) {
  return [...Array(5)].map((_, i) =>
    `<span class="star ${i < rating ? "filled" : ""}">${i < rating ? "★" : "☆"}</span>`
  ).join('');
}

// Fügt ein Produkt dem Warenkorb hinzu
function addToCart(productId) {
  fetch('../../backend/api/cart_add.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: productId, session_id: sessionId, quantity: 1 })
  })
    .then(response => {
      if (!response.ok) throw new Error('Netzwerkantwort war nicht ok');
      return response.json();
    })
    .then(data => {
      if (data.success) {
        loadCartCount(); // Anzeige aktualisieren
        showNotification("Produkt wurde zum Warenkorb hinzugefügt!");
      } else {
        showNotification("Fehler beim Hinzufügen zum Warenkorb", "error");
      }
    })
    .catch(error => {
      console.error("Fehler beim Hinzufügen zum Warenkorb:", error);
      showNotification("Fehler beim Hinzufügen zum Warenkorb", "error");
    });
}

// Holt Anzahl & Gesamtpreis der Produkte im Warenkorb
function loadCartCount() {
  fetch(`../../backend/api/cart_count.php?session_id=${sessionId}`)
    .then(res => {
      if (!res.ok) throw new Error('Netzwerkantwort war nicht ok');
      return res.json();
    })
    .then(data => {
      // Badge im Header aktualisieren
      const cartNavLink = document.querySelector("#nav-cart .nav-link");
      if (cartNavLink) {
        const existingBadge = cartNavLink.querySelector(".cart-badge");
        if (existingBadge) existingBadge.remove();
        if (data.count && data.count > 0) {
          const badge = document.createElement("span");
          badge.className = "cart-badge";
          badge.textContent = data.count;
          cartNavLink.appendChild(badge);
        }
      }

      // Optional: Vorschau-Box unten rechts aktualisieren
      fetch(`../../backend/api/get_cart.php?session_id=${sessionId}`)
        .then(res => res.json())
        .then(cartData => {
          const cartPreview = document.getElementById("cart-preview");
          if (cartPreview) {
            const cartCountElement = cartPreview.querySelector("#cart-count");
            if (cartCountElement) cartCountElement.textContent = data.count || 0;

            const cartTotalElement = document.getElementById("cart-total");
            if (cartTotalElement) {
              cartTotalElement.innerHTML = `Gesamtpreis: <span id="cart-total-price">${(cartData.total || 0).toFixed(2)}</span> € (${data.count || 0} Artikel)`;
            }
          }
        })
        .catch(error => {
          console.error("Fehler beim Laden des Warenkorbs:", error);
        });
    })
    .catch(error => {
      console.error("Fehler beim Laden der Warenkorbanzahl:", error);
    });
}

// Holt Kategorien aus dem Backend und baut Dropdown auf
function loadCategories() {
  fetch("../../backend/api/get_categories.php")
    .then(res => res.json())
    .then(categories => {
      const select = document.getElementById("category-select");
      select.innerHTML = '<option value="all">Alle Kategorien</option>';

      categories.forEach(category => {
        const option = document.createElement("option");
        option.value = category.id;
        option.textContent = category.name;
        select.appendChild(option);
      });

      // Filter nach Auswahl
      select.addEventListener("change", (e) => {
        const categoryId = e.target.value;
        categoryId === "all" ? loadProducts() : loadProductsByCategory(categoryId);
      });
    })
    .catch(error => {
      console.error("Fehler beim Laden der Kategorien:", error);
    });
}

// Zeigt eine kleine Benachrichtigung unten im Fenster
function showNotification(message, type = "success") {
  document.querySelectorAll(".notification").forEach(n => n.remove());

  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.textContent = message;
  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add("show");
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => notification.remove(), 300);
    }, 2000);
  }, 10);
}

// Legt den Warenkorb-Zielbereich für Drag-&-Drop an
function setupDragAndDrop() {
  let cartDropTarget = document.getElementById("cart-drop-target");

  if (!cartDropTarget) {
    cartDropTarget = document.createElement("div");
    cartDropTarget.id = "cart-drop-target";
    cartDropTarget.className = "cart-drop-target";
    cartDropTarget.style.cssText = `
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 999;
      display: flex;
    `;
    cartDropTarget.innerHTML = `
      <div class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="drop-text">Produkte hier reindraggen</span>
      </div>
    `;
    document.body.appendChild(cartDropTarget);
  }

  cartDropTarget.addEventListener("dragover", (e) => {
    e.preventDefault(); // Drop erlauben
    cartDropTarget.classList.add("drag-over");
  });

  cartDropTarget.addEventListener("dragleave", () => {
    cartDropTarget.classList.remove("drag-over");
  });

  cartDropTarget.addEventListener("drop", handleDrop); // Produkt ablegen
}

// Wird ausgelöst, wenn Drag gestartet wird
function handleDragStart(e) {
  const productId = e.currentTarget.getAttribute("data-product-id");
  e.dataTransfer.setData("text/plain", productId);
  e.currentTarget.classList.add("dragging");
}

// Verarbeitet das Ablegen eines Produkts im Warenkorb
function handleDrop(e) {
  e.preventDefault();
  const cartDropTarget = document.getElementById("cart-drop-target");
  cartDropTarget.classList.remove("drag-over");

  const productId = e.dataTransfer.getData("text/plain");
  if (productId) {
    addToCart(parseInt(productId));
    const draggedElement = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    if (draggedElement) {
      draggedElement.classList.remove("dragging");
      draggedElement.classList.add("added-to-cart");
      setTimeout(() => draggedElement.classList.remove("added-to-cart"), 1000);
    }
  }
}

// Entfernt Drag-Klasse nach Drag-Ende
document.addEventListener("dragend", () => {
  document.querySelectorAll(".dragging").forEach(el => el.classList.remove("dragging"));
});
