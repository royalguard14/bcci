<?php 
require_once 'BaseController.php'; 
class CampusController extends BaseController { 
    public function __construct($db) { 
     parent::__construct($db, ['14']); 
 }




public function showCampusProfile() {
    try {
        // Fetch academic years
        $stmt = $this->db->prepare("SELECT * FROM academic_year ORDER BY start ASC");
        $stmt->execute();
        $academicYear = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch campus information
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id NOT IN (0)");
        $stmt->execute();
        $campusInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);







        // Pass both datasets to the view
        include 'views/campus/profile.php';
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}




public function updateCampusInfo() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['campus_info'])) {
        // Loop through the posted data and update each record
        foreach ($_POST['campus_info'] as $id => $function) {
            // Check if the value is an array (e.g., for Operating Days)
            if (is_array($function)) {
                $function = implode(',', $function); // Convert array to a comma-separated string
            } else {
                $function = trim($function); // Clean the input value
            }

            // Validate the input
            if (empty($function)) {
                $_SESSION['error'] = "Error: All fields must be filled in.";
                header("Location: /BCCI/campus-profile");
                exit();
            }

            try {
                // Update the record in the database
                $stmt = $this->db->prepare("UPDATE campus_info SET function = :function WHERE id = :id");
                $stmt->bindParam(':function', $function, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $_SESSION['error'] = "Error: Could not update campus info.";
                    header("Location: /BCCI/campus-profile");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
                header("Location: /BCCI/campus-profile");
                exit();
            }
        }

        $_SESSION['success'] = "Campus info updated successfully!";
        header("Location: /BCCI/campus-profile");
        exit();
    }
}



public function addCampusSchoolYear() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start(); // Start session for error handling
        $startYear = isset($_POST['start_school_year']) ? (int)$_POST['start_school_year'] : null;
        $endYear = isset($_POST['end_school_year']) ? (int)$_POST['end_school_year'] : null;

        // Validate input
        if (!$startYear || !$endYear || $startYear >= $endYear) {
            $_SESSION['error'] = "Error: Invalid school year range.";
            header("Location: /BCCI/campus-profile");
            exit();
        }

        try {
            // Check if the start year is unique
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM academic_year WHERE start = :start");
            $stmt->bindParam(':start', $startYear, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = "Error: Start year already exists.";
                header("Location: /BCCI/campus-profile");
                exit();
            }

            // Insert into academic_year table
            $stmt = $this->db->prepare("INSERT INTO academic_year (start, end) VALUES (:start, :end)");
            $stmt->bindParam(':start', $startYear, PDO::PARAM_INT);
            $stmt->bindParam(':end', $endYear, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: /BCCI/campus-profile");
                exit();
            } else {
                $_SESSION['error'] = "Error: Could not add school year.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

        // Redirect back with error
        header("Location: /BCCI/campus-profile");
        exit();
    }
}




public function deleteCampusSchoolYear() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['sy_id']) ? (int)$_POST['sy_id'] : null;

        // Validate input
        if (!$id) {
            echo "Error: Invalid school year ID.";
            return;
        }

        // Delete the school year
        $stmt = $this->db->prepare("DELETE FROM academic_year WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: /BCCI/campus-profile");
            exit();
        } else {
            echo "Error: Could not delete school year.";
        }
    }
}





