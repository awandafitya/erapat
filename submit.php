<?php
include 'koneksierapat.php';

// Konfigurasi upload
$allowed_types = ['pdf', 'docx', 'jpg', 'png'];
$max_size = 2 * 1024 * 1024; // 2MB
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

function uploadFile($file, $upload_dir, $allowed_types, $max_size) {
    if ($file["error"] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file["error"]);
        return false;
    }

    $filename = basename($file["name"]);
    $target_file = $upload_dir . $filename;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($file["size"] > $max_size) {
        error_log("File size exceeds limit: $filename");
        return false;
    }

    if (!in_array($file_type, $allowed_types)) {
        error_log("Invalid file type: $filename");
        return false;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        error_log("Failed to move uploaded file: $filename");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_name = $_POST["schedule_name"];
    $date = $_POST["date"];
    $description = $_POST["description"];
    $location = $_POST["location"];

    // Upload files
    $invitation_letter = !empty($_FILES["invitation_letter"]["name"]) ? 
        uploadFile($_FILES["invitation_letter"], $upload_dir, $allowed_types, $max_size) : "";
    $documentation = !empty($_FILES["documentation"]["name"]) ? 
        uploadFile($_FILES["documentation"], $upload_dir, $allowed_types, $max_size) : "";

    if ($invitation_letter === false || $documentation === false) {
        echo "<script>alert('File upload failed! Check server logs.');</script>";
        exit;
    }

    // Insert data ke database
    $sql = "INSERT INTO agenda (schedule_name, date, description, location, invitation_letter, documentation) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $schedule_name, $date, $description, $location, $invitation_letter, $documentation);
    if ($stmt->execute()) {
        echo "<script>alert('Schedule successfully added!'); window.location.href='schedule.php';</script>";
    } else {
        error_log("Database error: " . $stmt->error);
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>