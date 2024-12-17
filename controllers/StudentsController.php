<?php 
require_once 'BaseController.php'; 

class StudentsController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['13']);  
    } 





public function deleteFile(){
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $myID = (int) $_SESSION['user_id'];
     $stmt = $this->db->prepare("
        SELECT
            username
        FROM 
            users
        WHERE 
            user_id = :myID;
    ");
    $stmt->bindValue(':myID', $myID, PDO::PARAM_INT);
    $stmt->execute();
    $myLRN = $stmt->fetch(PDO::FETCH_ASSOC)['username'];
            
    $file = basename($_POST['file']); // Ensure no directory traversal
    $uploadDir = "assets/documents/" . $myLRN . "/";
    $filePath = $uploadDir . $file;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            $_SESSION['success'] = "File deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete the file.";
        }
    } else {
        $_SESSION['error'] = "File does not exist.";
    }
}
        header("Location: /BCCI/documents");
exit();
    
}



public function uploadDocs(){
// Ensure the target directory exists
function createDirectory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$myID = (int) $_SESSION['user_id'];
    $stmt = $this->db->prepare("
        SELECT
            username
        FROM 
            users
        WHERE 
            user_id = :myID;
    ");
    $stmt->bindValue(':myID', $myID, PDO::PARAM_INT);
    $stmt->execute();
    $myLRN = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    $uploadDir = "assets/documents/" . $myLRN . "/";
    // Ensure upload directory exists
    createDirectory($uploadDir);
    // Handle uploaded file
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']);
        $fileDestination = $uploadDir . $fileName;
        // Move uploaded file
        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
             $_SESSION['success'] = "File Uploaded Successfully";
            header("Location: /BCCI/documents");
            exit();
        } else {
            $_SESSION['error'] = "Error: Failed to move uploaded file.";
        }
    } else {
        $_SESSION['error'] = "Error: No file uploaded or there was an upload error.";
    }
}



}