public function showCampusGrade() {
    $stmt = $this->db->prepare("SELECT * FROM grade_level");
    $stmt->execute();
    $grade_level = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include 'views/campus/grade.php';
}
public function createCampusGrade() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $level = trim($_POST['grade_name']);
        if (empty($level)) {
            echo "Error: Role name cannot be empty.";
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO grade_level (level) VALUES (:level)");
        $stmt->bindParam(':level', $level, PDO::PARAM_STR);
        if ($stmt->execute()) {
            header("Location: /BCCI/campus-grades");
            exit();
        } else {
            echo "Error: Could not create role.";
        }
    }
}
public function updateCampusGrade() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_id']) && isset($_POST['grade_name'])) {
        $grade_id = (int) $_POST['grade_id'];
        $grade_name = trim($_POST['grade_name']);
        $stmt = $this->db->prepare("UPDATE grade_level SET level = :grade_name WHERE id = :grade_id");
        $stmt->bindParam(':grade_name', $grade_name, PDO::PARAM_STR);
        $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: /BCCI/campus-grades"); 
            exit();
        } else {
            echo "Error: Could not update role.";
        }
    }
}
public function deleteCampusGrade() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_id'])) {
        $grade_id = (int) $_POST['grade_id'];
        $stmt = $this->db->prepare("DELETE FROM grade_level WHERE id = :grade_id");
        $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: /BCCI/campus-grades");
            exit();
        } else {
            echo "Error: Could not delete role.";
        }
    }
}
public function showCampusSection() {
    // Retrieve sections
    $stmt = $this->db->prepare("SELECT * FROM sections");
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve teachers with default names if profile information is missing
    $stmt = $this->db->prepare("
        SELECT 
            u.user_id, 
            u.role_id,
            CONCAT(
                COALESCE(p.last_name, 'Joe'), ', ', 
                COALESCE(p.first_name, 'John'), ' ', 
                LEFT(COALESCE(p.middle_name, 'Smith'), 1)
            ) AS name
        FROM 
            users u
        LEFT JOIN 
            profiles p ON u.user_id = p.profile_id
        WHERE 
            u.role_id = 2
    ");
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Include the view
    include 'views/campus/section.php';
}

public function createCampusSection() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $section_name = trim($_POST['section_name']);
        $section_session = trim($_POST['section_sched']);
        if (empty($section_name)) {
            echo "Error: Role name cannot be empty.";
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO sections (section_name,daytime) VALUES (:section_name,:daytime)");
        $stmt->bindParam(':section_name', $section_name, PDO::PARAM_STR);
        $stmt->bindParam(':daytime', $section_session, PDO::PARAM_STR);
        if ($stmt->execute()) {
            header("Location: /BCCI/campus-sections");
            exit();
        } else {
            echo "Error: Could not create role.";
        }
    }
}
public function deleteCampusSection() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section_id'])) {
        $section_id = (int) $_POST['section_id'];
        $stmt = $this->db->prepare("DELETE FROM sections WHERE id = :section_id");
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
           header("Location: /BCCI/campus-sections");
           exit();
       } else {
        echo "Error: Could not delete role.";
    }
}
}
public function updateCampusSection() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sec_id']) && isset($_POST['sec_name']) && isset($_POST['sec_sched'])) {
        $sec_id = (int) $_POST['sec_id'];
        $section_name = trim($_POST['sec_name']);
        $section_session = trim($_POST['sec_sched']);
        $stmt = $this->db->prepare("UPDATE sections SET section_name = :section_name,  daytime = :section_session WHERE id = :sec_id");
        $stmt->bindParam(':section_name', $section_name, PDO::PARAM_STR);
        $stmt->bindParam(':section_session', $section_session, PDO::PARAM_STR);
        $stmt->bindParam(':sec_id', $sec_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
           header("Location: /BCCI/campus-sections");
           exit();
       } else {
        echo "Error: Could not update role.";
    }
}
}
public function updateAdviser() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section_id'], $_POST['adviser_id'])) {
        session_start();
        $sectionId = (int)$_POST['section_id'];
        $adviserId = (int)$_POST['adviser_id'];
        try {
            $stmt = $this->db->prepare("UPDATE sections SET adviser_id = :adviser_id WHERE id = :section_id");
            $stmt->bindParam(':adviser_id', $adviserId, PDO::PARAM_INT);
            $stmt->bindParam(':section_id', $sectionId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: /BCCI/campus-sections");
                exit();
            } else {
                $_SESSION['error'] = "Error: Could not update adviser.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // UNIQUE constraint violation
                $_SESSION['error'] = "Error: Adviser is already assigned to another section.";
            } else {
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
        }
        header("Location: /BCCI/campus-sections");
        exit();
    }
}





public function showCampusSubject() {
    $stmt = $this->db->prepare("SELECT * FROM subjects");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include 'views/campus/subject.php';
}

public function createCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sub_name = trim($_POST['sub_name']);
        $sub_desc = trim($_POST['sub_desc']);
        if (empty($sub_name)) {
            echo "Error: Role name cannot be empty.";
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO subjects (name,description) VALUES (:name,:description)");
        $stmt->bindParam(':name', $sub_name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $sub_desc, PDO::PARAM_STR);
        if ($stmt->execute()) {
            header("Location: /BCCI/campus-subjects");
            exit();
        } else {
            echo "Error: Could not create role.";
        }
    }
}
public function deleteCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub_id'])) {
        $sub_id = (int) $_POST['sub_id'];
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = :sub_id");
        $stmt->bindParam(':sub_id', $sub_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
           header("Location: /BCCI/campus-subjects");
           exit();
       } else {
        echo "Error: Could not delete role.";
    }
}
}
public function updateCampusSubject() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub_id']) && isset($_POST['sub_name'])) {
        $sub_id = (int) $_POST['sub_id'];
        $sub_name = trim($_POST['sub_name']);
        $sub_desc = trim($_POST['sub_desc']);

        $stmt = $this->db->prepare("UPDATE subjects SET name = :name,  description = :description WHERE id = :sub_id");
        $stmt->bindParam(':name', $sub_name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $sub_desc, PDO::PARAM_STR);
        $stmt->bindParam(':sub_id', $sub_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
           header("Location: /BCCI/campus-subjects");
           exit();
       } else {
        echo "Error: Could not update role.";
    }
}
}




