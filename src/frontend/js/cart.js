const sessionId = getOrCreateSessionId();

document.addEventListener("DOMContentLoaded", () => {
  loadCartItems();
});

function getOrCreateSessionId() {
  let id = localStorage.getItem("session_id");
  if (!id) {
    id = crypto.randomUUID();
    localStorage.setItem("session_id", id);
  }
  return id;
}

function loadCartItems() {
  fetch(`../../backend/api/get_cart.php?session_id=${sessionId}`)
    .then(res => res.json())
    .then(data => {
      displayCartItems(data.items);
      updateCartTotal(data.total);
    })
    .catch(error => {
      console.error("Fehler beim Laden des Warenkorbs:", error);
    });
}

function displayCartItems(items) {
  const cartItemsContainer = document.getElementById("cart-items");
  const emptyCartMessage = document.getElementById("empty-cart-message");
  
  // Container leeren
  cartItemsContainer.innerHTML = "";
  
  if (!items || items.length === 0) {
    emptyCartMessage.style.display = "block";
    return;
  }
  
  emptyCartMessage.style.display = "none";
  
  // Tabelle für Warenkorb-Elemente erstellen
  const table = document.createElement("table");
  table.className = "table table-striped";
  
  // Tabellenkopf
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
  
  // Tabellenkörper
  const tbody = document.createElement("tbody");
  
  items.forEach(item => {
    const tr = document.createElement("tr");
    
    // Bildpfad anpassen
    let imagePath = item.image_path;
    if (imagePath && !imagePath.startsWith("../res/images/")) {
      imagePath = "../res/images/" + imagePath;
    }
    
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
    
    tbody.appendChild(tr);
  });
  
  table.appendChild(tbody);
  cartItemsContainer.appendChild(table);
}

function updateQuantity(cartId, newQuantity) {
  if (newQuantity < 1) {
    // Wenn Menge unter 1, Artikel entfernen
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
      loadCartItems();
    } else {
      showNotification("Fehler beim Aktualisieren der Menge", "error");
    }
  })
  .catch(error => {
    console.error("Fehler beim Aktualisieren der Menge:", error);
  });
}

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
      loadCartItems();
      showNotification("Artikel wurde aus dem Warenkorb entfernt");
    } else {
      showNotification("Fehler beim Entfernen aus dem Warenkorb", "error");
    }
  })
  .catch(error => {
    console.error("Fehler beim Entfernen aus dem Warenkorb:", error);
  });
}

function updateCartTotal(total) {
  document.getElementById('cart-total-price').textContent = parseFloat(total).toFixed(2);
}

function showNotification(message, type = "success") {
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
