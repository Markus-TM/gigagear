// Wird ausgeführt, sobald das DOM vollständig geladen ist
document.addEventListener("DOMContentLoaded", () => {
    // Prüfen, ob der Benutzer eingeloggt ist (user_id im localStorage)
    const userId = parseInt(localStorage.getItem("user_id") || "0");
    if (!userId) return; // Falls kein Login → keine Anfrage senden

    // Holt alle Bestellungen des eingeloggten Benutzers
    fetch(`../../backend/api/get_orders.php?user_id=${userId}`, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem("token") // Auth-Token mitsenden
        }
    })
        .then(res => res.json())
        .then(orders => {
            const tbody = document.getElementById("order-list");
            tbody.innerHTML = ""; // Alte Inhalte leeren

            // Wenn keine Bestellungen vorhanden sind, entsprechende Nachricht anzeigen
            if (orders.length === 0) {
                tbody.innerHTML = "<tr><td colspan='5'>Keine Bestellungen vorhanden.</td></tr>";
                return;
            }

            const total = orders.length;

            // Bestellungen werden umgekehrt angezeigt: Neueste oben
            orders.slice().reverse().forEach((order, index) => {
                const row = document.createElement("tr");

                // HTML-Zeile mit Rechnungsinformationen und Aktionsbuttons
                row.innerHTML = `
                <td>${index + 1}</td>
                <td>${order.invoice_number}</td>
                <td>${order.order_date}</td>
                <td>${parseFloat(order.total_price).toFixed(2)} €</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="printInvoice(${order.id})">🖨️</button>
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="toggleDetails(${order.id}, this)">Details anzeigen</button>
                </td>
            `;

                // Nur die ersten 3 Bestellungen direkt anzeigen – Rest wird versteckt
                if (index >= 3) {
                    row.classList.add("extra-order");
                    row.style.display = "none";
                }

                tbody.appendChild(row);
            });

            // "Mehr anzeigen" Button einfügen, wenn mehr als 3 Bestellungen vorhanden
            if (orders.length > 3) {
                const toggleBtnRow = document.createElement("tr");
                toggleBtnRow.innerHTML = `
                <td colspan="5" class="text-center">
                    <button id="toggleMoreOrders" class="btn btn-secondary btn-sm">Mehr anzeigen</button>
                </td>
            `;
                tbody.appendChild(toggleBtnRow);

                // Umschaltfunktion für versteckte Bestellungen
                document.getElementById("toggleMoreOrders").addEventListener("click", function () {
                    const hiddenRows = document.querySelectorAll(".extra-order");
                    const expanded = hiddenRows[0].style.display !== "none";

                    hiddenRows.forEach(row => {
                        row.style.display = expanded ? "none" : "";
                    });

                    this.textContent = expanded ? "Mehr anzeigen" : "Weniger anzeigen";
                });
            }
        });
});


