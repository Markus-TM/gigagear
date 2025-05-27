// Authentifizierungs-Token aus dem localStorage abrufen
const token = localStorage.getItem('token');

// Funktion, um die E-Mail-Adresse teilweise zu maskieren (z. B. zu Anzeigezwecken)
function maskEmail(email) {
    const at = email.indexOf("@");
    if (at < 3) return "***" + email.slice(at); // bei sehr kurzen Adressen alles maskieren
    return email.slice(0, 2) + "***" + email.slice(at); // sonst: Anfang zeigen, Rest verstecken
}

// Sobald die Seite geladen ist, werden Benutzerdaten vom Backend abgefragt
document.addEventListener("DOMContentLoaded", () => {
    fetch('../../backend/api/get_user.php', {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token // Authentifizierung per Token
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Fehler beim Abrufen der Daten.');
            return response.json();
        })
        .then(data => {
            // Daten in die Anzeige-Felder einfügen
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = maskEmail(data.email); // E-Mail maskiert anzeigen
            document.getElementById('address').value = data.address;
            document.getElementById('zipcode').value = data.zipcode;
            document.getElementById('city').value = data.city;

            // Daten auch ins Bearbeitungsformular vorbefüllen
            document.getElementById('firstnameEdit').value = data.firstname;
            document.getElementById('lastnameEdit').value = data.lastname;
            document.getElementById('addressEdit').value = data.address;
            document.getElementById('zipcodeEdit').value = data.zipcode;
            document.getElementById('cityEdit').value = data.city;
        })
        .catch(error => {
            alert(error.message); // Fehler anzeigen, z. B. bei ungültigem Token
        });
});

// Klick auf "Bearbeiten" – Anzeige ausblenden, Formular einblenden
document.getElementById('editBtn').addEventListener('click', () => {
    document.getElementById('accountInfo').style.display = 'none'; // Lesemodus ausblenden
    document.getElementById('editForm').classList.remove('d-none'); // Bearbeitungsformular zeigen
});

// Klick auf "Abbrechen" – Formular ausblenden, Anzeige einblenden
document.getElementById('cancelBtn').addEventListener('click', () => {
    document.getElementById('editForm').classList.add('d-none'); // Bearbeitungsformular verbergen
    document.getElementById('accountInfo').style.display = 'block'; // Lesemodus wieder einblenden
});

// Absenden des Bearbeitungsformulars (Profil aktualisieren)
document.getElementById('editForm').addEventListener('submit', (e) => {
    e.preventDefault(); // Seite soll sich nicht neu laden

    // Neue Nutzerdaten aus den Eingabefeldern sammeln
    const data = {
        firstname: document.getElementById('firstnameEdit').value,
        lastname: document.getElementById('lastnameEdit').value,
        address: document.getElementById('addressEdit').value,
        zipcode: document.getElementById('zipcodeEdit').value,
        city: document.getElementById('cityEdit').value,
        password: document.getElementById('passwordConfirm').value // zur Verifizierung
    };

    // Daten an das Backend senden (update_user.php)
    fetch('../../backend/api/update_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            // Fehler vom Server anzeigen (z. B. ungültige Eingabe)
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || 'Ein Fehler ist aufgetreten.');
                });
            }
            return response.json();
        })
        .then(result => {
            alert(result.message); // Erfolgsmeldung anzeigen
            location.reload();     // Seite neu laden, um aktualisierte Daten zu sehen
        })
        .catch(error => {
            alert(error.message); // Fehler ausgeben (z. B. bei fehlendem Passwort)
        });
});
