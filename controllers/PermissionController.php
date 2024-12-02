<?php 
require_once 'BaseController.php'; 
class PermissionController extends BaseController { 
    
    public function __construct($db) {
        
        parent::__construct($db, ['2']); 
    }


    public function showPermission() {
        $stmt = $this->db->prepare("SELECT * FROM permissions Order by permission_id");
        $stmt->execute();
        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/permissions/permissions.php';
    }
    
    public function createPermission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and validate the role name
            $permission_name = trim($_POST['permission_name']);
            if (empty($permission_name)) {
                echo "Error: Role name cannot be empty.";
                return;
            }
        // Prepare and execute the database insert
            $stmt = $this->db->prepare("INSERT INTO permissions (permission_name) VALUES (:permission_name)");
            $stmt->bindParam(':permission_name', $permission_name, PDO::PARAM_STR);
            if ($stmt->execute()) {
            // Redirect after successful creation
                header("Location: /BCCI/permissions");
                exit();
            } else {
                echo "Error: Could not create permissions.";
            }
        }
    }
    
    public function updatePermission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permission_id']) && isset($_POST['permission_name'])) {
            $permission_id = (int) $_POST['permission_id'];
            $permission_name = trim($_POST['permission_name']);
                    // Update the role in the database
            $stmt = $this->db->prepare("UPDATE permissions SET permission_name = :permission_name WHERE permission_id = :permission_id");
            $stmt->bindParam(':permission_name', $permission_name, PDO::PARAM_STR);
            $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                        header("Location: /BCCI/permissions"); // Redirect to roles page after update
                        exit();
                    } else {
                        echo "Error: Could not update role.";
                    }
                }
            }
            
            public function deletePermission() {
              if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permission_id'])) {
                $permission_id = (int) $_POST['permission_id'];
                $stmt = $this->db->prepare("DELETE FROM permissions WHERE permission_id = :permission_id");
                $stmt->bindParam(':permission_id', $permission_id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    header("Location: /BCCI/permissions");
                    exit();
                } else {
                    echo "Error: Could not delete permission.";
                }
            }
        }






    }