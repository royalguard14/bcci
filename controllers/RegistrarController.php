<?php 
require_once 'BaseController.php'; 

class RegistrarController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['7','5','9']);  
    } 



    private function fetchStudentsByStatus($isActive) {
        $stmt = $this->db->prepare("
            SELECT
            u.user_id AS id,
            DATE_FORMAT(u.created_at, '%m/%d/%Y') AS date_register,
            p.sex,
            p.photo_path,
            COALESCE(p.first_name, '') AS first_name,
            COALESCE(p.last_name, '') AS last_name,
            COALESCE(p.middle_name, '') AS middle_name,
            DATE_FORMAT(p.birth_date, '%m/%d/%Y') AS birth_date,
            COALESCE(p.house_street_sitio_purok, '') AS house_street_sitio_purok,
            COALESCE(p.barangay, '') AS barangay,
            COALESCE(p.municipality_city, '') AS municipality_city,
            COALESCE(p.province, '') AS province,
            COALESCE(p.contact_number, '') AS contact_number
            FROM users u
            LEFT JOIN profiles p ON u.user_id = p.profile_id
            WHERE 
            u.role_id = 4
            AND u.isActive = :isActive
            AND u.isDelete = 0
            ORDER BY
            u.created_at ASC
            ");
        $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function show() {
        try {
        // Fetch students based on their status
        $pending_students = $this->fetchStudentsByStatus(0); // Pending (isActive = 0)
        $accepted_students = $this->fetchStudentsByStatus(1); // Accepted (isActive = 1)
    } catch (Exception $e) {
        // Handle errors
        echo "Error fetching student data: " . $e->getMessage();
        return;
    }

    // Include the view and pass variables
    include 'views/registrar/registered.php';
}



public function count(){

    // Prepare and execute the query
    $stmt = $this->db->prepare("SELECT COUNT(*) AS user_count FROM users WHERE role_id = 4 AND isActive = 0 AND isDelete = 0");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the count as a JSON response
    echo json_encode(['count' => $result['user_count']]);

}


public function confirm() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        try {
            $userId = (int)$_POST['user_id'];
            $stmt = $this->db->prepare("UPDATE users SET isActive = 1 WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
    
               $_SESSION['success'] = "User successfully activated!";
               
           } else {
             $_SESSION['error'] = "Failed to update user.";

         }
     } catch (Exception $e) {
       $_SESSION['error'] =  "Error: " . $e->getMessage();

   }
}
header("Location: /BCCI/pending_student");
exit();

}






}#end

