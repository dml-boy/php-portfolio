<?php
session_start();
require_once '../private/config.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== 1) {
    header("Location: login.php");
    exit();
}

// Fetch all users with pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Prepare and execute the SQL query to fetch users with pagination
$stmt = $conn->prepare("SELECT id, email, failed_attempts, lockout_time FROM users LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch admin's own profile
$stmt_admin = $conn->prepare("SELECT id, email FROM users WHERE id = ?");
$stmt_admin->bind_param("i", $_SESSION['user_id']);
$stmt_admin->execute();
$admin_result = $stmt_admin->get_result();
$admin_profile = $admin_result->fetch_assoc();

// Fetch total number of users for pagination
$total_users_result = $conn->query("SELECT COUNT(id) AS total_users FROM users");
$total_users = $total_users_result->fetch_assoc()['total_users'];
$total_pages = ceil($total_users / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Admin Dashboard</h2>

        <!-- Success message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <!-- Admin profile link -->
        <div class="mb-3">
            <a href="profile.php" class="btn btn-info">View My Profile</a>
        </div>

        <!-- Button to view all jobs -->
        <div class="mb-3">
            <a href="view_jobs.php" class="btn btn-primary">View All Jobs</a>
        </div>

        <!-- Users Table -->
        <h3>All Users</h3>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Failed Attempts</th>
                        <th>Lockout Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['failed_attempts']; ?></td>
                            <td>
                                <?php
                                // Format the lockout time if it's not NULL
                                if ($row['lockout_time']) {
                                    $lockout_time = new DateTime($row['lockout_time']);
                                    echo $lockout_time->format('Y-m-d H:i:s');
                                } else {
                                    echo "Not Locked";
                                }
                                ?>
                            </td>
                            <td>
                                <form action="admin_unlock.php" method="POST">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                    <button type="submit" name="unlock" class="btn btn-success">Unlock</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">No users found.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="admin_dashboard.php?page=1">First</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="admin_dashboard.php?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="admin_dashboard.php?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="admin_dashboard.php?page=<?php echo $total_pages; ?>">Last</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Back to Profile -->
        <a href="profile.php" class="btn btn-secondary">Back to Profile</a>
    </div>
</body>

</html>