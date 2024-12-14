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
      
          header("Location: home");
        exit();
    }






// public function registrarDashboard() {
//     try {
//         // Fetch counts and statistics
//         $pendingCountStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM users WHERE role_id = 4 AND isActive = 0 AND isDelete = 0");
//         $pendingCountStmt->execute();
//         $pendingCount = $pendingCountStmt->fetch(PDO::FETCH_ASSOC)['count'];

//         $acceptedCountStmt = $this->db->prepare("SELECT COUNT(*) AS count FROM users WHERE role_id = 4 AND isActive = 1 AND isDelete = 0");
//         $acceptedCountStmt->execute();
//         $acceptedCount = $acceptedCountStmt->fetch(PDO::FETCH_ASSOC)['count'];

//         // Fetch payment log
//         $paymentLogStmt = $this->db->prepare("SELECT 
//                CONCAT(
//                     COALESCE(p.last_name, ''), ', ',
//                     COALESCE(p.first_name, ''), ' ',
//                     COALESCE(
//                         CASE
//                             WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
//                             THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
//                             ELSE ''
//                         END, 
//                         ''
//                     )
//                 ) AS fullname,
//             pm.amount, 
//             pm.date_pay 
//             FROM payments pm
//             LEFT JOIN enrollment_history eh on eh.id = pm.eh_id
//             LEFT JOIN profiles p on p.profile_id = eh.user_id

//             ORDER BY pm.date_pay DESC LIMIT 10");
//         $paymentLogStmt->execute();
//         $payment_log = $paymentLogStmt->fetchAll(PDO::FETCH_ASSOC);

//         // Enrollment stats
//         $stmt = $this->db->prepare("
//             SELECT 
//                 COUNT(eh.id) AS total_enrollments, 
//                 SUM(CASE WHEN eh.status = 'ENROLLED' THEN 1 ELSE 0 END) AS total_enrolled,
//                 SUM(CASE WHEN eh.status = 'Pending Payment' THEN 1 ELSE 0 END) AS pending_payment
//             FROM enrollment_history eh
//         ");
//         $stmt->execute();
//         $enrollmentStats = $stmt->fetch(PDO::FETCH_ASSOC);


//      include 'views/dashboard/registrar_dashboard.php';
//     } catch (Exception $e) {
//         echo "Error loading dashboard: " . $e->getMessage();
//     }
// }



