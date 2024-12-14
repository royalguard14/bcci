<?php 
require_once 'BaseController.php'; 
 
class StudentsController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['13']);  
    } 







   public function profilespace()
    {
        $myID = (int) $_SESSION['user_id'];
        try {
            $stmt = $this->db->prepare("
                SELECT
        
                u.email,
                u.username,
           
                p.sex,
                p.photo_path,
                COALESCE(p.first_name, 'No Data') AS first_name,
                COALESCE(p.last_name, 'No Data') AS last_name,
                COALESCE(p.middle_name, 'No Data') AS middle_name,
                DATE_FORMAT(p.birth_date, '%m/%d/%Y') AS birth_date,
                TIMESTAMPDIFF(YEAR, p.birth_date, '2024-10-31') - 
                (DATE_FORMAT(p.birth_date, '%m-%d') > '10-31') AS age,
                COALESCE(p.contact_number, '') AS contact_number,
                COALESCE(p.house_street_sitio_purok, '') AS house_street_sitio_purok,
                COALESCE(p.barangay, '') AS barangay,
                COALESCE(p.municipality_city, '') AS municipality_city,
                COALESCE(p.province, '') AS province
                FROM 
                profiles p
                LEFT JOIN 
                users u ON u.user_id = p.profile_id
                LEFT join
                enrollment_history eh ON eh.user_id = u.user_id 
                WHERE 
                u.user_id = :myID;
                ");
            $stmt->bindValue(':myID', $myID, PDO::PARAM_INT);
            $stmt->execute();
            $myInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 5");
            $stmt->execute();
            $CampusInfoData = $stmt->fetch(PDO::FETCH_ASSOC);
            $present_school_year = (int) $CampusInfoData['function'];






            // Fetch Enrollment History
$stmt = $this->db->prepare("
    SELECT * 
    FROM enrollment_history 
    WHERE user_id = :user_id 
    AND academic_year_id = :academic_year_id
");
$stmt->bindValue(':user_id', $myID, PDO::PARAM_INT);
$stmt->bindValue(':academic_year_id', $present_school_year, PDO::PARAM_INT);
$stmt->execute();
$myenrollment_history = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$myenrollment_history) {
    throw new Exception('You are not enrolled for the current academic year.');
}

// Decode subjects_taken JSON field
$subjects_taken = json_decode($myenrollment_history['subjects_taken'], true);


// Fetch subjects for the grade level
$stmt = $this->db->prepare("
    SELECT 
    s.id AS subject_id,
    s.name AS subject_name
    FROM 
    subjects s
    WHERE 
    FIND_IN_SET(s.id, (
        SELECT 
        sem.subject_ids
        FROM 
        semester sem
        WHERE 
        sem.course_id = :course_id AND sem.semester = :semester
    )) > 0
");
$stmt->bindValue(':course_id', $myenrollment_history['course_id'], PDO::PARAM_INT);
$stmt->bindValue(':semester', $myenrollment_history['semester_id'], PDO::PARAM_INT);
$stmt->execute();
$allSubjectInGrade = $stmt->fetchAll(PDO::FETCH_ASSOC);




$subjectIds = array_column($allSubjectInGrade, 'subject_id');

// Check if subjects were found
if (empty($subjectIds)) {
    throw new Exception('No subjects found for this grade level.');
}

// Get the subject IDs from subjects_taken
$takenSubjectIds = array_column($subjects_taken, 'subjectId');

// Find the intersection of available and taken subjects
$matchedSubjects = array_intersect($subjectIds, $takenSubjectIds);

if (empty($matchedSubjects)) {
    throw new Exception('No matching subjects found for your enrollment.');
}

// You can now proceed with the logic for handling matched subjects

        
            $gradesStmt = $this->db->prepare("
                SELECT 
                gr.user_id, 
                gr.subject_id, 
                gr.grade
                
                FROM 
                grade_records gr
                LEFT JOIN enrollment_history eh on eh.id = gr.eh_id
                WHERE

                gr.user_id = :user_id 
                
                AND gr.subject_id IN (" . implode(',', array_map('intval', $subjectIds)) . ")
                ");
            $gradesStmt->bindValue(':user_id', $myID, PDO::PARAM_INT);
            $gradesStmt->execute();
            $grades = $gradesStmt->fetchAll(PDO::FETCH_ASSOC);
            
         
            $gradeMap = [];
            foreach ($grades as $grade) {
    
                $gradeMap[$grade['subject_id']][$grade['grading_id']] = $grade['grade'];
            }





            $stmt = $this->db->prepare("
                SELECT 
                ar.date, 
                ar.status,
                MONTH(ar.date) AS month,
                YEAR(ar.date) AS year
                FROM 
                attendance_records ar
                LEFT JOIN 
                enrollment_history eh ON ar.user_id = eh.user_id
                WHERE 
                ar.user_id = :user_id
                AND eh.academic_year_id = :academic_year_id
                Order by ar.date DESC
                ");
            $stmt->bindValue(':user_id', $myID, PDO::PARAM_INT);
            $stmt->bindValue(':academic_year_id', $present_school_year, PDO::PARAM_INT);
            $stmt->execute();
            $myAttendance = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $attendanceByMonth = [];

            foreach ($myAttendance as $record) {
    $month = $record['month']; 
    $attendanceByMonth[$month][] = [
        'date' => $record['date'],
        'status' => $record['status'],
    ];
}


} catch (Exception $e) {
    echo $e->getMessage();
    return;
}
include 'views/student/profile.php';

}









public function acad_setup() {
    // Check if the academic report condition is met
    if ($this->acads_report <= 0) {
        // Fetch all departments
        $stmt = $this->db->prepare("SELECT id,course_name FROM department");
        $stmt->execute();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pass the data to the view
        include 'views/student/acad_setting.php';
    } else {
        // Redirect to unauthorized page
        header("Location: home");
        exit();
    }
}



public function updatemycourse()
{
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'User not logged in.';
            header('Location: home');
            exit();
        }

        $user_id = (int) $_SESSION['user_id'];
        $course_id = (int) $_POST['course_id'];

        try {
            // Check if the user already has a course in academic record
            $stmt_check = $this->db->prepare("SELECT * FROM academic_record WHERE user_id = :user_id");
            $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_check->execute();

            // If a record exists, we will insert a new one
            if ($stmt_check->rowCount() > 0) {
                $_SESSION['error'] = 'You already have a course selected.';
            } else {
                // Insert course into academic record
                $stmt = $this->db->prepare("
                    INSERT INTO academic_record (user_id, c_id) 
                    VALUES (:user_id, :course_id)
                ");

                // Bind parameters
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                // Execute query
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Course selected successfully.';


                } else {
                    $_SESSION['error'] = 'Failed to select course. Please try again.';
                }
            }
        } catch (PDOException $e) {
            // Handle database error
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }

        // Redirect back to the same page or another page
        header('Location: acad_setting');
        exit();
    } else {
        // Invalid request handling
        $_SESSION['error'] = 'Invalid request. Please try again.';
        header('Location: home');
        exit();
    }
}





public function addsubject() {
    if ($this->myEnrollmentStatus <= 0 && $this->campusDataEnrollmentStatus == 1) {
        $userId = $_SESSION['user_id']; // Get user ID from session

        // Fetch last enrollment information
        $stmt = $this->db->prepare(
            "SELECT semester_id, course_id, academic_year_id 
            FROM enrollment_history 
            WHERE user_id = :user_id 
            ORDER BY enrollment_date DESC 
            LIMIT 1"
        );
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $lastEnrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lastEnrollment) {
            $_SESSION['lastSem'] = 1;
            // Fetch subjects for the first semester if no enrollment history
            $stmt = $this->db->prepare(
                "SELECT subject_ids 
                FROM semester 
                WHERE semester = 1 AND course_id = (
                    SELECT c_id FROM academic_record WHERE user_id = :user_id
                )"
            );
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $firstSemesterData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$firstSemesterData) {
                $_SESSION['error_message'] = "No subjects found for the first semester.";
                header("Location: some_error_page.php");
                exit();
            }

            $subjectIds = explode(',', $firstSemesterData['subject_ids']);
        } else {
            // Fetch subjects for the current semester based on the last enrollment
            $semesterId = $lastEnrollment['semester_id'];
            $_SESSION['lastSem'] = $semesterId;
            $courseId = $lastEnrollment['course_id'];

            $stmt = $this->db->prepare(
                "SELECT subject_ids 
                FROM semester 
                WHERE id = :semester_id AND course_id = :course_id"
            );
            $stmt->bindParam(':semester_id', $semesterId, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->execute();
            $semesterData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$semesterData) {
                $_SESSION['error_message'] = "No subjects found for the semester.";
                header("Location: some_error_page.php");
                exit();
            }

            $subjectIds = explode(',', $semesterData['subject_ids']);
        }

        // Step 2: Check prerequisites and construct final subject list
        $subjectsToOffer = [];
        foreach ($subjectIds as $subjectId) {
            $stmt = $this->db->prepare("SELECT pre_req FROM subjects WHERE id = :subject_id");
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $subjectData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subjectData && !empty($subjectData['pre_req'])) {
                $preReqIds = explode(',', $subjectData['pre_req']); // Handle multiple prerequisites
                $allPreReqsPassed = true;

                foreach ($preReqIds as $preReqId) {
                    $stmt = $this->db->prepare(
                        "SELECT grade FROM grade_records 
                        WHERE user_id = :user_id AND subject_id = :pre_req_id"
                    );
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':pre_req_id', $preReqId, PDO::PARAM_INT);
                    $stmt->execute();
                    $gradeData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$gradeData || $gradeData['grade'] < 75) {
                        $allPreReqsPassed = false;
                        break;
                    }
                }

                if ($allPreReqsPassed) {
                    $subjectsToOffer[] = $subjectId;
                } else {
                    $subjectsToOffer = array_merge($subjectsToOffer, $preReqIds); // Include missing prerequisites
                }
            } else {
                $subjectsToOffer[] = $subjectId;
            }
        }

        // Step 3: Fetch details for all unique subjects to offer
        $subjectsToOffer = array_unique($subjectsToOffer);
        $detailedSubjects = [];
        foreach ($subjectsToOffer as $subjectId) {
            $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = :subject_id");
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $subjectDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subjectDetails) {
                $detailedSubjects[] = $subjectDetails;
            }

            // Fetch schedules for the subject
            $stmt = $this->db->prepare(
                "SELECT * FROM schedules 
                 WHERE subject_id = :subject_id 
                 AND academic_id = :academic_id 
                 AND course_id = :course_id
                 ORDER BY batch, day, time_slot"
            );
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->bindParam(':academic_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $this->mycourseID, PDO::PARAM_INT);
            $stmt->execute();
            $subjectSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group schedules by batch
            $batches = [];
            foreach ($subjectSchedules as $schedule) {
                $batchNumber = $schedule['batch'];  // Assuming 'batch' is in your schedules table
                $batches[$batchNumber][] = $schedule;
            }

            // Store the grouped schedules
            if ($batches) {
                $schedules[$subjectId] = $batches;
            }
        }

        // Display all detailed subjects in the view
        include 'views/student/addsubject.php';
    } else {
        $_SESSION['error_message'] = "Unauthorized access.";
        header("Location: home");
        exit();
    }
}




