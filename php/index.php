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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitPost'])) {
    if (isset($_SESSION['user'])) {
        $student_number = $_SESSION['user']['student_number'];
        $content = trim($_POST['content']);
        $category = $_POST['category'];
        $image_url = NULL;

        // Handle image upload
        if (!empty($_FILES["postImage"]["name"])) {
            $target_dir = "../uploads/";
            $image_name = basename($_FILES["postImage"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES["postImage"]["tmp_name"], $target_file)) {
                    $image_url = $image_name;
                }
            }
        }

        // Insert into database
        $query = $conn->prepare("INSERT INTO student_posts (student_number, content, category, image_url) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $student_number, $content, $category, $image_url);

        if ($query->execute()) {
            header("Location: index.php"); // Refresh page after posting
            exit();
        } else {
            echo "<script>alert('Error posting. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('You must be logged in to post.');</script>";
    }
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
                <button id="openMessages" onclick="showMessagesPopup()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Messages</button>
                <button onclick="window.location.href='events.php'" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Events</button>

                <!-- Notification Button -->
                <button class="relative bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">
                    <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
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

    <div id="messagesPopup" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-2/3 h-3/4 flex flex-col">
            <h2 class="text-lg font-semibold mb-4">Messages</h2>
            <div class="flex-1 overflow-auto space-y-4">
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/defaultuserprofile.jpg" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Sarah Chen</strong>
                        <p class="text-gray-600">Are you coming to the study group tonight?</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/defaultuserprofile.jpg" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Prof. Johnson</strong>
                        <p class="text-gray-600">Please submit your assignment.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/defaultuserprofile.jpg" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Mike Wilson</strong>
                        <p class="text-gray-600">Thanks for sharing the notes!</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3 p-3 bg-gray-100 rounded-lg">
                    <img src="../images/defaultuserprofile.jpg" class="w-10 h-10 rounded-full">
                    <div>
                        <strong>Campus Moderator</strong>
                        <p class="text-gray-600">Your event has been approved.</p>
                    </div>
                </div>
            </div>
            <button id="closeMessages" onclick="hideMessagesPopup()" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 mt-4">Close</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="p-6 max-w-6xl mx-auto flex">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-white p-4 rounded-lg shadow-md mr-6 -ml-6">
            <h2 class="text-lg font-semibold mb-4">Categories</h2>
            <ul class="space-y-2">
                <li><a href="events.php" class="block text-gray-700 hover:text-purple-600">📅 Events</a></li>
                <li><a href="announcements.php" class="block text-gray-700 hover:text-purple-600">📢 Announcements</a></li>
                <li><a href="academic.php" class="block text-gray-700 hover:text-purple-600">📘 Academic</a></li>
                <li><a href="sports.php" class="block text-gray-700 hover:text-purple-600">🏅 Sports</a></li>
                <li><a href="others.php" class="block text-gray-700 hover:text-purple-600">🔗 Others</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="flex">
                <div class="flex-1">
                    <!-- Create Post Modal -->
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <div class="flex items-center space-x-3">
                            <img src="<?php echo $profileImage; ?>" class="w-10 h-10 rounded-full">
                            <button onclick="showPostModal()" class="w-full text-left bg-gray-100 px-4 py-2 rounded-full text-gray-500">What's on your mind?</button>
                        </div>
                    </div>
                    
                    <!-- Post Modal -->
                    <div id="postModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                            <h2 class="text-lg font-semibold mb-3">Create Post</h2>
                            <form action="index.php" method="POST" enctype="multipart/form-data">
                                <textarea name="content" class="w-full border p-2 rounded-md" placeholder="What's on your mind?" required></textarea>
                                <select name="category" class="w-full mt-2 border p-2 rounded-md" required>
                                    <option value="General">General</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Events">Events</option>
                                    <option value="Announcements">Announcements</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Others">Others</option>
                                </select>
                                <input type="file" name="postImage" class="mt-2 w-full border p-2 rounded-md">
                                <div class="flex justify-end mt-4">
                                    <button type="button" onclick="hidePostModal()" class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
                                    <button type="submit" name="submitPost" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 ml-2">Post</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-4">
                    <?php
                        $query = $conn->query("SELECT sp.*, u.first_name, u.mid_name, u.last_name, u.profile_image, u.role FROM student_posts sp JOIN users u ON sp.student_number = u.student_number ORDER BY sp.created_at DESC");
                        if ($query->num_rows > 0):
                            while ($row = $query->fetch_assoc()):
                                $profileImagePath = !empty($row['profile_image']) ? '../uploads/' . $row['profile_image'] : '../images/defaultuserprofile.jpg';
                    ?>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo $profileImagePath; ?>" class="w-10 h-10 rounded-full">
                    <div>
                    <p class="font-semibold"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
                    <?php
                        $roleClass = '';
                        if ($row['role'] === 'Student') {
                            $roleClass = 'bg-green-100 text-green-600';
                        } elseif ($row['role'] === 'Moderator') {
                            $roleClass = 'bg-red-100 text-red-600';
                        } elseif ($row['role'] === 'Professor') {
                            $roleClass = 'bg-blue-100 text-blue-600';
                        }
                    ?>
                    <p class="text-sm px-2 py-1 rounded-md <?php echo $roleClass; ?>"><?php echo htmlspecialchars($row['role']); ?></p>
                    <?php
                    date_default_timezone_set('Asia/Manila');
                    $createdAt = new DateTime($row['created_at'], new DateTimeZone('Asia/Manila'));
                    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    $interval = $now->diff($createdAt);

                    if ($interval->y > 0) {
                        $timeDisplay = $createdAt->format('M d, Y');
                    } elseif ($interval->m > 0 || $interval->d > 6) {
                        $timeDisplay = $createdAt->format('M d');
                    } elseif ($interval->d > 0) {
                        $timeDisplay = $interval->d . 'd';
                    } elseif ($interval->h > 0) {
                        $timeDisplay = $interval->h . 'h';
                    } elseif ($interval->i > 0) {
                        $timeDisplay = $interval->i . 'm';
                    } else {
                        $timeDisplay = 'Just now';
                    }

                    $hoverDisplay = $createdAt->format('l, F j, Y \a\t g:i A');
                    ?>
                    <p class="text-gray-500" title="<?php echo $hoverDisplay; ?>"><?php echo $timeDisplay; ?></p>
                </div>
            </div>
            <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                 <p class="mt-2"><?php echo htmlspecialchars($row['content']); ?></p>
            <?php if (!empty($row['image_url'])): ?>
                <img src="../uploads/<?php echo $row['image_url']; ?>" class="mt-2 rounded-lg w-full max-h-60 object-cover">
            <?php endif; ?>
            
                    </div>
                    <?php
                    endwhile;else:
                        ?>
                            <p class="text-center text-gray-500">No Posts or Events Created Yet</p>
                        <?php
                        endif;
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../js/index.js"></script>
</body>
</html>
