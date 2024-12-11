<?php 
require_once 'BaseController.php'; 
class DepartmentController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['4']);  
    } 
    public function show()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM department ");
            $stmt->execute();
            $department = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        include 'views/campus/department.php';
    }
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and validate the role name
            $deptName = trim($_POST['department_name']);
            if (empty($deptName)) {
               $_SESSION['error'] = "Error: Department name cannot be empty.";
               return;
           }
        // Prepare and execute the database insert
           $stmt = $this->db->prepare("INSERT INTO department (course_name) VALUES (:course_name)");
           $stmt->bindParam(':course_name', $deptName, PDO::PARAM_STR);
           if ($stmt->execute()) {
               $_SESSION['success'] = "Department Successfuly register.";
               header("Location: /BCCI/campus-department");
               exit();
           } else {
               $_SESSION['error'] = "Error: Could not create Department.";
           }
       }
   }










   
public function getRooms() {
    $input = json_decode(file_get_contents('php://input'), true);
    $deptID = $input['id']; // Department ID

    // Fetch rooms assigned to the current department
    $stmt = $this->db->prepare("SELECT room_ids FROM department WHERE id = :deptID");
    $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
    $stmt->execute();
    $current_dept_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $current_assigned_rooms = !empty($current_dept_data['room_ids']) ? explode(',', $current_dept_data['room_ids']) : [];

    // Fetch rooms assigned to other departments
    $stmt = $this->db->prepare("SELECT room_ids FROM department WHERE id != :deptID");
    $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
    $stmt->execute();
    $other_departments_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine all rooms assigned to other departments
    $rooms_taken_by_others = [];
    foreach ($other_departments_data as $row) {
        $rooms_taken_by_others = array_merge($rooms_taken_by_others, explode(',', $row['room_ids']));
    }
    $rooms_taken_by_others = array_filter(array_unique($rooms_taken_by_others)); // Remove duplicates and empty values

    // Fetch all rooms
    $stmt = $this->db->prepare("SELECT * FROM rooms");
    $stmt->execute();
    $all_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter available rooms (not taken by other departments or already assigned to this department)
    $available_rooms = array_filter($all_rooms, function($room) use ($rooms_taken_by_others, $current_assigned_rooms) {
        return !in_array($room['id'], $rooms_taken_by_others) || in_array($room['id'], $current_assigned_rooms);
    });





    // Return the result as JSON
    echo json_encode([
        'success' => true,
        'rooms' => array_values($available_rooms), // Re-index the filtered array
        'assigned_rooms' => $current_assigned_rooms
    ]);
}




public function updateDepartmentRoomIds() {
    // Get the input data from the frontend (AJAX request)
    $input = json_decode(file_get_contents('php://input'), true);
    $departmentId = $input['id']; // The department ID
    $roomIds = isset($input['room_ids']) ? $input['room_ids'] : []; // Array of room IDs

    // Ensure roomIds is an array (if it's a string, split it into an array)
    if (is_string($roomIds)) {
        $roomIds = explode(',', $roomIds);  // Split a comma-separated string into an array
    }

    // Check if the department exists
    $stmt = $this->db->prepare("SELECT * FROM department WHERE id = :departmentId");
    $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
    $stmt->execute();
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$department) {
        // Department not found
        echo json_encode(['success' => false, 'message' => 'Department not found']);
        return;
    }

    // Update the room assignments for the department
    try {
        // Convert the room IDs array to a comma-separated string
        $roomIdsString = implode(',', $roomIds);

        // Prepare the SQL statement to update the department's room assignments
        $stmt = $this->db->prepare("UPDATE department SET room_ids = :roomIds WHERE id = :departmentId");
        $stmt->bindParam(':roomIds', $roomIdsString, PDO::PARAM_STR);
        $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Rooms updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update rooms']);
        }
    } catch (Exception $e) {
        // Handle any exceptions that occur during the update
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}







public function getSubjects() {
    $input = json_decode(file_get_contents('php://input'), true);
    $deptID = $input['id']; // Course ID
    $semID = $input['sem']; // Current Semester

    // Fetch all subjects assigned to the same course (across all semesters)
    $stmt = $this->db->prepare("SELECT subject_ids FROM semester WHERE course_id = :deptID");
    $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
    $stmt->execute();
    $assigned_subjects_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine all assigned subjects for this course into a single array
    $all_assigned_subjects = [];
    foreach ($assigned_subjects_data as $row) {
        $all_assigned_subjects = array_merge($all_assigned_subjects, explode(',', $row['subject_ids']));
    }
    $all_assigned_subjects = array_filter(array_unique($all_assigned_subjects)); // Remove duplicates and empty values

    // Fetch subjects assigned to the current semester
    $stmt = $this->db->prepare("SELECT subject_ids FROM semester WHERE course_id = :deptID AND semester = :sem");
    $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
    $stmt->bindParam(':sem', $semID, PDO::PARAM_INT);
    $stmt->execute();
    $current_semester_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $current_assigned_subjects = !empty($current_semester_data['subject_ids']) ? explode(',', $current_semester_data['subject_ids']) : [];

    // Fetch all subjects
    $stmt = $this->db->prepare("SELECT * FROM subjects");
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter out subjects already assigned in other semesters of the same course
    $available_subjects = array_filter($subjects, function($subject) use ($all_assigned_subjects, $current_assigned_subjects) {
        return !in_array($subject['id'], $all_assigned_subjects) || in_array($subject['id'], $current_assigned_subjects);
    });

    // Return the result as JSON
    echo json_encode([
        'success' => true,
        'subjects' => array_values($available_subjects), // Re-index the filtered array
        'assigned_subject' => $current_assigned_subjects
    ]);
}


         public function updateDepartmentSubject() {
        // Get the JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        $deptID = $data['department_id'];
        $sections = implode(',', $data['subject_ids']); // Convert array to comma-separated string
        $semester = $data['semid'];



        try {
            // Update the roles table with the new permission_id value
            $stmt = $this->db->prepare("UPDATE semester SET subject_ids = :subject_ids WHERE course_id = :deptID AND semester = :sem");
            $stmt->bindParam(':subject_ids', $sections, PDO::PARAM_STR);
            $stmt->bindParam(':deptID', $deptID, PDO::PARAM_INT);
             $stmt->bindParam(':sem', $semester, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Permissions updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to execute update statement.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
} //end