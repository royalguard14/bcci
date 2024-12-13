<?php 
require_once 'config/db.php'; 
class FrontController { 
    private $db;
    public $acads_report;
    public $campusDataCurrentAcademicYear;
    public $campusDataEnrollmentStatus;
    public $myEnrollmentStatus;
    public $mycourseID;
    
    public function __construct($db) {
        $this->db = $db;
        if (isset($_SESSION['log_in']) && $_SESSION['log_in'] === true) {
            $this->initializeUserDetails();
        }
    }
    protected function initializeUserDetails() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT COUNT(*) AS record_count FROM academic_record WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $this->acads_report = $stmt->fetch(PDO::FETCH_ASSOC)['record_count'];

        $stmt = $this->db->prepare("SELECT c_id FROM academic_record WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); 
        $stmt->execute();
        $course_id = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->mycourseID = $course_id ? (int) $course_id['c_id'] : null;

        $stmt = $this->db->prepare("SELECT function, name FROM campus_info WHERE id IN (5,7) ORDER BY FIELD(id, 5, 7);");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->campusDataCurrentAcademicYear = (int) $result[0]['function'];
        $this->campusDataEnrollmentStatus = (int) $result[1]['function'];

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
    }
    
    public function whome() {
        include 'views/website/home.php';
    }
    
    public function register() {
        include 'views/website/register.php';
    }
    
    public function contact() {
        include 'views/website/contact.php';
    }

    public function enroll(){
        function createDirectory($path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $photo_path = $_FILES['photo_path']['name'];
            $role_id = 4;
            $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 6");
            $stmt->execute();
            $institutional = $stmt->fetch(PDO::FETCH_ASSOC);
            $email_extension = $institutional['function'];
            $email = str_replace(' ', '.', strtolower($firstName . '.' . $lastName .'@'. $email_extension));
            $username = $this->generateRandomUsername(8);
            $password = 'password';
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $target_dir = "assets/documents/" . $username . "/";
            createDirectory($target_dir);
            $target_file = $target_dir . basename($photo_path);
            if (!move_uploaded_file($_FILES['photo_path']['tmp_name'], $target_file)) {
                $_SESSION['error'] = "File upload failed.";
                return;
            }
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $existing_email = $stmt->fetch(PDO::FETCH_ASSOC)['COUNT(*)'];
                if ($existing_email > 0) {
                    $_SESSION['info'] = "You have already register into our system, please contact the Registrar Office";
                }else{
                    $stmt = $this->db->prepare("
                        INSERT INTO users (email, password, username, role_id, isDelete) 
                        VALUES (:email, :password, :username, :role_id, 0)
                        ");
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $userId = $this->db->lastInsertId();
                    $stmt = $this->db->prepare("
                        INSERT INTO profiles (
                            photo_path, profile_id, last_name, first_name, middle_name, sex, birth_date, house_street_sitio_purok, barangay, municipality_city, province, contact_number
                            ) VALUES (
                            :photo_path, :profile_id, :last_name, :first_name, :middle_name, :sex, :birth_date, :house_street_sitio_purok, :barangay, :municipality_city, :province, :contact_number
                            )
                            ");
                    $stmt->bindParam(':photo_path', $target_file, PDO::PARAM_STR);
                    $stmt->bindParam(':profile_id', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':last_name', $_POST['last_name'], PDO::PARAM_STR);
                    $stmt->bindParam(':first_name', $_POST['first_name'], PDO::PARAM_STR);
                    $stmt->bindParam(':middle_name', $_POST['middle_name'], PDO::PARAM_STR);
                    $stmt->bindParam(':birth_date', $_POST['birth_date'], PDO::PARAM_STR);
                    $stmt->bindParam(':sex', $_POST['sex'], PDO::PARAM_STR);
                    $stmt->bindParam(':house_street_sitio_purok', $_POST['house_street_sitio_purok'], PDO::PARAM_STR);
                    $stmt->bindParam(':barangay', $_POST['barangay'], PDO::PARAM_STR);
                    $stmt->bindParam(':municipality_city', $_POST['municipality_city'], PDO::PARAM_STR);
                    $stmt->bindParam(':province', $_POST['province'], PDO::PARAM_STR);
                    $stmt->bindParam(':contact_number', $_POST['contact_number'], PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        $_SESSION['Register_code'] = $username;
                        $_SESSION['success'] = "Student registered successfully!";
                    } else {
                        $_SESSION['error'] = "Failed to register student.";
                    }
                }
            } catch (Exception $e) {
               $_SESSION['error'] = "Error: " . $e->getMessage();
           }
           header("Location: /BCCI/register");
           exit;
       }
   }
   private function generateRandomUsername($length = 8) {
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
}
?>