<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles.css">
    <script>
    function showLogin(type) {
        document.getElementById("introText").classList.add("hidden"); // Hide intro text
        document.getElementById("adminLoginForm").classList.add("hidden");
        document.getElementById("clientLoginForm").classList.add("hidden");

        if (type === "admin") {
            document.getElementById("adminLoginForm").classList.remove("hidden");
        } else if (type === "client") {
            document.getElementById("clientLoginForm").classList.remove("hidden");
        }
    }
</script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="menu">
            <h2>Login Options</h2>
            <button onclick="showLogin('admin')" class="btn">Admin Login</button>
            <button onclick="showLogin('client')" class="btn">Client Login</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome! Please Log In</h1>
        
        <!-- Intro Text (Shown Initially) -->
        <div id="introText">
            <p>Select an option from the left to log in as an Admin or a Client.</p>
        </div>

        <!-- Admin Login Form (Hidden by Default) -->
        <form id="adminLoginForm" action="../controllers/AuthController.php?type=admin" method="post" class="form-container hidden">
            <h2>Admin Login</h2>
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="admin_login" class="btn">Login as Admin</button>
        </form>

        <!-- Client Login Form (Hidden by Default) -->
        <form id="clientLoginForm" action="../controllers/AuthController.php?type=client" method="post" class="form-container hidden">
            <h2>Client Login</h2>
            <input type="text" name="event_code" placeholder="Event Code" required>
            <button type="submit" name="client_login" class="btn">Login as Client</button>
        </form>
    </div>

</body>
</html>