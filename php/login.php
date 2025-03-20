<?php
session_start();
require __DIR__ . '/config.php';

// Enable error reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action']; // "login" or "register"

    if ($action === "login") {
        // LOGIN PROCESS
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);

        if (empty($username) || empty($password) || empty($role)) {
            echo "<script>alert('All fields are required for login!'); window.location='/TCUCampusPulse/php/login.php';</script>";
            exit();
        }

        $stmt = $conn->prepare("SELECT id, username, password, is_verified, role FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 1) {
                session_regenerate_id(true);
                
                // Fetch student number if the role is "Student"
                $student_number = null;
                if ($role === "Student") {
                    $stmt = $conn->prepare("SELECT student_number FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $student_data = $result->fetch_assoc();
                    $student_number = $student_data['student_number'] ?? null;
                    $stmt->close();
                }
        
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'),
                    'role' => htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'),
                    'student_number' => $student_number // ✅ FIXED: Add student number to session
                ];
                
                header("Location: /TCUCampusPulse/php/index.php");
                exit();
            } else {
                echo "<script>alert('Your email is not verified. Please check your email.'); window.location='/TCUCampusPulse/php/login.php';</script>";
                exit();
            }
        }
        if ($action === "register") {
        // REGISTRATION PROCESS
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $role = trim($_POST['role']);
        $student_number = isset($_POST['student_number']) ? trim($_POST['student_number']) : null;

        if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
            echo "<script>alert('All fields are required!'); window.location='/TCUCampusPulse/php/login.php';</script>";
            exit();
        }

        // If role is Student, validate student number
        if ($role === "Student") {
            if (!preg_match('/^\d{2}-\d{5}$/', $student_number)) {
                echo "<script>alert('Invalid student number format! Use XX-XXXXX.'); window.location='/TCUCampusPulse/php/login.php';</script>";
                exit();
            }

            // Check if student number exists in valid_students
            $stmt = $conn->prepare("SELECT * FROM valid_students WHERE student_number = ?");
            $stmt->bind_param("s", $student_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows === 0) {
                echo "<script>alert('Student number not found. Contact admin for verification.'); window.location='/TCUCampusPulse/php/login.php';</script>";
                exit();
            }
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, role, student_number, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssss", $full_name, $username, $email, $hashed_password, $role, $student_number);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Verify your email before logging in.'); window.location='/TCUCampusPulse/php/login.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to register. Try again later.'); window.location='/TCUCampusPulse/php/login.php';</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCU Campus Pulse - Login/Register</title>
    <link rel="icon" type="image/png" href="../images/iconlogo.png">
    <link rel="stylesheet" href="../styles/loginsignup.css">
</head>
<body>
    <div class="container">
        <h1 class="title">TCU Campus Pulse</h1>
        <p class="subtitle">Connect with the TCU community</p>

        <div class="tabs">
            <button id="loginTab" class="active" onclick="showLogin()">Login</button>
            <button id="registerTab" onclick="showRegister()">Register</button>
        </div>

        <!-- LOGIN FORM -->
        <form id="loginForm" class="form-container" method="POST">
            <input type="hidden" name="action" value="login">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <p>Login as:</p>
            <div class="role-select">
                <button type="button" class="selected" data-role="Student">Student</button>
                <button type="button" data-role="Professor/Alumni">Professor/Alumni</button>
                <button type="button" data-role="Moderator">Moderator</button>
            </div>
            <input type="hidden" id="roleInputLogin" name="role">
            <button type="submit" class="submit">Login</button>
            <p class="switch">Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
        </form>
        

        <!-- REGISTER FORM -->
        <form id="registerForm" class="form-container hidden" method="POST">
            <input type="hidden" name="action" value="register">
            <input type="text" name="full_name" placeholder="Full name">
            <input type="text" name="username" placeholder="Username">
            <input type="email" name="email" placeholder="Email address">
            <input type="password" name="password" placeholder="Password">
            <p>I am a:</p>
            <div class="role-select">
                <button type="button" class="selected" data-role="Student">Student</button>
                <button type="button" data-role="Professor/Alumni">Professor/Alumni</button>
            </div>
            <input type="hidden" id="roleInputRegister" name="role">
            <input type="text" name="student_number" placeholder="Student Number" class="student-input">
            <button type="submit" class="submit">Create account</button>
            <p class="switch">Already have an account? <a href="#" onclick="showLogin()">Login</a></p>
        </form>
    </div>

    <script src="../js/login.js"></script>
</body>
</html>