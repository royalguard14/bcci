<?php 
require_once 'BaseController.php'; 
 
class StudentsController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['13']);  
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



public function addsubject()
{
    if ($this->myEnrollmentStatus <= 0 && $this->campusDataEnrollmentStatus == 1) {
        $userId = $_SESSION['user_id']; // Get user ID from session

        // Step 1: Fetch last enrollment
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
            // No enrollment history, fetch first semester subjects
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
            $semesterId = $lastEnrollment['semester_id'];
            $courseId = $lastEnrollment['course_id'];

            // Fetch current semester subjects
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

        $failedSubjects = [];
        $schedules = [];

        foreach ($subjectIds as $subjectId) {
            // Fetch prerequisite data
            $stmt = $this->db->prepare(
                "SELECT pre_req 
                 FROM subjects 
                 WHERE id = :subject_id"
            );
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $subjectData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subjectData) {
                continue; // Skip if subject data is not found
            }

            $preReqNames = explode(',', $subjectData['pre_req']);
            $allPreReqsPassed = true;

            foreach ($preReqNames as $preReqName) {
                $stmt = $this->db->prepare(
                    "SELECT grade 
                     FROM grade_records 
                     WHERE user_id = :user_id AND subject_id = (
                         SELECT id FROM subjects WHERE id = :pre_req_name
                     )"
                );
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':pre_req_name', $preReqName, PDO::PARAM_STR);
                $stmt->execute();
                $preReqGradeData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$preReqGradeData || $preReqGradeData['grade'] < 60) {
                    $allPreReqsPassed = false;
                    break;
                }
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

            if (!$allPreReqsPassed) {
                $failedSubjects[] = $subjectId;
                continue;
            }
        }

        // Fetch detailed subject data for failed subjects
        $subjectsDetails = [];
        foreach ($failedSubjects as $subjectId) {
            $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = :subject_id");
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $subject = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subject) {
                $subjectsDetails[] = $subject;
            }
        }

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

// public function checkScheduleConflict() {
//     // Get the schedule IDs from the AJAX request
//     $schedule_ids = isset($_POST['schedule_ids']) ? $_POST['schedule_ids'] : [];

//     // Check if there are selected schedules
//     if (!empty($schedule_ids)) {
//         // Convert the schedule IDs into a comma-separated string
//         $schedule_ids_str = implode(',', array_map('intval', $schedule_ids)); // Ensure values are integers for security

//         try {
//             // Query to check for conflicts based on the same day and time_slot
//             $stmt = $this->db->prepare("
//                 SELECT GROUP_CONCAT(id) as conflict_ids, day, time_slot
//                 FROM schedules
//                 WHERE id IN ($schedule_ids_str)
//                 GROUP BY day, time_slot
//                 HAVING COUNT(*) > 1
//             ");
//             $stmt->execute();
//             $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);

//             if ($conflicts) {
//                 // Build detailed conflict data
//                 $response = [
//                     'conflict' => true,
//                     'details' => $conflicts
//                 ];
//             } else {
//                 $response = ['conflict' => false];
//             }
//         } catch (Exception $e) {
//             // Handle error
//             $response = ['error' => 'Database error: ' . $e->getMessage()];
//         }
//     } else {
//         $response = ['error' => 'No schedule IDs provided.'];
//     }

//     // Send response as JSON
//     header('Content-Type: application/json');
//     echo json_encode($response);
// }






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











 
} 
