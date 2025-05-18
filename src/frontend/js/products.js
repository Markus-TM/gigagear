const sessionId = getOrCreateSessionId();

document.addEventListener("DOMContentLoaded", () => {
  loadCategories();
  loadProducts();
  loadCartCount();
  setupDragAndDrop();
  setupSearch();
});

function getOrCreateSessionId() {
  let id = localStorage.getItem("session_id");
  if (!id) {
    id = crypto.randomUUID();
    localStorage.setItem("session_id", id);
  }
  return id;
}

function setupSearch() {
  const searchInput = document.getElementById("product-search");
  const searchButton = document.querySelector(".search-button");
  
  if (searchInput) {
    // Event-Listener für Echtzeit-Suche während des Tippens
    searchInput.addEventListener("input", debounce(function(e) {
      performSearch(e.target.value);
    }, 300)); // 300ms Verzögerung
    
    // Event-Listener für Suche bei Klick auf den Lupen-Button
    if (searchButton) {
      searchButton.addEventListener("click", function() {
        performSearch(searchInput.value);
      });
    }
    
    // Event-Listener für Suche bei Drücken der Enter-Taste
    searchInput.addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        performSearch(searchInput.value);
      }
    });
  }
}

// Hilfsfunktion für Debounce (verhindert zu viele Anfragen)
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

// Funktion zum Durchführen der Suche
function performSearch(searchTerm) {
  searchTerm = searchTerm.trim().toLowerCase();
  
  // Aktuelle Kategorie berücksichtigen
  const categorySelect = document.getElementById("category-select");
  const selectedCategory = categorySelect ? categorySelect.value : "all";
  
  if (searchTerm === "") {
    // Wenn Suchfeld leer ist, zeige alle Produkte der aktuellen Kategorie
    if (selectedCategory === "all") {
      loadProducts();
    } else {
      loadProductsByCategory(selectedCategory);
    }
  } else {
    // Sonst filtere nach Suchbegriff und Kategorie
    searchProducts(searchTerm, selectedCategory);
  }
}

// Funktion zum Suchen von Produkten
function searchProducts(searchTerm, categoryId = "all") {
  let url = `../../backend/api/get_products.php?search=${encodeURIComponent(searchTerm)}`;
  
  if (categoryId !== "all") {
    url += `&category_id=${categoryId}`;
  }
  
  fetch(url)
    .then((res) => res.json())
    .then((products) => {
      console.log("Suchergebnisse:", products);
      const container = document.getElementById("products");
      container.innerHTML = "";
      
      if (products.length === 0) {
        container.innerHTML = `<p class='text-center mt-4'>Keine Produkte gefunden für "${searchTerm}".</p>`;
        return;
      }
      
      // Erstelle ein Grid-Container für die Produkte
      const productGrid = document.createElement("div");
      productGrid.className = "product-grid";
      container.appendChild(productGrid);
      
      products.forEach((p) => {
        const card = document.createElement("div");
        card.className = "product-card";
        card.setAttribute("draggable", "true");
        card.setAttribute("data-product-id", p.id);
        
        // Bildpfad anpassen
        const imagePath = "/gigagear/src/frontend/res/images/" + p.image_path;

        // Sterne-Bewertung erstellen
        const ratingStars = createRatingStars(p.rating || 0);
        
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
        
        // Event-Listener für Drag & Drop
        card.addEventListener("dragstart", handleDragStart);
      });
    })
    .catch(error => {
      console.error("Fehler bei der Produktsuche:", error);
      document.getElementById("products").innerHTML = 
        "<p class='text-center mt-4'>Fehler bei der Suche. Bitte versuche es erneut.</p>";
    });
}


