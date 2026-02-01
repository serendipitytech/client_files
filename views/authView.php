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
            <h1 class="landing-title">Production Asset Management</h1>
            <p class="landing-subtitle">Your centralized portal for event production files</p>
        </div>

        <!-- Main Content Card -->
        <div class="landing-card">
            <div class="landing-intro">
                <p>
                    Live event productions require precise coordination of digital assets—walk-in music,
                    presentation decks, testimonial videos, and welcome montages. This portal eliminates
                    version confusion and last-minute scrambles by providing a single, organized location
                    for all your production files.
                </p>
            </div>

            <div class="landing-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Easy Uploads</h3>
                        <p>Submit audio, video, and presentation files directly to your event space</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Review & Approval</h3>
                        <p>Production staff reviews submissions and confirms final versions</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Version Control</h3>
                        <p>Full history ensures everyone knows which version is show-ready</p>
                    </div>
                </div>
            </div>

            <div class="landing-cta">
                <button onclick="openLoginModal()" class="btn btn-large">
                    <i class="fas fa-right-to-bracket"></i>
                    Sign In to Your Portal
                </button>
                <p class="landing-help">
                    Use your event code to access your files, or sign in as an administrator.
                </p>
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
        /* Landing Page Styles */
        body {
            justify-content: center;
            align-items: center;
            padding: var(--space-xl);
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
            max-width: 680px;
            margin: 0 auto;
            text-align: center;
            animation: fadeInUp 0.6s var(--transition-bounce);
        }

        .landing-hero {
            margin-bottom: var(--space-2xl);
        }

        .landing-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--color-accent) 0%, #5856d6 100%);
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-xl);
            box-shadow: var(--shadow-lg);
        }

        .landing-logo i {
            font-size: 3rem;
            color: white;
        }

        .landing-title {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: var(--space-sm);
            background: linear-gradient(135deg, var(--color-text-primary) 0%, var(--color-text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .landing-subtitle {
            font-size: 1.25rem;
            color: var(--color-text-tertiary);
        }

        .landing-card {
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: var(--space-2xl);
            box-shadow: var(--shadow-md);
        }

        .landing-intro {
            margin-bottom: var(--space-xl);
        }

        .landing-intro p {
            font-size: 1.0625rem;
            line-height: 1.7;
            color: var(--color-text-secondary);
        }

        .landing-features {
            display: flex;
            flex-direction: column;
            gap: var(--space-lg);
            margin-bottom: var(--space-2xl);
            padding: var(--space-xl) 0;
            border-top: 1px solid var(--color-border);
            border-bottom: 1px solid var(--color-border);
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: var(--space-md);
            text-align: left;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            background: var(--color-accent-light);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon i {
            font-size: 1.25rem;
            color: var(--color-accent);
        }

        .feature-text h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .feature-text p {
            font-size: 0.9375rem;
            color: var(--color-text-tertiary);
            margin: 0;
        }

        .landing-cta {
            text-align: center;
        }

        .btn-large {
            padding: var(--space-md) var(--space-2xl);
            font-size: 1.0625rem;
        }

        .landing-help {
            font-size: 0.875rem;
            color: var(--color-text-tertiary);
            margin-top: var(--space-md);
        }

        .landing-footer {
            margin-top: var(--space-xl);
        }

        .landing-footer p {
            font-size: 0.8125rem;
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

        /* Responsive */
        @media (max-width: 640px) {
            .landing-title {
                font-size: 1.75rem;
            }

            .landing-card {
                padding: var(--space-xl);
            }

            .feature-item {
                flex-direction: column;
                text-align: center;
            }

            .feature-icon {
                margin: 0 auto;
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
