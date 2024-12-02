<?php ob_start();?>


<h1>Dashboard</h1>
<p>Welcome to the dashboard page!</p>

<?php
$content = ob_get_clean();
include 'views/master.php';
?>
