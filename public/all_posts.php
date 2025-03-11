<?php
// all_posts.php
session_start();
require '../private/config.php';
require '../src/User.php';

use App\User;

$user = new User($conn);
$posts = $user->getAllPosts();
$searchResults = [];
$searchQuery = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $searchResults = $user->searchUsers($searchQuery);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling for posts grid */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .post-item {
            background-color: #333;
            padding: 10px;
            border-radius: 8px;
        }

        .post-caption {
            margin-top: 10px;
            color: #ddd;
        }

        /* Styling for the search form */
        .search-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .search-results {
            margin-top: 20px;
        }

        .search-results a {
            text-decoration: none;
            color: #007bff;
        }

        .search-results a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>All Posts</h2>

        <!-- Search Form -->
        <div class="search-container">
            <form method="POST" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search users" value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

        <!-- Display Search Results -->
        <?php if (!empty($searchResults)): ?>
            <div class="search-results">
                <h3>Search Results</h3>
                <ul class="list-unstyled">
                    <?php if (count($searchResults) > 0): ?>
                        <?php foreach ($searchResults as $user): ?>
                            <li>
                                <a href="profile.php?id=<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['username']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No users found matching your search.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Display Posts -->
        <div class="posts-grid mt-4">
            <?php foreach ($posts as $post): ?>
                <div class="post-item">
                    <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" class="img-fluid rounded mb-2">
                    <?php if (!empty($post['caption'])): ?>
                        <div class="post-caption"><?= htmlspecialchars($post['caption']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>