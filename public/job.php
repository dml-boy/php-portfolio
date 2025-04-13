<?php
require_once '../private/config.php';
$job_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT jobs.*, users.username FROM jobs JOIN users ON jobs.user_id = users.id WHERE jobs.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    echo "Job not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> | Job Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2><?= htmlspecialchars($job['title']) ?></h2>
        <p class="text-muted">Posted by <?= htmlspecialchars($job['username']) ?> on <?= date('M d, Y', strtotime($job['created_at'])) ?></p>
        <p><strong>Type:</strong> <?= $job['job_type'] ?> | <strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
        <hr>
        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>

        <a href="mailto:alamutumubarak01@gmail.com?subject=Applying for <?= urlencode($job['title']) ?>" class="btn btn-primary mt-3">
            Apply Now
        </a>
    </div>
</body>

</html>