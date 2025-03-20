<?php
session_start();
require_once 'config.php'; // Ensure database connection

if (isset($_POST['confirmLogout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Default profile image
$profileImage = "../images/defaultuserprofile.jpg"; 

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id']; // Get user ID from session

    // Prepare the SQL query
    $query = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $query->bind_result($profileImageDb);
    
    // Fetch result properly
    if ($query->fetch() && !empty($profileImageDb)) {
        $uploadDir = '../uploads/'; // Define upload directory
        $profileImagePath = $uploadDir . $profileImageDb;

        // Check if the image file exists before using it
        if (file_exists($profileImagePath)) {
            $profileImage = $profileImagePath;
        }
    }

    $query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCU Campus Pulse</title>
    <link rel="icon" type="image/png" href="../images/iconlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="../js/index.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-purple-600">⚡ TCU Campus Pulse</div>

        <!-- Search Bar -->
        <input type="text" class="border px-4 py-2 rounded-md w-1/3" placeholder="Search posts, events, or users...">

        <!-- Conditional Buttons -->
        <div class="relative flex items-center space-x-4">
            <?php if(isset($_SESSION['user'])): ?>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Messages</button>
                <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Events</button>
                <button class="relative bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">3</span>
                    🔔
                </button>
                <!-- Profile Icon with Dropdown -->
                <div class="relative">
                    <button onclick="toggleProfileDropdown()" class="focus:outline-none">
                        <img src="<?php echo $profileImage; ?>" class="w-8 h-8 rounded-full">
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-md rounded-md overflow-hidden">
                        <a href="/TCUCampusPulse/php/userprofile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="/TCUCampusPulse/php/settings.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="#" onclick="showLogoutModal()" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button onclick="window.location.href='/TCUCampusPulse/php/login.php'"
                        class="bg-transparent border border-purple-600 text-purple-600 px-4 py-2 rounded-md hover:bg-purple-100">
                    Log in
                </button>
                <button onclick="window.location.href='/TCUCampusPulse/php/login.php?register=true'"
                        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                    Sign up
                </button>
            <?php endif; ?>
        </div>
    </header>

    <!-- Logout Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-lg font-semibold">Confirm Logout</h2>
            <p class="text-gray-600 mt-2">Are you sure you want to log out?</p>
            <form method="POST">
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="hideLogoutModal()" class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" name="confirmLogout" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 ml-2">Logout</button>
                </div>
            </form>
        </div>
    </div>
    
    <main class="flex p-6">
    <!-- Left Section (Main Content) -->
    <section class="w-3/4 space-y-6">
        <!-- Sorting UI -->
        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-md">
            <div class="flex border border-gray-300 rounded-md overflow-hidden">
                <button id="sortPopular" class="px-4 py-2 bg-gray-100 text-black font-semibold flex items-center">
                    🔥 Popular
                </button>
                <button id="sortRecent" class="px-4 py-2 text-gray-500 flex items-center">
                    ⏳ Recent
                </button>
            </div>
        </div>

        <!-- Post Card -->
        <div class="flex bg-white shadow-lg rounded-lg overflow-hidden">
            <img src="../images/graduation.jpg" class="w-48 h-32 object-cover">
            <div class="p-4">
                <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Announcements</span>
                <h2 class="text-lg font-semibold mt-2">Campus Library Extended Hours During Finals Week</h2>
                <p class="text-gray-600 text-sm mt-1">The campus library will be open 24/7 from Dec 10 to Dec 17...</p>
                <div class="flex justify-between items-center mt-3">
                    <div class="text-green-600 font-semibold">👍 124 | 👎 2</div>
                    <span class="text-gray-500 text-sm">💬 18</span>
                </div>
            </div>
        </div>
    </section>

        <aside class="w-1/4 ml-6 bg-white shadow-lg p-4 rounded-lg">
            <h3 class="font-semibold text-lg">Categories</h3>
            <ul class="mt-2 space-y-2 text-gray-600">
                <li>📌 All Posts</li>
                <li>🔥 Trending</li>
                <li>📚 Academic</li>
                <li>🏀 Sports</li>
                <li>📅 Events</li>
                <li>📢 Announcements</li>
            </ul>
            <button class="mt-4 w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">View All Events</button>
        </aside>
    </main>

    <script src="../js/index.js"></script>
</body>
</html>
