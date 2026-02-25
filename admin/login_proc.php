<?php
session_start();
require_once '../includes/db.php'; // Check correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Validate CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed."); 
    }

    // 2. Get Input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 3. Prepare Query
    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();

        // 4. Verify Password
        if ($user && password_verify($password, $user['password'])) {
            // Success! Set session variables
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Fail
            header("Location: index.php?error=invalid");
            exit();
        }

    } catch (PDOException $e) {
        // Log error and redirect generic error
        error_log($e->getMessage());
        header("Location: index.php?error=system");
        exit();
    }
} else {
    // If someone tries to access this file directly without POST
    header("Location: index.php");
    exit();
}
?>
