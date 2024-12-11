<?php 
require_once 'config/db.php'; 
 
class FrontController { 

        private $db;

    public function __construct($db) {
        $this->db = $db; // Assign the database connection to a class property
    }


        function generateRandomUsername($length = 8) {
        // Define the characters that can be used in the username
        $characters = '0123456789';
        $randomString = '';
        // Loop to generate a string of the specified length
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }




public function whome(){
    include 'views/website/home.php';
}

public function register(){
    include 'views/website/register.php';
}

public function contact(){
    include 'views/website/contact.php';
}

public function enroll(){

function createDirectory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}





    // Only execute after POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Get POST data
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $photo_path = $_FILES['photo_path']['name'];
        $role_id = 4;
        
        // Get email extension
        $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 6");
        $stmt->execute();
        $institutional = $stmt->fetch(PDO::FETCH_ASSOC);
        $email_extension = $institutional['function'];

        // Generate email
        $email = str_replace(' ', '.', strtolower($firstName . '.' . $lastName .'@'. $email_extension));

        // Generate a random username and password
        $username = $this->generateRandomUsername(8);
        $password = 'password'; // You should ideally generate a random password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Handle file upload
        $target_dir = "assets/documents/" . $username . "/";
    createDirectory($target_dir);
        $target_file = $target_dir . basename($photo_path);


        
        if (!move_uploaded_file($_FILES['photo_path']['tmp_name'], $target_file)) {
            $_SESSION['error'] = "File upload failed.";
            return;
        }

        try {
            // Insert into users table
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

            // Insert into profiles table
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
                $_SESSION['success'] = "Student registered successfully!";
            } else {
                $_SESSION['error'] = "Failed to register student.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

          header("Location: /BCCI/register");
                exit;
    }


}


}#end

