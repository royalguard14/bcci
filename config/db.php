
<?php
// Database configuration
$host = 'localhost';
$dbname = 'bcci';
$username = 'root';
$password = '';

// Create a PDO instance (database connection)
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
