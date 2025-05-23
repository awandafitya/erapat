<?php
include 'koneksierapat.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];

    $sql = "INSERT INTO editor_content (content, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $content);

    if ($stmt->execute()) {
        echo "Data berhasil disimpan!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
