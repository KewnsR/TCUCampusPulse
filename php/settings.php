<?php
session_start();
include 'config.php'; // Corrected path to config.php

if (!isset($_SESSION['user']['student_number'])) {
    header("Location: login.php");
    exit();
}

$student_number = $_SESSION['user']['student_number'];

// Fetch user details
$query = $conn->prepare("SELECT id, first_name, mid_name, last_name, username, bio, department, year, status, profile_image, name_display_format FROM users WHERE student_number = ?");
$query->bind_param("s", $student_number);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch password length
$stmt = $conn->prepare("SELECT LENGTH(password) AS password_length FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($password_length);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_image'])) {
        $upload_date = date("YmdHis");
        $unique_filename = "{$student_number}_{$upload_date}_" . basename($_FILES["profile_image"]["name"]);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $unique_filename;
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();
            echo json_encode(["success" => true, "profile_image" => $target_file]);
            exit();
        }
    }
    if (isset($_POST['first_name']) && isset($_POST['last_name'])) {
        $first_name = trim($_POST['first_name']);
        $mid_name = trim($_POST['mid_name']);
        $last_name = trim($_POST['last_name']);
        $username = trim($_POST['username']);
        $bio = trim($_POST['bio']);
        $department = trim($_POST['department']);
        $year = trim($_POST['year']);
        $status = trim($_POST['status']);
        $name_display_format = $_POST['name_display_format'];

        $stmt = $conn->prepare("UPDATE users SET first_name=?, mid_name=?, last_name=?, username=?, bio=?, department=?, year=?, status=?, name_display_format=? WHERE id=?");
        $stmt->bind_param("sssssssssi", $first_name, $mid_name, $last_name, $username, $bio, $department, $year, $status, $name_display_format, $user_id);
        $stmt->execute();
        header("Location: settings.php");
        exit();
    }
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

    if (isset($_POST['old_password'], $_POST['new_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        
        if (password_verify($old_password, $hashed_password)) {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect old password"]);
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="../js/setting.js" defer></script> 
    <link rel="stylesheet" href="../styles/settings.css">
</head>
<body class="bg-gray-100 p-10">
    <div class="flex justify-between mb-4">
        <a href="index.php" class="inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-800">← Back</a>
        <button type="submit" form="settingsForm" class="bg-purple-600 text-white px-4 py-2 rounded-md">Save Changes</button>
    </div>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold">Settings</h2>
        <div class="mt-4">
            <ul class="flex justify-center space-x-4 border-b">
                <li class="-mb-px">
                    <a class="bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 text-blue-700 font-semibold" href="#profile">Profile</a>
                </li>
                <li>
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" href="#notification">Notification</a>
                </li>
                <li>
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" href="#appearance">Appearance</a>
                </li>
                <li>
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" href="#privacy">Privacy</a>
                </li>
                <li>
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" href="#security">Security</a>
                </li>
            </ul>
            <div id="profile" class="tab-content mt-4">
                <div class="flex flex-col items-center space-y-4 mt-4">
                    <div class="relative w-32 h-32 overflow-hidden border rounded-full">
                        <img id="profileImg" src="<?= $user['profile_image'] ?: '../images/defaultuserprofile.jpg' ?>" class="w-full h-full object-cover">
                    </div>

                    <!-- Upload and Crop Buttons -->
                    <div class="flex flex-col space-y-2">
                        <label for="fileInput" class="custom-file-upload bg-blue-500 text-white px-4 py-2 rounded-md cursor-pointer">
                            Choose File
                        </label>
                        <input type="file" id="fileInput" class="hidden">
                    </div>
                </div>

                <!-- Cropper Modal -->
                <div id="cropperModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                        <h2 class="text-xl font-semibold mb-4">Adjust Your Image</h2>
                        <div class="w-full h-64">
                            <img id="cropperImage" class="max-w-full">
                        </div>
                        <div class="mt-4 flex justify-between">
                            <button onclick="cropperZoomIn()" class="bg-gray-300 px-3 py-1 rounded-md">+</button>
                            <button onclick="cropperZoomOut()" class="bg-gray-300 px-3 py-1 rounded-md">-</button>
                            <button onclick="cropperMoveLeft()" class="bg-gray-300 px-3 py-1 rounded-md">←</button>
                            <button onclick="cropperMoveRight()" class="bg-gray-300 px-3 py-1 rounded-md">→</button>
                        </div>
                        <div class="mt-4 flex justify-end space-x-4">
                            <button onclick="closeCropper()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                            <button onclick="saveCroppedImage()" class="bg-green-500 text-white px-4 py-2 rounded-md">Save</button>
                        </div>
                    </div>
                </div>
                <form method="POST" id="settingsForm" class="mt-6 space-y-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                        <div class="flex-1">
                            <label>Middle Name</label>
                            <input type="text" name="mid_name" value="<?= htmlspecialchars($user['mid_name']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                        <div class="flex-1">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label>Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                        <div class="flex-1">
                            <label>Display Name Format</label>
                            <select name="name_display_format" class="w-full border px-4 py-2 rounded-md">
                                <option value="lfm" <?= $user['name_display_format'] == 'lfm' ? 'selected' : '' ?>>Last Name, First Name Middle Name</option>
                                <option value="fml" <?= $user['name_display_format'] == 'fml' ? 'selected' : '' ?>>First Name Middle Name Last Name</option>
                                <option value="lf" <?= $user['name_display_format'] == 'lf' ? 'selected' : '' ?>>Last Name, First Name</option>
                                <option value="fl" <?= $user['name_display_format'] == 'fl' ? 'selected' : '' ?>>First Name Last Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label>Department</label>
                            <input type="text" name="department" value="<?= htmlspecialchars($user['department']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                        <div class="flex-1">
                            <label>Year</label>
                            <input type="text" name="year" value="<?= htmlspecialchars($user['year']) ?>" class="w-full border px-4 py-2 rounded-md">
                        </div>
                        <div class="flex-1">
                            <label>Status</label>
                            <select name="status" class="w-full border px-4 py-2 rounded-md">
                                <option value="Regular" <?= $user['status'] == 'Regular' ? 'selected' : '' ?>>Regular</option>
                                <option value="Irregular" <?= $user['status'] == 'Irregular' ? 'selected' : '' ?>>Irregular</option>
                            </select>
                        </div>
                    </div>
                    
                    <label>Bio</label>
                    <textarea name="bio" class="w-full border px-4 py-2 rounded-md"><?= htmlspecialchars($user['bio']) ?></textarea>
                    
                    <label>Password</label>
                    <input type="password" id="passwordField" value="<?= str_repeat('*', $password_length) ?>" class="mt-4 px-4 py-2 rounded-md border-2 border-gray-500" readonly>
                    <button type="button" onclick="openPasswordModal()" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-md">Change</button>        
                </form>
            </div>
            <div id="notification" class="tab-content mt-4 hidden">
                <!-- Notification settings content -->
            </div>
            <div id="appearance" class="tab-content mt-4 hidden">
                <!-- Appearance settings content -->
            </div>
            <div id="privacy" class="tab-content mt-4 hidden">
                <!-- Privacy settings content -->
            </div>
            <div id="security" class="tab-content mt-4 hidden">
                <!-- Security settings content -->
            </div>
        </div>
    </div>
    <div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>
            <input type="password" id="oldPassword" placeholder="Old Password" class="w-full border px-4 py-2 rounded-md mb-2">
            <input type="password" id="newPassword" placeholder="New Password" class="w-full border px-4 py-2 rounded-md mb-2">
            <p id="passwordStrength" class="text-sm"></p>
            <input type="password" id="confirmPassword" placeholder="Confirm Password" class="w-full border px-4 py-2 rounded-md mb-4">
            <button onclick="changePassword()" class="bg-green-500 text-white px-4 py-2 rounded-md">OK</button>
            <button onclick="closePasswordModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
        </div>
    </div>
    <script src="../js/setting.js"></script> <!-- Corrected path to setting.js -->
</body>
</html>