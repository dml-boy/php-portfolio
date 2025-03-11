<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Session error: User not logged in.");
}

// Database connection
require_once '../private/config.php';

$userId = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = trim($_POST['job_title']);
    $jobDescription = trim($_POST['job_description']);
    $jobBudget = trim($_POST['job_budget']);
    $jobDeadline = trim($_POST['job_deadline']);

    // Validate inputs
    if (empty($jobTitle) || empty($jobDescription) || empty($jobBudget) || empty($jobDeadline)) {
        header('Location: jobs.php?error=' . urlencode("Please fill out all required fields"));
        exit;
    }

    // Ensure upload directory exists
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file uploads
    $uploadedFiles = [];
    if (!empty($_FILES['job_files']['name'][0]) && is_uploaded_file($_FILES['job_files']['tmp_name'][0])) {
        foreach ($_FILES['job_files']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['job_files']['name'][$key]);
            $filePath = $uploadDir . time() . "_" . $fileName;
            $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

            // File validation
            if (!in_array($fileType, $allowedFileTypes)) {
                header('Location: jobs.php?error=' . urlencode("Invalid file type. Only JPG, JPEG, PNG, GIF, and PDF files are allowed."));
                exit;
            }

            // Optionally, limit file size (e.g., 5MB max)
            if ($_FILES['job_files']['size'][$key] > 5242880) {
                header('Location: jobs.php?error=' . urlencode("File size exceeds 5MB limit."));
                exit;
            }

            // Move the uploaded file
            if (move_uploaded_file($tmpName, $filePath)) {
                $uploadedFiles[] = $filePath;
            }
        }
    }

    $filesString = implode(',', $uploadedFiles);

    // Update job in database using MySQLi
    $stmt = $conn->prepare("UPDATE jobs SET title = ?, description = ?, budget = ?, deadline = ?, files = ? WHERE user_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssdsi", $jobTitle, $jobDescription, $jobBudget, $jobDeadline, $filesString, $userId);

    if ($stmt->execute()) {
        header('Location: job_status.php?success=' . urlencode("Job updated successfully"));
        exit;
    } else {
        // Log the error for debugging (but don't expose it to the user)
        error_log("Query failed: " . $stmt->error);
        header('Location: jobs.php?error=' . urlencode("Failed to update job"));
        exit;
    }

    $stmt->close();
}

$conn->close();
