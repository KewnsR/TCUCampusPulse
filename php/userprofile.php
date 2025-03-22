<?php
session_start();
include '../php/config.php'; // Database connection

if (!isset($_SESSION['user']['student_number'])) {
    header("Location: login.php");
    exit();
}

$student_number = $_SESSION['user']['student_number'];

// Fetch user details
$query = $conn->prepare("SELECT last_name,first_name, mid_name, username, bio, department, year, status, profile_image, name_display_format FROM users WHERE student_number = ?");
$query->bind_param("s", $student_number);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
function formatUserName($user) {
    $format = $user['name_display_format'] ?? 'lfm';
    $first = htmlspecialchars($user['first_name']);
    $middle = htmlspecialchars($user['mid_name']);
    $last = htmlspecialchars($user['last_name']);

    switch ($format) {
        case 'lfm':
            return "$last, $first $middle";
        case 'fml':
            return "$first $middle $last";
        case 'lf':
            return "$last, $first";
        case 'fl':
            return "$first $last";
        default:
            return "$last, $first $middle";
    }
}

$display_name = formatUserName($user);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="flex justify-start mb-4">
        <a href="index.php" class="inline-block text-gray-600 hover:text-gray-800">&larr; Back to Feed</a>
    </div>
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="relative">
            <div class="h-32 bg-gradient-to-r from-purple-500 to-blue-500 rounded-t-lg"></div>
            <img src="<?= $user['profile_image'] ?: '../images/defaultuserrofile.jpg' ?>" 
                 class="w-24 h-24 rounded-full border-4 border-white absolute top-20 left-6">
        </div>
        <div class="mt-14 px-6">
        <h2 class="text-2xl font-bold"> <?= $display_name ?> </h2>
        <p class="text-gray-600">@<?= strtolower(str_replace(' ', '', $user['username'])) ?></p>
            <span class="px-2 py-1 text-sm bg-green-100 text-green-700 rounded"> <?= $user['status'] ?> </span>
            <p class="mt-2 text-gray-700"> <?= $user['bio'] ?> </p>
            <p class="text-gray-500 mt-2"><i class="fas fa-graduation-cap"></i> <?= $user['department'] ?> &bull; <?= $user['year'] ?></p>
        </div>
    </div>
</body>
</html>
