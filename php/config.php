<?php
// Database configuration
$servername = "localhost"; // Change if hosted elsewhere
$username = "root"; // Default for XAMPP, change if different
$password = ""; // Default is empty for XAMPP, change if needed
$database = "tcucampus_pulse"; // Ensure this database name is correct

// Enable error reporting for debugging (Remove in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $database);
    
    // Set charset to UTF-8 (Prevents encoding issues)
    $conn->set_charset("utf8mb4");
    
} catch (mysqli_sql_exception $e) {
    // Handle connection error gracefully
    die("Database connection failed: " . $e->getMessage());
}
?>