public function registrarDashboard() {
    try {
        // Fetch pending and accepted counts
        $pendingCount = $this->getPendingCount();
        $acceptedCount = $this->getAcceptedCount();

        // Fetch latest payment log
        $payment_log = $this->getLatestPayments(10);

        // Fetch enrollment stats
        $enrollmentStats = $this->getEnrollmentStats();

        // Fetch graph data for enrollments over time (last 6 months)
        $enrollmentTrends = $this->getEnrollmentTrends();

        // Fetch graph data for payments over time (last 6 months)
        $paymentTrends = $this->getPaymentTrends();

        // Fetch Accepted and Pending Students Trends
$acceptedStudentsTrends = $this->getAcceptedStudentsTrends();
$pendingStudentsTrends = $this->getPendingStudentsTrends();


        // Render the dashboard view
        include 'views/dashboard/registrar_dashboard.php';
    } catch (Exception $e) {
        error_log("Dashboard error: " . $e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo "An unexpected error occurred. Please try again later.";
    }
}


// Fetch enrollment trends (for graph: number of enrollments over last 6 months)
private function getEnrollmentTrends() {
    $stmt = $this->db->prepare("
        SELECT 
            DATE_FORMAT(eh.enrollment_date, '%Y-%m') AS month, 
            COUNT(*) AS enrollments
        FROM enrollment_history eh
        WHERE eh.enrollment_date >= CURDATE() - INTERVAL 6 MONTH
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch payment trends (for graph: total payments over last 6 months)
private function getPaymentTrends() {
    $stmt = $this->db->prepare("
        SELECT 
            DATE_FORMAT(pm.date_pay, '%Y-%m') AS month, 
            SUM(pm.amount) AS total_payments
        FROM payments pm
        WHERE pm.date_pay >= CURDATE() - INTERVAL 6 MONTH
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch Accepted Students Trends (for graph: number of accepted students over the last 6 months)
private function getAcceptedStudentsTrends() {
    $stmt = $this->db->prepare("
        SELECT 
            DATE_FORMAT(uh.created_at, '%Y-%m') AS month, 
            COUNT(*) AS accepted_students
        FROM users uh
        WHERE uh.role_id = 4 AND uh.isActive = 1 AND uh.isDelete = 0
        AND uh.created_at >= CURDATE() - INTERVAL 6 MONTH
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch Pending Students Trends (for graph: number of pending students over the last 6 months)
private function getPendingStudentsTrends() {
    $stmt = $this->db->prepare("
        SELECT 
            DATE_FORMAT(uh.created_at, '%Y-%m') AS month, 
            COUNT(*) AS pending_students
        FROM users uh
        WHERE uh.role_id = 4 AND uh.isActive = 0 AND uh.isDelete = 0
        AND uh.created_at >= CURDATE() - INTERVAL 6 MONTH
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Modular helper methods
private function getPendingCount() {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM users WHERE role_id = 4 AND isActive = 0 AND isDelete = 0");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

private function getAcceptedCount() {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM users WHERE role_id = 4 AND isActive = 1 AND isDelete = 0");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

private function getLatestPayments($limit) {
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
            pm.amount, 
            DATE_FORMAT(pm.date_pay, '%M %d, %Y') AS date_pay
        FROM payments pm
        LEFT JOIN enrollment_history eh ON eh.id = pm.eh_id
        LEFT JOIN profiles p ON p.profile_id = eh.user_id
        ORDER BY pm.date_pay DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

private function getEnrollmentStats() {
    $stmt = $this->db->prepare("
        SELECT 
            COUNT(eh.id) AS total_enrollments, 
            SUM(CASE WHEN eh.status = 'ENROLLED' THEN 1 ELSE 0 END) AS total_enrolled,
            SUM(CASE WHEN eh.status = 'Pending Payment' THEN 1 ELSE 0 END) AS pending_payment
        FROM enrollment_history eh
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}








    private function defaultDashboard() {
      
        include 'views/dashboard/default_dashboard.php';
    }


 private function accountingDashboard() {
    try {
        // Fetching payees with Pending Payment status
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
            WHERE
                eh.status = 'Pending Payment'
        ");
        $stmt->execute();
        $payee = $stmt->fetchAll(PDO::FETCH_ASSOC);


                // Get total payment amount for the current year
        $currentYear = date('Y'); // Get current year
        $paymentStmt = $this->db->prepare("
            SELECT SUM(amount) AS total_amount
            FROM payments
            WHERE YEAR(date_pay) = :currentYear
        ");
        $paymentStmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $paymentStmt->execute();
        $totalPayment = $paymentStmt->fetch(PDO::FETCH_ASSOC);
        $totalAmountPaidThisYear = $totalPayment['total_amount'] ?? 0; // Default to 0 if no payments found


    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    include 'views/dashboard/accounting_dashboard.php';
}








    public function showDashboard() {
        $userRole = $_SESSION['role_id'];
        switch ($userRole) {
             case 2: //Registrar
            $this->registrarDashboard();
            break;
    
            case 3: // instructor
            $this->adviserDashboard();
            break;
            case 4: //Learners
            $this->studentDashboard();
            break;
             case 5: //Accounting Staff
            $this->accountingDashboard();
            break;
             case 6: //Auditor
            $this->studentDashboard();
            break;
             case 7: //dean
            $this->studentDashboard();
            break;
       







            default://Parents
            $this->defaultDashboard();
            break;
        }
    }










}
?>
