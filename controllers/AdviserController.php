<?php 
require_once 'BaseController.php'; 
 
class AdviserController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['6','9']);  
    } 



    public function gradestudent(){
    // Get the schedule_id from the POST request
    if (isset($_POST['schedule_id'])) {
        $scheduleId = $_POST['schedule_id'];

        // Fetch all data from enrollment_history
        $stmt = $this->db->prepare("SELECT 
            eh.subjects_taken,
            eh.user_id,
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
                ) AS fullname
            FROM enrollment_history eh
            LEFT JOIN profiles p ON p.profile_id = eh.user_id");
        $stmt->execute();
        $enrollmentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Array to hold the user_ids of students enrolled in the given schedule_id
        $studentsEnrolled = [];

        // Loop through the enrollment history to check for the schedule_id in subjects_taken
        foreach ($enrollmentHistory as $enrollment) {
            // Assuming subjects_taken is a JSON string that contains an array of subjects
            $subjectsTaken = json_decode($enrollment['subjects_taken'], true);

            // Loop through the subjects and check if the schedule_id is in scheduleIds
            foreach ($subjectsTaken as $subject) {
                if (in_array($scheduleId, $subject['scheduleIds'])) {
                    // If a match is found, add the user_id to the array
                    $studentsEnrolled[] = $enrollment['fullname'];
                    break; // No need to check other subjects if we found a match
                }
            }
        }

        // Check if students were found
        if (!$studentsEnrolled) {
            // If no students were found, return an empty array as a response
            echo json_encode(['status' => 'no_students']);
        } else {
            // If students are enrolled, return the students and a flag indicating redirection
            echo json_encode([
                'status' => 'students_found',
                'students' => $studentsEnrolled,
                'redirect_url' => 'gradingsubject'
            ]);

            $_SESSION['activeSchedID'] = $scheduleId;
        }
    }
}

