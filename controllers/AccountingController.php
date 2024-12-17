<?php 
require_once 'BaseController.php'; 
 
class AccountingController extends BaseController { 
    public function __construct($db) { 
        parent::__construct($db, ['8','9']);  
    } 

public function paymentSetting() {
    try {
        // Get payment setting info from campus_info where id = 8
        $stmt = $this->db->prepare("SELECT * FROM campus_info WHERE id = 8");
        $stmt->execute();
        $campusInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Decode the JSON stored in the 'function' column
        $paymentSettings = json_decode($campusInfo['function'], true);

        // Pass the decoded payment settings to the view
        include 'views/accounting/paymentSetting.php';

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


public function updatePaymentSetting() {
    try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the values from the form submission
            $unit_fee = $_POST['unit_fee'];
            $handling_fee = $_POST['handling_fee'];
            $laboratory_fee = $_POST['laboratory_fee'];
            $miscellaneous_fee = $_POST['miscellaneous_fee'];
            $other_fee = $_POST['other_fee'];
            $registration_fee = $_POST['registration_fee'];

            // Create an array with the updated payment settings
            $updatedSettings = array(
                "unit_fee" => $unit_fee,
                "handling_fee" => $handling_fee,
                "laboratory_fee" => $laboratory_fee,
                "miscellaneous_fee" => $miscellaneous_fee,
                "other_fee" => $other_fee,
                "registration_fee" => $registration_fee
            );

            // Encode the array as JSON
            $jsonSettings = json_encode($updatedSettings);

            // Update the payment settings in the database
            $stmt = $this->db->prepare("UPDATE campus_info SET function = :function WHERE id = 8");
            $stmt->bindParam(':function', $jsonSettings);
            $stmt->execute();

            // Set a success message and redirect to the payment settings page
            $_SESSION['success'] = "Payment settings updated successfully!";
            header("Location: paymentSetting");
            exit;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


public function bayadna() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Get POST data
            $ehID = $_POST['ehID'];
            $amount = $_POST['amount'];
            $remarks = $_POST['remarks'];
      

            // Insert payment details into the payments table
            $stmt = $this->db->prepare("
                INSERT INTO payments (eh_id, amount, date_pay, remark) 
                VALUES (:eh_id, :amount, NOW(), :remark)
            ");
            $stmt->bindParam(':eh_id', $ehID);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':remark', $remarks);
            $stmt->execute();

            // Check the total amount paid for the same eh_id
            $paymentStmt = $this->db->prepare("
                SELECT SUM(amount) AS total_paid 
                FROM payments 
                WHERE eh_id = :eh_id AND remark = 'enrolmentfee'
            ");
            $paymentStmt->bindParam(':eh_id', $ehID);
            $paymentStmt->execute();
            $paymentData = $paymentStmt->fetch(PDO::FETCH_ASSOC);

            // If the total amount paid is greater than or equal to 1500, update the status
            if ($paymentData['total_paid'] >= 1500) {
                // Update the enrollment status to "Paid"
                $updateStmt = $this->db->prepare("UPDATE enrollment_history SET status = 'Paid' WHERE id = :ehID");
                $updateStmt->bindParam(':ehID', $ehID);
                $updateStmt->execute();
            }

            $_SESSION['success'] = "Payment successfully recorded!";
            header("Location: dashboard"); // Redirect to the accounting dashboard or appropriate page
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = "Error processing payment: " . $e->getMessage();
            header("Location: dashboard"); // Redirect on error
            exit;
        }
    }
}



public function bayadnapo() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Get POST data
            $ehID = $_POST['ehID'];
            $amount = $_POST['amount'];
            $remarks = $_POST['remarks'];


            // Insert payment details into the payments table
            $stmt = $this->db->prepare("
                INSERT INTO payments (eh_id, amount, date_pay, remark) 
                VALUES (:eh_id, :amount, NOW(), :remark)
            ");
            $stmt->bindParam(':eh_id', $ehID);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':remark', $remarks);
            $stmt->execute();



            $_SESSION['success'] = "Payment successfully recorded!";
            header("Location: paynow"); // Redirect to the accounting dashboard or appropriate page
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = "Error processing payment: " . $e->getMessage();
            header("Location: paynow"); // Redirect on error
            exit;
        }
    }
}



public function paymentlog(){
    try {
        $stmt = $this->db->prepare("SELECT 
            pm.id AS payment_id,
            pm.amount,
            pm.date_pay,
            pm.remark,
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
            FROM payments pm
            LEFT JOIN enrollment_history eh ON eh.id = pm.eh_id
            LEFT JOIN profiles p ON eh.user_id = p.profile_id
            ORDER BY pm.id ASC");

        $stmt->execute();
        $payment_log = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/accounting/paymentlog.php';
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


public function sakitsaulo() {
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
                eh.status = 'ENROLLED'
        ");
        $stmt->execute();
        $payee = $stmt->fetchAll(PDO::FETCH_ASSOC);



    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    include 'views/accounting/paybill.php';
}



}