// Generiert und speichert eine PDF-Rechnung über jsPDF
async function printInvoice(orderId) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    try {
        // Holt Rechnungs- und Bestelldaten vom Server
        const response = await fetch(`../../backend/api/get_order_details.php?order_id=${orderId}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });

        const data = await response.json();
        const order = data.order || {};
        const items = data.items || [];

        let y = 20; // vertikale Position im PDF

        doc.setFontSize(10);

        // Firmenadresse (oben rechts)
        doc.text("Gigagear", 150, y);
        doc.text("Marxergasse 1", 150, y + 5);
        doc.text("1030 Wien", 150, y + 10);
        y += 20;

        // Kundendaten
        doc.setFont(undefined, "bold");
        doc.text("Rechnungsempfänger:", 20, y);
        doc.setFont(undefined, "normal");

        const { firstname = "", lastname = "", address = "", zipcode = "", city = "" } = order;

        doc.text(`${firstname} ${lastname}`, 20, y + 5);
        doc.text(address, 20, y + 10);
        doc.text(`${zipcode} ${city}`, 20, y + 15);

        y += 25;

        // Titel und Rechnungsinfos
        doc.setFontSize(12);
        doc.setFont(undefined, "bold");
        doc.text("Rechnung", 105, y, { align: "center" });
        y += 10;

        doc.setFontSize(10);
        doc.setFont(undefined, "normal");
        doc.text(`Rechnungsnummer: ${order.invoice_number}`, 20, y);
        const onlyDate = (order.order_date || "").split(" ")[0];
        doc.text(`Datum: ${onlyDate}`, 150, y);
        y += 10;

        doc.text("Vielen Dank für Ihre Bestellung. Hier die Übersicht:", 20, y);
        y += 10;

        // Tabellenkopf
        doc.setFont(undefined, "bold");
        doc.text("Pos", 20, y);
        doc.text("Produkt", 35, y);
        doc.text("Menge", 120, y);
        doc.text("Einzelpreis", 140, y);
        doc.text("Gesamt", 170, y);
        y += 5;
        doc.setLineWidth(0.3);
        doc.line(20, y, 190, y);
        y += 5;

        // Artikelzeilen
        doc.setFont(undefined, "normal");
        let total = 0;
        items.forEach((item, index) => {
            const price = parseFloat(item.unit_price) * item.quantity;
            total += price;
            doc.text(`${index + 1}`, 20, y);
            const wrappedText = doc.splitTextToSize(item.name, 80);
            doc.text(wrappedText, 35, y);
            y += wrappedText.length * 1.5;

            doc.text(`${item.quantity}`, 130, y, { align: "right" });
            doc.text(`${parseFloat(item.unit_price).toFixed(2)} €`, 160, y, { align: "right" });
            doc.text(`${price.toFixed(2)} €`, 190, y, { align: "right" });
            y += 7;
        });

        // Gesamtbetrag
        y += 5;
        doc.line(20, y, 190, y);
        y += 5;
        doc.setFont(undefined, "bold");
        doc.text("Gesamtbetrag:", 140, y);
        const safeTotal = isNaN(total) ? "0.00" : total.toFixed(2);
        doc.text(`${safeTotal} €`, 190, y, { align: "right" });

        // Fußzeile mit rechtlichen Angaben
        y = 270;
        doc.setFontSize(8);
        doc.setFont(undefined, "normal");
        doc.text("Gigagear GmbH • Marxergasse 1 • 1030 Wien", 20, y);
        doc.text("UID: ATU68472908 • FN: 395284k • Geschäftsführer: Enes Demir", 20, y + 5);
        doc.text("E-Mail: info@gigagear.at • Mitglied der WKO/WKW", 20, y + 10);

        doc.save(`Rechnung_${order.invoice_number || orderId}.pdf`); // PDF speichern
    } catch (err) {
        alert("Fehler beim Laden der Rechnungsdaten.");
        console.error(err);
    }
}

// Klappt Produktdetails einer Bestellung auf oder zu
function toggleDetails(orderId, btn) {
    const row = btn.closest("tr");
    const nextRow = row.nextElementSibling;

    // Wenn Details bereits angezeigt → ausblenden
    if (nextRow && nextRow.classList.contains("order-details")) {
        nextRow.remove();
        btn.textContent = "Details anzeigen";
        return;
    }

    // Details vom Server holen und unterhalb einfügen
    fetch(`../../backend/api/get_order_details.php?order_id=${orderId}`)
        .then(res => res.json())
        .then(data => {
            const detailRow = document.createElement("tr");
            detailRow.classList.add("order-details");
            detailRow.innerHTML = `
                <td colspan="5">
                    <strong>Produkte:</strong>
                    <ul style="margin-top: 10px;">
                        ${data.items.map(i => `<li>${i.name} (${i.quantity} × ${parseFloat(i.unit_price).toFixed(2)} €)</li>`).join("")}
                    </ul>
                </td>
            `;
            row.parentNode.insertBefore(detailRow, row.nextSibling);
            btn.textContent = "Verbergen";
        });
}
