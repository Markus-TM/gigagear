// Session-ID generieren oder aus dem LocalStorage abrufen
const sessionId = getOrCreateSessionId();

// Sobald das DOM geladen ist, werden die Warenkorbdaten vom Server geholt
document.addEventListener("DOMContentLoaded", () => {
  loadCartItems();
});

// Holt oder erstellt eine eindeutige Sitzungs-ID (z. B. für Gäste)
function getOrCreateSessionId() {
  let id = localStorage.getItem("session_id");
  if (!id) {
    id = crypto.randomUUID(); // neue eindeutige ID erzeugen
    localStorage.setItem("session_id", id); // im Browser speichern
  }
  return id;
}

// Holt alle Artikel im Warenkorb vom Backend
function loadCartItems() {
  fetch(`../../backend/api/get_cart.php?session_id=${sessionId}`)
    .then(res => res.json())
    .then(data => {
      displayCartItems(data.items); // Artikel anzeigen
      updateCartTotal(data.total);  // Gesamtpreis anzeigen
    })
    .catch(error => {
      console.error("Fehler beim Laden des Warenkorbs:", error);
    });
}

// Zeigt die Artikel im HTML-Warenkorb an
function displayCartItems(items) {
  const cartItemsContainer = document.getElementById("cart-items");
  const emptyCartMessage = document.getElementById("empty-cart-message");

  cartItemsContainer.innerHTML = ""; // Container leeren

  if (!items || items.length === 0) {
    emptyCartMessage.style.display = "block"; // Leerer-Warenkorb-Hinweis zeigen
    return;
  }

  emptyCartMessage.style.display = "none"; // Hinweis ausblenden, wenn Artikel vorhanden

  const table = document.createElement("table");
  table.className = "table table-striped";

  // Tabellenkopf erzeugen
  const thead = document.createElement("thead");
  thead.innerHTML = `
    <tr>
      <th>Produkt</th>
      <th>Name</th>
      <th>Preis</th>
      <th>Anzahl</th>
      <th>Gesamt</th>
      <th>Aktionen</th>
    </tr>
  `;
  table.appendChild(thead);

  const tbody = document.createElement("tbody");

  // Für jeden Artikel eine Tabellenzeile erstellen
  items.forEach(item => {
    const tr = document.createElement("tr");

    // Bildpfad anpassen, falls nicht bereits korrekt
    let imagePath = item.image_path;
    if (imagePath && !imagePath.startsWith("../res/images/")) {
      imagePath = "../res/images/" + imagePath;
    }

    // HTML-Zeile mit Artikelinformationen und Bedienelementen
    tr.innerHTML = `
      <td>
        <img src="${imagePath}" alt="${item.name}" width="50" onerror="this.src='../res/images/placeholder.jpg'">
      </td>
      <td>${item.name}</td>
      <td>${parseFloat(item.price).toFixed(2)} €</td>
      <td>
        <div class="quantity-control">
          <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.cart_id}, ${item.quantity - 1})">-</button>
          <span class="quantity mx-2">${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.cart_id}, ${item.quantity + 1})">+</button>
        </div>
      </td>
      <td>${(parseFloat(item.price) * item.quantity).toFixed(2)} €</td>
      <td>
        <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.cart_id})">Entfernen</button>
      </td>
    `;

    tbody.appendChild(tr); // Zeile zur Tabelle hinzufügen
  });

  table.appendChild(tbody);
  cartItemsContainer.appendChild(table); // Tabelle ins DOM einfügen
}

// Aktualisiert die Menge eines Artikels im Warenkorb
function updateQuantity(cartId, newQuantity) {
  if (newQuantity < 1) {
    // Wenn Menge kleiner als 1, wird der Artikel entfernt
    removeFromCart(cartId);
    return;
  }

  fetch('../../backend/api/update_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      cart_id: cartId,
      quantity: newQuantity,
      session_id: sessionId
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadCartItems(); // Ansicht neu laden
      } else {
        showNotification("Fehler beim Aktualisieren der Menge", "error");
      }
    })
    .catch(error => {
      console.error("Fehler beim Aktualisieren der Menge:", error);
    });
}

// Entfernt einen Artikel aus dem Warenkorb
function removeFromCart(cartId) {
  fetch('../../backend/api/remove_from_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      cart_id: cartId,
      session_id: sessionId
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadCartItems(); // Artikel entfernen und Ansicht aktualisieren
        showNotification("Artikel wurde aus dem Warenkorb entfernt");
      } else {
        showNotification("Fehler beim Entfernen aus dem Warenkorb", "error");
      }
    })
    .catch(error => {
      console.error("Fehler beim Entfernen aus dem Warenkorb:", error);
    });
}

// Zeigt den Gesamtbetrag des Warenkorbs an
function updateCartTotal(total) {
  document.getElementById('cart-total-price').textContent = parseFloat(total).toFixed(2);
}

// Zeigt eine kurze Benachrichtigung im unteren Bereich
function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
  notification.textContent = message;

  document.body.appendChild(notification);

  // Zeigt die Benachrichtigung für 2 Sekunden und entfernt sie dann
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

// Bei Klick auf den Checkout-Button wird der Warenkorb als Bestellung abgeschickt
document.getElementById("checkout-btn").addEventListener("click", () => {
  const userId = parseInt(localStorage.getItem("user_id") || "0");

  fetch("../../backend/api/place_order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      session_id: sessionId,
      user_id: userId
    })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showNotification("Bestellung erfolgreich! Rechnungsnummer: " + data.invoice_number);
        loadCartItems(); // Warenkorb nach Bestellung leeren und aktualisieren
      } else {
        showNotification(data.error || "Bestellung fehlgeschlagen", "error");
      }
    })
    .catch(err => {
      console.error("Fehler beim Bestellen:", err);
      showNotification("Serverfehler beim Bestellen", "error");
    });
});
