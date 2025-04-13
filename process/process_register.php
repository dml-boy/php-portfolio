<?php
session_start();
require_once '../private/config.php';
require_once '../src/user.php';

use App\User;

// Enable dev error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user = new User($conn);

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/register.php");
    exit();
}

// CSRF token validation
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    header("Location: ../public/register.php?error=" . urlencode("Invalid CSRF token"));
    exit();
}

// Sanitize and validate inputs
$username = trim($_POST['username'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validations
if (!$username || !$email || !$password || !$confirm_password) {
    header("Location: ../public/register.php?error=" . urlencode("All fields are required"));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../public/register.php?error=" . urlencode("Invalid email address"));
    exit();
}

if ($password !== $confirm_password) {
    header("Location: ../public/register.php?error=" . urlencode("Passwords do not match"));
    exit();
}

// Default profile picture
$profile_pic = "default.png";

// Handle profile picture upload if present
if (!empty($_FILES["profile_pic"]["name"])) {
    $upload_dir = realpath(__DIR__ . '/../public\uploads') . '/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }

    $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . $file_name;
    $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check === false) {
        header("Location: ../public/register.php?error=" . urlencode("Invalid image file"));
        exit();
    }

    // Limit size (max 2MB)
    if ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) {
        header("Location: ../public/register.php?error=" . urlencode("Image must be under 2MB"));
        exit();
    }

    // Restrict file types
    $allowed = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($image_type, $allowed)) {
        header("Location: ../public/register.php?error=" . urlencode("Only JPG, JPEG, PNG, and GIF allowed"));
        exit();
    }

    // Save the file
    if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        header("Location: ../public/register.php?error=" . urlencode("File upload failed"));
        exit();
    }

    $profile_pic = $file_name;
}

// Register user
$register_result = $user->register($username, $email, $password, $profile_pic);

if ($register_result === true) {
    $_SESSION['user_id'] = $user->getUserIdByEmail($email);
    header("Location: ../public/profile.php");
    exit();
} else {
    header("Location: ../public/register.php?error=" . urlencode($register_result));
    exit();
}
