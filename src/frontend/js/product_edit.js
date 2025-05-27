// Produkte vom Backend laden und HTML-Tabelle befüllen
const fetchProducts = () => {
    fetch("../../backend/api/get_products.php", {
        method: "GET",
        headers: {
            // Token aus localStorage für Authentifizierung
            "Authorization": "Bearer " + localStorage.getItem("token")
        }
    })
        .then(response => response.json())
        .then(products => {
            const productList = document.getElementById("product-list");
            productList.innerHTML = ""; // vorherige Zeilen entfernen

            products.forEach(product => {
                const row = document.createElement("tr");
                // Neue Tabellenzeile mit Produktdaten erstellen
                row.innerHTML = `
                <td>
                    <img src="${product.image_path}" alt="${product.name}" style="width: 60px; height: 60px; object-fit: cover;">
                </td>
                <td>${product.name}</td>
                <td style="white-space: nowrap;">${parseFloat(product.price).toFixed(2)} €</td>
                <td>${product.category}</td>
                <td>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light btn-sm" title="Bearbeiten" onclick='editProduct(${JSON.stringify(product)})'>⚙️</button>
                        <button class="btn btn-danger btn-sm" title="Löschen" onclick='deleteProduct(${product.id})'>🗑️</button>
                    </div>
                </td>
            `;
                productList.appendChild(row); // Zeile in Tabelle einfügen
            });
        })
        .catch(err => {
            console.error("Fehler beim Laden der Produkte:", err); // Fallback bei Fehler
        });
};

// Wird beim Laden der Seite ausgeführt – holt direkt die Produkte
document.addEventListener("DOMContentLoaded", () => {
    fetchProducts();
});

// Produktdaten ins Formular laden, um zu bearbeiten
window.editProduct = (product) => {
    document.getElementById("productFormContainer").classList.remove("d-none"); // Formular einblenden
    document.getElementById("formTitle").textContent = "Produkt bearbeiten";

    // Werte in die Formularfelder setzen
    document.getElementById("product-id").value = product.id;
    document.getElementById("name").value = product.name;
    document.getElementById("description").value = product.description;
    document.getElementById("price").value = product.price;
    document.getElementById("category").value = product.category;
    document.getElementById("image_path").value = product.image_path;

    // Tabelle & Button ausblenden während Bearbeitung
    document.getElementById("product-table").classList.add("d-none");
    document.getElementById("add-product-btn").classList.add("d-none");
};

// Produkt über API löschen mit Sicherheitsabfrage
window.deleteProduct = (productId) => {
    if (!confirm("Möchten Sie dieses Produkt wirklich löschen?")) return;

    fetch("../../backend/api/delete_product.php", {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + localStorage.getItem("token"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: productId }) // ID an Backend senden
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Produkt erfolgreich gelöscht.");
                fetchProducts(); // Tabelle neu laden
            } else {
                alert("Fehler: " + (result.error || "Unbekannter Fehler"));
            }
        })
        .catch(error => {
            console.error("Fehler beim Löschen des Produkts:", error);
            alert("Technischer Fehler beim Löschen.");
        });
};

// Wenn „+ Produkt anlegen“ geklickt wird
document.getElementById("add-product-btn").addEventListener("click", () => {
    document.getElementById("productFormContainer").classList.remove("d-none"); // Formular zeigen
    document.getElementById("formTitle").textContent = "Produkt anlegen"; // Titel ändern
    document.getElementById("productForm").reset(); // Formular leeren
    document.getElementById("product-id").value = ""; // ID entfernen

    // Tabelle & Button ausblenden
    document.getElementById("product-table").classList.add("d-none");
    document.getElementById("add-product-btn").classList.add("d-none");
});

// Wenn Benutzer das Formular abbricht
document.getElementById("cancelProductFormBtn").addEventListener("click", () => {
    document.getElementById("productFormContainer").classList.add("d-none"); // Formular ausblenden
    document.getElementById("product-table").classList.remove("d-none");    // Tabelle wieder einblenden
    document.getElementById("add-product-btn").classList.remove("d-none");  // Button wieder zeigen
});

// Formular absenden (Anlegen oder Aktualisieren eines Produkts)
document.getElementById("productForm").addEventListener("submit", (event) => {
    event.preventDefault(); // Standardverhalten unterdrücken

    const productId = document.getElementById("product-id").value;
    const isEdit = productId !== ""; // True, wenn Bearbeitungsmodus

    // Formularinhalte als Objekt sammeln
    const formData = {
        id: productId,
        name: document.getElementById("name").value,
        description: document.getElementById("description").value,
        price: parseFloat(document.getElementById("price").value),
        category: document.getElementById("category").value,
        image_path: document.getElementById("image_path").value
    };

    // Endpunkt abhängig vom Modus (neu vs. bearbeiten)
    const endpoint = isEdit ? "update_product.php" : "add_product.php";

    fetch(`../../backend/api/${endpoint}`, {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + localStorage.getItem("token"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData) // Daten ans Backend senden
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(isEdit ? "Produkt wurde aktualisiert!" : "Produkt wurde erfolgreich angelegt!");

                // Formular zurücksetzen und Ansicht zurückwechseln
                document.getElementById("productForm").reset();
                document.getElementById("productFormContainer").classList.add("d-none");
                document.getElementById("product-table").classList.remove("d-none");
                document.getElementById("add-product-btn").classList.remove("d-none");
                fetchProducts(); // Neue Daten laden
            } else {
                alert("Fehler: " + (result.error || "Unbekannter Fehler"));
            }
        })
        .catch(error => {
            console.error("Fehler beim Speichern:", error);
            alert("Technischer Fehler beim Speichern.");
        });
});
