<?php
session_start();
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
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-purple-600">⚡ TCU Campus Pulse</div>
        
        <!-- Search Bar -->
        <input type="text" class="border px-4 py-2 rounded-md w-1/3" placeholder="Search posts, events, or users...">

        <!-- Conditional Buttons -->
        <div>
            <?php if(isset($_SESSION['user'])): ?>
                <button onclick="window.location.href='profile.php'" 
                        class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    Profile
                </button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Messages
                </button>
                <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                    Events
                </button>
                <button class="relative bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">3</span>
                    🔔
                </button>
                <button onclick="window.location.href='php/logout.php'" 
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    Logout
                </button>
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

    <!-- Main Content -->
    <main class="flex p-6">
        <section class="w-3/4 space-y-6">
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
</body>
</html>