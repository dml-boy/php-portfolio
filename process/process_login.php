<?php
session_start();
require_once '../private/config.php';
require_once '../src/user.php';

use App\User;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check for empty fields
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=" . urlencode("Email and password are required"));
        exit();
    }

    $user = new User($conn);

    // Check if the user exists
    $stmt = $conn->prepare("SELECT id, password, failed_attempts, lockout_time FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password, $failed_attempts, $lockout_time);
    $stmt->fetch();
    $stmt->close();

    // If no user is found
    if (!$user_id) {
        header("Location: login.php?error=" . urlencode("Invalid email or password"));
        exit();
    }

    // Check if the user is locked out
    if ($failed_attempts >= 5 && strtotime($lockout_time) > time() - (15 * 60)) {
        header("Location: login.php?error=" . urlencode("Too many failed attempts. Try again in 15 minutes."));
        exit();
    }

    // Check if the entered password is correct
    if (password_verify($password, $hashed_password)) {
        // Reset failed attempts on successful login
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Store user information in session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;

        header("Location: profile.php");
        exit();
    } else {
        // Increase failed attempts and set lockout time if necessary
        $failed_attempts++;
        if ($failed_attempts >= 5) {
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, lockout_time = NOW() WHERE email = ?");
        } else {
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = ? WHERE email = ?");
        }
        $stmt->bind_param("is", $failed_attempts, $email);
        $stmt->execute();
        $stmt->close();

        header("Location: login.php?error=" . urlencode("Invalid email or password"));
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
