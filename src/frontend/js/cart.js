// Laden der Kategorien und initialer Produkte
fetch('/api/categories')
  .then(res => res.json())
  .then(categories => {
    renderCategories(categories);
    loadProducts(categories[0].id);
  });

function renderCategories(categories) {
  const container = document.getElementById('categories');
  container.innerHTML = '';
  categories.forEach(cat => {
    const button = document.createElement('button');
    button.textContent = cat.name;
    button.onclick = () => loadProducts(cat.id);
    container.appendChild(button);
  });
}

function loadProducts(categoryId) {
  fetch(`/api/products?category_id=${categoryId}`)
    .then(res => res.json())
    .then(products => renderProducts(products));
}

function renderProducts(products) {
  const list = document.getElementById('product-list');
  list.innerHTML = '';
  products.forEach(p => {
    const div = document.createElement('div');
    div.innerHTML = `
      <h3>${p.name}</h3>
      <img src="${p.image}" alt="${p.name}">
      <p>Preis: ${p.price} €</p>
      <p>Bewertung: ${p.rating} ★</p>
      <a href="#" onclick="addToCart(${p.id}); return false;">In den Warenkorb legen</a>
    `;
    list.appendChild(div);
  });
}

function loadCart() {
  fetch('/api/cart')
    .then(res => res.json())
    .then(data => {
      renderCart(data.items);
      document.getElementById('total-price').textContent = data.total;
    });
}

function renderCart(items) {
  const list = document.getElementById('cart-list');
  list.innerHTML = '';
  items.forEach(item => {
    const div = document.createElement('div');
    div.innerHTML = `
      <p>${item.name} – ${item.price} € x ${item.quantity}</p>
      <button onclick="changeQuantity(${item.id}, 1)">+</button>
      <button onclick="changeQuantity(${item.id}, -1)">-</button>
      <button onclick="removeFromCart(${item.id})">Entfernen</button>
    `;
    list.appendChild(div);
  });
}

function changeQuantity(productId, delta) {
  fetch('/api/cart/quantity', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: productId, change: delta })
  }).then(() => {
    loadCart();
    updateCartCount();
  });
}

function removeFromCart(productId) {
  fetch(`/api/cart/${productId}`, { method: 'DELETE' })
    .then(() => {
      loadCart();
      updateCartCount();
    });
}
