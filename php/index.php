<?php
session_start();
require_once 'config.php'; // Ensure database connection

// Handle Logout
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
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white">
    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-800 shadow-md p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-purple-600 dark:text-purple-400">⚡ TCU Campus Pulse</div>

        <!-- Search Bar -->
        <input type="text" class="border px-4 py-2 rounded-md w-1/3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search posts, events, or users...">

        <!-- Conditional Buttons -->
        <div class="relative flex items-center space-x-4">
            <button id="darkModeToggle" class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400 dark:bg-gray-700 dark:text-white">
                🌙 Dark Mode
            </button>
            <?php if(isset($_SESSION['user'])): ?>
                <button id="openMessages" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Messages</button>
                <button onclick="window.location.href='events.php'" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Events</button>

                <!-- Notification Button -->
                <button class="relative bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                    🔔
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileButton" class="focus:outline-none">
                        <img src="<?php echo $profileImage; ?>" class="w-8 h-8 rounded-full">
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white shadow-md rounded-md overflow-hidden">
                        <a href="/TCUCampusPulse/php/userprofile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="/TCUCampusPulse/php/settings.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                        <a href="#" id="logoutLink" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
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

     <!-- Messages Popup -->
    <div id="messagesPopup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-2/3 h-3/4 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Messages</h2>
            <div class="flex-1 overflow-auto space-y-4">
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/user1.png" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Sarah Chen</strong>
                        <p class="text-gray-600">Are you coming to the study group tonight?</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/user2.png" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Prof. Johnson</strong>
                        <p class="text-gray-600">Please submit your assignment.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/user3.png" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Mike Wilson</strong>
                        <p class="text-gray-600">Thanks for sharing the notes!</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/user4.png" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Campus Moderator</strong>
                        <p class="text-gray-600">Your event has been approved.</p>
                    </div>
                </div>
            </div>
            <button id="closeMessages" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 mt-4">Close</button>
        </div>
    </div>

    <main class="flex p-6">
        <!-- Left Section (Main Content) -->
        <section class="w-3/4 space-y-6">
            <!-- Sorting UI -->
            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <div class="flex border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden">
                    <button id="sortPopular" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-black dark:text-white font-semibold flex items-center">🔥 Popular</button>
                    <button id="sortRecent" class="px-4 py-2 text-gray-500 dark:text-gray-400 flex items-center">⏳ Recent</button>
                </div>
            </div>

            <!-- Post Card -->
            <div class="flex bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <img src="../images/graduation.jpg" class="w-48 h-32 object-cover">
                <div class="p-4">
                    <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Announcements</span>
                    <h2 class="text-lg font-semibold mt-2">Campus Library Extended Hours During Finals Week</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">The campus library will be open 24/7 from Dec 10 to Dec 17...</p>
                    <div class="flex justify-between items-center mt-3">
                        <div class="text-green-600 font-semibold">👍 124 | 👎 2</div>
                        <span class="text-gray-500 dark:text-gray-400 text-sm">💬 18</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right Sidebar -->
        <aside class="w-1/4 ml-6 bg-white dark:bg-gray-800 shadow-lg p-4 rounded-lg">
            <h3 class="font-semibold text-lg dark:text-white">Categories</h3>
            <ul class="mt-2 space-y-2 text-gray-600 dark:text-gray-400">
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
    
    
    <script>
    //MESSAGES
    document.addEventListener("DOMContentLoaded", () => {
    const openMessagesButton = document.getElementById("openMessages");
    const closeMessagesButton = document.getElementById("closeMessages");
    const messagesPopup = document.getElementById("messagesPopup");

    if (openMessagesButton && closeMessagesButton && messagesPopup) {
        openMessagesButton.addEventListener("click", () => {
            messagesPopup.classList.remove("hidden");
        });

        closeMessagesButton.addEventListener("click", () => {
            messagesPopup.classList.add("hidden");
        });

        // Close messages popup when clicking outside of it
        document.addEventListener("click", (e) => {
            if (!messagesPopup.contains(e.target) && e.target !== openMessagesButton) {
                messagesPopup.classList.add("hidden");
            }
        });
    }
});

// Dark Mode Initialization
document.addEventListener("DOMContentLoaded", () => {
            const darkModeToggle = document.getElementById("darkModeToggle");
            const html = document.documentElement; // Target <html> for mode changes

            // Function to update the button text based on the current theme
            const updateButtonText = () => {
                if (html.classList.contains("dark")) {
                    darkModeToggle.textContent = "☀️ Light Mode"; // Set button to Light Mode
                } else {
                    darkModeToggle.textContent = "🌙 Dark Mode"; // Set button to Dark Mode
                }
            };

            // Check localStorage for saved mode
            const savedMode = localStorage.getItem("theme");
            if (savedMode === "dark") {
                html.classList.add("dark");
            } else {
                html.classList.remove("dark");
            }
            updateButtonText(); // Update button text on page load

            // Toggle dark mode
            darkModeToggle.addEventListener("click", () => {
                if (html.classList.contains("dark")) {
                    html.classList.remove("dark");
                    localStorage.setItem("theme", "light");
                } else {
                    html.classList.add("dark");
                    localStorage.setItem("theme", "dark");
                }
                updateButtonText(); // Update button text on toggle
            });

            // Sorting Buttons
            const sortPopularButton = document.getElementById("sortPopular");
            const sortRecentButton = document.getElementById("sortRecent");

            if (sortPopularButton && sortRecentButton) {
                sortPopularButton.addEventListener("click", () => {
                    // Logic for sorting by Popular
                    console.log("Sorting by Popular");
                    // Add your sorting logic here (e.g., fetch sorted data via AJAX)
                });

                sortRecentButton.addEventListener("click", () => {
                    // Logic for sorting by Recent
                    console.log("Sorting by Recent");
                    // Add your sorting logic here (e.g., fetch sorted data via AJAX)
                });
            }
        });
    </script>
    
</body>
</html>
