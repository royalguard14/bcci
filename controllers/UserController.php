<?php 
require_once 'BaseController.php'; 
 
class UserController extends BaseController { 
    public function __construct($db) {
        
        parent::__construct($db, ['3']); 
    }





    // Show all users
    public function showUsers() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE isDelete = 0");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $stmt = $this->db->prepare("SELECT * FROM roles");
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/user/user.php'; 
    }


    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $role_id = (int) $_POST['role_id'];

            if (empty($username) || empty($password)) {
                echo "Error: Username and password cannot be empty.";
                return;
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the database
            $stmt = $this->db->prepare("INSERT INTO users (username, password, role_id) VALUES (:username, :password, :role_id)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);


            if ($stmt->execute()) {
                header("Location: /BCCI/accounts");
                exit();
            } else {
                echo "Error: Could not create user.";
            }
        }
    }


        public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $user_id = (int) $_POST['user_id'];
            $stmt = $this->db->prepare("UPDATE users SET isDelete = 1 WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: /BCCI/accounts");
                exit();
            } else {
                echo "Error: Could not delete user.";
            }
        }
    }



        public function updateUser() {
        // Check if form was submitted with POST and required fields are present
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['role_id'])) {
            $user_id = (int) $_POST['user_id'];
            $username = trim($_POST['username']);
            $role_id = (int) $_POST['role_id'];
            $isActive = isset($_POST['isActive']) ? (int) $_POST['isActive'] : 0;

            // Update the user's basic information
            $stmt = $this->db->prepare("UPDATE users SET username = :username, role_id = :role_id, isActive = :isActive WHERE user_id = :user_id");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Check if a new password was provided
                if (!empty($_POST['password'])) {
                    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
                    
                    // Update the user's password
                    $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                
                // Redirect back to the user list
                header("Location: /BCCI/accounts");
                exit();
            } else {
                echo "Error: Could not update user.";
            }
        } else {
            echo "Error: Missing required information.";
        }
    }






 
} 
