<?php 
require_once 'BaseController.php'; 

class RegistrarController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['7','5','9']);  
    } 



private function fetchStudentsByStatus($isActive) {
    $stmt = $this->db->prepare("
        SELECT
        u.username as username,
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
        AND (p.first_name IS NOT NULL OR p.last_name IS NOT NULL OR p.birth_date IS NOT NULL)  -- Only include users with profile data
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





 public function enrollies()
{
    try {
        $stmt = $this->db->prepare("
            SELECT
                eh.id as ehID, 
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
                d.course_name,
                d.code as dcode,
                eh.subjects_taken,
                eh.status,
                eh.semester_id,
                CONCAT(ay.start, '-', ay.end) AS acads_year, 
                eh.enrollment_date
            FROM 
                enrollment_history eh
            LEFT JOIN
                profiles p ON p.profile_id = eh.user_id
            LEFT JOIN
                department d ON d.id = eh.course_id
            LEFT JOIN 
                academic_year ay ON ay.id = eh.academic_year_id
        ");
        $stmt->execute();
        $evaluation = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode subjects_taken and fetch additional data
        foreach ($evaluation as &$record) {
            $subjectsTaken = json_decode($record['subjects_taken'], true);
            $subjectNames = [];
            $scheduleIds = [];

            if ($subjectsTaken) {
                foreach ($subjectsTaken as $entry) {
                    // Fetch subject name and schedule IDs
                    $subjectStmt = $this->db->prepare("
                        SELECT 
                            s.name AS subject_name, 
                            sc.id AS schedule_id
                        FROM 
                            subjects s
                        LEFT JOIN 
                            schedules sc ON sc.subject_id = s.id
                        WHERE 
                            s.id = :subject_id
                    ");
                    $subjectStmt->execute(['subject_id' => $entry['subjectId']]);
                    $subjectData = $subjectStmt->fetch(PDO::FETCH_ASSOC);

                    if ($subjectData) {
                        $subjectNames[] = $subjectData['subject_name'];
                        $scheduleIds = array_merge($scheduleIds, $entry['scheduleIds']);
                    }
                }
            }

            // Add the details to the record
            $record['subject_names'] = implode(', ', $subjectNames);
            $record['schedule_ids'] = implode(', ', $scheduleIds);
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    include 'views/registrar/evaluation.php';
}


public function toPayment()
{
    try {
        // Retrieve the enrollment history ID from the POST request
        if (isset($_POST['ehID'])) {
            $ehID = $_POST['ehID'];

            // Prepare the SQL query to update the status
            $stmt = $this->db->prepare("
                UPDATE enrollment_history 
                SET status = :status 
                WHERE id = :ehID
            ");

            // Bind parameters
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':ehID', $ehID, PDO::PARAM_INT);

            // Set the new status
            $status = "Pending Payment";

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display success message
                $_SESSION['success'] = "Status updated to 'Pending Payment' successfully.";
             
                // You can also use a redirect here
                header('Location: enrollies');
                exit;
            } else {
              
                $_SESSION['error'] = "Failed to update the status.";
            }
        } else {
                $_SESSION['error'] = "No ehID provided.";
        }
    } catch (Exception $e) {
        // Handle any exceptions
         $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}



public function toPaymentConfirm()
{
    try {
        // Retrieve the enrollment history ID from the POST request
        if (isset($_POST['ehID'])) {
            $ehID = $_POST['ehID'];

            // Prepare the SQL query to update the status
            $stmt = $this->db->prepare("
                UPDATE enrollment_history 
                SET status = :status 
                WHERE id = :ehID
            ");

            // Bind parameters
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':ehID', $ehID, PDO::PARAM_INT);

            // Set the new status
            $status = "ENROLLED";

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display success message
                $_SESSION['success'] = "Status updated to 'ENROLLED' successfully.";
             
                // You can also use a redirect here
                header('Location: enrollies');
                exit;
            } else {
              
                $_SESSION['error'] = "Failed to update the status.";
            }
        } else {
             $_SESSION['error'] = "No ehID provided.";
        
        }
    } catch (Exception $e) {
         $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}


public function getDetailCOE() {
    // Check if ehID is passed
    if (isset($_GET['ehID'])) {
        $ehID = $_GET['ehID'];
        
        try {
            // Query to fetch COE details along with subject and schedule information
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
                    d.course_name as course_name,
                    CONCAT(ay.start, '-', ay.end) AS acads_year, 
                    eh.enrollment_date,
                    eh.subjects_taken,
                    eh.status,
                    eh.semester_id,
                    SUM(pmt.amount) AS total_payment
                FROM enrollment_history eh
                LEFT JOIN profiles p ON p.profile_id = eh.user_id
                LEFT JOIN payments pmt ON pmt.eh_id = eh.id
                LEFT JOIN department d ON d.id = eh.course_id
                LEFT JOIN academic_year ay ON ay.id = eh.academic_year_id
                WHERE eh.id = :ehID
                GROUP BY eh.id
            ");
            $stmt->bindParam(':ehID', $ehID);
            $stmt->execute();
            $coeDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($coeDetails) {
                // Decode subjects_taken into an array of subjects and schedules
                $subjectsTaken = json_decode($coeDetails['subjects_taken'], true);
                $subjectNames = [];
                $allSchedules = [];

                // Fetch subject and schedule details for each subject and its scheduleIds
                if ($subjectsTaken) {
                    foreach ($subjectsTaken as $subject) {
                        // Fetch subject name and unit_lab using subjectId
                        $subjectStmt = $this->db->prepare("
                            SELECT 
                                s.name AS subject_name,
                                s.unit_lab
                            FROM subjects s
                            WHERE s.id = :subject_id
                        ");
                        $subjectStmt->execute(['subject_id' => $subject['subjectId']]);
                        $subjectData = $subjectStmt->fetch(PDO::FETCH_ASSOC);
dd($subjectData);
                        if ($subjectData) {
                            $subjectNames[] = $subjectData['subject_name'];

                            // Now fetch all schedules for each scheduleId in the scheduleIds array
                            $scheduleDetails = [];
                            foreach ($subject['scheduleIds'] as $scheduleId) {
                                $scheduleStmt = $this->db->prepare("
                                    SELECT
                                        sc.day,
                                        sc.time_slot,
                                        sc.session_type,
                                        sc.adviser,
                                        sc.batch
                                    FROM schedules sc
                                    WHERE sc.id = :schedule_id
                                ");
                                $scheduleStmt->execute(['schedule_id' => $scheduleId]);
                                $scheduleData = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

                                if ($scheduleData) {
                                    // Store schedule details for each scheduleId
                                    $scheduleDetails[] = $scheduleData['day'] . ', ' . $scheduleData['time_slot'] . ' | ' . $scheduleData['session_type'] ;
                                }
                            }

                            // Combine all schedules for the subject and store it
                            $allSchedules[] = implode('; ', $scheduleDetails);
                        }
                    }
                }

                // Add subjects and schedules to the COE details
                $coeDetails['subject_names'] = implode(', ', $subjectNames);
                $coeDetails['schedules'] = implode('; ', $allSchedules);

                

                // Output the COE details
                echo "<h4>Certificate of Employment</h4>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($coeDetails['fullname']) . "</p>";
                echo "<p><strong>Course:</strong> " . htmlspecialchars($coeDetails['course_name']) . "</p>";
                echo "<p><strong>Academic Year:</strong> " . htmlspecialchars($coeDetails['acads_year']) . "</p>";
                echo "<p><strong>Enrollment Date:</strong> " . htmlspecialchars($coeDetails['enrollment_date']) . "</p>";
                echo "<p><strong>Status:</strong> " . htmlspecialchars($coeDetails['status']) . "</p>";
                
                // List Subjects and Schedules
                echo "<h5>Subjects</h5>";
                echo "<table border='1' cellpadding='5'>
                        <tr><th>Subject</th><th>Schedule</th></tr>";
                $subjectNames = explode(', ', $coeDetails['subject_names']);
                $allSchedules = explode('; ', $coeDetails['schedules']);
                
                foreach ($subjectNames as $index => $subject) {
                    echo "<tr><td>$subject</td><td>{$allSchedules[$index]}</td></tr>";
                }
                echo "</table>";
                
                // Display total payment
                echo "<p><strong>Total Payment:</strong> ₱" . number_format($coeDetails['total_payment'], 2) . "</p>";
            } else {
                echo "<p>No details found for this enrollment.</p>";
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}





}#end

