<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | DML Dev</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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

        .register-card {
            background: var(--darker);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 480px;
        }

        .register-header h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light);
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

        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .register-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .register-footer a:hover {
            opacity: 0.8;
        }

        .input-group .btn {
            border-radius: 0 0.375rem 0.375rem 0;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <div class="register-header text-center">
            <h2>Create an Account</h2>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="../process/process_register.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required maxlength="50">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    required
                    maxlength="100"
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                    title="Enter a valid email address"
                    autocomplete="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required minlength="6" maxlength="128" autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                        <i class="bi bi-eye-slash" id="password-toggle"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6" maxlength="128" autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">
                        <i class="bi bi-eye-slash" id="confirm_password-toggle"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-4">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input type="file"
                    name="profile_pic"
                    id="profile_pic"
                    class="form-control"
                    accept="image/*"
                    required
                    onchange="previewProfilePic(event)">
            </div>
            <img id="preview-img" src="uploads/default.png" alt="Image Preview" class="mt-3 rounded-circle" style="max-width: 150px; display: none;">

            <button type="submit" class="btn btn-primary w-100">Register Now</button>
        </form>

        <div class="register-footer mt-4">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        }

        function previewProfilePic(event) {
            const input = event.target;
            const preview = document.getElementById('preview-img');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>