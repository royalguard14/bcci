<?php
require_once 'BaseController.php';
class RoleController extends BaseController {


    public function __construct($db) {
        
        parent::__construct($db, ['1']); 
    }

    
    public function showRoles() {
        $stmt = $this->db->prepare("SELECT * FROM roles");
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include 'views/roles/roles.php';
    }



    public function createRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and validate the role name
            $role_name = trim($_POST['role_name']);
            if (empty($role_name)) {
                echo "Error: Role name cannot be empty.";
                return;
            }
        // Prepare and execute the database insert
            $stmt = $this->db->prepare("INSERT INTO roles (role_name) VALUES (:role_name)");
            $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR);
            if ($stmt->execute()) {
            // Redirect after successful creation
                header("Location: /BCCI/roles");
                exit();
            } else {
                echo "Error: Could not create role.";
            }
        }
    }
    

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id']) && isset($_POST['role_name'])) {
            $role_id = (int) $_POST['role_id'];
            $role_name = trim($_POST['role_name']);
                    // Update the role in the database
            $stmt = $this->db->prepare("UPDATE roles SET role_name = :role_name WHERE role_id = :role_id");
            $stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                        header("Location: /BCCI/roles"); // Redirect to roles page after update
                        exit();
                    } else {
                        echo "Error: Could not update role.";
                    }
                }
            }


            public function deleteRole() {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id'])) {
                    $role_id = (int) $_POST['role_id'];
                    $stmt = $this->db->prepare("DELETE FROM roles WHERE role_id = :role_id");
                    $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        // Redirect to the roles page after deletion
                        header("Location: /BCCI/roles");
                        exit();
                    } else {
                        echo "Error: Could not delete role.";
                    }
                }
            }


public function getUsersByRoleId() {
    if (isset($_POST['role_id'])) {
        // Sanitize and cast role_id to integer
        $role_id = (int) $_POST['role_id'];

        try {
            // Prepare the query
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id, 
                    COALESCE(
                        CONCAT(p.last_name, ', ', p.first_name, ' ', LEFT(p.middle_name, 1)), 
                        'Rosales, Jerico S.'
                    ) AS full_name
                FROM users u
                LEFT JOIN profiles p ON u.user_id = p.profile_id
                WHERE u.isDelete = 0 AND u.role_id = :role_id
            ");

            // Bind the role_id parameter
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch all results
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return results as JSON
            header('Content-Type: application/json');
            echo json_encode($users);

        } catch (Exception $e) {
            // Handle exceptions
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Handle missing role_id
        header('Content-Type: application/json', true, 400);
        echo json_encode(['error' => 'Role ID not provided']);
    }
    exit();
}




         public function updateRolePermissions() {
        // Get the JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        $roleId = $data['role_id'];
        $permissions = implode(',', $data['permissions']); // Convert array to comma-separated string

        try {
            // Update the roles table with the new permission_id value
            $stmt = $this->db->prepare("UPDATE roles SET permission_id = :permissions WHERE role_id = :role_id");
            $stmt->bindParam(':permissions', $permissions, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Permissions updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to execute update statement.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }








    public function getRolePermissions() {
    $input = json_decode(file_get_contents('php://input'), true);
    $role_id = $input['role_id'];

    // Fetch role and assigned permissions
    $stmt = $this->db->prepare("SELECT permission_id FROM roles WHERE role_id = :role_id");
    $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
    $stmt->execute();
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert permission_id from a string to an array
    $assigned_permissions = explode(',', $role['permission_id']);

    // Fetch all permissions
    $stmt = $this->db->prepare("SELECT * FROM permissions");
    $stmt->execute();
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'permissions' => $permissions,
        'assigned_permissions' => $assigned_permissions
    ]);
}



        }
    ?>