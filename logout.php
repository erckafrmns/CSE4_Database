<?php
session_start();

// Check if student session exists
if(isset($_SESSION['student_id'])) {
    // Unset student session
    unset($_SESSION['student_id']);
}

// Check if admin session exists
if(isset($_SESSION['admin_id'])) {
    // Unset admin session
    unset($_SESSION['admin_id']);
}

header("Location: index.php");
exit();
?>
