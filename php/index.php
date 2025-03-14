<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCU Campus Pulse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-purple-600">🔻 TCU Campus Pulse</div>
        
        <!-- Search Bar -->
        <input type="text" class="border px-4 py-2 rounded-md w-1/3" placeholder="Search posts, events, or users...">

        <!-- Conditional Buttons -->
        <div>
            <?php if(isset($_SESSION['user'])): ?>
                <!-- If logged in: Show Profile, Notifications, and Logout -->
                <button onclick="window.location.href='profile.php'" 
                        class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    Profile
                </button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Notifications
                </button>
                <button onclick="window.location.href='php/logout.php'" 
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    Logout
                </button>
            <?php else: ?>
                <!-- If NOT logged in: Show Login & Signup -->
                <button onclick="window.location.href='/TCUCampusPulse/php/login.php'" 
                        class="bg-transparent border border-purple-600 text-purple-600 px-4 py-2 rounded-md hover:bg-purple-100">
                    Log in
                </button>
                <button onclick="window.location.href='/TCUCampusPulse/php/signup.php'" 
                        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                    Sign up
                </button>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex p-6">
        <!-- Left Side (Feed) -->
        <section class="w-3/4 space-y-6">
            <div class="flex bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="images/basketball.jpg" class="w-48 h-32 object-cover">
                <div class="p-4">
                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">Sports</span>
                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Event</span>
                    <h2 class="text-lg font-semibold mt-2">Basketball Team Needs Your Support!</h2>
                    <p class="text-gray-600 text-sm mt-1">Support our basketball team in the championship game...</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-gray-500 text-sm">📅 Dec 18, 7:30 PM - 📍 Campus Stadium</span>
                        <div class="text-green-600 font-semibold">👍 215 | 👎 5</div>
                    </div>
                </div>
            </div>

            <div class="flex bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="images/library.jpg" class="w-48 h-32 object-cover">
                <div class="p-4">
                    <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Announcements</span>
                    <h2 class="text-lg font-semibold mt-2">Campus Library Extended Hours</h2>
                    <p class="text-gray-600 text-sm mt-1">The library will be open 24/7 during finals week...</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-gray-500 text-sm">📅 Dec 10-17 - 📍 Campus Library</span>
                        <div class="text-green-600 font-semibold">👍 124 | 👎 2</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right Side (Categories) -->
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
</body>
</html>
