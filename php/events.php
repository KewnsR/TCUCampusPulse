<?php
session_start();
require_once 'config.php'; // Ensure database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-purple-700 dark:text-purple-400">Campus Events</h1>
        <p class="text-gray-600 dark:text-gray-300">Discover and join upcoming events at TCU</p>
        
        <!-- Search Bar -->
        <input type="text" placeholder="Search events..." class="w-full p-3 border rounded-md mt-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        
        <div class="grid grid-cols-4 gap-4 mt-6">
            <!-- Filters -->
            <aside class="col-span-1 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold dark:text-white">Categories</h2>
                <div class="space-y-2 mt-2">
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox" checked> All</label>
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox"> Academic</label>
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox"> Sports</label>
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox"> Clubs</label>
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox"> Arts & Culture</label>
                    <label class="flex items-center dark:text-gray-300"><input type="checkbox"> Campus</label>
                </div>
                
                <h2 class="text-xl font-bold mt-4 dark:text-white">Date Range</h2>
                <input type="text" class="w-full border p-2 rounded-md mt-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="Mar 21, 2025 - Apr 20, 2025">
                
                <h2 class="text-xl font-bold mt-4 dark:text-white">Distance</h2>
                <input type="range" min="0" max="50" class="w-full mt-2">
                
                <button class="w-full bg-purple-600 text-white p-2 rounded-md mt-4">Apply Filters</button>
            </aside>
            
            <!-- Main Events Section -->
            <section class="col-span-3">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs">Featured Event</span>
                    <h2 class="text-2xl font-bold mt-2 dark:text-white">Homecoming Week 2023</h2>
                    <p class="text-gray-600 dark:text-gray-300">Join us for a week of TCU traditions, including the parade, pep rally, and football game against Texas Tech.</p>
                    <p class="text-gray-500 dark:text-gray-400">📅 Sat, Oct 21 - Sat, Oct 28 • Various Locations</p>
                    <button class="bg-purple-600 text-white px-4 py-2 rounded-md mt-4">Register Now</button>
                </div>
                
                <!-- Calendar View -->
                <div class="flex justify-between items-center mt-6">
                    <h2 class="text-xl font-bold dark:text-white">March 2025</h2>
                    <div>
                        <button class="px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">⬅</button>
                        <button class="px-4 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">➡</button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-2 mt-4">
                    <?php
                    $days = range(1, 31);
                    foreach ($days as $day) {
                        echo "<div class='p-4 border rounded-md text-center bg-gray-50 dark:bg-gray-700 dark:text-white'>$day</div>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>