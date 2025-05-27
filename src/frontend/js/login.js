// Event-Listener: Reagiert auf Absenden des Login-Formulars
document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // Verhindert das Standardverhalten (Seitenreload)

    // Eingabewerte aus dem Formular auslesen
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked; // Checkbox für „angemeldet bleiben“

    // API-Request an das Backend zur Login-Prüfung
    const response = await fetch('../../backend/api/login_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // Wir senden JSON-Daten
        },
        body: JSON.stringify({ username, password, remember }) // Daten an Server senden
    });

    const data = await response.json(); // Antwort als JSON interpretieren

    if (response.ok) {
        // Erfolgreicher Login
        alert('Login erfolgreich!');

        // Wichtige Nutzerdaten im localStorage speichern (für spätere Nutzung im Frontend)
        localStorage.setItem('token', data.token);           // Authentifizierungstoken
        localStorage.setItem('username', data.username);     // Nutzername
        localStorage.setItem('role', data.role);             // Benutzerrolle (z. B. admin/user)
        localStorage.setItem('user_id', data.user_id);       // ID zur Identifizierung im System

        // Weiterleitung zur geschützten Seite nach erfolgreichem Login
        window.location.href = '../sites/imprint.php';
    } else {
        // Fehlerausgabe bei ungültigen Zugangsdaten o.ä.
        alert(data.error || 'Login fehlgeschlagen');
    }
});
