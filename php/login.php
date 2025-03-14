<?php
// Enable error reporting to debug blank pages
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session only if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if the password is correct
        if (password_verify($password, $user['password'])) {
            
            // Check if email is verified
            if ($user['is_verified'] == 1) {
                $_SESSION['user'] = $user; // Store user data in session
                header("Location: /TCUCampusPulse/index.php"); // Redirect to homepage
                exit();
            } else {
                echo "<script>alert('Your email is not verified. Please check your email.'); window.location='/TCUCampusPulse/php/login.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid password!'); window.location='/TCUCampusPulse/php/login.php';</script>";
        }
    } else {
        echo "<script>alert('Username not found!'); window.location='/TCUCampusPulse/php/login.php';</script>";
    }
}
?>
