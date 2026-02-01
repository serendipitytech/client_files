<?php
$loginType = $_GET['type'] ?? 'client';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Client Files</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <!-- Logo -->
        <div class="auth-logo">
            <i class="fas fa-folder-open"></i>
        </div>

        <!-- Welcome Text -->
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to access your files</p>

        <!-- Login Type Toggle -->
        <div class="auth-toggle">
            <button
                onclick="showLogin('client')"
                class="auth-toggle-btn <?php echo $loginType === 'client' ? 'active' : ''; ?>"
                id="clientToggle"
            >
                <i class="fas fa-user"></i>
                Client
            </button>
            <button
                onclick="showLogin('admin')"
                class="auth-toggle-btn <?php echo $loginType === 'admin' ? 'active' : ''; ?>"
                id="adminToggle"
            >
                <i class="fas fa-shield-halved"></i>
                Admin
            </button>
        </div>

        <!-- Client Login Form -->
        <form
            id="clientLoginForm"
            action="controllers/AuthController.php?type=client"
            method="post"
            class="form-container <?php echo $loginType !== 'client' ? 'hidden' : ''; ?>"
        >
            <div class="form-group">
                <label for="event_code">Event Code</label>
                <input
                    type="text"
                    name="event_code"
                    id="event_code"
                    placeholder="Enter your event code"
                    required
                    autocomplete="off"
                >
            </div>
            <button type="submit" name="client_login" class="btn">
                <i class="fas fa-arrow-right"></i>
                Access Files
            </button>
            <p class="text-muted" style="font-size: 0.875rem; margin-top: var(--space-md);">
                Enter the event code provided by your administrator
            </p>
        </form>

        <!-- Admin Login Form -->
        <form
            id="adminLoginForm"
            action="controllers/AuthController.php?type=admin"
            method="post"
            class="form-container <?php echo $loginType !== 'admin' ? 'hidden' : ''; ?>"
        >
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Enter username"
                    required
                    autocomplete="username"
                >
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <button type="submit" name="admin_login" class="btn">
                <i class="fas fa-arrow-right"></i>
                Sign In
            </button>
        </form>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error" style="margin-top: var(--space-lg);">
                <i class="fas fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
        /* Auth-specific overrides */
        body {
            justify-content: center;
        }

        body::before {
            background-image:
                radial-gradient(ellipse at top, rgba(0, 113, 227, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 1px 1px, rgba(0,0,0,0.03) 1px, transparent 0);
            background-size: 100% 100%, 32px 32px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .auth-toggle {
            display: flex;
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 4px;
            margin-bottom: var(--space-xl);
            box-shadow: var(--shadow-sm);
        }

        .auth-toggle-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: var(--space-md);
            font-family: inherit;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--color-text-secondary);
            background: transparent;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .auth-toggle-btn:hover {
            color: var(--color-text-primary);
        }

        .auth-toggle-btn.active {
            background: var(--color-accent);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .auth-toggle-btn i {
            font-size: 1rem;
        }

        .form-container {
            text-align: left;
        }

        .form-container .btn {
            width: 100%;
            margin-top: var(--space-sm);
        }
    </style>

    <script>
        function showLogin(type) {
            const clientForm = document.getElementById("clientLoginForm");
            const adminForm = document.getElementById("adminLoginForm");
            const clientToggle = document.getElementById("clientToggle");
            const adminToggle = document.getElementById("adminToggle");

            if (type === "admin") {
                clientForm.classList.add("hidden");
                adminForm.classList.remove("hidden");
                clientToggle.classList.remove("active");
                adminToggle.classList.add("active");
            } else {
                adminForm.classList.add("hidden");
                clientForm.classList.remove("hidden");
                adminToggle.classList.remove("active");
                clientToggle.classList.add("active");
            }

            // Update URL without reload
            history.replaceState(null, '', 'index.php?page=auth&type=' + type);
        }
    </script>
</body>
</html>