function loadProducts() {
  fetch("../../backend/api/get_products.php")
    .then((res) => res.json())
    .then((products) => {
      console.log("Geladene Produkte:", products);
      const container = document.getElementById("products");
      container.innerHTML = "";
      
      // Erstelle ein Grid-Container für die Produkte
      const productGrid = document.createElement("div");
      productGrid.className = "product-grid";
      container.appendChild(productGrid);
      
      products.forEach((p) => {
        const card = document.createElement("div");
        card.className = "product-card";
        card.setAttribute("draggable", "true");
        card.setAttribute("data-product-id", p.id);
        
        // Bildpfad anpassen - nur den Dateinamen verwenden
        const imagePath = "/gigagear/src/frontend/res/images/" + p.image_path;

        // Sterne-Bewertung erstellen
        const ratingStars = createRatingStars(p.rating || 0);
        
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
        
        // Event-Listener für Drag & Drop
        card.addEventListener("dragstart", handleDragStart);
      });
    })
    .catch(error => {
      console.error("Fehler beim Laden der Produkte:", error);
      document.getElementById("products").innerHTML = 
        "<p>Fehler beim Laden der Produkte. Bitte überprüfe die Konsole.</p>";
    });
}

function createRatingStars(rating) {
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    if (i <= rating) {
      stars += '<span class="star filled">★</span>';
    } else {
      stars += '<span class="star">☆</span>';
    }
  }
  return stars;
}

