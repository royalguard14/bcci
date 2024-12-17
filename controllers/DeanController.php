<?php 
require_once 'BaseController.php'; 

class DeanController extends BaseController { 



    public function __construct($db) { 
        parent::__construct($db, ['15']);  
    } 






public function updateAdviser()
{
    try {
        // Get the input data from the request
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (!isset($input['schedule_ids']) || !array_key_exists('adviser_id', $input)) {
            echo json_encode(['success' => false, 'error' => 'Invalid input data']);
            return;
        }

        $scheduleIds = $input['schedule_ids'];  // Array of schedule IDs
        $adviserId = $input['adviser_id'];      // Adviser ID (can be null)

        // Get the list of schedules already assigned to this adviser
        $stmt = $this->db->prepare("SELECT day, time_slot
                                    FROM schedules
                                    WHERE adviser = :adviser");
        $stmt->bindValue(':adviser', $adviserId, PDO::PARAM_INT);
        $stmt->execute();
        $allschedniAdviser = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the day and time_slot for the provided scheduleIds
        $placeholders = implode(',', array_fill(0, count($scheduleIds), '?'));
        $stmt = $this->db->prepare("SELECT day, time_slot FROM schedules WHERE id IN ($placeholders)");
        $stmt->execute($scheduleIds); // Bind the actual schedule IDs
        $scheduledTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Merge the two arrays of schedules
        $mergedSchedules = array_merge($allschedniAdviser, $scheduledTimes);

        $encounteredSchedules = [];
        $duplicates = [];

        // Check for conflicts (duplicate day and time_slot pairs)
        foreach ($mergedSchedules as $schedule) {
            $dayTime = $schedule['day'] . '|' . $schedule['time_slot'];

            // If this schedule has already been encountered, it's a duplicate
            if (in_array($dayTime, $encounteredSchedules)) {
                $duplicates[] = $schedule;
            } else {
                $encounteredSchedules[] = $dayTime;
            }
        }

        // If duplicates are found, return error message with details
        if (count($duplicates) > 0) {
            $duplicateDetails = [];
            foreach ($duplicates as $duplicate) {
                $duplicateDetails[] = $duplicate['day'] . ' ' . $duplicate['time_slot'];
            }
            echo json_encode(['success' => false, 'error' => 'Conflict found for schedules: ' . implode(', ', $duplicateDetails)]);
            return;
        }

        // Proceed to update the adviser if no conflicts were found
        if (!is_array($scheduleIds) || empty($scheduleIds)) {
            echo json_encode(['success' => false, 'error' => 'No schedules provided']);
            return;
        }

        // Prepare the SQL query to update the adviser for the provided schedules
        $placeholders = implode(',', array_fill(0, count($scheduleIds), '?'));
        
        // Check if adviser_id is null, update the query dynamically
        if (is_null($adviserId)) {
            $stmt = $this->db->prepare("UPDATE schedules SET adviser = NULL WHERE id IN ($placeholders)");
            $params = $scheduleIds;
        } else {
            $stmt = $this->db->prepare("UPDATE schedules SET adviser = ? WHERE id IN ($placeholders)");
            $params = array_merge([$adviserId], $scheduleIds);
        }

        $stmt->execute($params);

        // Return success or failure message based on the update result
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No rows were updated']);
        }
    } catch (Exception $e) {
        // Handle any errors that occur during the process
        echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
    }
}







public function fetchSchedule() {
    try {
        // Get the list of subject_ids from earlier
        $stmt = $this->db->prepare("SELECT GROUP_CONCAT(DISTINCT subject_id) AS subject_ids
            FROM schedules
            WHERE course_id = :course_id
            GROUP BY course_id");
        $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
        $stmt->execute();
        $subject_ids = $stmt->fetch(PDO::FETCH_ASSOC)['subject_ids'];

        // If subject_ids are empty or invalid, log an error and stop
        if (empty($subject_ids)) {
            throw new Exception("No subject IDs found for this course.");
        }

        // Fetch schedules along with subject names, schedule IDs, and advisers
        $stmt = $this->db->prepare("
            SELECT s.id AS schedule_id, s.subject_id, sub.name AS subject_name, s.batch, s.day, s.time_slot, s.adviser
            FROM schedules s
            JOIN subjects sub ON s.subject_id = sub.id
            WHERE s.academic_id = :academic_id 
            AND s.course_id = :course_id 
            AND s.subject_id IN ($subject_ids)
            ORDER BY s.subject_id, s.batch, s.day, s.time_slot
            ");

        $stmt->bindValue(':academic_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT);
        $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
        $stmt->execute();
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to store the merged schedules
        $mergedSchedules = [];

        // Group schedules by subject_id and batch, and concatenate time slots for the same batch
        foreach ($schedules as $schedule) {
            // Initialize subject_id and batch if not already in the array
            if (!isset($mergedSchedules[$schedule['subject_id']])) {
                $mergedSchedules[$schedule['subject_id']] = [
                    'subject_name' => $schedule['subject_name'],
                    'batches' => []
                ];
            }
            if (!isset($mergedSchedules[$schedule['subject_id']]['batches'][$schedule['batch']])) {
                $mergedSchedules[$schedule['subject_id']]['batches'][$schedule['batch']] = [
                    'batch' => $schedule['batch'],
                    'schedule' => [],
                    'schedule_ids' => [],  // Initialize an empty array to store schedule IDs
                    'adviser' => $schedule['adviser']  // Store the adviser for each batch
                ];
            }

            // Concatenate day and time_slot for each subject_id and batch
            $mergedSchedules[$schedule['subject_id']]['batches'][$schedule['batch']]['schedule'][] = $schedule['day'] . " " . $schedule['time_slot'];
            // Add the schedule_id to the array
            $mergedSchedules[$schedule['subject_id']]['batches'][$schedule['batch']]['schedule_ids'][] = $schedule['schedule_id'];
        }

        // Now we prepare the final format as a clean array for output
        $finalSchedules = [];
        foreach ($mergedSchedules as $subject_id => $subjectData) {
            foreach ($subjectData['batches'] as $batch => $batchData) {
                $finalSchedules[] = [
                    'subject_id' => $subject_id,
                    'subject_name' => $subjectData['subject_name'],
                    'batch' => $batch,
                    'schedule' => implode(", ", $batchData['schedule']), // Join all schedule times for this batch
                    'schedule_ids' => $batchData['schedule_ids'],  // Add the schedule IDs array
                    'adviser' => $batchData['adviser']  // Include the adviser information
                ];
            }
        }



// Fetch advisers with role_id = 2
        $stmt = $this->db->prepare("SELECT  

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
            ) AS name,
          u.user_id as id
          FROM users u
          LEFT JOIN profiles p ON p.profile_id = u.user_id
          LEFT JOIN employment_info ei ON ei.user_id = u.user_id
          WHERE role_id = 3 AND ei.course_id = :course_id");
        $stmt->bindValue(':course_id', $this->deanDeptid, PDO::PARAM_INT);
        $stmt->execute();
        $advisers = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Pass the data to the view
        include 'views/dean/schedule.php';

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
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