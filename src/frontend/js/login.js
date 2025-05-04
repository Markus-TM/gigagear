document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;

    const response = await fetch('../../backend/api/login_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password, remember })
    });

    const data = await response.json();

    if (response.ok) {
        alert('Login erfolgreich!');
        localStorage.setItem('token', data.token);
        localStorage.setItem('username', data.username);
        localStorage.setItem('role', data.role);
        window.location.href = '../sites/imprint.php';
    } else {
        alert(data.error || 'Login fehlgeschlagen');
    }
});