public function getSubjs() {
    if (isset($_GET['subject_id']) && isset($_GET['batch_index'])) {
        $subjectId = $_GET['subject_id'];
        $batchIndex = $_GET['batch_index'];

        // Use the correct $this->db for accessing the database
        $stmt = $this->db->prepare("SELECT * FROM schedules WHERE subject_id = :subject_id AND batch = :batch_index AND course_id = :course_id");
        $stmt->bindParam(':course_id', $this->mycourseID, PDO::PARAM_INT);
        $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
        $stmt->bindParam(':batch_index', $batchIndex, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch schedules and return them as JSON
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($schedules);
    }
}




public function checkScheduleConflict() {
    // Get the schedule IDs from the AJAX request
    $schedule_ids = isset($_POST['schedule_ids']) ? $_POST['schedule_ids'] : [];

    // Check if there are selected schedules
    if (!empty($schedule_ids)) {
        // Convert the schedule IDs into a comma-separated string
        $schedule_ids_str = implode(',', $schedule_ids);

        try {
            // Query to check for conflicts based on the same day and time_slot
            $stmt = $this->db->prepare("
                SELECT s1.id AS conflict_id, s2.id AS conflicting_with_id, s1.day, s1.time_slot
                FROM schedules s1
                INNER JOIN schedules s2 ON s1.day = s2.day AND s1.time_slot = s2.time_slot
                WHERE s1.id IN ($schedule_ids_str) AND s2.id IN ($schedule_ids_str) AND s1.id != s2.id
                ORDER BY s1.day, s1.time_slot
            ");
            $stmt->execute();
            $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($conflicts) {
                // Group conflicts by day and time_slot
                $conflictDetails = [];
                foreach ($conflicts as $conflict) {
                    $key = $conflict['day'] . '|' . $conflict['time_slot'];
                    if (!isset($conflictDetails[$key])) {
                        $conflictDetails[$key] = [
                            'day' => $conflict['day'],
                            'time_slot' => $conflict['time_slot'],
                            'conflict_ids' => []
                        ];
                    }
                    $conflictDetails[$key]['conflict_ids'][] = $conflict['conflict_id'];
                    $conflictDetails[$key]['conflict_ids'][] = $conflict['conflicting_with_id'];
                }

                // Prepare unique conflicts for response
                $response = [
                    'conflict' => true,
                    'details' => array_values(array_map(function ($detail) {
                        $detail['conflict_ids'] = implode(',', array_unique($detail['conflict_ids']));
                        return $detail;
                    }, $conflictDetails))
                ];
            } else {
                $response = ['conflict' => false];
            }

        } catch (Exception $e) {
            // Handle error
            $response = ['error' => 'Database error: ' . $e->getMessage()];
        }

    } else {
        $response = ['error' => 'No schedule IDs provided.'];
    }

    // Send response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}



public function enrollSubjects() {
    // Assuming you have a PDO connection to your database
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Decode the enrollment data from the POST request
        $selectedData = json_decode($_POST['enrollmentData'], true); // Decode the JSON string

        // Check if the data is valid
        if (empty($selectedData)) {
            $_SESSION['error'] = "No subjects selected.";
            header("Location: home");
            exit();
        }

        $user_id = $_SESSION['user_id'];  // The logged-in user's ID
        $course_id = $this->mycourseID;  // The course the user is enrolling in
        $semester_id = $_SESSION['lastSem'];  // The current semester ID
        $academic_year_id = $this->campusDataCurrentAcademicYear;  // The academic year
        $status = 'Evaluation';  // Enrollment status (e.g., Pending, Completed)

        // Convert the selected subjects and their schedules into a JSON-encoded string for storage
        $subjects_taken = json_encode($selectedData);

        // SQL query to insert the enrollment data
        $query = "
            INSERT INTO enrollment_history (user_id, course_id, semester_id, subjects_taken, status, academic_year_id, enrollment_date) 
            VALUES (:user_id, :course_id, :semester_id, :subjects_taken, :status, :academic_year_id, NOW())
        ";

        // Prepare the statement
        $stmt = $this->db->prepare($query);

        // Bind parameters to the SQL query
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':semester_id', $semester_id);
        $stmt->bindParam(':subjects_taken', $subjects_taken);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':academic_year_id', $academic_year_id);

         // Execute the statement
        if ($stmt->execute()) {
            // If successful, return a JSON response indicating success
            echo json_encode(['success' => true]);
             
        } else {
            // If an error occurs, return a JSON response with an error message
            echo json_encode([
                'success' => false,
                'error' => 'Error during enrollment.'
            ]);
        }

        exit(); 
        
    }
}





 
} 