function addToCart(productId) {
  fetch('../../backend/api/cart_add.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      product_id: productId, 
      session_id: sessionId,
      quantity: 1
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Netzwerkantwort war nicht ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      loadCartCount();
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

function loadCartCount() {
  fetch(`../../backend/api/cart_count.php?session_id=${sessionId}`)
    .then(res => {
      if (!res.ok) {
        throw new Error('Netzwerkantwort war nicht ok');
      }
      return res.json();
    })
    .then(data => {
      // Füge die Anzahl zum Warenkorb-Link in der Navbar hinzu
      const cartNavLink = document.querySelector("#nav-cart .nav-link");
      if (cartNavLink) {
        // Entferne zuerst einen vorhandenen Badge, falls vorhanden
        const existingBadge = cartNavLink.querySelector(".cart-badge");
        if (existingBadge) {
          existingBadge.remove();
        }
        
        // Füge den neuen Badge hinzu, wenn Artikel im Warenkorb sind
        if (data.count && data.count > 0) {
          const badge = document.createElement("span");
          badge.className = "cart-badge";
          badge.textContent = data.count;
          cartNavLink.appendChild(badge);
        }
      }
      
      // Lade den Gesamtpreis für die Vorschau
      fetch(`../../backend/api/get_cart.php?session_id=${sessionId}`)
        .then(res => res.json())
        .then(cartData => {
          // Aktualisiere den Warenkorb-Vorschau-Bereich
          const cartPreview = document.getElementById("cart-preview");
          if (cartPreview) {
            const cartCountElement = cartPreview.querySelector("#cart-count");
            if (cartCountElement) {
              cartCountElement.textContent = data.count || 0;
            }
            
            const totalPriceElement = document.getElementById("cart-total-price");
            if (totalPriceElement) {
              totalPriceElement.textContent = (cartData.total || 0).toFixed(2);
              
              // Aktualisiere auch den Text mit der Artikelanzahl
              const cartTotalElement = document.getElementById("cart-total");
              if (cartTotalElement) {
                cartTotalElement.innerHTML = `Gesamtpreis: <span id="cart-total-price">${(cartData.total || 0).toFixed(2)}</span> € (${data.count || 0} Artikel)`;
              }
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

function loadCategories() {
  fetch("../../backend/api/get_categories.php")
    .then((res) => res.json())
    .then((categories) => {
      const select = document.getElementById("category-select");
      
      // Behalte die "Alle Kategorien" Option
      select.innerHTML = '<option value="all">Alle Kategorien</option>';
      
      categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id;
        option.textContent = category.name;
        select.appendChild(option);
      });
      
      // Event-Listener für Kategorieauswahl
      select.addEventListener("change", (e) => {
        const categoryId = e.target.value;
        if (categoryId === "all") {
          loadProducts();
        } else {
          loadProductsByCategory(categoryId);
        }
      });
    })
    .catch(error => {
      console.error("Fehler beim Laden der Kategorien:", error);
    });
}

function loadProductsByCategory(categoryId) {
  fetch(`../../backend/api/get_products.php?category_id=${categoryId}`)
    .then((res) => res.json())
    .then((products) => {
      console.log("Gefilterte Produkte:", products);
      const container = document.getElementById("products");
      container.innerHTML = "";
      
      const productGrid = document.createElement("div");
      productGrid.className = "product-grid";
      container.appendChild(productGrid);
      
      if (products.length === 0) {
        container.innerHTML = "<p>Keine Produkte in dieser Kategorie gefunden.</p>";
        return;
      }
      
      products.forEach((p) => {
        const card = document.createElement("div");
        card.className = "product-card";
        card.setAttribute("draggable", "true");
        card.setAttribute("data-product-id", p.id);
        
        // Bildpfad anpassen - nur den Dateinamen verwenden
        const imagePath = "/gigagear/src/frontend/res/images/" + p.image_path;

        // Sterne-Bewertung erstellen
        const ratingStars = createRatingStars(p.rating || 0);
        
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
        
        // Event-Listener für Drag & Drop
        card.addEventListener("dragstart", handleDragStart);
      });
    })
    .catch(error => {
      console.error("Fehler beim Laden der Produkte:", error);
      document.getElementById("products").innerHTML = 
        "<p>Fehler beim Laden der Produkte. Bitte überprüfe die Konsole.</p>";
    });
}

function showNotification(message, type = "success") {
  // Entferne vorhandene Benachrichtigungen
  const existingNotifications = document.querySelectorAll(".notification");
  existingNotifications.forEach(notification => {
    notification.remove();
  });
  
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add("show");
    
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 2000);
  }, 10);
}

// Drag & Drop Funktionalität
function setupDragAndDrop() {
  // Erstelle einen Warenkorb-Drop-Bereich, wenn er noch nicht existiert
  let cartDropTarget = document.getElementById("cart-drop-target");
  
  if (!cartDropTarget) {
    cartDropTarget = document.createElement("div");
    cartDropTarget.id = "cart-drop-target";
    cartDropTarget.className = "cart-drop-target";
    cartDropTarget.style.position = "fixed";
    cartDropTarget.style.bottom = "30px";
    cartDropTarget.style.right = "30px";
    cartDropTarget.style.zIndex = "999";
    cartDropTarget.style.display = "flex"; // Immer anzeigen statt "none"
    cartDropTarget.innerHTML = `
      <div class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="drop-text">Produkte hier reindraggen</span>
      </div>
    `;
    document.body.appendChild(cartDropTarget);
  } else {
    // Stelle sicher, dass der Bereich immer sichtbar ist
    cartDropTarget.style.display = "flex";
  }
  
  cartDropTarget.addEventListener("dragover", (e) => {
    e.preventDefault();
    cartDropTarget.classList.add("drag-over");
  });
  
  cartDropTarget.addEventListener("dragleave", () => {
    cartDropTarget.classList.remove("drag-over");
  });
  
  cartDropTarget.addEventListener("drop", handleDrop);
}


function handleDragStart(e) {
  const productId = e.currentTarget.getAttribute("data-product-id");
  e.dataTransfer.setData("text/plain", productId);
  e.currentTarget.classList.add("dragging");
  
  // Zeige den Drop-Bereich an
  const cartDropTarget = document.getElementById("cart-drop-target");
  if (cartDropTarget) {
    cartDropTarget.style.display = "flex";
  }
}

function handleDrop(e) {
  e.preventDefault();
  const cartDropTarget = document.getElementById("cart-drop-target");
  cartDropTarget.classList.remove("drag-over");
  
  const productId = e.dataTransfer.getData("text/plain");
  if (productId) {
    addToCart(parseInt(productId));
    
    // Visuelles Feedback
    const draggedElement = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    if (draggedElement) {
      draggedElement.classList.remove("dragging");
      draggedElement.classList.add("added-to-cart");
      setTimeout(() => {
        draggedElement.classList.remove("added-to-cart");
      }, 1000);
    }
  }
}

// Füge Event-Listener hinzu, um den Drop-Bereich zu verstecken, wenn nicht mehr gedragt wird
document.addEventListener("dragend", () => {
  const cartDropTarget = document.getElementById("cart-drop-target");
  if (cartDropTarget) {
  }
  
  // Entferne die dragging-Klasse von allen Produkten
  const draggingElements = document.querySelectorAll(".dragging");
  draggingElements.forEach(el => {
    el.classList.remove("dragging");
  });
});
