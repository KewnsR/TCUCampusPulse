<?php
session_start();
include '../php/config.php'; // Include database connection

if (!isset($_SESSION['user']['student_number'])) {
    header("Location: login.php");
    exit();
}

$student_number = $_SESSION['user']['student_number'];

// Fetch user details
$query = $conn->prepare("SELECT id, full_name, username, bio, department, year, status, profile_image FROM users WHERE student_number = ?");
$query->bind_param("s", $student_number);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_image'])) {
        $target_dir = "../uploads/";
        // Ensure the upload directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file type
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->bind_param("si", $target_file, $user_id);
                $stmt->execute();
                header("Location: settings.php");
                exit();
            } else {
                echo "<script>alert('Error uploading the file. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
        }
    }
    if (isset($_POST['full_name']) && isset($_POST['username'])) {
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $bio = trim($_POST['bio']);
        $department = trim($_POST['department']);
        $year = trim($_POST['year']);
        $status = trim($_POST['status']);

        $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, bio=?, department=?, year=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssi", $full_name, $username, $bio, $department, $year, $status, $user_id);
        $stmt->execute();
        header("Location: settings.php");
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
</head>
<body class="bg-gray-100 p-10">

    <!-- Back Button -->
    <a href="index.php" class="mb-4 inline-block bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-800">
        ← Back
    </a>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold">Settings</h2>
        <div class="flex items-center space-x-4 mt-4">
            <img src="<?= $user['profile_image'] ?: '../images/defaultuserprofile.jpg' ?>" class="w-24 h-24 rounded-full border">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer">
                <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-md">Upload</button>
            </form>
        </div>
        <form method="POST" id="settingsForm" class="mt-6 space-y-4">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full border px-4 py-2 rounded-md">

            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full border px-4 py-2 rounded-md">
            
            <label>Bio</label>
            <textarea name="bio" class="w-full border px-4 py-2 rounded-md"><?= htmlspecialchars($user['bio']) ?></textarea>
            
            <label>Department</label>
            <input type="text" name="department" value="<?= htmlspecialchars($user['department']) ?>" class="w-full border px-4 py-2 rounded-md">
            
            <label>Year</label>
            <input type="text" name="year" value="<?= htmlspecialchars($user['year']) ?>" class="w-full border px-4 py-2 rounded-md">
            
            <label>Status</label>
            <select name="status" class="w-full border px-4 py-2 rounded-md">
                <option value="Regular" <?= $user['status'] == 'Regular' ? 'selected' : '' ?>>Regular</option>
                <option value="Irregular" <?= $user['status'] == 'Irregular' ? 'selected' : '' ?>>Irregular</option>
            </select>
            
            <!-- Save Changes Button (opens modal) -->
            <button type="button" onclick="openModal()" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-md">Save Changes</button>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Confirm Changes</h2>
            <p>Are you sure you want to save these changes?</p>
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                <button onclick="document.getElementById('settingsForm').submit()" class="bg-green-500 text-white px-4 py-2 rounded-md">Save</button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }
    </script>

</body>
</html>
