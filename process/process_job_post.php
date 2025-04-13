<?php
session_start();
require_once '../private/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$location = trim($_POST['location']);
$job_type = $_POST['job_type'];

if (!$title || !$description) {
    $_SESSION['error'] = "Job title and description are required.";
    header("Location: ../public/jobs.php");
    exit();
}

$stmt = $conn->prepare("INSERT INTO jobs (user_id, title, description, location, job_type) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $description, $location, $job_type);
$stmt->execute();

$_SESSION['success'] = "Job posted successfully!";
header("Location: ../public/jobs_board.php");
exit();
