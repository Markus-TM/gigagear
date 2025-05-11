const sessionId = getOrCreateSessionId();

document.addEventListener("DOMContentLoaded", () => {
  loadProducts();
  // loadCartCount();
});

function getOrCreateSessionId() {
  let id = localStorage.getItem("session_id");
  if (!id) {
    id = crypto.randomUUID();
    localStorage.setItem("session_id", id);
  }
  return id;
}

function loadProducts() {
  fetch("../../backend/api/get_products.php")
    .then((res) => res.json())
    .then((products) => {
      const container = document.getElementById("products");
      container.innerHTML = "";
      products.forEach((p) => {
        const card = document.createElement("div");
        card.className = "product-card";
        card.innerHTML = `
          <h3>${p.name}</h3>
          <img src="${p.image_path}" alt="${p.name}" width="150">
          <p>${p.description}</p>
          <p>Bewertung: ${p.rating}/5</p>
          <button onclick="addToCart(${p.id})">In den Warenkorb</button>
        `;
        container.appendChild(card);
      });
    });
}
/*
function addToCart(productId) {
  fetch('../../backend/api/cart_add.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: productId, session_id: sessionId })
  })
  .then(() => loadCartCount());
}

function loadCartCount() {
  fetch(`../../backend/api/cart_count.php?session_id=${sessionId}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('cart-count').textContent = data.count;
    });
}*/
