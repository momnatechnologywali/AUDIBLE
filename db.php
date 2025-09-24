<?php
// db.php
// Database connection file - Include this in other PHP files
 
$host = 'localhost';  // Assuming localhost; change if needed
$dbname = 'dbxmlqbqybbxtu';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Session start for auth
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
