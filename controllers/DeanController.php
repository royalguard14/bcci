<?php 
require_once 'BaseController.php'; 
class DeanController extends BaseController { 
    


    public function __construct($db) { 
        parent::__construct($db, ['15']);  
    } 
    


    public function instructors(){
        try {
            $stmt = $this->db->prepare("
                SELECT 
                CONCAT(
                    COALESCE(p.last_name, ''), ', ',
                    COALESCE(p.first_name, ''), ' ',
                    COALESCE(
                        CASE
                        WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                        THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                        ELSE ''
                        END, 
                        ''
                        )
                    ) AS fullname,
                u.user_id
                FROM users u
                LEFT JOIN profiles p ON p.profile_id = u.user_id
                LEFT JOIN employment_info ei ON ei.user_id = u.user_id
                WHERE u.role_id = '3' 
                AND ei.course_id = :course_id
                ");
            $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
            $stmt->execute();
            $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->db->prepare("
                SELECT 
                CONCAT(
                    COALESCE(p.last_name, ''), ', ',
                    COALESCE(p.first_name, ''), ' ',
                    COALESCE(
                        CASE
                        WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                        THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                        ELSE ''
                        END, 
                        ''
                        )
                    ) AS fullname,
                u.user_id
                FROM users u
                LEFT JOIN profiles p ON p.profile_id = u.user_id
                LEFT JOIN employment_info ei ON ei.user_id = u.user_id
                WHERE u.role_id = '3' 
        AND ei.user_id IS NULL  -- Ensures the instructor is not assigned in employment_info
        ");
            $stmt->execute();
            $instructors_unassign = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $_SESSION['error'] = $e;
        }
        include 'views/dean/teacher.php';
    }
    


    public function addInstrutors() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $instructorId = $_POST['instructor_id'];  // Get the selected instructor's user_id
        if ($instructorId) {
            try {
                // Proceed to register the instructor (insert into employment_info table)
                $stmt = $this->db->prepare("INSERT INTO employment_info (user_id, course_id) VALUES (:user_id, :course_id)");
                $stmt->bindValue(':user_id', $instructorId, PDO::PARAM_INT);
                $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
                $stmt->execute();
                // If the insertion is successful, set a success message in the session
                $_SESSION['success'] = 'Instructor registered successfully';
            } catch (PDOException $e) {
                // If an error occurs during the query execution, set an error message in the session
                $_SESSION['error'] = 'Error registering instructor: ' . $e->getMessage();
            }
        } else {
            // If the instructorId is missing, set an error message in the session
            $_SESSION['error'] = 'Instructor ID is missing';
        }
        header("Location: /BCCI/instructors");
        exit();
    }
}



    public function removeInstrutors() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $instructorId = $_POST['user_id'];  // Get the selected instructor's user_id
        if ($instructorId) {
            try {
                // Proceed to register the instructor (insert into employment_info table)
                $stmt = $this->db->prepare("DELETE FROM employment_info where user_id = :user_id AND course_id = :course_id ");
                $stmt->bindValue(':user_id', $instructorId, PDO::PARAM_INT);
                $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
                $stmt->execute();
                // If the insertion is successful, set a success message in the session
                $_SESSION['success'] = 'Instructor Deleted successfully';
            } catch (PDOException $e) {
                // If an error occurs during the query execution, set an error message in the session
                $_SESSION['error'] = 'Error Deleting instructor: ' . $e->getMessage();
            }
        } else {
            // If the instructorId is missing, set an error message in the session
            $_SESSION['error'] = 'Instructor ID is missing';
        }
        header("Location: /BCCI/instructors");
        exit();
    }
}


}