public function getGradeSections() {
    $input = json_decode(file_get_contents('php://input'), true);
    $gradeLevelId = $input['glId'];

    // Fetch role and assigned permissions for the grade level
    $stmt = $this->db->prepare("SELECT section_ids FROM grade_level WHERE id = :glID");
    $stmt->bindParam(':glID', $gradeLevelId, PDO::PARAM_INT);
    $stmt->execute();
    $gradelevel = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert section_ids from a string to an array
    $assigned_section = !empty($gradelevel['section_ids']) ? explode(',', $gradelevel['section_ids']) : [];

    // Fetch all sections
    $stmt = $this->db->prepare("SELECT * FROM sections");
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch sections already assigned to other grade levels
    $stmt = $this->db->prepare("SELECT section_ids FROM grade_level WHERE id != :glID");
    $stmt->bindParam(':glID', $gradeLevelId, PDO::PARAM_INT);
    $stmt->execute();
    $assigned_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert assigned section ids to a flat list
    $assigned_section_ids = [];
    foreach ($assigned_sections as $row) {
        $assigned_section_ids = array_merge($assigned_section_ids, explode(',', $row['section_ids']));
    }

    // Filter out the sections already assigned to other grade levels
    $available_sections = array_filter($sections, function($section) use ($assigned_section_ids) {
        return !in_array($section['id'], $assigned_section_ids);
    });

    echo json_encode([
        'success' => true,
        'sections' => array_values($available_sections), // Re-index the filtered array
        'assigned_section' => $assigned_section
    ]);
}






         public function updateGradeSections() {
        // Get the JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        $glid = $data['glid'];
        $sections = implode(',', $data['sections']); // Convert array to comma-separated string

        try {
            // Update the roles table with the new permission_id value
            $stmt = $this->db->prepare("UPDATE grade_level SET section_ids = :section_ids WHERE id = :glid");
            $stmt->bindParam(':section_ids', $sections, PDO::PARAM_STR);
            $stmt->bindParam(':glid', $glid, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Permissions updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to execute update statement.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }


public function getGradeSubjects() {
    $input = json_decode(file_get_contents('php://input'), true);
    $gradeLevelId = $input['glId'];

    // Fetch the assigned subjects for the grade level
    $stmt = $this->db->prepare("SELECT subject_ids FROM grade_level WHERE id = :glID");
    $stmt->bindParam(':glID', $gradeLevelId, PDO::PARAM_INT);
    $stmt->execute();
    $gradeLevel = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert subject_ids from a string to an array
    $assigned_subjects = explode(',', $gradeLevel['subject_ids']);

    // Fetch all subjects
    $stmt = $this->db->prepare("SELECT * FROM subjects");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects already assigned to other grade levels
    $stmt = $this->db->prepare("SELECT subject_ids FROM grade_level WHERE id != :glID");
    $stmt->bindParam(':glID', $gradeLevelId, PDO::PARAM_INT);
    $stmt->execute();
    $assigned_subjects_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert assigned subject ids to a flat list
    $assigned_subject_ids = [];
    foreach ($assigned_subjects_rows as $row) {
        $assigned_subject_ids = array_merge($assigned_subject_ids, explode(',', $row['subject_ids']));
    }

    // Filter out the subjects already assigned to other grade levels
    $available_subjects = array_filter($subjects, function($subject) use ($assigned_subject_ids) {
        return !in_array($subject['id'], $assigned_subject_ids);
    });

    echo json_encode([
        'success' => true,
        'subjects' => array_values($available_subjects), // Re-index the filtered array
        'assigned_subjects' => $assigned_subjects
    ]);
}

public function updateGradeSubjects() {
    $data = json_decode(file_get_contents('php://input'), true);
    $glid = $data['glid'];
    $subjects = implode(',', $data['subjects']); // Convert array to comma-separated string

    // Check for conflicts with other grade levels
    try {
        // Check if any of the selected subjects are already assigned to other grade levels
        $stmt = $this->db->prepare("SELECT id FROM grade_level WHERE id != :glid AND FIND_IN_SET(:subject_id, subject_ids) > 0");
        $stmt->bindParam(':glid', $glid, PDO::PARAM_INT);

        // Loop through selected subjects to check for conflicts
        foreach ($data['subjects'] as $subject_id) {
            $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // If there's a conflict (subject already assigned to another grade level), return error
                echo json_encode(['success' => false, 'message' => 'One or more selected subjects are already assigned to another grade.']);
                return;
            }
        }

        // If no conflict, proceed with the update
        $stmt = $this->db->prepare("UPDATE grade_level SET subject_ids = :subject_ids WHERE id = :glid");
        $stmt->bindParam(':subject_ids', $subjects, PDO::PARAM_STR);
        $stmt->bindParam(':glid', $glid, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->rowCount() > 0) {
                // If update successful
                echo json_encode(['success' => true, 'message' => 'Subjects updated successfully.']);
            } else {
                // No rows affected (i.e., the data may not have changed)
                echo json_encode(['success' => false, 'message' => 'No changes made, the data is already up to date.']);
            }
        } else {
            // Query execution failed, return error message
            echo json_encode(['success' => false, 'message' => 'Failed to update subjects.']);
        }
    } catch (PDOException $e) {
        // Log any exceptions and return the error message
        error_log($e->getMessage());  // Log the error for debugging purposes
        echo json_encode(['success' => false, 'message' => 'An error occurred during the update. Please try again later.']);
    }
}







}
?>