<?php

namespace App;

require_once __DIR__ . '/../private/config.php';

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Register a new user
    public function register($username, $email, $password, $profile_pic, $is_admin = 0)
    {
        // Check if email or username already exists
        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            return "Username or email already exists.";
        }

        $checkStmt->close();

        // Default values
        $profile_pic = $profile_pic ?: 'default.jpg';
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $bio = ""; // Default empty bio

        // Insert User
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, profile_pic, bio, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $email, $hashed_password, $profile_pic, $bio, $is_admin);

        return $stmt->execute() ? true : "Error registering user: " . $stmt->error;
    }

    public function is_admin()
    {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    // Login user
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT id, username, password, profile_pic, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'default.jpg';
                $_SESSION['is_admin'] = ($email === "dmlisgud@gmail.com") ? 1 : $user['is_admin'];

                // Redirect admin to jobs_view.php
                if ($_SESSION['is_admin'] == 1) {
                    header("Location: ../public/jobs_view.php");
                    exit();
                } else {
                    header("Location: ../public/profile.php");
                    exit();
                }
            }
        }
        return false; // Login failed
    }

    public function getAllUsers($username = null, $email = null, $id = null)
    {
        $query = "SELECT id, username, email, is_admin FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    // Fetch user by ID
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email, profile_pic, bio FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get followers count
    public function getFollowersCount($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM followers WHERE following_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] ?? 0;
    }

    // Get following count
    public function getFollowingCount($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM followers WHERE follower_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] ?? 0;
    }

    // Get user posts count
    public function getUserPostsCount($user_id)
    {
        $query = "SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['post_count'];
    }

    // Fetch user posts
    public function getUserPosts($id)
    {
        $stmt = $this->conn->prepare("SELECT id, image, caption, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get user ID by email
    public function getUserIdByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($row = $result->fetch_assoc()) ? $row['id'] : null;
    }

    // Check if user is following another user
    public function isFollowing($follower_id, $followed_id)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // Follow a user
    public function followUser($follower_id, $followed_id)
    {
        if (!$this->isFollowing($follower_id, $followed_id)) {
            $stmt = $this->conn->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $follower_id, $followed_id);
            return $stmt->execute();
        }
        return false; // Already following
    }

    // Unfollow a user
    public function unfollowUser($follower_id, $followed_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        return $stmt->execute();
    }

    // Promote user to admin
    public function promoteUserToAdmin($user_id)
    {
        $stmt = $this->conn->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    // Check if logged-in user is admin
    public function isAdmin()
    {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    // Get a single post by ID
    public function getPostById($post_id)
    {
        $stmt = $this->conn->prepare("SELECT posts.id, posts.image, posts.caption, posts.user_id, users.username 
                                      FROM posts 
                                      JOIN users ON posts.user_id = users.id
                                      WHERE posts.id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Create a new post
    public function createPost($user_id, $image, $caption)
    {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, image, caption) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $image, $caption);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Error creating post: " . $stmt->error;
        }
    }

    // Delete a post
    public function deletePost($post_id, $user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        return $stmt->execute();
    }

    // Get all posts with search functionality
    public function getAllPosts($query = '', $limit = 10, $offset = 0)
    {
        // Modify the SQL query to filter posts by caption or username and add LIMIT and OFFSET for pagination
        $sql = "SELECT posts.id, posts.image, posts.caption, posts.user_id, users.username, users.profile_pic 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            WHERE posts.caption LIKE ? OR users.username LIKE ? 
            ORDER BY posts.created_at DESC 
            LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);

        // Prepare the search parameter
        $search_param = "%$query%";

        // Bind the parameters (search term, limit, and offset)
        $stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);

        // Execute the statement
        $stmt->execute();

        // Get the results
        $result = $stmt->get_result();

        // Return the fetched posts as an associative array
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // Search users by username or email
    public function searchUsers($query)
    {
        $stmt = $this->conn->prepare("SELECT id, username, profile_pic FROM users WHERE username LIKE ? OR email LIKE ?");
        $search_param = "%$query%";
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update user profile
    public function updateProfile($user_id, $username, $bio, $profile_pic = null)
    {
        $sql = "UPDATE users SET username = ?, bio = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $bio, $user_id);
        $stmt->execute();

        if ($profile_pic) {
            $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $profile_pic, $user_id);
            $stmt->execute();
        }
    }
    public function getTotalPostsCount($search_query = '')
    {
        $sql = "SELECT COUNT(*) FROM posts WHERE caption LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_row();
        return $result[0];
    }
}
