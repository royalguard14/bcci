<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT user_id, password, role_id, isActive, isDelete FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if account is active and not deleted
            if (!$user['isActive'] || $user['isDelete']) {
                return "Account is inactive or deleted.";
            }

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, return user data
                return [
                    'user_id' => $user['user_id'],
                    'role_id' => $user['role_id']
                ];
            } else {
                return "Invalid password.";
            }
        } else {
            return "User not found.";
        }
    }
}
