const token = localStorage.getItem('token');

function maskEmail(email) {
    const at = email.indexOf("@");
    if (at < 3) return "***" + email.slice(at);
    return email.slice(0, 2) + "***" + email.slice(at);
}

// Daten laden
document.addEventListener("DOMContentLoaded", () => {
    fetch('../../backend/api/get_user.php', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Fehler beim Abrufen der Daten.');
        return response.json();
    })
    .then(data => {
        document.getElementById('username').value = data.username;
        document.getElementById('email').value = maskEmail(data.email);
        document.getElementById('address').value = data.address;
        document.getElementById('zipcode').value = data.zipcode;
        document.getElementById('city').value = data.city;

        // Vorbefüllen fürs Bearbeiten
        document.getElementById('firstnameEdit').value = data.firstname;
        document.getElementById('lastnameEdit').value = data.lastname;
        document.getElementById('addressEdit').value = data.address;
        document.getElementById('zipcodeEdit').value = data.zipcode;
        document.getElementById('cityEdit').value = data.city;
    })
    .catch(error => {
        alert(error.message);
    });
});

// Bearbeiten
document.getElementById('editBtn').addEventListener('click', () => {
    document.getElementById('accountInfo').style.display = 'none';
    document.getElementById('editForm').classList.remove('d-none');
});

// Abbrechen
document.getElementById('cancelBtn').addEventListener('click', () => {
    document.getElementById('editForm').classList.add('d-none');
    document.getElementById('accountInfo').style.display = 'block';
});

// Formular absenden
document.getElementById('editForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const data = {
        firstname: document.getElementById('firstnameEdit').value,
        lastname: document.getElementById('lastnameEdit').value,
        address: document.getElementById('addressEdit').value,
        zipcode: document.getElementById('zipcodeEdit').value,
        city: document.getElementById('cityEdit').value,
        password: document.getElementById('passwordConfirm').value
    };

    fetch('../../backend/api/update_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) return response.json().then(err => { throw new Error(err.error || 'Ein Fehler ist aufgetreten.'); });
        return response.json();
    })
    .then(result => {
        alert(result.message);
        location.reload();
    })
    .catch(error => {
        alert(error.message);
    });
});