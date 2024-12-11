<?php
ob_start();
$pageTitle = ''; 


?>


     <img src="assets/slide-3.jpg" style="width: 80vw">






<?php
$content = ob_get_clean();
include 'views/master-top.php';
?>