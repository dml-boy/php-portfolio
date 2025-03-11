<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | DML Dev</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .register-card {
            background: var(--darker);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            margin: auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .register-header h2 {
            color: var(--primary);
            margin: 0;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .alert-danger {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            margin-bottom: 1.5rem;
        }

        .register-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .register-footer a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<body>
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <h2>Create an Account</h2>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="../process/process_register.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                            <i class="bi bi-eye-slash" id="password-toggle"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">
                            <i class="bi bi-eye-slash" id="confirm-password-toggle"></i>
                        </button>
                    </div>
                </div>

                <script>
                    function togglePassword(id) {
                        const passwordField = document.getElementById(id);
                        const toggleIcon = document.getElementById(id + '-toggle');
                        if (passwordField.type === "password") {
                            passwordField.type = "text";
                            toggleIcon.classList.remove("bi-eye-slash");
                            toggleIcon.classList.add("bi-eye");
                        } else {
                            passwordField.type = "password";
                            toggleIcon.classList.remove("bi-eye");
                            toggleIcon.classList.add("bi-eye-slash");
                        }
                    }
                </script>


                <div class="mb-4">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file"
                        name="profile_pic"
                        id="profile_pic"
                        class="form-control"
                        accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Register Now
                </button>
            </form>

            <div class="register-footer mt-4">
                <p>Already have an account?
                    <a href="login.php">Login here</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>