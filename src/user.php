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

    public function register($username, $email, $password, $profile_pic = 'default.jpg', $is_admin = 0)
    {
        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $checkStmt->bind_param("ss", $email, $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            return "Username or email already exists.";
        }

        $checkStmt->close();
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $bio = "";

        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, profile_pic, bio, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $email, $hashed_password, $profile_pic, $bio, $is_admin);
        return $stmt->execute() ? true : "Error registering user: " . $stmt->error;
    }

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT id, username, password, profile_pic, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'default.jpg';
                $_SESSION['is_admin'] = ($email === "dmlisgud@gmail.com") ? 1 : $user['is_admin'];

                header("Location: ../public/" . ($_SESSION['is_admin'] ? "jobs_view.php" : "profile.php"));
                exit();
            }
        }
        return false;
    }

    public function isAdmin()
    {
        return !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function getAllUsers()
    {
        $result = $this->conn->query("SELECT id, username, email, is_admin FROM users");
        return $result;
    }

    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT id, username, email, profile_pic, bio FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getFollowersCount($id)
    {
        return $this->countRelation("following_id", $id);
    }

    public function getFollowingCount($id)
    {
        return $this->countRelation("follower_id", $id);
    }

    private function countRelation($field, $id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM followers WHERE $field = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    }

    public function getUserPostsCount($user_id)
    {
        return $this->countPosts("user_id = ?", "i", $user_id);
    }

    public function getTotalPostsCount($search_query = '')
    {
        $search_param = "%$search_query%";
        return $this->countPosts("caption LIKE ?", "s", $search_param);
    }

    private function countPosts($condition, $type, $param)
    {
        $sql = "SELECT COUNT(*) FROM posts WHERE $condition";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($type, $param);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_row();
        return $result[0];
    }

    public function getUserPosts($id)
    {
        $stmt = $this->conn->prepare("SELECT id, image, caption, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserIdByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($row = $result->fetch_assoc()) ? $row['id'] : null;
    }

    public function isFollowing($follower_id, $followed_id)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function followUser($follower_id, $followed_id)
    {
        if (!$this->isFollowing($follower_id, $followed_id)) {
            $stmt = $this->conn->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $follower_id, $followed_id);
            return $stmt->execute();
        }
        return false;
    }

    public function unfollowUser($follower_id, $followed_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        return $stmt->execute();
    }

    public function promoteUserToAdmin($user_id)
    {
        $stmt = $this->conn->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

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

    public function createPost($user_id, $image, $caption)
    {
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, image, caption) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $image, $caption);
        return $stmt->execute();
    }

    public function deletePost($post_id, $user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        return $stmt->execute();
    }

    public function getAllPosts($query = '', $limit = 10, $offset = 0)
    {
        $search_param = "%$query%";
        $stmt = $this->conn->prepare("SELECT posts.id, posts.image, posts.caption, posts.user_id, users.username, users.profile_pic 
                                      FROM posts 
                                      JOIN users ON posts.user_id = users.id 
                                      WHERE posts.caption LIKE ? OR users.username LIKE ? 
                                      ORDER BY posts.created_at DESC 
                                      LIMIT ? OFFSET ?");
        $stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function searchUsers($query)
    {
        $search_param = "%$query%";
        $stmt = $this->conn->prepare("SELECT id, username, profile_pic FROM users WHERE username LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateProfile($user_id, $username, $bio, $profile_pic = null)
    {
        $stmt = $this->conn->prepare("UPDATE users SET username = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $bio, $user_id);
        $stmt->execute();

        if ($profile_pic) {
            $stmt = $this->conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $profile_pic, $user_id);
            $stmt->execute();
        }
    }
}
