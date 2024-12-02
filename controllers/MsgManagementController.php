<?php 
require_once 'BaseController.php'; 
class MsgManagementController extends BaseController { 



    public function __construct($db) { 
        parent::__construct($db, ['12']); // Adjust roles as needed
    } 



public function chatavailable() {
    // Fetch available chat users from the database
    try {
        $myID = (int) $_SESSION['user_id'];

        // Get the current academic year
        $stmt = $this->db->prepare("SELECT function FROM campus_info WHERE id = 6");
        $stmt->execute();
        $campusInfoData = $stmt->fetch(PDO::FETCH_ASSOC);
        $presentSchoolYear = (int) $campusInfoData['function'];

        // Get the user's enrollment details
        $stmt = $this->db->prepare("
            SELECT grade_level_id, section_id, adviser_id 
            FROM enrollment_history 
            WHERE user_id = :user_id AND academic_year_id = :academic_year_id
        ");
        $stmt->bindParam(':user_id', $myID, PDO::PARAM_INT);
        $stmt->bindParam(':academic_year_id', $presentSchoolYear, PDO::PARAM_INT);
        $stmt->execute();
        $enrollmentInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($enrollmentInfo) {
            $gradeLevelId = $enrollmentInfo['grade_level_id'];
            $sectionId = $enrollmentInfo['section_id'];
            $adviserId = $enrollmentInfo['adviser_id'];

            // Fetch classmates
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id AS id, 
                    CONCAT(
                        COALESCE(p.last_name, ''), ', ', 
                        COALESCE(p.first_name, ''), ' ',
                        COALESCE(
                            CASE 
                                WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                                THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                                ELSE '' 
                            END, '') 
                    ) AS name
                FROM users u
                LEFT JOIN profiles p ON u.user_id = p.profile_id
                JOIN enrollment_history eh ON u.user_id = eh.user_id
                WHERE 
                    eh.grade_level_id = :grade_level_id
                    AND eh.section_id = :section_id
                    AND eh.academic_year_id = :academic_year_id
                    AND u.user_id != :user_id
                    AND u.isActive = 1 
                    AND u.isDelete = 0
                ORDER BY p.sex, p.last_name ASC
            ");
            $stmt->bindParam(':grade_level_id', $gradeLevelId, PDO::PARAM_INT);
            $stmt->bindParam(':section_id', $sectionId, PDO::PARAM_INT);
            $stmt->bindParam(':academic_year_id', $presentSchoolYear, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $myID, PDO::PARAM_INT);
            $stmt->execute();
            $classmates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch adviser
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id AS id, 
                    CONCAT(
                        COALESCE(p.last_name, 'N/A'), ', ', 
                        COALESCE(p.first_name, 'N/A'), ' ',
                        COALESCE(
                            CASE 
                                WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                                THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                                ELSE '' 
                            END, '') 
                    ) AS name
                FROM users u
                LEFT JOIN profiles p ON u.user_id = p.profile_id
                WHERE 
                    u.user_id = :adviserId
            ");
            $stmt->bindParam(':adviserId', $adviserId, PDO::PARAM_INT);
            $stmt->execute();
            $adviser = $stmt->fetch(PDO::FETCH_ASSOC);

            





            $parents = [];

            // Check if data exists
            if (empty($classmates) && empty($adviser)) {
                echo json_encode(['error' => 'No contacts available']);
            } else {
                echo json_encode([
                    'classmates' => $classmates, 
                    'adviser' => $adviser,
                    'parents' => $parents
                ]);
            }
        } else {
            echo json_encode(['error' => 'Enrollment data not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}



public function sendMessage() {

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate sender and receiver IDs
            $sender_id = (int) $_SESSION['user_id'];
            $receiver_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
            $content = isset($data['message']) ? trim($data['message']) : '';

            if (!$receiver_id || empty($content)) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Invalid input. Ensure the message content and recipient ID are provided.'
                ]);
                return;
            }

            // Insert the message into the database
            $stmt = $this->db->prepare("
                INSERT INTO messages (sender_id, receiver_id, content, is_read, sent_at) 
                VALUES (:sender_id, :receiver_id, :content, 0, NOW())
            ");
            $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Fetch sender details
                $stmt2 = $this->db->prepare("
                    SELECT 
                        CONCAT(COALESCE(p.last_name, ''), ', ', COALESCE(p.first_name, ''), ' ', 
                            COALESCE(
                                CASE 
                                WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                                THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                                ELSE '' 
                                END, ''
                            )
                        ) AS sender_name,
                        COALESCE(p.photo_path, 'assets/img/default-profile.png') AS sender_avatar
                    FROM profiles p
                    JOIN users u ON p.profile_id = u.user_id
                    WHERE u.user_id = :sender_id
                ");
                $stmt2->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
                $stmt2->execute();
                $senderInfo = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($senderInfo) {
                    echo json_encode([
                        'status' => 'success',
                        'sender_name' => $senderInfo['sender_name'],
                        'sender_avatar' => $senderInfo['sender_avatar'],
                        'message' => $content,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);

                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Sender details not found.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send the message.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }



                    exit();

}






public function fetchmessage() {
    $data = json_decode(file_get_contents('php://input'), true);
    $loggedInUserId = (int) $_SESSION['user_id'];
    $userId = (int) $data['user_id'];
    try {
        $stmt = $this->db->prepare("
           SELECT 
           m.content AS message,
           m.sent_at AS timestamp,
           CONCAT(
            COALESCE(p.last_name, ''), ', ', 
            COALESCE(p.first_name, ''), ' ',
            COALESCE(
                CASE 
                WHEN p.middle_name IS NOT NULL AND p.middle_name != '' 
                THEN CONCAT(SUBSTRING(p.middle_name, 1, 1), '.')
                ELSE '' 
                END, ''
                ) 
            ) AS sender_name,
           m.receiver_id AS sender_id,
           COALESCE(p.photo_path, 'assets/img/default-profile.png') AS sender_avatar
           FROM messages m
           JOIN profiles p ON m.sender_id = p.profile_id
           WHERE (m.sender_id = :logged_in_user_id AND m.receiver_id = :user_id) 
           OR (m.sender_id = :user_id AND m.receiver_id = :logged_in_user_id)
           ORDER BY m.sent_at;
           ");
        $stmt->bindParam(':logged_in_user_id', $loggedInUserId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }

     exit();
}
 



public function markAsRead() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = (int) $_POST['user_id'];
        $contact_id = (int) $_POST['contact_id'];
        try {
            $stmt = $this->db->prepare("
                UPDATE messages 
                SET is_read = 1 
                WHERE receiver_id = :user_id AND sender_id = :contact_id AND is_read = 0
                ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Messages marked as read.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update messages.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    exit();
}

}