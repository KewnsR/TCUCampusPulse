<?php
session_start();
include '../php/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $student_number = $_SESSION['user']['student_number'];
    $date = date("Ymd_His"); // Format: YYYYMMDD_HHMMSS

    $target_dir = "../uploads/";
    $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
    $file_name = $student_number . "_" . $date . "." . $file_extension; 
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $user_id = $_SESSION['user']['id'];
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();

        echo json_encode(["success" => true, "image_url" => $target_file]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
