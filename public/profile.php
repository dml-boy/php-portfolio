<?php
session_start();
require '../private/config.php';
require '../src/User.php';

use App\User;

$user = new User($conn);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

// Get the profile ID from the URL or session (if viewing own profile)
$profile_id = $_GET['id'] ?? $_SESSION['user_id'];
$logged_in_id = $_SESSION['user_id'];

// Fetch user data and check if user exists
$user_data = $user->getUserById($profile_id);
if (!$user_data) {
    die("User not found.");
}

// Fetch followers and following count
$followers_count = $user->getFollowersCount($profile_id);
$following_count = $user->getFollowingCount($profile_id);

// Fetch posts made by the user
$posts = $user->getUserPosts($profile_id);

// Check if the logged-in user is following the profile
$isFollowing = $user->isFollowing($logged_in_id, $profile_id);

// Handle follow/unfollow actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow'])) {
        $user->followUser($logged_in_id, $profile_id);
        $isFollowing = true;
    } elseif (isset($_POST['unfollow'])) {
        $user->unfollowUser($logged_in_id, $profile_id);
        $isFollowing = false;
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit();
    }
    header("Location: profile.php?id=$profile_id");
    exit();
}
$postsPerPage = 9;
$totalPosts = $user->getUserPostsCount($profile_id);
$totalPages = ceil($totalPosts / $postsPerPage);
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $postsPerPage;

$posts = $user->getUserPosts($profile_id, $offset, $postsPerPage);

?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user_data['username']) ?>'s Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #f39c12;
            --dark: #121212;
            --darker: #1a1a1a;
            --light: #f8f9fa;
        }

        body {
            background-color: var(--dark);
            color: var(--light);
            font-family: 'Poppins', sans-serif;
        }

        .profile-header {
            background: var(--darker);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        .user-info {
            text-align: left;
        }

        .stats {
            display: flex;
            gap: 15px;
            font-size: 1.1em;
            margin-top: 1rem;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 2rem;
        }

        .post-item {
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .post-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .post-caption {
            position: absolute;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            width: 100%;
            padding: 5px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
        }

        .btn-jobs {
            background: #2ecc71;
            color: white;
        }

        .btn-jobs:hover {
            background: #27ae60;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="d-flex align-items-center">
                <img src="<?= file_exists("../uploads/" . $user_data['profile_pic']) ? "../uploads/" . htmlspecialchars($user_data['profile_pic']) : '../uploads/default.png' ?>" class="profile-pic mx-5" alt="Profile Picture">

                <div class="user-info ms-3">
                    <h2><?= htmlspecialchars($user_data['username']) ?></h2>
                    <p class="text-muted"><?= htmlspecialchars($user_data['bio'] ?? 'No bio available') ?></p>
                    <div class="stats">
                        <span><strong><?= $followers_count ?></strong> Followers</span>
                        <span><strong><?= $following_count ?></strong> Following</span>
                    </div>

                    <!-- Follow/Unfollow Button -->
                    <?php if ($profile_id != $logged_in_id): ?>
                        <form method="POST" class="mt-2">
                            <?php if ($isFollowing): ?>
                                <button type="submit" name="unfollow" class="btn btn-danger">Unfollow</button>
                            <?php else: ?>
                                <button type="submit" name="follow" class="btn btn-primary">Follow</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right-Side Buttons (Only for Logged-in User) -->
            <?php if ($profile_id == $logged_in_id): ?>
                <div class="action-buttons">
                    <a href="posts.php" class="btn btn-primary">Create Post</a>
                    <a href="jobs.php" class="btn btn-jobs">Post a Job</a>
                    <a href="update_profile.php" class="btn btn-secondary">Edit Profile</a>
                    <form method="POST">
                        <button type="submit" name="logout" class="btn btn-logout">Logout</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Posts Section -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?id=<?= $profile_id ?>&page=<?= $i ?>" class="btn btn-outline-primary"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <h3 class="mt-4">Posts</h3>
        <?php if (empty($posts)): ?>
            <p class="text-muted">No posts yet.</p>
        <?php else: ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <img src="
                        <?= file_exists("../uploads/" . $post['image']) ? "../uploads/" . htmlspecialchars($post['image']) : '../uploads/default.png' ?>" alt="Post Image">

                        <?php if (!empty($post['caption'])): ?>
                            <div class="post-caption"><?= htmlspecialchars($post['caption']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>