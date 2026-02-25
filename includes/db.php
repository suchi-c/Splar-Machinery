<?php
/*
 * Database Connection File
 * Using PDO (PHP Data Objects) for security and flexibility.
 */

// 1. Database Credentials
// Update these if you deploy to a live server later.
$host = 'localhost';
$db_name = 'splar_machinery';
$username = 'root';      // Default XAMPP username
$password = '';          // Default XAMPP password is empty

// 2. Set DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";

// 3. Create Options for Better Error Handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw errors instead of silent failures
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return arrays with column names (['name' => 'Solar Machine'])
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements (Security)
];

// 4. Attempt Connection
try {
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Uncomment the line below only for testing, then delete it.
    // echo "Connected successfully to the database!"; 
    
} catch (\PDOException $e) {
    // If something goes wrong, stop everything and show error
    // In production, log this to a file instead of showing the user
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>