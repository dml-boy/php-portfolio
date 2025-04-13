<?php
session_start();
$error = $_GET['error'] ?? '';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | DML Dev</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <style>
        :root {
            --primary: #f39c12;
            --dark: #121212;
            --darker: #1a1a1a;
            --light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background: var(--darker);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            max-width: 420px;
            width: 100%;
            padding: 2rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header h4 {
            color: var(--primary);
            font-weight: 600;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light);
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.2);
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            font-weight: 600;
            padding: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .alert-danger {
            background-color: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }

        .login-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .login-footer a:hover {
            opacity: 0.8;
        }

        .position-relative .password-toggle {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <main class="login-card">
        <header class="login-header">
            <h4>Welcome Back</h4>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="../process/process_login.php" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    required
                    maxlength="100"
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                    title="Enter a valid email address"
                    autocomplete="email"
                    autofocus />
            </div>

            <div class="mb-4 position-relative">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    required
                    minlength="6"
                    maxlength="128"
                    title="Password must be at least 6 characters"
                    autocomplete="off" />
                <i class="bi bi-eye password-toggle my-3" id="togglePassword" role="button" aria-label="Toggle password visibility"></i>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">Sign In</button>
        </form>

        <footer class="login-footer mt-4">
            <p>Don't have an account? <a href="register.php">Create one</a></p>
            <p><a href="forgot-password.php">Forgot password?</a></p>
        </footer>
    </main>

    <script>
        const toggle = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        toggle.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            toggle.classList.toggle('bi-eye');
            toggle.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>