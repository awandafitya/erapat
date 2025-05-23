<?php
include 'koneksierapat.php';

if (!isset($_GET['schedule_id']) || empty($_GET['schedule_id'])) {
    echo "<tr><td colspan='5' style='text-align:center;'>Select meeting frist</td></tr>";
    exit;
}

$schedule_id = $_GET['schedule_id'];

$sql_hadir = "SELECT p.nip, p.name, p.photo, p.signature FROM presence p WHERE p.schedule_id = ?";
$stmt = $conn->prepare($sql_hadir);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result_hadir = $stmt->get_result();

if ($result_hadir->num_rows > 0) {
    $no = 1;
    while ($row = $result_hadir->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>" . htmlspecialchars($row['nip']) . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>";

        // **Perbaikan Path Foto**
        if (!empty($row['photo'])) {
            echo "<td><img src='uploads/" . htmlspecialchars($row['photo']) . "' width='50'></td>";
        } else {
            echo "<td>No Photo</td>";
        }

        // **Perbaikan Path Signature**
        if (!empty($row['signature'])) {
            if (file_exists("uploads/" . $row['signature'])) {
                // Jika signature adalah file
                echo "<td><img src='uploads/" . htmlspecialchars($row['signature']) . "' width='100'></td>";
            } else {
                // Jika signature disimpan dalam format Base64 di database
                echo "<td><img src='data:image/png;base64," . base64_encode($row['signature']) . "' width='100'></td>";
            }
        } else {
            echo "<td>No Signature</td>";
        }

        echo "</tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center;'>No attendance data</td></tr>";
}

$stmt->close();
$conn->close();
?>