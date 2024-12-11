<?php 
require_once 'BaseController.php'; 

class SubjectController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['14']);  
    } 



        public function showCampusSubject() {
        $stmt = $this->db->prepare("SELECT * FROM subjects");
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/campus/subject.php';
    }



public function createCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $code = trim($_POST['code']);
        $unit_lec = isset($_POST['unit_lec']) ? (int)$_POST['unit_lec'] : null;
        $unit_lab = isset($_POST['unit_lab']) ? (int)$_POST['unit_lab'] : null;
        $pre_req = trim($_POST['pre_req']);

        if (empty($name) || empty($code)) {
            $_SESSION['error'] = "Subject name and code cannot be empty.";
            header("Location: /BCCI/campus-subjects");
            exit();
        }

        $stmt = $this->db->prepare("INSERT INTO subjects (name, description, code, unit_lec, unit_lab, pre_req) 
                                    VALUES (:name, :description, :code, :unit_lec, :unit_lab, :pre_req)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':unit_lec', $unit_lec, PDO::PARAM_INT);
        $stmt->bindParam(':unit_lab', $unit_lab, PDO::PARAM_INT);
        $stmt->bindParam(':pre_req', $pre_req, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject successfully created.";
            header("Location: /BCCI/campus-subjects");
            exit();
        } else {
            $_SESSION['error'] = "Could not create subject.";
            header("Location: /BCCI/campus-subjects");
            exit();
        }
    }
}

public function updateCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $code = trim($_POST['code']);
        $unit_lec = isset($_POST['unit_lec']) ? (int)$_POST['unit_lec'] : null;
        $unit_lab = isset($_POST['unit_lab']) ? (int)$_POST['unit_lab'] : null;
        $pre_req = trim($_POST['pre_req']);




        if (empty($name) || empty($code)) {
            $_SESSION['error'] = "Subject name and code cannot be empty.";
            header("Location: /BCCI/campus-subjects");
            exit();
        }

        $stmt = $this->db->prepare("UPDATE subjects 
                                    SET name = :name, description = :description, code = :code, 
                                        unit_lec = :unit_lec, unit_lab = :unit_lab, pre_req = :pre_req 
                                    WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':unit_lec', $unit_lec, PDO::PARAM_INT);
        $stmt->bindParam(':unit_lab', $unit_lab, PDO::PARAM_INT);
        $stmt->bindParam(':pre_req', $pre_req, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject successfully updated.";
            header("Location: /BCCI/campus-subjects");
            exit();
        } else {
            $_SESSION['error'] = "Could not update subject.";
            header("Location: /BCCI/campus-subjects");
            exit();
        }
    }
}

public function deleteCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub_id'])) {
        $id = (int)$_POST['sub_id'];
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject successfully deleted.";
            header("Location: /BCCI/campus-subjects");
            exit();
        } else {
            $_SESSION['error'] = "Could not delete subject.";
            header("Location: /BCCI/campus-subjects");
            exit();
        }
    }
}


} 
