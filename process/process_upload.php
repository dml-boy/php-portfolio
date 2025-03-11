<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $upload_dir = __DIR__ . '/../public/uploads/';

    // Create the uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    $max_file_size = 5 * 1024 * 1024; // 5MB

    // Check file size
    if ($_FILES["profile_pic"]["size"] > $max_file_size) {
        $_SESSION['error'] = "File size is too large. Maximum size allowed is 5MB.";
    }
    // Check if the file type is allowed
    elseif (!in_array($imageFileType, $allowed_types)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
    // Check if the file is an actual image
    elseif (!getimagesize($_FILES["profile_pic"]["tmp_name"])) {
        $_SESSION['error'] = "File is not a valid image.";
    }
    // Move uploaded file to the target directory
    elseif (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        // Sanitize the filename to avoid special characters
        $sanitized_file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $sanitized_file_name, $user_id);
        $stmt->execute();

        // Success message
        $_SESSION['success'] = "Profile picture updated successfully!";
    } else {
        $_SESSION['error'] = "Error uploading file.";
    }
}

header("Location: profile.php");
exit();
