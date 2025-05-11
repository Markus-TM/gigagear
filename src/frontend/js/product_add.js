document.addEventListener("DOMContentLoaded", () => {
  const productForm = document.getElementById("product-form");
  loadCategories();
  loadCartCount();
  const sessionId = getOrCreateSessionId();

  productForm.addEventListener("submit", (event) => {
    event.preventDefault(); // Verhindert das Standardverhalten (Seitenreload)

    const formData = new FormData(productForm);
    const productData = {
      name: formData.get("name"),
      description: formData.get("description"),
      price: parseFloat(formData.get("price")),
      category: formData.get("category"),
      image_path: formData.get("image_path"),
      is_active: true, // Standardmäßig true
    };

    // Daten per fetch an den Server schicken
    fetch("../../backend/api/add_product.php", {
      method: "POST",
      headers: {
        Authorization: "Bearer " + localStorage.getItem("token"),
        "Content-Type": "application/json",
      },
      body: JSON.stringify(productData),
    })
      .then((response) => response.json())
      .then((responseData) => {
        if (responseData.success) {
          alert("Produkt wurde erfolgreich hinzugefügt!");
          // Optional: Formular zurücksetzen
          productForm.reset();
        } else {
          alert("Fehler: " + responseData.error);
        }
      })
      .catch((err) =>
        console.error("Fehler beim Hinzufügen des Produkts:", err)
      );
  });
});
