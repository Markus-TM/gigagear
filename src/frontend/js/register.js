document.getElementById("form").addEventListener("submit", async function (e) {
    e.preventDefault();
    
    // Password match check
    if (document.getElementById("password").value !== 
        document.getElementById("passwordconf").value) {
        alert("Passwords don't match!");
        return;
    }

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch("../../backend/api/register_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.error) {
            alert("Error: " + result.error);
        } else {
            alert(result.message);
            //Redirect to login
            window.location.href = "login.php"; 
        }
    } catch (error) {
        alert("Network error - please try again");
        console.error(error);
    }
});