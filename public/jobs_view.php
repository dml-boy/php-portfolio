<?php
session_start();
require_once '../private/config.php';
require_once '../src/user.php';

use App\User;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize User class with database connection
$user = new User($conn);

// Fetch job requests
$stmt = $conn->prepare("SELECT * FROM jobs ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Job Requests</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Budget</th>
                    <th>Deadline</th>
                    <th>Files</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td>
                            <!-- Add a tooltip or truncation for long descriptions -->
                            <span data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($job['description']); ?>">
                                <?php echo strlen($job['description']) > 50 ? substr(htmlspecialchars($job['description']), 0, 50) . '...' : htmlspecialchars($job['description']); ?>
                            </span>
                        </td>
                        <td>$<?php echo htmlspecialchars($job['budget']); ?></td>
                        <td><?php echo htmlspecialchars($job['deadline']); ?></td>
                        <td>
                            <?php if (!empty($job['files'])): ?>
                                <?php foreach (explode(',', $job['files']) as $file): ?>
                                    <a href="<?php echo htmlspecialchars($file); ?>" target="_blank">View File</a><br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                No files attached
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Enable tooltips -->
    <script>
        var tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>

</html>