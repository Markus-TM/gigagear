//document.addEventListener('DOMContentLoaded', () => {
    // Kundenliste laden
    fetchCustomers();

    // Kunden anzeigen
    function fetchCustomers() {
        fetch("../../backend/api/get_customers.php", {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        .then(response => response.json())
        .then(customers => {
            const customerList = document.getElementById('customer-list');
            customerList.innerHTML = '';

            customers.forEach(customer => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${customer.firstname}</td>
                    <td>${customer.lastname}</td>
                    <td>${customer.email}</td>
                    <td>
                        <button class="btn btn-info" onclick="showOrderDetails(${customer.id})">Details anzeigen</button>
                        <button class="btn btn-danger" onclick="deactivateCustomer(${customer.id})">Deaktivieren</button>
                    </td>
                `;
                customerList.appendChild(row);
            });
        })
        .catch(err => console.error('Fehler beim Laden der Kunden:', err));
    }
/*
    // Bestellübersicht anzeigen
    window.showOrderDetails = function(customerId) {
        fetch(`../../backend/api/get_orders.php?user_id=${customerId}`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        .then(response => response.json())
        .then(orders => {
            const orderTable = document.getElementById('order-details');
            orderTable.innerHTML = '';
            
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>Bestell-Nr: ${order.id}</td>
                    <td>${order.date}</td>
                    <td>
                        <button class="btn btn-warning" onclick="removeProductFromOrder(${order.id}, ${customerId})">Produkt entfernen</button>
                    </td>
                `;
                orderTable.appendChild(row);
            });
            new bootstrap.Modal(document.getElementById('order-modal')).show();
        })
        .catch(err => console.error('Fehler beim Laden der Bestellungen:', err));
    };

    // Kunde deaktivieren
    window.deactivateCustomer = function(customerId) {
        if (confirm('Möchten Sie diesen Kunden wirklich deaktivieren?')) {
            fetch(`../../backend/api/deactivate_customer.php?id=${customerId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    alert('Kunde wurde deaktiviert.');
                    fetchCustomers(); // Aktualisiere die Kundenliste
                } else {
                    alert('Fehler: ' + response.error);
                }
            })
            .catch(err => console.error('Fehler beim Deaktivieren des Kunden:', err));
        }
    };

    // Produkt aus Bestellung entfernen
    window.removeProductFromOrder = function(orderId, customerId) {
        if (confirm('Möchten Sie dieses Produkt wirklich aus der Bestellung entfernen?')) {
            fetch(`../../backend/api/remove_product_from_order.php?order_id=${orderId}&user_id=${customerId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    alert('Produkt wurde entfernt.');
                    showOrderDetails(customerId); // Aktualisiere die Bestellübersicht
                } else {
                    alert('Fehler: ' + response.error);
                }
            })
            .catch(err => console.error('Fehler beim Entfernen des Produkts:', err));
        }
    };
//});*/
