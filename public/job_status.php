<?php
session_start();
require_once '../private/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch jobs for the logged-in user
$stmt = $conn->prepare("SELECT * FROM jobs WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Status | DML Dev</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .status-completed {
            color: green;
            font-weight: bold;
        }

        .status-in-progress {
            color: blue;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .alert {
            font-size: 1.1rem;
            margin-top: 15px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2 class="text-center text-warning">Your Job Status</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <table class="table table-dark table-striped mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Budget</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['description']) ?></td>
                        <td>$<?= htmlspecialchars($job['budget']) ?></td>
                        <td><?= htmlspecialchars($job['deadline']) ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            switch (strtolower($job['status'])) {
                                case 'completed':
                                    $statusClass = 'status-completed';
                                    break;
                                case 'in progress':
                                    $statusClass = 'status-in-progress';
                                    break;
                                case 'pending':
                                    $statusClass = 'status-pending';
                                    break;
                                default:
                                    $statusClass = '';
                            }
                            ?>
                            <span class="<?= $statusClass ?>"><?= htmlspecialchars($job['status']) ?></span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>