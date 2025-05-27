// customer_edit.js

// Warten bis das komplette DOM geladen ist, dann Kunden abrufen und Funktionen aktivieren
document.addEventListener("DOMContentLoaded", () => {
    fetchCustomers(); // Hauptfunktion zur Anzeige aller Kunden

    // Ruft Kunden vom Backend ab und zeigt sie in der Tabelle an
    function fetchCustomers() {
        fetch("../../backend/api/get_customers.php", {
            headers: {
                Authorization: "Bearer " + localStorage.getItem("token"), // Authentifizierung über Token
            },
        })
            .then((res) => res.json())
            .then((customers) => {
                const customerList = document.getElementById("customer-list");
                customerList.innerHTML = ""; // Liste zunächst leeren

                customers.forEach((customer) => {
                    const row = document.createElement("tr");

                    // Text und Button-Style je nach Aktivitätsstatus setzen
                    const statusText = customer.is_active == 1 ? "Aktiv" : "Inaktiv";
                    const toggleButtonLabel = customer.is_active == 1 ? "Deaktivieren" : "Aktivieren";
                    const toggleButtonClass = customer.is_active == 1 ? "btn-danger" : "btn-success";

                    // HTML für die Tabellenzeile – enthält Daten, Detail-Button, Status-Button
                    row.innerHTML = `
                    <td>${customer.firstname}</td>
                    <td>${customer.lastname}</td>
                    <td>${customer.email}</td>
                    <td>
                      <button class="btn btn-info" data-id="${customer.id}" data-name="${customer.firstname} ${customer.lastname}">Details anzeigen</button>
                    </td>
                    <td>
                      <span class="me-2">${statusText}</span>
                      <button class="btn ${toggleButtonClass} btn-sm toggle-status-btn">
                        ${toggleButtonLabel}
                      </button>
                    </td>
                `;

                    // Klick auf „Details anzeigen“ lädt Bestellungen des Kunden
                    row.querySelector(".btn-info").addEventListener("click", (e) => {
                        const id = e.target.dataset.id;
                        const name = e.target.dataset.name;
                        showCustomerOrders(id, name);
                    });

                    // Klick auf „Aktivieren/Deaktivieren“ ändert den Status des Kunden
                    row.querySelector(".toggle-status-btn").addEventListener("click", () => {
                        toggleCustomerStatus(customer.id, customer.is_active);
                    });

                    customerList.appendChild(row); // Zeile zur Tabelle hinzufügen
                });
            });
    }

    // Schaltet den Aktivitätsstatus eines Kunden um (aktiv / inaktiv)
    function toggleCustomerStatus(customerId, currentStatus) {
        const newStatus = currentStatus == 1 ? 0 : 1;
        const action = newStatus == 0 ? "deaktiviert" : "aktiviert";

        if (confirm(`Möchten Sie diesen Kunden wirklich ${action}?`)) {
            fetch("../../backend/api/toggle_customer_status.php", {
                method: "POST",
                headers: {
                    Authorization: "Bearer " + localStorage.getItem("token"),
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    id: customerId,
                    active: newStatus,
                }),
            })
                .then((response) => response.json())
                .then((response) => {
                    if (response.success) {
                        alert(`Kunde wurde erfolgreich ${action}.`);
                        fetchCustomers(); // Liste neu laden nach Statuswechsel
                    } else {
                        alert("Fehler: " + response.error);
                    }
                })
                .catch((err) => console.error("Fehler beim Umschalten des Kundenstatus:", err));
        }
    }

    // Holt alle Bestellungen eines Kunden und zeigt sie in einer neuen Ansicht
    function showCustomerOrders(userId, fullName) {
        document.getElementById("customer-table").classList.add("d-none"); // Kundenliste ausblenden
        document.getElementById("customer-orders-section").classList.remove("d-none"); // Bestellbereich einblenden
        document.getElementById("customer-name-title").textContent = fullName; // Kundenname anzeigen

        fetch(`../../backend/api/get_orders.php?user_id=${userId}`, {
            headers: {
                Authorization: "Bearer " + localStorage.getItem("token"),
            },
        })
            .then((res) => res.json())
            .then((orders) => {
                const tbody = document.getElementById("customer-order-list");
                tbody.innerHTML = ""; // Alte Daten entfernen

                if (orders.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='6'>Keine Bestellungen vorhanden.</td></tr>";
                    return;
                }

                const total = orders.length;

                // Bestellungen in umgekehrter Reihenfolge anzeigen (neueste zuerst)
                orders.slice().reverse().forEach((order, index) => {
                    const row = document.createElement("tr");

                    row.innerHTML = `
                    <td>${total - index}</td>
                    <td>${order.invoice_number || "—"}</td>
                    <td>${order.order_date || order.date}</td>
                    <td>${parseFloat(order.total_price || 0).toFixed(2)} €</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm">Details</button>
                    </td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm">Bearbeiten</button>
                        <button class="btn btn-danger btn-sm" title="Löschen" onclick="deleteOrder(${order.id}, ${userId}, '${fullName.replace(/'/g, "\\'")}')">🗑️</button>
                    </td>
                `;

                    // Klick auf „Details“ zeigt Produktdetails der Bestellung
                    row.querySelector(".btn-outline-primary").addEventListener("click", () => {
                        toggleAdminOrderDetails(order.id, row);
                    });

                    // Klick auf „Bearbeiten“ öffnet das Bearbeitungsformular
                    row.querySelector(".btn-outline-secondary").addEventListener("click", () => {
                        document.getElementById("orderEditFormContainer").classList.remove("d-none");
                        document.getElementById("edit-order-id").value = order.id;
                        document.getElementById("edit-invoice-number").value = order.invoice_number || "";
                        document.getElementById("edit-order-date").value = new Date(order.order_date).toISOString().slice(0, 16);

                        document.getElementById("orderEditForm").dataset.customerId = userId;
                        document.getElementById("orderEditForm").dataset.customerName = fullName;

                        document.getElementById("customer-orders-section").classList.add("d-none");
                    });

                    tbody.appendChild(row); // Zeile zur Bestelltabelle hinzufügen
                });
            });
    }

    // Zeigt oder versteckt die Details (Produkte) zu einer bestimmten Bestellung
    function toggleAdminOrderDetails(orderId, row) {
        const nextRow = row.nextElementSibling;

        if (nextRow && nextRow.classList.contains("order-details")) {
            nextRow.remove(); // Details ausblenden
            row.querySelector(".btn-outline-primary").textContent = "Details";
            return;
        }

        fetch(`../../backend/api/get_order_details.php?order_id=${orderId}`)
            .then((res) => res.json())
            .then((data) => {
                const detailRow = document.createElement("tr");
                detailRow.classList.add("order-details");

                // Erzeugt eine neue Zeile mit Produktdetails als Liste
                detailRow.innerHTML = `
                <td colspan="6">
                    <strong>Produkte:</strong>
                    <ul>
                        ${data.items.map(
                    (i) => `<li>${i.name} (${i.quantity} × ${parseFloat(i.unit_price).toFixed(2)} €)</li>`
                ).join("")}
                    </ul>
                </td>
            `;
                row.parentNode.insertBefore(detailRow, row.nextSibling);
                row.querySelector(".btn-outline-primary").textContent = "Verbergen";
            });
    }

    // Zurück zur Kundenübersicht aus der Bestellansicht
    document.getElementById("back-to-customers").addEventListener("click", () => {
        document.getElementById("customer-orders-section").classList.add("d-none");
        document.getElementById("customer-table").classList.remove("d-none");
    });

    // Speichert Änderungen an einer Bestellung (Rechnungsnummer, Datum)
    document.getElementById("orderEditForm").addEventListener("submit", (event) => {
        event.preventDefault(); // Standardverhalten (Seitenreload) verhindern

        const orderId = document.getElementById("edit-order-id").value;
        const invoiceNumber = document.getElementById("edit-invoice-number").value;
        const orderDate = document.getElementById("edit-order-date").value;

        fetch("../../backend/api/update_order.php", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("token"),
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id: orderId,
                invoice_number: invoiceNumber,
                order_date: orderDate
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Bestellung wurde erfolgreich aktualisiert.");

                    // Formular schließen, Ansicht zurücksetzen und Liste neu laden
                    document.getElementById("orderEditFormContainer").classList.add("d-none");
                    document.getElementById("customer-orders-section").classList.remove("d-none");

                    const customerId = document.getElementById("orderEditForm").dataset.customerId;
                    const customerName = document.getElementById("orderEditForm").dataset.customerName;
                    showCustomerOrders(customerId, customerName);
                } else {
                    alert("Fehler: " + (result.error || "Unbekannter Fehler"));
                }
            })
            .catch(error => {
                console.error("Fehler beim Speichern:", error);
                alert("Technischer Fehler beim Speichern.");
            });
    });

    // Schließt das Bearbeitungsformular ohne zu speichern
    document.getElementById("cancelEditOrderBtn").addEventListener("click", () => {
        document.getElementById("orderEditFormContainer").classList.add("d-none");
        document.getElementById("customer-orders-section").classList.remove("d-none");
    });

    // Löscht eine Bestellung nach Bestätigung
    window.deleteOrder = (orderId, customerId, customerName) => {
        if (!confirm("Möchten Sie diese Bestellung wirklich löschen?")) return;

        fetch("../../backend/api/delete_order.php", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("token"),
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: orderId })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Bestellung erfolgreich gelöscht.");
                    showCustomerOrders(customerId, customerName); // Liste neu laden
                } else {
                    alert("Fehler: " + (result.error || "Unbekannter Fehler"));
                }
            })
            .catch(error => {
                console.error("Fehler beim Löschen der Bestellung:", error);
                alert("Technischer Fehler beim Löschen.");
            });
    };
});