public function gradingsubject(){
    // Get the schedule_id from the GET request
    $scheduleId = $_SESSION['activeSchedID'];


    // Fetch all data from enrollment_history
    $stmt = $this->db->prepare("SELECT 
        s.name,
        s.id
        FROM subjects s
        LEFT JOIN schedules sc ON sc.subject_id = s.id
        WHERE sc.course_id = :course_id AND sc.academic_id = :academic_year_id AND sc.id = :schedID");
    $stmt->bindParam(':schedID', $scheduleId, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $this->deanDeptid, PDO::PARAM_INT);
    $stmt->bindParam(':academic_year_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT);
    $stmt->execute();
    $result =  $stmt->fetch(PDO::FETCH_ASSOC);

$subjectName = $result['name'];

$subjectID = (int)$result['id'];
   

    // Fetch all data from enrollment_history
    $stmt = $this->db->prepare("SELECT 
        eh.subjects_taken,
        eh.user_id,
        eh.id,
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
            ) AS fullname
        FROM enrollment_history eh
        LEFT JOIN profiles p ON p.profile_id = eh.user_id
        WHERE eh.course_id = :course_id AND eh.academic_year_id = :academic_year_id");
    $stmt->bindParam(':course_id', $this->deanDeptid, PDO::PARAM_INT);
    $stmt->bindParam(':academic_year_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT);
    $stmt->execute();

    $enrollmentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Array to hold the user_ids, fullnames, and eh_id of students enrolled in the given schedule_id
    $studentsEnrolled = [];

    // Loop through the enrollment history to check for the schedule_id in subjects_taken
    foreach ($enrollmentHistory as $enrollment) {
        // Assuming subjects_taken is a JSON string that contains an array of subjects
        $subjectsTaken = json_decode($enrollment['subjects_taken'], true);

        // Loop through the subjects and check if the schedule_id is in scheduleIds
        foreach ($subjectsTaken as $subject) {
            if (in_array($scheduleId, $subject['scheduleIds'])) {
                // If a match is found, add both user_id, fullname, and eh_id to the array
                $studentsEnrolled[] = [
                    'user_id' => $enrollment['user_id'],
                    'fullname' => $enrollment['fullname'],
                    'eh_id' => $enrollment['id']
                ];
                break; // No need to check other subjects if we found a match
            }
        }
    }

    // Fetch grades for each student in the studentsEnrolled array
    foreach ($studentsEnrolled as &$student) {
        // Initialize an array to hold grades for each term (1, 2, 3, 4)
        $grades = [
            1 => 0, // Default grade for term 1
            2 => 0, // Default grade for term 2
            3 => 0, // Default grade for term 3
            4 => 0  // Default grade for term 4
        ];

        // Fetch the grade records for the student by matching user_id and eh_id
        $stmt = $this->db->prepare("SELECT term_id, grade 
                                    FROM grade_records 
                                    WHERE user_id = :user_id AND eh_id = :eh_id AND subject_id = :subject");
        $stmt->bindParam(':subject', $subjectID, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $student['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':eh_id', $student['eh_id'], PDO::PARAM_INT);
        $stmt->execute();

        $gradeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through the fetched grade records and assign the grade to the correct term
        foreach ($gradeRecords as $gradeRecord) {
            $termId = $gradeRecord['term_id'];
            if (isset($grades[$termId])) {
                $grades[$termId] = $gradeRecord['grade']; // Assign grade to the respective term
            }
        }

        // Add the grades array to the student data
        $student['grades'] = $grades;
    }

  
    // Include the view for grading
    include 'views/adviser/gradingsubject.php';
}


public function updategrade(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {




    $userId = $_POST['user_id'];
    $ehId = $_POST['eh_id'];
    $termId = $_POST['term_id'];
    $grade = $_POST['grade'];
    $schedule_id = $_SESSION['activeSchedID'];


   try {
    // Fetch the subject_id from the schedules table
    $stmt = $this->db->prepare("SELECT subject_id FROM schedules WHERE id = :id");
    $stmt->bindParam(':id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
    $subject_id = (int) $stmt->fetch(PDO::FETCH_ASSOC)['subject_id'];

    // Check if the record already exists
    $stmt = $this->db->prepare("
        SELECT COUNT(*) AS record_count 
        FROM grade_records 
        WHERE user_id = :user_id 
          AND eh_id = :eh_id 
          AND subject_id = :subject_id 
          AND term_id = :term_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':eh_id', $ehId, PDO::PARAM_INT);
    $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
    $stmt->bindParam(':term_id', $termId, PDO::PARAM_INT);
    $stmt->execute();
    $checkData = $stmt->fetch(PDO::FETCH_ASSOC)['record_count'];

    if ($checkData > 0) {
        // If the record exists, perform an UPDATE
        $stmt = $this->db->prepare("
            UPDATE grade_records 
            SET grade = :grade, updated_at = NOW()
            WHERE user_id = :user_id 
              AND eh_id = :eh_id 
              AND subject_id = :subject_id 
              AND term_id = :term_id
        ");
    } else {
        // If the record doesn't exist, perform an INSERT
        $stmt = $this->db->prepare("
            INSERT INTO grade_records (user_id, eh_id, subject_id, term_id, grade, created_at, updated_at)
            VALUES (:user_id, :eh_id, :subject_id, :term_id, :grade, NOW(), NOW())
        ");
    }

    // Bind parameters for both queries
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':eh_id', $ehId, PDO::PARAM_INT);
    $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
    $stmt->bindParam(':term_id', $termId, PDO::PARAM_INT);
    $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Grade updated successfully."]);
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

}
}




public function mysched(){
    unset($_SESSION['activeSchedID']);
    try {
        $adviserId = $_SESSION['user_id']; // Or replace with actual adviser ID

        // Fetch the adviser’s schedule
        $stmt = $this->db->prepare("SELECT s.day, s.time_slot, sb.name as subject, s.id as schedule_id,s.batch as batch
                                    
                                    FROM schedules s
                                    LEFT JOIN subjects sb ON s.subject_id = sb.id
                                    WHERE adviser = :adviser_id 
                                    ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
        $stmt->bindParam(':adviser_id', $adviserId, PDO::PARAM_INT);
        $stmt->execute();
        $adviserSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array for the adviser schedule
        $adviserScheduleMap = [
            'Monday' => [], 'Tuesday' => [], 'Wednesday' => [],
            'Thursday' => [], 'Friday' => [], 'Saturday' => []
        ];

        // Populate the schedule map with data
        foreach ($adviserSchedules as $schedule) {
            $day = $schedule['day'];
            $timeSlot = $schedule['time_slot'];
            $subjectId = $schedule['subject'];
            $scheduleId = $schedule['schedule_id'];
            $batchID = $schedule['batch'];

            // Add the subject and schedule_id for the respective day and time slot
            $adviserScheduleMap[$day][$timeSlot] = [
                'subject' => $subjectId, 
                'schedule_id' => $scheduleId,
                'batch_id' => $batchID
            ];
        }

        // Extract unique time slots
        $adviserTimeSlots = [];
        foreach ($adviserSchedules as $schedule) {
            $adviserTimeSlots[] = $schedule['time_slot'];
        }
        $timeSlots  = array_unique($adviserTimeSlots);

        function timeTo24Hour($time) {
            preg_match('/(\d{1,2}):(\d{2})(AM|PM)/', $time, $matches);
            $hour = (int) $matches[1];
            $minute = (int) $matches[2];
            $isPM = $matches[3] === 'PM';
            
            if ($hour === 12) $hour = 0; // Convert 12AM to 0 hours
            if ($isPM) $hour += 12; // Convert PM to 24-hour format

            return $hour * 60 + $minute;  // Total minutes for easy comparison
        }

        // Sort time slots based on the start time
        usort($timeSlots, function($a, $b) {
            // Extract start times from each slot (before the '-')
            $startA = explode('-', $a)[0];
            $startB = explode('-', $b)[0];
            
            // Convert start times to 24-hour format and compare
            return timeTo24Hour($startA) - timeTo24Hour($startB);
        });

        // Days of the week in the desired order
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // dd($adviserSchedules); // You can use dd to check the data structure if needed.
    } catch (Exception $e) {
        echo $e->getMessage();
        return;
    }

    include 'views/adviser/myschedule.php';
}









  public function grade(){
        try {
        } catch (Exception $e) {
          echo $e->getMessage();
          return;
      }
      include 'views/adviser/grades.php';
  }




 
} 
