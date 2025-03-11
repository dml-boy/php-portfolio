<?php

namespace App;

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "Dara_240211";
    private $database = "portfolio_db";
    private $conn;

    public function __construct()
    {
        $this->conn = new \mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function createTables()
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) UNIQUE NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
profile_pic VARCHAR(255) DEFAULT NULL
)",
            "CREATE TABLE IF NOT EXISTS posts (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
content TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)"
        ];

        foreach ($queries as $query) {
            $this->conn->query($query);
        }
    }
}
