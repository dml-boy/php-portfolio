<?php
session_start();
require '../private/config.php';
require '../src/user.php';

use App\User;

$user = new User($conn);

// Sanitize the search query
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Validate the search query (optional)
if (strlen($search_query) > 100) {
    $search_query = substr($search_query, 0, 100); // Limit search query length
}

// Fetch posts and users based on the search query
$posts = $user->getAllPosts($search_query);
$users = $user->searchUsers($search_query);

// Pagination variables
$limit = 6; // Number of posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch paginated posts
$posts = $user->getAllPosts($search_query, $limit, $offset);

// Get total number of posts for pagination
$total_posts = $user->getTotalPostsCount($search_query);
$total_pages = ceil($total_posts / $limit);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Latest Posts</h2>

        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search Posts or Users..." name="search" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- Display Users -->
        <?php if (count($users) > 0): ?>
            <h3>Users</h3>
            <ul class="list-group">
                <?php foreach ($users as $user_data): ?>
                    <li class="list-group-item">
                        <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="rounded-circle me-2" width="40" height="40" alt="User">
                        <a href="profile.php?id=<?= $user_data['id'] ?>" class="text-light text-decoration-none"><?= htmlspecialchars($user_data['username']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No users found.</p>
        <?php endif; ?>

        <!-- Display Posts -->
        <h3>Posts</h3>
        <?php if (count($posts) > 0): ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4">
                        <div class="card bg-secondary text-light mb-3">
                            <div class="card-header d-flex align-items-center">
                                <img src="../uploads/<?= htmlspecialchars($post['profile_pic']) ?>" class="rounded-circle me-2" width="40" height="40" alt="User">
                                <a href="profile.php?id=<?= $post['user_id'] ?>" class="text-light text-decoration-none">
                                    <?= htmlspecialchars($post['username']) ?>
                                </a>
                            </div>
                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top" alt="Post Image">
                            <?php if (!empty($post['caption'])): ?>
                                <div class="card-body">
                                    <p class="card-text"><?= htmlspecialchars($post['caption']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <ul class="pagination">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search_query) ?>&page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search_query) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search_query) ?>&page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <p class="text-muted">No posts available.</p>
        <?php endif; ?>
    </div>
</body>

</html>