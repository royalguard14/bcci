<?php 

class LoginController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Show login form
    public function showLoginForm() {
        if (isset($_SESSION['log_in'])) {
            // Redirect to login if not logged in
            header('Location: /BCCI/dashboard');
            exit();
        }

        include 'views/auth/login1.php';
    }

public function handleLogin() {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = $_POST['password'];
        $stmt = $this->db->prepare("
        SELECT user_id, username, password, role_id, isActive, email 
        FROM users 
        WHERE (username = :identifier OR email = :identifier) 
        AND isActive = 1
        ");
        $stmt->bindParam(':identifier', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['log_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['last_activity'] = time();  // Set last activity time

            header('Location: /BCCI/dashboard');
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid login credentials.";
            header('Location: /BCCI/login');
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Please enter both username and password.";
        header('Location: /BCCI/login');
        exit();
    }
}




public function handleLogout() {
    // Destroy session data
    session_unset();
    session_destroy();
    
    // Redirect to login page
    header('Location: /BCCI/login');
    exit();
}
}

 ?>