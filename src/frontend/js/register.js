// Eventlistener beim Absenden des Formulars
document.getElementById("form").addEventListener("submit", async function (e) {
    e.preventDefault(); // Standardverhalten (Reload) verhindern

    // Passwort-Abgleich
    if (document.getElementById("password").value !==
        document.getElementById("passwordconf").value) {
        alert("Passwords don't match!");
        return; // Abbrechen, wenn Passwörter nicht übereinstimmen
    }

    // Formulardaten auslesen
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries()); // In Objekt umwandeln

    try {
        // Daten an Backend senden
        const response = await fetch("../../backend/api/register_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" }, // JSON als Format
            body: JSON.stringify(data)
        });

        // Antwort in JSON umwandeln
        const result = await response.json();

        if (result.error) {
            alert("Error: " + result.error); // Server-Fehlermeldung anzeigen
        } else {
            alert(result.message); // Erfolgsnachricht
            window.location.href = "login.php"; // Weiterleitung zum Login
        }

    } catch (error) {
        alert("Network error - please try again"); 
        console.error(error); // Fehler für Entwickler in der Konsole
    }
});
