<?php
// posts.php
session_start();
require '../private/config.php';
require '../src/User.php';

use App\User;

$user = new User($conn);

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'] ?? '';
    $image = $_FILES['image'] ?? null;
    $error = '';

    // Sanitize caption to avoid XSS
    $caption = htmlspecialchars($caption);

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $file_name = basename($image['name']);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 5 * 1024 * 1024; // 5MB max size

        // Validate file type and size
        if (!in_array($file_extension, $allowed_extensions)) {
            $error = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        } elseif ($image['size'] > $max_file_size) {
            $error = "File is too large. Maximum size is 5MB.";
        } else {
            // Rename the file to avoid overwriting
            $new_file_name = uniqid('post_', true) . '.' . $file_extension;
            $target_file = $target_dir . $new_file_name;

            if (!move_uploaded_file($image['tmp_name'], $target_file)) {
                $error = "There was an error uploading the file.";
            }
        }
    }

    // If there were no errors, create the post
    if (!$error) {
        $user->createPost($_SESSION['user_id'], $caption, $image ? $new_file_name : null);
        header("Location: profile.php?id=" . $_SESSION['user_id']);
        exit();
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

        <!-- Display error message if any -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
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