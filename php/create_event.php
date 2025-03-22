<?php
session_start();
require_once 'config.php'; // Ensure database connection

// Check if the connection is still active
if (!$conn->ping()) {
    $conn->close();
    require_once 'config.php'; // Reconnect to the database
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    $event_description = trim($_POST['event_description']);
    $event_date = $_POST['event_date'];
    $event_image = NULL;

    // Handle image upload
    if (!empty($_FILES["event_image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["event_image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $file_size = $_FILES["event_image"]["size"];

        // Validate file type and size
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        $max_file_size = 2 * 1024 * 1024; // 2 MB
        if (in_array($imageFileType, $allowed_types) && $file_size <= $max_file_size) {
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $event_image = $image_name;
            }
        } else {
            echo "<script>alert('Invalid file type or file size exceeds 2 MB.');</script>";
            exit();
        }
    }

    // Insert event into database
    $query = $conn->prepare("INSERT INTO events (event_name, event_description, event_date, event_image) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssss", $event_name, $event_description, $event_date, $event_image);

    if ($query->execute()) {
        header("Location: events.php"); // Redirect to events page
        exit();
    } else {
        echo "<script>alert('Error creating event. Please try again.');</script>";
    }
}
?>
