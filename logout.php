<?php
// logout.php - Logout handler
include 'db.php';
session_destroy();
echo 'Logged out';
?>
