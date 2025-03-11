<?php
session_start();
require_once '../private/config.php';

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["unlock"])) {
    $email = $_POST["email"];

    // Check if user exists before proceeding
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // User exists, proceed with unlocking
        $stmt_unlock = $conn->prepare("UPDATE users SET failed_attempts = 0, lockout_time = NULL WHERE email = ?");
        $stmt_unlock->bind_param("s", $email);
        $stmt_unlock->execute();

        // Check if the update was successful
        if ($stmt_unlock->affected_rows > 0) {
            header("Location: admin_unlock.php?success=" . urlencode("User unlocked successfully."));
        } else {
            header("Location: admin_unlock.php?error=" . urlencode("Failed to unlock the user. Please try again."));
        }
    } else {
        // User does not exist
        header("Location: admin_unlock.php?error=" . urlencode("No user found with that email address."));
    }
    exit();
}
