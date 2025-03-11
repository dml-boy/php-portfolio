<?php
session_start();
require '../private/config.php';
require '../src/User.php';

use App\User;

$user = new User($conn);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $profile_pic = null; // Default to null, in case no file is uploaded.

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile_pic']['name']);
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Validate file type and size (example: only allow jpg, png, jpeg files, and limit size to 5MB)
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (in_array($fileType, $allowedTypes) && $_FILES['profile_pic']['size'] <= $maxFileSize) {
            // Create a unique file name to avoid overwriting
            $uniqueName = uniqid('profile_', true) . '.' . $fileType;
            $uploadFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
                $profile_pic = $uniqueName;
            } else {
                $error = "Error uploading the file.";
            }
        } else {
            $error = "Invalid file type or size.";
        }
    }

    // Update user profile
    $user->updateProfile($user_id, $username, $bio, $profile_pic);

    header("Location: profile.php?id=$user_id");
    exit();
}

$user_data = $user->getUserById($user_id);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>

        <!-- Display error message if any -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" id="bio" name="bio"><?= htmlspecialchars($user_data['bio'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input class="form-control" type="file" id="profile_pic" name="profile_pic">
                <small class="form-text text-muted">Allowed formats: jpg, jpeg, png. Max size: 5MB.</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>

</html>