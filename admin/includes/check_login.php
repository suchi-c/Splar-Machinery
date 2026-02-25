<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ../admin/index.php?error=unauthorized");
    exit();
}
?>
