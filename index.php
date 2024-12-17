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
   





     'fetch-chat-available' => ['controller' => 'MsgManagementController', 'action' => 'chatavailable'],
     'fetch-message' => ['controller' => 'MsgManagementController', 'action' => 'fetchmessage'],
     
     'sendMessage' => ['controller' => 'MsgManagementController', 'action' => 'sendMessage'],



'campus-profile' => ['controller' => 'CampusController', 'action' => 'showCampusProfile'],
'campus-school-year/create' => ['controller' => 'CampusController', 'action' => 'addCampusschoolyear'],
'campus-school-year/delete' => ['controller' => 'CampusController', 'action' => 'deleteCampusschoolyear'],
'campus-info/update' => ['controller' => 'CampusController', 'action' => 'updateCampusInfo'],





'campus-department' => ['controller' => 'DepartmentController', 'action' => 'show'],
'campus-department/create' => ['controller' => 'DepartmentController', 'action' => 'store'],
'campus-department/update' => ['controller' => 'DepartmentController', 'action' => 'update'],
'campus-department/delete' => ['controller' => 'DepartmentController', 'action' => 'delete'],
'campus-department/subject' => ['controller' => 'DepartmentController', 'action' => 'getSubjects'],

'campus-department/rooms' => ['controller' => 'DepartmentController', 'action' => 'getRooms'],
'campus-department/dean' => ['controller' => 'DepartmentController', 'action' => 'getDean'],
'campus-department/deanadd' => ['controller' => 'DepartmentController', 'action' => 'getDeanadd'],
'campus-department/update-rooms' => ['controller' => 'DepartmentController', 'action' => 'updateDepartmentRoomIds'],


'update-department-subject-ids' => ['controller' => 'DepartmentController', 'action' => 'updateDepartmentSubject'],



'campus-subjects' => ['controller' => 'SubjectController', 'action' => 'showCampusSubject'],
'campus-subjects/create' => ['controller' => 'SubjectController', 'action' => 'createCampusSubject'],
'campus-subjects/update' => ['controller' => 'SubjectController', 'action' => 'updateCampusSubject'],
'campus-subjects/delete' => ['controller' => 'SubjectController', 'action' => 'deleteCampusSubject'],




'home' => ['controller' => 'FrontController', 'action' => 'whome'],
'contact' => ['controller' => 'FrontController', 'action' => 'contact'],
'register' => ['controller' => 'FrontController', 'action' => 'register'],
'enroll' => ['controller' => 'FrontController', 'action' => 'enroll'],



'pending_student' => ['controller' => 'RegistrarController', 'action' => 'show'],
'pending_student_count' => ['controller' => 'RegistrarController', 'action' => 'count'],
'pending_student_procced' => ['controller' => 'RegistrarController', 'action' => 'confirm'],



'acad_setting' => ['controller' => 'StudentsController', 'action' => 'acad_setup'],
'updatemycourse' => ['controller' => 'StudentsController', 'action' => 'updatemycourse'],
'addsubject' => ['controller' => 'StudentsController', 'action' => 'addsubject'],
'fetchSubject' => ['controller' => 'StudentsController', 'action' => 'getSubjs'],
'checkScheduleConflict' => ['controller' => 'StudentsController', 'action' => 'checkScheduleConflict'],
'enrollSubjects' => ['controller' => 'StudentsController', 'action' => 'enrollSubjects'],
'profile' => ['controller' => 'StudentsController', 'action' => 'profilespace'],
'uploadprofile' => ['controller' => 'StudentsController', 'action' => 'uploadprofile'],
'updateuserpass' => ['controller' => 'StudentsController', 'action' => 'updateuserpass'],
'enrollment-history' => ['controller' => 'StudentsController', 'action' => 'enrollmenthistory'],
'academic-record' => ['controller' => 'StudentsController', 'action' => 'allgrades'],
'academic-payment' => ['controller' => 'StudentsController', 'action' => 'allpayments'],
'documents' => ['controller' => 'StudentsController', 'action' => 'mydocuments'],
'uploadDocs' => ['controller' => 'StudentsController', 'action' => 'uploadDocs'],
'deleteFile' => ['controller' => 'StudentsController', 'action' => 'deleteFile'],






'enrollies' => ['controller' => 'RegistrarController', 'action' => 'enrollies'],
'toPayment' => ['controller' => 'RegistrarController', 'action' => 'toPayment'],
'toPaymentConfirm' => ['controller' => 'RegistrarController', 'action' => 'toPaymentConfirm'],
'getDetailCOE' => ['controller' => 'RegistrarController', 'action' => 'getDetailCOE'],
 

'paymentSetting' => ['controller' => 'AccountingController', 'action' => 'paymentSetting'],
'updatePaymentSetting' => ['controller' => 'AccountingController', 'action' => 'updatePaymentSetting'],
'paymentlog' => ['controller' => 'AccountingController', 'action' => 'paymentlog'],
'bayadna' => ['controller' => 'AccountingController', 'action' => 'bayadna'],
'bayadnapo' => ['controller' => 'AccountingController', 'action' => 'bayadnapo'],
'paynow' => ['controller' => 'AccountingController', 'action' => 'sakitsaulo'],





'instructors' => ['controller' => 'DeanController', 'action' => 'instructors'],
'addInstructors' => ['controller' => 'DeanController', 'action' => 'addInstrutors'],
'remove-teacher-from-dept' => ['controller' => 'DeanController', 'action' => 'removeInstrutors'],

'fetchSchedule' => ['controller' => 'DeanController', 'action' => 'fetchSchedule'],
'updateAdviser' => ['controller' => 'DeanController', 'action' => 'updateAdviser'],


'mysched' => ['controller' => 'AdviserController', 'action' => 'mysched'],
'gradestudent' => ['controller' => 'AdviserController', 'action' => 'gradestudent'],
'gradingsubject' => ['controller' => 'AdviserController', 'action' => 'gradingsubject'],
'grade' => ['controller' => 'AdviserController', 'action' => 'grade'],
'updategrade' => ['controller' => 'AdviserController', 'action' => 'updategrade'],

    
    
'test' => ['controller' => 'CampusController', 'action' => 'createScheduleForSemester'],
];

//Check if the user is logged in before allowing access to other pages
// if (!isset($_SESSION['log_in']) && $requestPath !== 'login' && $requestPath !== 'login/submit' && $requestPath !== 'logout') {
//     header('Location: /BCCI/login');
//     exit();
// }


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
        header("Location: /BCCI/unauthorized");
    }
} else {
    http_response_code(404);
    header("Location: /BCCI/unauthorized");
}
?>
