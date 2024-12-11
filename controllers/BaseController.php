<?php 

// Global dd function
function dd($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    die();  // Stop the script execution
}



class BaseController {
    protected $db;
    protected $timeoutDuration = 3600;  // 1 hour timeout in seconds


    public function __construct($db, $permissions = []) {
        $this->db = $db;
        $this->checkLoginStatus();
        $this->checkSessionTimeout();
        // Automatically check permission for the provided actions
        if (!empty($permissions)) {
            $this->checkPermissions($permissions);
        }
    }

    // Method to check login status
    protected function checkLoginStatus() {
        if (!isset($_SESSION['log_in'])) {
            header('Location: /BCCI/login');
            exit();
        }
    }

    // Method to check permissions
    protected function checkPermissions($permissions) {
        foreach ($permissions as $permission) {
            $this->checkPermission($permission);
        }
    }

    // Check if a user has the required permission
    protected function checkPermission($permissionId) {
        $roleId = $_SESSION['role_id'];

        // Fetch the permissions for the current role
        $stmt = $this->db->prepare("SELECT permission_id FROM roles WHERE role_id = :role_id");
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            $permissions = explode(',', $role['permission_id']);
            if (!in_array($permissionId, $permissions)) {
                // Redirect to unauthorized page if permission is not found
                header('Location: /BCCI/unauthorized');
                exit();
            }
        } else {
            // Redirect if role doesn't exist
            header('Location: /BCCI/unauthorized');
            exit();
        }
    }

        public function errors() {
        include 'views/error.php';
    }




       protected function checkSessionTimeout() {
        if (isset($_SESSION['last_activity'])) {
            $elapsedTime = time() - $_SESSION['last_activity'];
            if ($elapsedTime > $this->timeoutDuration) {
                // Destroy session and redirect to login
                session_unset();
                session_destroy();
                header('Location: /BCCI/login');
                exit();
            }
        }
        // Update last activity time
        $_SESSION['last_activity'] = time();
    }




protected function getAdviserSectionAndGrade($adviserId) {
    // Query to get the section ID and corresponding grade level
    $stmt = $this->db->prepare("
        SELECT 
            s.id AS section_id,
            gl.id AS grade_level_id
        FROM 
            sections s
        INNER JOIN 
            grade_level gl 
        ON 
            FIND_IN_SET(s.id, gl.section_ids)
        WHERE 
            s.adviser_id = :adviser_id
    ");
    $stmt->bindValue(':adviser_id', $adviserId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch and return the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no section is assigned, throw an exception or handle the error
    if (!$result || !$result['section_id']) {
        throw new Exception("Error: You are not assigned to a section. Please contact the admin.");
    }

    return $result;
}


protected function checkEnrollmentStatus($userId, $currentYear) {
    $stmt = $this->db->prepare("
        SELECT * 
        FROM enrollment_history 
        WHERE user_id = :user_id 
          AND academic_year_id = :current_year
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':current_year', $currentYear, PDO::PARAM_STR); // Assuming academic_year is a string like "2024-2025"
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC); // Returns the row if found, or false if not
}









}



 ?>