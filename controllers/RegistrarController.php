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
                    ) AS fullname
               
                FROM profiles p

                WHERE p.profile_id = :user_id
        
                ");
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $myNames = $stmt->fetch(PDO::FETCH_ASSOC);


            // Query to fetch COE details along with subject and schedule information
            $stmt = $this->db->prepare("
                SELECT
                u.username,
                p.sex,
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
                d.code as course_code,
                SUM(pmt.amount) AS total_payment
                FROM enrollment_history eh
                LEFT JOIN users u ON u.user_id = eh.user_id
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
                $subjectCodes = [];
                $allSchedules = [];
                $allunits = [];
                     $totalUnits = 0; // Initialize total units
                // Fetch subject and schedule details for each subject and its scheduleIds
                     if ($subjectsTaken) {
                        foreach ($subjectsTaken as $subject) {
                        // Fetch subject name using subjectId
                            $subjectStmt = $this->db->prepare("
                                SELECT 
                                s.name AS subject_name,
                                s.code as code,
                                s.unit_lec,
                                s.unit_lab
                                FROM subjects s
                                WHERE s.id = :subject_id
                                ");
                            $subjectStmt->execute(['subject_id' => $subject['subjectId']]);
                            $subjectData = $subjectStmt->fetch(PDO::FETCH_ASSOC);

                            if ($subjectData) {
                                $subjectNames[] = $subjectData['subject_name'];
                                $subjectCodes[] = $subjectData['code'];
                                $allunits[] = $subjectData['unit_lec'] + $subjectData['unit_lab'];
                                    // Calculate units for the subject
                                $totalUnits += $subjectData['unit_lec'] + $subjectData['unit_lab'];

                            // Now fetch all schedules for each scheduleId in the scheduleIds array
                                $scheduleDetails = [];
                                foreach ($subject['scheduleIds'] as $scheduleId) {
                                    $scheduleStmt = $this->db->prepare("
                                        SELECT
                                        sc.day,
                                        sc.time_slot,
                                        sc.session_type
                                        FROM schedules sc
                                        WHERE sc.id = :schedule_id
                                        ");
                                    $scheduleStmt->execute(['schedule_id' => $scheduleId]);
                                    $scheduleData = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
                                    if ($scheduleData) {
                                    // Store schedule details for each scheduleId
                                        $scheduleDetails[] = $scheduleData['day'] . ', ' . $scheduleData['time_slot'] ;
                                    }
                                }
                            // Combine all schedules for the subject and store it
                                $allSchedules[] = implode('; ', $scheduleDetails);
                            }
                        }
                    }



$campusStmt = $this->db->prepare("
    SELECT 
        SUM(amount) as EnrollmentFeePaid, 
        MAX(date_pay) as LastPayDate
    FROM payments
    WHERE eh_id = :eh_id
    AND remark = 'enrolmentfee'
");
$campusStmt->bindParam(':eh_id', $ehID); // Correct variable name
$campusStmt->execute();
$result = $campusStmt->fetch(PDO::FETCH_ASSOC);

$EnrollmentFeePaid = $result['EnrollmentFeePaid'];
$LastPayDate = $result['LastPayDate'];
$date = new DateTime($LastPayDate);
$formattedDate = $date->format('M. d, Y');





 // Decode campus_info for fees
                    $campusStmt = $this->db->prepare("
                        SELECT function
                        FROM campus_info
                        WHERE id = 8
                        ");
                    $campusStmt->execute();
                    $feesRow = $campusStmt->fetch(PDO::FETCH_ASSOC);
                    if ($feesRow) {
    // Decode the JSON string in the 'function' column
                        $fees = json_decode($feesRow['function'], true);
                        if ($fees) {
        $unitFee = $fees['unit_fee']; // Unit Fee
        $fixedFees = $fees['handling_fee'] + $fees['laboratory_fee'] + 
        $fees['miscellaneous_fee'] + $fees['other_fee'] + 
                     $fees['registration_fee']; // Total Fixed Fees
        // Calculate the current assessment
                     $currentAssessment = ($totalUnits * $unitFee) + $fixedFees;
        // Include current assessment in the COE details
                     $coeDetails['current_assessment'] = $currentAssessment;
                 } else {
                    echo "Error: Unable to decode fees JSON.";
                }
            } else {
                echo "Error: Fees data not found.";
            }
// Calculate each fee
            $handlingFee = $fees['handling_fee'];
            $laboratoryFee = $fees['laboratory_fee'];
            $miscellaneousFee = $fees['miscellaneous_fee'];
            $otherFee = $fees['other_fee'];
            $registrationFee = $fees['registration_fee'];
            $tuitionFee = $fees['unit_fee'] * $totalUnits;
// Calculate total fee
            $totalFee = $handlingFee + $laboratoryFee + $miscellaneousFee + $otherFee + $registrationFee + $tuitionFee;
                // Add subjects and schedules to the COE details
            $coeDetails['subject_names'] = implode(', ', $subjectNames);
            $coeDetails['schedules'] = implode('; ', $allSchedules);

            // Convert subject codes to a single string, joined by commas
$subjectCodesString = implode(", ", $subjectCodes);
            $coeDetails['subject_codes'] = $subjectCodesString;

            $unitsCodesString = implode(", ", $allunits);
            $coeDetails['subject_units'] = $unitsCodesString;


            $semesterName = in_array($coeDetails['semester_id'], [1, 3, 5, 7]) ? '1st Semester' : '2nd Semester';
            if ($coeDetails['semester_id'] == 1 || $coeDetails['semester_id'] == 2) {
                $yearLevel = 'I';
            } elseif ($coeDetails['semester_id'] == 3 || $coeDetails['semester_id'] == 4) {
                $yearLevel = 'II';
            } elseif ($coeDetails['semester_id'] == 5 || $coeDetails['semester_id'] == 6) {
                $yearLevel = 'III';
            } else {
                $yearLevel = 'IV'; 
            }
            echo '
            <section class="studentInfo">
            <table>
            <tr>
            <td><strong>STUDENT INFORMATION</strong></td>
            <td style="text-align:left;">ID NO : <br><strong>'. htmlspecialchars(ucwords($coeDetails['username'])) .'</strong></td>
            <td style="text-align:left;">Academic Year: <br><strong>'. htmlspecialchars(ucwords($coeDetails['acads_year'])) .' | '.  $semesterName  .'</strong></td>
            <td style="text-align:left;">GENDER: <br><strong>'. htmlspecialchars(
                $coeDetails['sex'] === "M" ? "Male" : "Female"
                ) .'</strong></td>
            </tr>
            <tr>
            <td colspan="2" style="text-align:left;">Name (Last name, First name, Middle name, Suffix)<br><strong>'. htmlspecialchars(ucwords($coeDetails['fullname'])) .'</strong></td>
            <td colspan="1" style="text-align:left;">COURSE/YR<br><strong>'. htmlspecialchars(ucwords($coeDetails['course_code'])) .' / '.$yearLevel.'</strong></td>
            <td style="text-align:left; vertical-align: top;">Status:  <strong>OLD</strong></td>
            </tr>
            </table>
            </section>
            ';
                // List Subjects and Schedules
            echo '<section class="course-info">
           <table class="no-margin-no-padding">
            <thead>
            <tr><td colspan="6">Study Load</td>
            </tr>
            <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Units</th>
            <th>Schedule</th>
            </tr>
            </thead>
            <tbody>';
$subjectNames = explode(', ', $coeDetails['subject_names']);  // Split the subjects into an array
$allSchedules = explode('; ', $coeDetails['schedules']);  // Split the schedules into an array
$allcodes = explode(', ', $coeDetails['subject_codes']);
$uns = explode(', ', $coeDetails['subject_units']);


                   $campusStmt = $this->db->prepare("
                        SELECT function
                        FROM campus_info
                        WHERE id = 10
                        ");
                    $campusStmt->execute();
                    $schooldirector = $campusStmt->fetch(PDO::FETCH_ASSOC);
$subjectIndex = 0;  // Initialize subject index


foreach ($subjectNames as $index => $subject) {
    // Initialize the schedule list for this subject
    $subjectSchedules = [];
    // Continue adding schedules for this subject
    // Loop through all schedules, adding schedules based on the subject
    while ($subjectIndex < count($allSchedules) && count($subjectSchedules) < 2) {
        $subjectSchedules[] = $allSchedules[$subjectIndex];
        $subjectIndex++;
    }
    
    // Get the subject code from the $allcodes array using the current index
    $subjectCode = isset($allcodes[$index]) ? $allcodes[$index] : '';
    $subjUnits = isset($uns[$index]) ? $uns[$index] : '';

    // Display subject code and its schedules in a list format
    echo "<tr >
        <td class='small-font'>$subjectCode</td>
        <td class='small-font'>$subject</td>
        <td class='small-font'>$subjUnits</td>
        <td class='small-font'><ul>";

    // Loop through the schedules for this subject and display them
    foreach ($subjectSchedules as $schedule) {
        echo "<li>$schedule</li>";
    }

    echo "</ul></td></tr>";
}
echo '    <tfoot>
        <td colspan="2" style="text-align:right">Total Units: </td>
        <td colspan="12" >'.$totalUnits.'</td>
    </tfoot>';

echo "</table>";
    // Display the fee breakdown



$currentDateTime = date('Y-m-d'); // Format: YYYY-MM-DD HH:MM:SS

echo '
<section class="payments">
   <table>
       <tbody>

        <tr>
            <td>Schedule of Payment</td>
            <td>Term</td>
            <td>Assessment</td>
            <td>OLDAcc/Bridging/Tutorial</td>
            <td>Total</td>
            <td>EXAM PERMIT <br> 2425-1-253</td>
        </tr>
        <tr>

        </tr>
        <tr>
            <td style="width: fit-content;">09/09/2024 - 09/13/2024 <br> 10/07/2024 - 10/11/2024 <br> 11/11/2024 - 11/15/2024 <br> 12/09/2024 - 12/13/2024</td>
            <td style="text-align:center;">Prelim<br>Midterm<br>PreFinal<br>Final</td>
 
<td style="text-align:center;">
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.4477, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.2123, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.2123, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.1592, 2).'
</td>





            <td style="text-align:center;">0.00<br>0.00<br>0.00<br>0.00</td>
     <td style="text-align:center;">
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.4477, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.2123, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.2123, 2).'<br>
    '.number_format(($totalFee - $EnrollmentFeePaid) * 0.1592, 2).'
</td>
            <td rowspan=""><strong>Prelim</strong></td>
        </tr>
      <tr>
    <td colspan="5" style="text-align: center; font-weight: bold;">Assessment Details</td>
    <td rowspan="2" style="text-align: center; vertical-align: middle; font-weight: bold;">
        Midterm
    </td>
</tr>
<tr>
    <td colspan="3" style="white-space: pre; text-align: left; vertical-align: top;">
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Current Assessment:</strong></span>
            <span>'. number_format($totalFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Current Payments</strong><br>
           Date: '. $formattedDate .' : '. number_format($EnrollmentFeePaid, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Current Balance:</strong></span>
            <span>'. number_format($totalFee - $EnrollmentFeePaid, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>OLD ACCOUNTS:</strong></span>
            <span>0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>TOTAL ACCOUNTS:</strong></span>
            <span>'. number_format($totalFee - $EnrollmentFeePaid, 2) .'</span>
        </div>
        <div style="display: flex;">
            <span><strong>PAYABLE</strong><br>
            Date Issued: '.(new DateTime('2024-12-17'))->format('F d, Y').'</span>
        </div>
    </td>
    <td colspan="2" style="white-space: pre; text-align: left; vertical-align: top;">
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Handling fee:</strong></span>
            <span>'. number_format($handlingFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Laboratory fee:</strong></span>
            <span>'. number_format($laboratoryFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Miscellaneous fee:</strong></span>
            <span>'. number_format($miscellaneousFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Other fee:</strong></span>
            <span>'. number_format($otherFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Registration fee:</strong></span>
            <span>'. number_format($registrationFee, 2) .'</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span><strong>Tuition fee:</strong></span>
            <span>'. number_format($tuitionFee, 2) .'</span>
        </div>
    </td>
</tr>

        <tr>
<td>DATE RELEASED <br> '.$currentDateTime.'</td>
<td>SCHOOL DIRECTOR: <br> <br> <br>'.ucwords($schooldirector['function']).'</td>
<td>CONFORME: <br> <br> <br>'.ucwords($coeDetails['fullname']).'</td>
<td>REGISTRAR: <br> <br> <br>'.ucwords($myNames).'</td>
<td>ACCOUNTING: <br> <br> <br></td>
<td rowspan="1"><strong>PreFinal</strong></td>
        </tr>
        <tr>
           <td colspan="5"><strong>END OF CERTIFICATE</strong></td> 
           <td style="vertical-align: middle;"><strong>Final</strong><br><br><br></td>
        </tr>
    </tbody>
</table>
</section>

';























} else {
    echo "<p>No details found for this enrollment.</p>";
}
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
}
}




}#end

