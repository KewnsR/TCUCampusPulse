<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $student_number = $_POST['student_number'] ?? NULL;
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location='../signup.html';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Generate a 6-digit verification code
    $verification_code = rand(100000, 999999);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (student_number, username, email, password, role, verification_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $student_number, $username, $email, $hashed_password, $role, $verification_code);

    if ($stmt->execute()) {
        // Send verification email
        $subject = "TCU Campus Pulse - Email Verification";
        $message = "Your verification code is: $verification_code";
        $headers = "From: no-reply@campuspulse.com";
        mail($email, $subject, $message, $headers);

        // Redirect to verification page
        header("Location: ../verify.html?email=$email");
        exit();
    } else {
        echo "<script>alert('Registration failed! Try again.'); window.location='../signup.html';</script>";
    }
}
?>
