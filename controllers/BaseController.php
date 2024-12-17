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
    protected $acads_report;

    protected $campusDataCurrentAcademicYear;
    protected $campusDataCurrentTerm;
    protected $campusDataEnrollmentStatus;
    protected $myEnrollmentStatus;
    protected $mycourseID;

    protected $myName;
    protected $mycurrenEhID;
    protected $myRoleID;
    protected $deanDept;
    protected $deanDeptid;
    
    protected $campusName;



    public function __construct($db, $permissions = []) {
        $this->db = $db;
        $this->checkLoginStatus();
        $this->checkSessionTimeout();
        $this->checkacadsreport();
        $this->initializeUserDetails();
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


protected function checkacadsreport(){

        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT COUNT(*) AS record_count FROM academic_record WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
     $this->acads_report = $stmt->fetch(PDO::FETCH_ASSOC)['record_count'];

}

    protected function initializeUserDetails() {
        $userId = $_SESSION['user_id'];



$stmt = $this->db->prepare("
    SELECT d.course_name, d.id
    FROM employment_info ei
    LEFT JOIN department d ON ei.course_id = d.id
    WHERE ei.user_id = :user_id
");

$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

// Fetch the result
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if a result was returned
if ($result) {
    $this->deanDept = $result['course_name'];
     $this->deanDeptid = $result['id'];
} else {
    // Handle case where no department is found for this user
    $this->deanDept = null; // Or set it to a default value, if necessary
    $this->deanDeptid = null;
}



$stmt = $this->db->prepare("SELECT 
role_id
FROM users WHERE user_id = :user_id");

$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$this->myRoleID = (int)$stmt->fetch(PDO::FETCH_ASSOC)['role_id'];

$stmt = $this->db->prepare("SELECT 
    CONCAT(
        COALESCE(last_name, ''), ', ',
        COALESCE(first_name, ''), ' ',
        COALESCE(
            CASE
                WHEN middle_name IS NOT NULL AND middle_name != '' 
                THEN CONCAT(SUBSTRING(middle_name, 1, 1), '.')
                ELSE ''
            END, 
            ''
        )
    ) AS fullname
FROM profiles WHERE profile_id = :user_id");

$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$this->myName = $stmt->fetch(PDO::FETCH_ASSOC);

if ($this->myName) {
    $this->myName['fullname'];
} else {
    $_SESSION['error'] = "No Profile data yet";
}




        $stmt = $this->db->prepare("SELECT COUNT(*) AS record_count FROM academic_record WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $this->acads_report = $stmt->fetch(PDO::FETCH_ASSOC)['record_count'];

        $stmt = $this->db->prepare("SELECT c_id FROM academic_record WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); 
        $stmt->execute();
        $course_id = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->mycourseID = $course_id ? (int) $course_id['c_id'] : null;

        $stmt = $this->db->prepare("SELECT function, name FROM campus_info WHERE id IN (5,7,2,9) ORDER BY FIELD(id, 5, 7, 2, 9);");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->campusDataCurrentAcademicYear = (int) $result[0]['function'];
        $this->campusDataEnrollmentStatus = (int) $result[1]['function'];
        $this->campusName = $result[2]['function'];
        $this->campusDataCurrentTerm = (int)$result[3]['function'];

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS record_count 
            FROM enrollment_history eh
            WHERE 
            eh.user_id = :user_id
            AND eh.course_id = :course_id
            AND eh.academic_year_id = :academic_year_id
        
            ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $this->mycourseID, PDO::PARAM_INT); 
        $stmt->bindParam(':academic_year_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT); 
        $stmt->execute();
        $this->myEnrollmentStatus = (int) $stmt->fetch(PDO::FETCH_ASSOC)['record_count'];
    


  $stmt = $this->db->prepare("SELECT id FROM enrollment_history WHERE user_id = :user_id AND academic_year_id = :academic_year_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); 
        $stmt->bindParam(':academic_year_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT); 
        $stmt->execute();
        $eh_id = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->mycurrenEhID = $eh_id ? (int) $eh_id['id'] : null;


    }







}



 ?>