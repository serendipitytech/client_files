<?php
$loginType = $_GET['type'] ?? 'client';
$showLoginModal = isset($_GET['login']) && $_GET['login'] === 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Asset Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Landing Page -->
    <div class="landing-page">
        <!-- Hero Section -->
        <div class="landing-hero">
            <div class="landing-logo">
                <i class="fas fa-film"></i>
            </div>
            <div class="landing-hero-text">
                <h1 class="landing-title">Production Asset Management</h1>
                <p class="landing-subtitle">Your centralized portal for event production files</p>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="landing-card">
            <div class="landing-content">
                <div class="landing-intro">
                    <p>
                        Eliminate version confusion and last-minute scrambles. Submit all your production
                        files—walk-in music, presentations, videos—to one organized location for review and approval.
                    </p>
                    <div class="landing-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>
                            <div class="feature-text">
                                <h3>Easy Uploads</h3>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-text">
                                <h3>Review & Approval</h3>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <div class="feature-text">
                                <h3>Version Control</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="landing-cta">
                    <button onclick="openLoginModal()" class="btn btn-large">
                        <i class="fas fa-right-to-bracket"></i>
                        Sign In
                    </button>
                    <p class="landing-help">
                        Use your event code or admin credentials
                    </p>
                </div>
            </div>
        </div>

        <footer class="landing-footer">
            <p>Powered by Serendipity Technology</p>
        </footer>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal <?php echo $showLoginModal ? 'show' : ''; ?>">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>

            <!-- Login Type Toggle -->
            <div class="auth-toggle">
                <button
                    onclick="showLoginType('client')"
                    class="auth-toggle-btn <?php echo $loginType === 'client' ? 'active' : ''; ?>"
                    id="clientToggle"
                >
                    <i class="fas fa-user"></i>
                    Client
                </button>
                <button
                    onclick="showLoginType('admin')"
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
                class="<?php echo $loginType !== 'client' ? 'hidden' : ''; ?>"
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
                <button type="submit" name="client_login" class="btn" style="width: 100%;">
                    <i class="fas fa-arrow-right"></i>
                    Access My Files
                </button>
                <p class="text-muted" style="font-size: 0.875rem; margin-top: var(--space-md); text-align: center;">
                    Enter the event code provided by your production team
                </p>
            </form>

            <!-- Admin Login Form -->
            <form
                id="adminLoginForm"
                action="controllers/AuthController.php?type=admin"
                method="post"
                class="<?php echo $loginType !== 'admin' ? 'hidden' : ''; ?>"
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
                <button type="submit" name="admin_login" class="btn" style="width: 100%;">
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
    </div>

    <style>
        /* Landing Page Styles - Compact Horizontal Layout */
        body {
            justify-content: center;
            align-items: center;
            padding: var(--space-lg);
        }

        body::before {
            background-image:
                radial-gradient(ellipse at top, rgba(0, 113, 227, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse at bottom right, rgba(88, 86, 214, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 1px 1px, rgba(0,0,0,0.025) 1px, transparent 0);
            background-size: 100% 100%, 100% 100%, 32px 32px;
        }

        .landing-page {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
            animation: fadeInUp 0.5s var(--transition-bounce);
        }

        .landing-hero {
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            margin-bottom: var(--space-lg);
        }

        .landing-logo {
            width: 64px;
            height: 64px;
            min-width: 64px;
            background: linear-gradient(135deg, var(--color-accent) 0%, #5856d6 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
        }

        .landing-logo i {
            font-size: 1.75rem;
            color: white;
        }

        .landing-hero-text {
            text-align: left;
        }

        .landing-title {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 2px;
            color: var(--color-text-primary);
        }

        .landing-subtitle {
            font-size: 1rem;
            color: var(--color-text-tertiary);
        }

        .landing-card {
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: var(--space-xl);
            box-shadow: var(--shadow-sm);
        }

        .landing-content {
            display: flex;
            gap: var(--space-xl);
            align-items: flex-start;
        }

        .landing-intro {
            flex: 1;
        }

        .landing-intro p {
            font-size: 0.9375rem;
            line-height: 1.6;
            color: var(--color-text-secondary);
            margin-bottom: var(--space-md);
        }

        .landing-features {
            display: flex;
            gap: var(--space-lg);
            margin-top: var(--space-md);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;
            background: var(--color-accent-light);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon i {
            font-size: 0.875rem;
            color: var(--color-accent);
        }

        .feature-text h3 {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-text-primary);
            margin: 0;
        }

        .landing-cta {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-left: var(--space-xl);
            border-left: 1px solid var(--color-border);
            min-width: 200px;
        }

        .btn-large {
            padding: var(--space-md) var(--space-xl);
            font-size: 0.9375rem;
            white-space: nowrap;
        }

        .landing-help {
            font-size: 0.75rem;
            color: var(--color-text-tertiary);
            margin-top: var(--space-sm);
            text-align: center;
        }

        .landing-footer {
            margin-top: var(--space-lg);
            text-align: center;
        }

        .landing-footer p {
            font-size: 0.75rem;
            color: var(--color-text-tertiary);
        }

        /* Login Modal Overrides */
        .modal.show {
            display: flex;
        }

        .auth-toggle {
            display: flex;
            background: var(--color-bg-primary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 4px;
            margin-bottom: var(--space-xl);
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

        /* Responsive - stack on smaller screens */
        @media (max-width: 768px) {
            .landing-content {
                flex-direction: column;
            }

            .landing-cta {
                padding-left: 0;
                padding-top: var(--space-lg);
                border-left: none;
                border-top: 1px solid var(--color-border);
                width: 100%;
                min-width: auto;
            }

            .landing-features {
                flex-direction: column;
                gap: var(--space-sm);
            }

            .landing-hero {
                flex-direction: column;
                text-align: center;
            }

            .landing-hero-text {
                text-align: center;
            }
        }
    </style>

    <script>
        function openLoginModal() {
            document.getElementById("loginModal").style.display = "flex";
            // Update URL
            history.pushState(null, '', 'index.php?page=auth&login=true');
        }

        function closeLoginModal() {
            document.getElementById("loginModal").style.display = "none";
            // Update URL
            history.pushState(null, '', 'index.php?page=auth');
        }

        function showLoginType(type) {
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
        }

        // Auto-open modal if login=true in URL or if there's an error
        <?php if ($showLoginModal || isset($errorMessage)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("loginModal").style.display = "flex";
        });
        <?php endif; ?>

        // Close modal on outside click
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoginModal();
            }
        });
    </script>
</body>
</html>
