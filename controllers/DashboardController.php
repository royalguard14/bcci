<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
    }





    private function deanDashboard() {
        $deptID = $this->deanDeptid;
        $totalStudent = 0;
        $totalInstructor = 0;
        $totalGraduate = 0;
        $totalSubject = 0;


        $stmt = $this->db->prepare("SELECT count(*) as totalstudent FROM academic_record WHERE c_id = :c_id");
        $stmt->bindValue(':c_id', $deptID, PDO::PARAM_INT);
        $stmt->execute();
        $campusInfoData = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalStudent = (int)$campusInfoData['totalstudent'];


        $stmt = $this->db->prepare("SELECT count(*) as totalInstructor 
            FROM users u 
            LEFT JOIN employment_info ei ON ei.user_id = u.user_id
            WHERE 
            u.role_id = '3' 
            AND ei.course_id = :c_id
            ");
        $stmt->bindValue(':c_id', $deptID, PDO::PARAM_INT);
        $stmt->execute();
        $campusInfoData = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalInstructor = (int)$campusInfoData['totalInstructor'];


        $stmt = $this->db->prepare("SELECT subject_ids FROM semester WHERE course_id = :c_id");
        $stmt->bindValue(':c_id', $deptID, PDO::PARAM_INT);
        $stmt->execute();
        $allsubsid = $stmt->fetchALL(PDO::FETCH_ASSOC);
        $allSubjectIds = [];
        foreach ($allsubsid as $row) {
            $subjectIds = explode(',', $row['subject_ids']);
            $allSubjectIds = array_merge($allSubjectIds, $subjectIds);
        }
        $allSubjectIds = array_filter($allSubjectIds, function($value) {
            return !empty($value);
        });
        $allSubjectIds = array_unique($allSubjectIds);
        $allSubjectIds = array_values($allSubjectIds);
        $totalSubject = count($allSubjectIds);



##from here para sa graduation List
// Fetch all students under the department
        $stmt = $this->db->prepare("SELECT user_id FROM academic_record WHERE c_id = :c_id");
        $stmt->bindValue(':c_id', $deptID, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract student IDs into an array
        $studentIds = [];
        foreach ($students as $student) {
            $studentIds[] = $student['user_id'];
        }



// Fetch grade records for these students
        $stmt = $this->db->prepare("
            SELECT gr.user_id, gr.subject_id, gr.term_id, gr.grade
            FROM grade_records gr
            WHERE gr.user_id IN (" . implode(",", $studentIds) . ")");
        $stmt->execute();
        $gradeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Group grades by student and subject
        $studentsGrades = [];

        foreach ($gradeRecords as $gradeRecord) {
            $userId = $gradeRecord['user_id'];
            $subjectId = $gradeRecord['subject_id'];
            $grade = $gradeRecord['grade'];

    // Initialize the student array if not exists
            if (!isset($studentsGrades[$userId])) {
                $studentsGrades[$userId] = [];
            }

    // Initialize the subject array if not exists
            if (!isset($studentsGrades[$userId][$subjectId])) {
                $studentsGrades[$userId][$subjectId] = [
                    'grades' => []
                ];
            }

    // Add grade to the corresponding subject
            $studentsGrades[$userId][$subjectId]['grades'][] = $grade;
        }

        $graduatingStudents = [];
$passingGrade = 75;  // Set the passing grade threshold

// Loop through each student
foreach ($studentsGrades as $userId => $subjects) {
    $allPassed = true;  // Assume student passes all subjects initially

    // Loop through each subject (from all available subjects)
    foreach ($allSubjectIds as $subjectId) {
        // Check if the student has grades for this subject
        if (isset($subjects[$subjectId])) {
            // Calculate the average grade for this subject
            $averageGrade = array_sum($subjects[$subjectId]['grades']) / count($subjects[$subjectId]['grades']);

            // If the average grade is less than the passing grade, mark as failed
            if ($averageGrade < $passingGrade) {
                $allPassed = false;
                break;  // No need to check further subjects for this student
            }
        } else {
            // If the student doesn't have grades for the subject, mark as failed
            $allPassed = false;
            break;
        }
    }

    // If the student passed all subjects, add to the graduation list
    if ($allPassed) {
        $graduatingStudents[] = $userId;
    }
}

#uptohere
// foreach ($graduatingStudents as $userId) {
//     echo "Student ID: " . $userId . " is eligible to graduate.<br>";
// }

$totalGraduate = count($graduatingStudents);







include 'views/dashboard/dean_dashboard.php';
}









public function adviserDashboard() {
 
 try{

    

    } catch (Exception $e) {
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
            $this->deanDashboard();
            break;
       







            default:
            $this->defaultDashboard();
            break;
        }
    }










}
?>
