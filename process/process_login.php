<?php
session_start();
require_once '../private/config.php';
require_once '../src/user.php';

use App\User;

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["login"])) {
    http_response_code(403);
    header("Location: login.php");
    exit();
}

// CSRF Protection
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    header("Location: login.php?error=" . urlencode("Invalid CSRF token."));
    exit();
}

// Input Sanitization
$email = filter_var(trim($_POST["email"] ?? ''), FILTER_VALIDATE_EMAIL);
$password = trim($_POST["password"] ?? '');

if (!$email || empty($password)) {
    header("Location: login.php?error=" . urlencode("Email and password are required."));
    exit();
}

// Rate Limiting & Fetch User
$stmt = $conn->prepare("SELECT id, password, failed_attempts, lockout_time FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: login.php?error=" . urlencode("Invalid email or password."));
    exit();
}

$stmt->bind_result($user_id, $hashed_password, $failed_attempts, $lockout_time);
$stmt->fetch();
$stmt->close();

// Check for lockout (15 minutes)
$lockout_duration = 15 * 60;
if ($failed_attempts >= 5 && strtotime($lockout_time) > time() - $lockout_duration) {
    $remaining = ceil(($lockout_duration - (time() - strtotime($lockout_time))) / 60);
    header("Location: login.php?error=" . urlencode("Too many failed attempts. Try again in {$remaining} minute(s)."));
    exit();
}

// Password Verification
if (password_verify($password, $hashed_password)) {
    $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;

    header("Location: ../public/profile.php");
    exit();
}

// Update Failed Attempts
$failed_attempts++;
$lockout_sql = $failed_attempts >= 5
    ? "UPDATE users SET failed_attempts = ?, lockout_time = NOW() WHERE email = ?"
    : "UPDATE users SET failed_attempts = ? WHERE email = ?";
$stmt = $conn->prepare($lockout_sql);
$stmt->bind_param("is", $failed_attempts, $email);
$stmt->execute();
$stmt->close();

header("Location: login.php?error=" . urlencode("Invalid email or password."));
exit();
