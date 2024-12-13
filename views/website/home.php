<?php
ob_start();
$pageTitle = ''; 

// Function to display toast messages
function displayToastMessage($session_key, $toast_class, $title) {
    if (isset($_SESSION[$session_key])) {
        $message = $_SESSION[$session_key];
        unset($_SESSION[$session_key]);
        echo "<script type='text/javascript'>
        document.querySelector('.preloader').style.display = 'none';
        document.addEventListener('DOMContentLoaded', function() {
            $(document).Toasts('create', {
                class: '$toast_class',
                title: '$title',
                autohide: true,
                delay: 3000,
                body: '" . addslashes($message) . "' 
            });
        });
        </script>";
    }
}

// Display session messages
if (isset($_SESSION['error'])) {
    displayToastMessage('error', 'bg-danger', 'Error');
}
if (isset($_SESSION['info'])) {
    displayToastMessage('info', 'bg-info', 'Information');
}
if (isset($_SESSION['success'])) {
    displayToastMessage('success', 'bg-success', 'Success');
}


?>


     <img src="assets/slide-3.jpg" style="width: 80vw">






<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>