<?php
require_once '../private/config.php';

$result = $conn->query("SELECT jobs.*, users.username FROM jobs JOIN users ON jobs.user_id = users.id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Job Board | DML Dev</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2 class="mb-4">Browse Jobs</h2>
        <?php while ($job = $result->fetch_assoc()): ?>
            <div class="card mb-3 bg-secondary-subtle text-light">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($job['title']) ?></h4>
                    <p class="card-text"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?> | <strong>Type:</strong> <?= $job['job_type'] ?></p>
                    <p class="text-muted">Posted by: <?= htmlspecialchars($job['username']) ?> on <?= date('M d, Y', strtotime($job['created_at'])) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>