public function mydocuments()
{
    $myID = (int)$_SESSION['user_id'];
    $stmt = $this->db->prepare("
        SELECT
            username
        FROM 
            users
        WHERE 
            user_id = :myID;
    ");
    $stmt->bindValue(':myID', $myID, PDO::PARAM_INT);
    $stmt->execute();
    $myLRN = $stmt->fetch(PDO::FETCH_ASSOC)['username'];
    $uploadDir = "assets/documents/" . $myLRN . "/";

    // Scan files if directory exists
    $files = [];
    if (is_dir($uploadDir)) {
        $files = array_diff(scandir($uploadDir), array('.', '..'));
    }

    // Pass files to the view
    include 'views/student/myDocuments.php';
}




public function allpayments(){


   try {
        $stmt = $this->db->prepare("SELECT 
            pm.amount,
            pm.date_pay
            FROM payments pm
            LEFT JOIN
            enrollment_history eh ON pm.eh_id = eh.id
            WHERE
            eh.user_id = :user_id

            ORDER BY pm.date_pay DESC");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $payment_log = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $this->db->prepare("SELECT 
    SUM(pm.amount) as bayadna
    FROM payments pm
    LEFT JOIN
    enrollment_history eh ON pm.eh_id = eh.id
    WHERE
    eh.user_id = :user_id AND
    eh.status = 'ENROLLED' AND
    eh.academic_year_id = :academic_year_id
    ORDER BY pm.date_pay DESC");

$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':academic_year_id', $this->campusDataCurrentAcademicYear, PDO::PARAM_INT);
$stmt->execute();

// Fetch the result and convert 'bayadna' to integer
$binayad = $stmt->fetch(PDO::FETCH_ASSOC);

// Convert bayadna to an integer
$binayad['bayadna'] = intval($binayad['bayadna']);


  

// Fetch campus fees from campus_info table
$campusStmt = $this->db->prepare("
    SELECT function
    FROM campus_info
    WHERE id = 8
");
$campusStmt->execute();
$feesRow = $campusStmt->fetch(PDO::FETCH_ASSOC);


    $fees = json_decode($feesRow['function'], true);

$ehID = $this->mycurrenEhID;
    // Query to fetch COE details along with subject and schedule information
            $stmt = $this->db->prepare("
                SELECT
                  
                    eh.subjects_taken
                
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
 $subjectsTaken = json_decode($coeDetails['subjects_taken'], true);
$totalUnits = 0; // Initialize total units

// Fetch subject and schedule details for each subject and its scheduleIds
if ($subjectsTaken) {
    foreach ($subjectsTaken as $subject) {
        // Fetch subject name using subjectId
        $subjectStmt = $this->db->prepare("
            SELECT 
                s.unit_lec,  
                s.unit_lab  
            FROM subjects s
            WHERE s.id = :subject_id
        ");
        $subjectStmt->execute(['subject_id' => $subject['subjectId']]);
        $subjectData = $subjectStmt->fetch(PDO::FETCH_ASSOC);

        if ($subjectData) {
            // Calculate units for the subject (Lecture + Laboratory)
            $totalUnits += $subjectData['unit_lec'] + $subjectData['unit_lab'];
        }
    }
}

        // Calculate individual fees
        $unitFee = $fees['unit_fee'];
        $handlingFee = $fees['handling_fee'];
        $laboratoryFee = $fees['laboratory_fee'];
        $miscellaneousFee = $fees['miscellaneous_fee'];
        $otherFee = $fees['other_fee'];
        $registrationFee = $fees['registration_fee'];

        // Calculate tuition fee based on total units
        $tuitionFee = $totalUnits * $unitFee;

        // Calculate the total payment
        $totalPayment = $tuitionFee + $handlingFee + $laboratoryFee +
                        $miscellaneousFee + $otherFee + $registrationFee;
 






        
          include 'views/student/acad_payments.php';
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

}
public function allgrades()
{
    try {
        $courseId = $this->mycourseID;

        // Query to fetch all subject_ids for the given course ID
        $stmt = $this->db->prepare("
            SELECT 
                subject_ids
            FROM 
                semester
            WHERE 
                course_id = :course_id
        ");
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch all rows
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to collect all subject IDs
        $allSubjectIds = [];

        foreach ($results as $row) {
            if (!empty($row['subject_ids'])) {
                // Split the subject_ids by commas and merge them into the main array
                $subjectIds = explode(',', $row['subject_ids']);
                $allSubjectIds = array_merge($allSubjectIds, $subjectIds);
            }
        }

        // Remove duplicate IDs, if any
        $subjectIdsList = implode(',', array_unique($allSubjectIds));

        // Query to fetch the grades for each term (1 to 4) for each subject
        $stmt = $this->db->prepare("
            SELECT 
                s.name AS subject_name,
                s.code AS subject_code,
                gr.subject_id,
                gr.grade,
                gr.term_id
            FROM 
                grade_records gr
            JOIN 
                subjects s ON s.id = gr.subject_id
            WHERE 
                gr.user_id = :user_id
                AND gr.subject_id IN ($subjectIdsList)
                AND gr.term_id IN (1, 2, 3, 4)  -- Prelim, Midterm, Pre-final, Finals
            ORDER BY 
                s.code ASC, gr.term_id ASC  -- Order by subject code first, then term_id
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to store subject grades and calculate average
        $subjectGrades = [];

        // Initialize all subjects first, even if they have no grades
        foreach ($allSubjectIds as $subjectId) {
            // Fetch subject details
            $stmt = $this->db->prepare("
                SELECT name, code 
                FROM subjects 
                WHERE id = :subject_id
            ");
            $stmt->bindParam(':subject_id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $subjectData = $stmt->fetch(PDO::FETCH_ASSOC);

            $subjectGrades[$subjectId] = [
                'subject_name' => $subjectData['name'],
                'subject_code' => $subjectData['code'],
                'grades' => [0, 0, 0, 0], // Initialize grades array for 4 terms
                'total_grades' => 0, // Sum of the grades
                'terms_count' => 0, // Count of terms that have grades
            ];
        }

        // Organize grades by subject_id
        foreach ($grades as $grade) {
            $subjectId = $grade['subject_id'];
            $termId = $grade['term_id'];
            $gradeValue = $grade['grade'];

            // Store the grade for the corresponding term (adjusting for term 1-4 indexing)
            $subjectGrades[$subjectId]['grades'][$termId - 1] = $gradeValue;
            $subjectGrades[$subjectId]['total_grades'] += $gradeValue;
            $subjectGrades[$subjectId]['terms_count']++;
        }


// Initialize a flag to track if the student is qualified for graduation
$isQualifiedForGraduation = true;
$passingGrade = 3.00; // Set passing grade threshold (adjust as needed)
$hasNoGrades = false; // Flag to check if there are any subjects with no grades yet

// Calculate the average grade for each subject and evaluate graduation status
foreach ($subjectGrades as &$subjectGrade) {
    if ($subjectGrade['terms_count'] > 0) {
        // Average based on the number of terms with grades
        $subjectGrade['average_grade'] = $subjectGrade['total_grades'] / 4;
    } else {
        $subjectGrade['average_grade'] = 'No grade yet';
    }

    // Check if the subject has no grade yet
    if ($subjectGrade['average_grade'] === 'No grade yet') {
        $hasNoGrades = true; // Set flag if no grades are available
    }

    // If the subject has a grade, evaluate if it's below the passing grade
    if ($subjectGrade['average_grade'] !== 'No grade yet' && $subjectGrade['average_grade'] < $passingGrade) {
        $isQualifiedForGraduation = false;
    }
}

// If there are any subjects with no grades yet, the student is not qualified
if ($hasNoGrades) {
    $isQualifiedForGraduation = false;
}













        // Sort the subjects by the subject code to ensure order
        usort($subjectGrades, function ($a, $b) {
            return strcmp($a['subject_code'], $b['subject_code']);
        });

        // Include the view to display the grades
        include 'views/student/academic_grades.php';

    } catch (Exception $e) {
        // Handle exceptions and debugging
        dd($e);
    }
}









public function enrollmenthistory()
{
    try {
        $usr_id = $_SESSION['user_id'];

        // Fetch enrollment history and subjects_taken for the user
        $stmt = $this->db->prepare("
            SELECT 
                eh.enrollment_date AS enrolldate,
                d.course_name AS course,
                CONCAT(a.start, ' - ', a.end) AS academic_year,
                eh.subjects_taken
            FROM 
                enrollment_history eh
            LEFT JOIN 
                department d ON d.id = eh.course_id
            LEFT JOIN 
                academic_year a ON a.id = eh.academic_year_id
            WHERE 
                eh.user_id = :userid
                AND eh.status = 'ENROLLED'
            ORDER BY 
                eh.enrollment_date DESC
        ");
        $stmt->bindParam(':userid', $usr_id, PDO::PARAM_INT);
        $stmt->execute();
        $enrollmentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare the final results
        $results = [];

        foreach ($enrollmentHistory as $record) {
            $subjectsTaken = json_decode($record['subjects_taken'], true);
            $subjectIds = array_column($subjectsTaken, 'subjectId');

            $totalUnits = 0;

            if (!empty($subjectIds)) {
                // Dynamic query to fetch total units for all subjects
                $placeholders = str_repeat('?,', count($subjectIds) - 1) . '?';
                $query = "
                    SELECT 
                        (SUM(unit_lec) + SUM(unit_lab)) AS total_units
                    FROM 
                        subjects
                    WHERE 
                        id IN ($placeholders)
                ";

                $stmt = $this->db->prepare($query);
                $stmt->execute($subjectIds);
                $totalUnits = $stmt->fetchColumn();
            }

            // Append the required fields with total units
            $results[] = [
                'enrolldate' => $record['enrolldate'],
                'course' => $record['course'],
                'academic_year' => $record['academic_year'],
                'total_units' => $totalUnits
            ];
        }


    } catch (Exception $e) {
        echo $e->getMessage();
        return;
    }
    include 'views/student/academic_enrollment.php';





}



    public function updateuserpass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = (int) $_SESSION['user_id']; 
            $username = $_POST['username'];
            $password = $_POST['passwd'];

            if (!empty($password)) {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET 
                    username = :username,
                    password = :password
                    WHERE user_id = :student_id
                    ");
                $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            } else {

                $stmt = $this->db->prepare("
                    UPDATE users 
                    SET 
                    username = :username
                    WHERE user_id = :student_id
                    ");
            }

            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':student_id', $studentId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: /BCCI/profile");
                exit();
            } else {
                echo "Error: Could not update user credentials.";
            }
        }
    }



    private function resizeImage($fileTmpPath, $width, $height, $extension) {

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
            $image = imagecreatefromjpeg($fileTmpPath);
            break;
            case 'png':
            $image = imagecreatefrompng($fileTmpPath);
            break;
            default:
            return false;
        }

        if ($image === false) {
            return false;
        }


        list($origWidth, $origHeight) = getimagesize($fileTmpPath);


        $resizedImage = imagecreatetruecolor($width, $height);


        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);


        return $resizedImage;
    }


    public function uploadprofile() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
            $userId = (int) $_SESSION['user_id'];

        // Fetch user profile
            $stmt = $this->db->prepare("SELECT 
                u.username,
                p.photo_path
                FROM profiles p
                LEFT JOIN users u ON u.user_id = p.profile_id 
                WHERE p.profile_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$profile) {
                echo json_encode(['success' => false, 'message' => 'User profile not found.']);
                return;
            }

            $lrn = $profile['username'];

        // Handle file upload
            if ($_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
                $fileName = $_FILES['profile_pic']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Allowed file extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadDir = 'assets/img/profile/';
                    $newFileName = $lrn . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                // Resize and save image
                    $resizedImage = $this->resizeImage($fileTmpPath, 128, 128, $fileExtension);
                if ($resizedImage && imagejpeg($resizedImage, $destPath, 90)) { // Adjust the quality if needed
                    // Update the database
                    $stmt = $this->db->prepare("UPDATE profiles SET photo_path = :photo_path WHERE profile_id = :user_id");
                    $stmt->bindParam(':photo_path', $destPath);
                    $stmt->bindParam(':user_id', $userId);

                    if ($stmt->execute()) {
                        echo json_encode([
                            'success' => true,
                            'newPhotoPath' => $destPath,
                        ]);
                        return;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error updating profile photo in the database.']);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error saving or resizing the image.']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a .jpg, .jpeg, or .png file.']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error uploading the file. Error code: ' . $_FILES['profile_pic']['error']]);
            return;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or wrong request method.']);
        return;
    }
}




public function profilespace()
{
    $myID = (int) $_SESSION['user_id'];
    try {
        $stmt = $this->db->prepare("
            SELECT
            d.course_name as section,
            u.email,
            u.username,
            eh.semester_id as grade,
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
            Left join
            department d on eh.course_id = d.id
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
            gr.grade,
            gr.term_id

            FROM 
            grade_records gr
            LEFT JOIN enrollment_history eh on eh.id = gr.eh_id
            WHERE

            gr.user_id = :user_id 
            AND gr.term_id IN (1, 2, 3, 4)
            AND gr.subject_id IN (" . implode(',', array_map('intval', $subjectIds)) . ")
            ");
        $gradesStmt->bindValue(':user_id', $myID, PDO::PARAM_INT);
        $gradesStmt->execute();
        $grades = $gradesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $gradeMap = [];
        foreach ($grades as $grade) {

            $gradeMap[$grade['subject_id']][$grade['term_id']] = $grade['grade'];
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

//for schedule logic

        $stmt = $this->db->prepare("
            SELECT subjects_taken 
            FROM enrollment_history 
            WHERE user_id = :user_id AND status = 'ENROLLED'
            ORDER BY enrollment_date DESC 
            LIMIT 1
            ");
        $stmt->bindParam(':user_id', $myID, PDO::PARAM_INT);
        $stmt->execute();
        $enrollmentHistory = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollmentHistory) {
            die("No enrollment history found for the student.");
        }


// Decode the subjects_taken JSON
        $subjectsTaken = json_decode($enrollmentHistory['subjects_taken'], true);


// Prepare to fetch data dynamically
        $subjectIds = [];
        $scheduleIds = [];
        foreach ($subjectsTaken as $record) {
            $subjectIds[] = $record['subjectId'];
            $scheduleIds = array_merge($scheduleIds, $record['scheduleIds']);
        }


// Fetch subjects by their IDs
        $subjectPlaceholders = implode(',', array_fill(0, count($subjectIds), '?'));
        $stmt = $this->db->prepare("SELECT id, name FROM subjects WHERE id IN ($subjectPlaceholders)");
        $stmt->execute($subjectIds);
        $subjects = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);



// Fetch schedules by their IDs
        $schedulePlaceholders = implode(',', array_fill(0, count($scheduleIds), '?'));
        $stmt =  $this->db->prepare("SELECT id, day, time_slot FROM schedules WHERE id IN ($schedulePlaceholders)");
        $stmt->execute($scheduleIds);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);



        $scheduleMap = [];
        foreach ($subjectsTaken as $record) {
            $subjectName = $subjects[$record['subjectId']];
            foreach ($record['scheduleIds'] as $scheduleId) {
                foreach ($schedules as $schedule) {
                    if ($schedule['id'] == $scheduleId) {
                        $day = $schedule['day'];
                        $timeslot = $schedule['time_slot'];
                        $scheduleMap[$day][$timeslot][] = $subjectName;
                    }
                }
            }
        }
// Desired day order
        $desiredOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $days = array_keys($scheduleMap);
// Sort the days according to the desired order
        usort($days, function ($a, $b) use ($desiredOrder) {
            return array_search($a, $desiredOrder) - array_search($b, $desiredOrder);
        });

// Extract and sort time slots
        $timeSlots = array_unique(array_reduce(array_values($scheduleMap), function ($carry, $daySchedules) {
            return array_merge($carry, array_keys($daySchedules));
        }, []));

// Normalize times for sorting
        usort($timeSlots, function ($a, $b) {
            $timeTo24Hour = function ($time) {
                preg_match('/(\d{1,2}):(\d{2})(AM|PM)/', $time, $matches);
                $hour = (int) $matches[1];
                $minute = (int) $matches[2];
                $isPM = $matches[3] === 'PM';
        if ($hour === 12) $hour -= 12; // Convert 12AM to 0 hours
        if ($isPM) $hour += 12;       // Convert PM to 24-hour format
        return $hour * 60 + $minute;  // Return total minutes for easy comparison
    };
    return $timeTo24Hour(explode('-', $a)[0]) - $timeTo24Hour(explode('-', $b)[0]);
});























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
