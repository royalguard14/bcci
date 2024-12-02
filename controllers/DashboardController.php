<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
    }


public function adviserDashboard() {
    // Get the current grading from campus_info table
    $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 8");
    $stmt->execute();
    $campusInfoData = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentGrading = (int)$campusInfoData['function'];

    $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id = 6");
    $stmt->execute();
    $CampusInfoData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $present_school_year = (int) $CampusInfoData[0]['function'];

    // Get the adviser ID from the session
    $adviserId = $_SESSION['user_id'];

    // Get the current month
    $currentMonth = date('m'); // This gets the current month (01 to 12)
    
    // Get today's date in YYYY-MM-DD format
    $today = date('Y-m-d'); 

    try {
        // Fetch the adviser's section and related grade information
        $result = $this->getAdviserSectionAndGrade($adviserId);
        $_SESSION['section_id'] = $result['section_id']; // Store the section ID in session for future use

        // Fetch the top 10 students based on average grade in the current grading period
        $stmt = $this->db->prepare("
            SELECT 
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
                        '') 
                ) AS fullname,
                COALESCE(p.lrn, 'No Data') AS lrn,
                p.sex,
                AVG(gr.grade) AS average_grade
            FROM grade_records gr
            LEFT JOIN enrollment_history eh ON gr.eh_id = eh.id
            LEFT JOIN profiles p ON eh.user_id = p.profile_id
            WHERE eh.section_id = :section_id AND gr.grading_id = :grading_id AND eh.adviser_id = :adviser_id AND academic_year_id = :academic_year_id
            GROUP BY eh.user_id
            ORDER BY average_grade DESC
            LIMIT 10
        ");
        
        // Bind values for section ID and current grading period
        $stmt->bindValue(':adviser_id', $adviserId, PDO::PARAM_INT);
        $stmt->bindValue(':academic_year_id', $present_school_year, PDO::PARAM_INT);
        $stmt->bindValue(':section_id', $_SESSION['section_id'], PDO::PARAM_INT);
        $stmt->bindValue(':grading_id', $currentGrading, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the top 10 students
        $top10Students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch the top 1 student per subject
        $stmt = $this->db->prepare("
            SELECT 
                gr.subject_id,
                s.name AS subject_name,  -- Add the subject name
                CONCAT(
                    COALESCE(p.last_name, ''), ', ',
                    COALESCE(p.first_name, ''), ' ',
                    COALESCE(
                        CASE
                            WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                            THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                            ELSE ''
                        END, 
                        '') 
                ) AS fullname,
                COALESCE(p.lrn, 'No Data') AS lrn,
                p.sex,
                gr.grade
            FROM grade_records gr
            LEFT JOIN enrollment_history eh ON gr.eh_id = eh.id
            LEFT JOIN profiles p ON eh.user_id = p.profile_id
            LEFT JOIN subjects s ON gr.subject_id = s.id 
            WHERE gr.grade = (
                SELECT MAX(gr2.grade)
                FROM grade_records gr2
                WHERE gr2.subject_id = gr.subject_id
                AND eh.section_id = :section_id AND adviser_id = :adviser_id AND academic_year_id = :academic_year_id AND grading_id = :grading_id
            )
            ORDER BY gr.subject_id;
        ");
        
        // Bind values for section ID and current grading period
        $stmt->bindValue(':adviser_id', $adviserId, PDO::PARAM_INT);
        $stmt->bindValue(':academic_year_id', $present_school_year, PDO::PARAM_INT);
        $stmt->bindValue(':section_id', $_SESSION['section_id'], PDO::PARAM_INT);
        $stmt->bindValue(':grading_id', $currentGrading, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the top 1 student per subject
        $topStudentsPerSubject = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch students with birthdays this month
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
                        '') 
                ) AS fullname,
                p.birth_date
            FROM profiles p
            WHERE MONTH(p.birth_date) = :current_month
            ORDER BY p.birth_date DESC
        ");
        
        // Bind the current month value
        $stmt->bindValue(':current_month', $currentMonth, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the students with birthdays this month
        $studentsWithBirthday = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch students' attendance records for today
        $stmt = $this->db->prepare("
            SELECT 
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
                        '') 
                ) AS fullname,
                ar.status
            FROM attendance_records ar
            LEFT JOIN enrollment_history eh ON ar.eh_id = eh.id
            LEFT JOIN profiles p ON eh.user_id = p.profile_id
            WHERE eh.section_id = :section_id AND ar.date = :today
        ");

        // Bind the current section ID and today's date
        $stmt->bindValue(':section_id', $_SESSION['section_id'], PDO::PARAM_INT);
        $stmt->bindValue(':today', $today, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the attendance data
        $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Separate the attendance data into present and absent lists
        $presentStudents = [];
        $absentStudents = [];
        $tardyStudents = [];
        $exuseStudents = [];
        foreach ($attendanceData as $attendance) {
            if ($attendance['status'] === 'P') {
                $presentStudents[] = $attendance;
            }elseif ($attendance['status'] === 'T') {
               $tardyStudents[] = $attendance;
            }elseif ($attendance['status'] === 'E') {
               $exuseStudents[] = $attendance;
            } else {
                $absentStudents[] = $attendance;
            }
        }

        // Fetch the total number of male and female students enrolled in the section
        $stmt = $this->db->prepare("
            SELECT sex, COUNT(*) as count
            FROM profiles p
            LEFT JOIN enrollment_history eh ON p.profile_id = eh.user_id
            WHERE eh.section_id = :section_id
            GROUP BY sex
        ");
        $stmt->bindValue(':section_id', $_SESSION['section_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the count of male and female students
        $genderCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $maleCount = 0;
        $femaleCount = 0;
        foreach ($genderCounts as $gender) {
            if ($gender['sex'] === 'M') {
                $maleCount = $gender['count'];
            } elseif ($gender['sex'] === 'F') {
                $femaleCount = $gender['count'];
            }
        }



    } catch (Exception $e) {
        // If there's any error, output the error message
        echo $e->getMessage();
        return;
    }

    // Include the dashboard view to display the data
    include 'views/dashboard/adviser_dashboard.php';
}



   
    private function studentDashboard() {
      
        include 'views/dashboard/student_dashboard.php';
    }

    private function registrarDashboard() {
    echo "Welcome to the Registrar Dashboard!";
    include 'views/dashboard/registrar_dashboard.php';
}

    private function defaultDashboard() {
      
        include 'views/dashboard/default_dashboard.php';
    }








    public function showDashboard() {
        $userRole = $_SESSION['role_id'];

        switch ($userRole) {
            case 2: // Faculty
            $this->adviserDashboard();
            break;
            case 3: //Learners
            $this->studentDashboard();
            break;
            case 4: //Registrar
            $this->registrarDashboard();
            break;
            default://Parents
            $this->defaultDashboard();
            break;
        }
    }










}
?>
