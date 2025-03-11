<?php
require_once '../private/config.php';
require_once '../src/user.php';

use App\User;

$user = new User($conn);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = trim($_POST["username"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=" . urlencode("Invalid email address"));
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=" . urlencode("Passwords do not match"));
        exit();
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_pic = "default.png"; // Default profile picture
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = __DIR__ . "/../public/uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if upload directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0775, true);
        }

        // Check if it's an image and file size (Max 2MB)
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            header("Location: register.php?error=" . urlencode("Invalid image file"));
            exit();
        }

        // Check file size (2MB max)
        if ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) {
            header("Location: register.php?error=" . urlencode("File size exceeds 2MB"));
            exit();
        }

        // Allow only specific image file types
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            header("Location: register.php?error=" . urlencode("Only JPG, JPEG, PNG, and GIF files are allowed"));
            exit();
        }

        // Move the uploaded file
        if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            header("Location: register.php?error=" . urlencode("File upload failed: Error " . $_FILES["profile_pic"]["error"]));
            exit();
        }

        $profile_pic = $file_name;
    }

    // Register user
    if ($user->register($username, $email, $hashed_password, $profile_pic)) {
        session_start();
        $_SESSION['user_id'] = $user->getUserIdByEmail($email); // Store user ID in session
        header("Location: ../public/profile.php");
        exit();
    } else {
        header("Location: ../public/register.php?error=" . urlencode("Registration failed"));
        exit();
    }
}
