<?php
include "database.php";
header('Content-Type: application/json');

$sql = "SELECT * FROM agenda ORDER BY date ASC";
$result = $conn->query($sql);

$agenda = [];

while ($row = $result->fetch_assoc()) {
    $agenda[] = $row;
}

echo json_encode($agenda);
$conn->close();
?>