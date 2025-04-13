<?php
// posts.php
session_start();
require '../private/config.php';
require '../src/User.php';

use App\User;

$user = new User($conn);

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

// CSRF setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token.");
    }

    $caption = trim($_POST['caption'] ?? '');
    $image = $_FILES['image'] ?? null;

    // Sanitize caption on output (not now), just ensure it's trimmed
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_name = basename($image['name']);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 5 * 1024 * 1024;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $image['tmp_name']);
        $valid_mimes = ['image/jpeg', 'image/png'];
        finfo_close($finfo);

        if (!in_array($file_extension, $allowed_extensions) || !in_array($mime, $valid_mimes)) {
            $error = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        } elseif ($image['size'] > $max_file_size) {
            $error = "File is too large. Maximum size is 5MB.";
        } else {
            $new_file_name = uniqid('post_', true) . '.' . $file_extension;
            $target_file = $target_dir . $new_file_name;

            if (!move_uploaded_file($image['tmp_name'], $target_file)) {
                $error = "There was an error uploading the file.";
            }
        }
    }

    // If no errors, attempt to save post
    if (!$error) {
        try {
            $user->createPost($_SESSION['user_id'], $caption, $image ? $new_file_name : null);
            $_SESSION['success'] = "Post created successfully!";
            header("Location: profile.php?id=" . $_SESSION['user_id']);
            exit();
        } catch (Exception $e) {
            if (isset($target_file) && file_exists($target_file)) {
                unlink($target_file); // Cleanup if DB save fails
            }
            $error = "Failed to save post. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Create a Post</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label class="form-label">Caption</label>
                <input type="text" name="caption" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Post</button>
        </form>
    </div>
</body>

</html>