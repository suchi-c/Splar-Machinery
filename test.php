<?php
require 'includes/db.php';

// Try to fetch settings to see if tables exist
$stmt = $pdo->query("SELECT * FROM site_settings");
$settings = $stmt->fetch();

echo "<h1>Database Check:</h1>";
echo "Connected successfully.<br>";
echo "Office Address from DB: " . $settings['office_address'];
?>