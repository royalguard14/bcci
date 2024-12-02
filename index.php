<?php
require 'vendor/autoload.php';


// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
// Automatically include all PHP files in the controllers directory
foreach (glob("controllers/*.php") as $filename) {
    require_once $filename;
}


// Capture the requested URL path (remove the base URL and any query string)
$baseUrl = 'BCCI';
$requestPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Adjust the URL to handle the base URL (BCCI)
$requestPath = str_replace($baseUrl . '/', '', $requestPath);

// Define routes
$routes = [
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'showDashboard'], 
    'login' => ['controller' => 'LoginController', 'action' => 'showLoginForm'],
    'login/submit' => ['controller' => 'LoginController', 'action' => 'handleLogin'],  
    'logout' => ['controller' => 'LoginController', 'action' => 'handleLogout'],  
    'roles' => ['controller' => 'RoleController', 'action' => 'showRoles'],
    'roles/create' => ['controller' => 'RoleController', 'action' => 'createRole'],
    'roles/update' => ['controller' => 'RoleController', 'action' => 'updateRole'],
    'roles/delete' => ['controller' => 'RoleController', 'action' => 'deleteRole'],
    'roles/getUsersByRoleId' => ['controller' => 'RoleController', 'action' => 'getUsersByRoleId'],
    'permissions' => ['controller' => 'PermissionController', 'action' => 'showPermission'],
    'permissions/create' => ['controller' => 'PermissionController', 'action' => 'createPermission'],
    'permissions/update' => ['controller' => 'PermissionController', 'action' => 'updatePermission'],
    'permissions/delete' => ['controller' => 'PermissionController', 'action' => 'deletePermission'],
    'accounts' => ['controller' => 'UserController', 'action' => 'showUsers'],
    'account/create' => ['controller' => 'UserController', 'action' => 'createUser'],
    'account/update' => ['controller' => 'UserController', 'action' => 'updateUser'],
    'account/delete' => ['controller' => 'UserController', 'action' => 'deleteUser'],
    'role-permission' => ['controller' => 'RoleController', 'action' => 'getRolePermissions'],
    'update-role-permissions' => ['controller' => 'RoleController', 'action' => 'updateRolePermissions'],
    'unauthorized' => ['controller' => 'BaseController', 'action' => 'errors'],
    'campus-profile' => ['controller' => 'CampusController', 'action' => 'showCampusProfile'],
    'campus-school-year/create' => ['controller' => 'CampusController', 'action' => 'addCampusschoolyear'],
    'campus-school-year/delete' => ['controller' => 'CampusController', 'action' => 'deleteCampusschoolyear'],
    'campus-info/update' => ['controller' => 'CampusController', 'action' => 'updateCampusInfo'],
    'campus-grades' => ['controller' => 'CampusController', 'action' => 'showCampusGrade'],
    'campus-sections' => ['controller' => 'CampusController', 'action' => 'showCampusSection'],
    'campus-subjects' => ['controller' => 'CampusController', 'action' => 'showCampusSubject'],
    'campus-grades/create' => ['controller' => 'CampusController', 'action' => 'createCampusGrade'],
    'campus-grades/update' => ['controller' => 'CampusController', 'action' => 'updateCampusGrade'],
    'campus-grades/delete' => ['controller' => 'CampusController', 'action' => 'deleteCampusGrade'],
    'campus-sections/create' => ['controller' => 'CampusController', 'action' => 'createCampusSection'],
    'campus-sections/update' => ['controller' => 'CampusController', 'action' => 'updateCampusSection'],
    'campus-sections/delete' => ['controller' => 'CampusController', 'action' => 'deleteCampusSection'],
    'campus-sections/update-adviser' => ['controller' => 'CampusController', 'action' => 'updateAdviser'],
    'campus-subjects/create' => ['controller' => 'CampusController', 'action' => 'createCampusSubject'],
    'campus-subjects/update' => ['controller' => 'CampusController', 'action' => 'updateCampusSubject'],
    'campus-subjects/delete' => ['controller' => 'CampusController', 'action' => 'deleteCampusSubject'],
    'grade-sections' => ['controller' => 'CampusController', 'action' => 'getGradeSections'],
    'update-grade-sections' => ['controller' => 'CampusController', 'action' => 'updateGradeSections'],
    'grade-subjects' => ['controller' => 'CampusController', 'action' => 'getGradeSubjects'],
    'update-grade-subjects' => ['controller' => 'CampusController', 'action' => 'updateGradeSubjects'],
    'teacher-list' => ['controller' => 'RegistrarMngtController', 'action' => 'showTeacherList'],
    'teacher-list/create' => ['controller' => 'RegistrarMngtController', 'action' => 'addOrUploadTeacher'],
    'teacher-list/upload' => ['controller' => 'RegistrarMngtController', 'action' => 'addOrUploadTeacher'],
    'teacher-list/delete' => ['controller' => 'RegistrarMngtController', 'action' => 'deleteTeacher'],
    'students-list' => ['controller' => 'RegistrarMngtController', 'action' => 'showsStudentList'],
    'users/delete' => ['controller' => 'RegistrarMngtController', 'action' => 'deleteUser'],
    'users/update' => ['controller' => 'RegistrarMngtController', 'action' => 'updateUser'],
    'myclass-list' => ['controller' => 'AdviserMngtController', 'action' => 'show'],
    'myclass-list/enroll' => ['controller' => 'AdviserMngtController', 'action' => 'enroll'],
    'myclass-list/update' => ['controller' => 'AdviserMngtController', 'action' => 'update'],
    'myclass-list/drop' => ['controller' => 'AdviserMngtController', 'action' => 'unenroll'],
    'student-list/upload' => ['controller' => 'RegistrarMngtController', 'action' => 'addOrUploadStudent'],
    'schoolform1' => ['controller' => 'AdviserMngtController', 'action' => 'showschoolform1'],
    'schoolform2' => ['controller' => 'AdviserMngtController', 'action' => 'showAttendance'],
    'attendance/submit' => ['controller' => 'AdviserMngtController', 'action' => 'submitAttendance'],
    'attendance/updateRemark' => ['controller' => 'AdviserMngtController', 'action' => 'updateRemark'],
    'class-record' => ['controller' => 'AdviserMngtController', 'action' => 'showClassRecord'],
    'class-record-update-add' => ['controller' => 'AdviserMngtController', 'action' => 'addOrUpdateClassRecord'],
    'learners-profile' => ['controller' => 'StudentAccessController', 'action' => 'showProfile'],
    'learners-attendance' => ['controller' => 'StudentAccessController', 'action' => 'myAttendance'],
    'learners-enrollment-history' => ['controller' => 'StudentAccessController', 'action' => 'enrollmentHistory'],
    'learners-academic-history' => ['controller' => 'StudentAccessController', 'action' => 'academicHistory'],
    'learners-storage' => ['controller' => 'StudentAccessController', 'action' => 'storage'],
    'updateuserpass' => ['controller' => 'StudentAccessController', 'action' => 'updateuserpass'],
    'uploadprofile' => ['controller' => 'StudentAccessController', 'action' => 'uploadprofile'],
    'uploadDocs' => ['controller' => 'StudentAccessController', 'action' => 'uploadDocs'],
    'deleteFile' => ['controller' => 'StudentAccessController', 'action' => 'deleteFile'],
     'parents-list' => ['controller' => 'RegistrarMngtController', 'action' => 'showsParentList'],
     'parents-add' => ['controller' => 'RegistrarMngtController', 'action' => 'addParentList'],
     'parents-fetch' => ['controller' => 'RegistrarMngtController', 'action' => 'fetchParentList'],
     'parents-update' => ['controller' => 'RegistrarMngtController', 'action' => 'updateParentList'],
     'parents-family' => ['controller' => 'RegistrarMngtController', 'action' => 'familyParentList'],

     'fetch-chat-available' => ['controller' => 'MsgManagementController', 'action' => 'chatavailable'],
     'fetch-message' => ['controller' => 'MsgManagementController', 'action' => 'fetchmessage'],
     
     'sendMessage' => ['controller' => 'MsgManagementController', 'action' => 'sendMessage'],



    
    

];

// Check if the user is logged in before allowing access to other pages
if (!isset($_SESSION['log_in']) && $requestPath !== 'login' && $requestPath !== 'login/submit' && $requestPath !== 'logout') {
    header('Location: /BCCI/login');
    exit();
}


// Route the request to the appropriate controller and action
if (array_key_exists($requestPath, $routes)) {
    $route = $routes[$requestPath];
    $controllerName = $route['controller'];
    $action = $route['action'];

    // Instantiate and call the controller action
    $controller = new $controllerName($db);
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo "Error: Action not found.";
    }
} else {
    http_response_code(404);
    echo "Error: Page not found.";
}
?>
