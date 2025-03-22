<?php
session_start();
require_once 'config.php'; // Ensure database connection

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];
    $deleteQuery = $conn->prepare("DELETE FROM events WHERE id = ?");
    $deleteQuery->bind_param("i", $eventId);
    if ($deleteQuery->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='events.php';</script>";
    } else {
        echo "<script>alert('Failed to delete event.');</script>";
    }
}

// Handle edit action (redirect to edit page)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];
    header("Location: edit_event.php?id=$eventId");
    exit();
}

// Fetch events from the database
$query = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

// Get the current month and year or use query parameters
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Calculate the first and last days of the month
$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDayOfMonth);
$startDayOfWeek = date('w', $firstDayOfMonth);

// Adjust for Sunday as the first day of the week
$startDayOfWeek = $startDayOfWeek == 0 ? 6 : $startDayOfWeek - 1;

// Previous and next month navigation
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - TCU Campus Pulse</title>
    <link rel="icon" type="image/png" href="../images/iconlogo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body class="bg-white text-black">
    <!-- Navbar -->
    <header class="bg-gray-100 shadow-md p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-purple-600">⚡ TCU Campus Pulse</div>
        <a href="index.php" class="text-purple-600 hover:underline">Back to Home</a>
    </header>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-purple-700">Campus Events</h1>
        <p class="text-gray-600">Discover and join upcoming events at TCU</p>
        
        <!-- Search Bar -->
        <input type="text" placeholder="Search events..." class="w-full p-3 border rounded-md mt-4 bg-gray-50 border-gray-300 text-black">
        
        <div class="grid grid-cols-4 gap-4 mt-6">
            <!-- Filters -->
            <aside class="col-span-1 bg-gray-100 p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">Categories</h2>
                <div class="space-y-2 mt-2">
                    <label class="flex items-center text-gray-700"><input type="checkbox" checked> All</label>
                    <label class="flex items-center text-gray-700"><input type="checkbox"> Academic</label>
                    <label class="flex items-center text-gray-700"><input type="checkbox"> Sports</label>
                    <label class="flex items-center text-gray-700"><input type="checkbox"> Clubs</label>
                    <label class="flex items-center text-gray-700"><input type="checkbox"> Arts & Culture</label>
                    <label class="flex items-center text-gray-700"><input type="checkbox"> Campus</label>
                </div>
                
                <h2 class="text-xl font-bold mt-4">Date Range</h2>
                <input type="text" class="w-full border p-2 rounded-md mt-2 bg-gray-50 border-gray-300 text-black" value="Mar 21, 2025 - Apr 20, 2025">
                
                <h2 class="text-xl font-bold mt-4">Distance</h2>
                <input type="range" min="0" max="50" class="w-full mt-2">
                
                <button class="w-full bg-purple-600 text-white p-2 rounded-md mt-4">Apply Filters</button>
            </aside>
            
            <!-- Main Events Section -->
            <section class="col-span-3">
                <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                    <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs">Featured Event</span>
                    <h2 class="text-2xl font-bold mt-2">Homecoming Week 2023</h2>
                    <p class="text-gray-600">Join us for a week of TCU traditions, including the parade, pep rally, and football game against Texas Tech.</p>
                    <p class="text-gray-500">📅 Sat, Oct 21 - Sat, Oct 28 • Various Locations</p>
                    <button class="bg-purple-600 text-white px-4 py-2 rounded-md mt-4">Register Now</button>
                </div>
                
                <!-- Calendar View -->
                <div class="flex justify-between items-center mt-6">
                    <h2 class="text-xl font-bold"><?php echo date('F Y', $firstDayOfMonth); ?></h2>
                    <div>
                        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="px-4 py-2 border rounded-md bg-gray-50 border-gray-300 text-black">⬅</a>
                        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="px-4 py-2 border rounded-md bg-gray-50 border-gray-300 text-black">➡</a>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-2 mt-4 text-center">
                    <!-- Days of the week -->
                    <?php
                    $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    foreach ($daysOfWeek as $day) {
                        echo "<div class='font-bold text-gray-700'>$day</div>";
                    }

                    // Empty cells for days before the first day of the month
                    for ($i = 0; $i < $startDayOfWeek; $i++) {
                        echo "<div class='p-4'></div>";
                    }

                    // Calendar days
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        echo "<div class='p-4 border rounded-md bg-gray-50 text-black'>$day</div>";
                    }
                    ?>
                </div>
                <div class="space-y-4 mt-6">
                    <h1 class="text-2xl font-bold mb-4">Upcoming Events</h1>
                    <?php if ($query->num_rows > 0): ?>
                        <?php while ($event = $query->fetch_assoc()): ?>
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md relative">
                                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($event['event_name']); ?></h2>
                                <p class="text-gray-600"><?php echo htmlspecialchars($event['event_description']); ?></p>
                                <p class="text-gray-500 mt-2">📅 <?php echo date("F j, Y, g:i A", strtotime($event['event_date'])); ?></p>
                                <?php if (!empty($event['event_image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($event['event_image']); ?>" class="mt-2 rounded-lg w-full max-h-60 object-cover">
                                <?php endif; ?>

                                <!-- Three-dots settings menu -->
                                <div class="absolute top-4 right-4">
                                    <button class="relative focus:outline-none group">
                                        <span class="text-gray-500 hover:text-gray-700">⋮</span>
                                        <div class="hidden group-focus:block absolute right-0 mt-2 w-32 bg-white border rounded-lg shadow-lg">
                                            <a href="?action=edit&id=<?php echo $event['id']; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                            <a href="?action=delete&id=<?php echo $event['id']; ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500">No events available.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>