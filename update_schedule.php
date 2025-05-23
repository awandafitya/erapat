<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksierapat.php';

// Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metode request tidak valid!"]);
    exit;
}

// Periksa apakah ID agenda dikirim
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID agenda tidak ditemukan!"]);
    exit;
}

$agendaId = intval($_POST['id']);
$date = $_POST['date'] ?? "";
$schedule_name = $_POST['schedule_name'] ?? "";
$description = $_POST['description'] ?? "";
$location = $_POST['location'] ?? "";

// Pastikan semua field wajib diisi
if (empty($date) || empty($schedule_name) || empty($description) || empty($location)) {
    echo json_encode(["success" => false, "message" => "Semua field wajib diisi!"]);
    exit;
}

// Ambil data agenda lama
$query = "SELECT invitation_letter, documentation FROM agenda WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agendaId);
$stmt->execute();
$result = $stmt->get_result();
$existingAgenda = $result->fetch_assoc();
$stmt->close();

$uploadDir = "uploads/";
$invitation_path = $existingAgenda['invitation_letter'] ?? null;
$documentation_path = $existingAgenda['documentation'] ?? null;
$allowedExt = ['pdf', 'jpg', 'png', 'jpeg'];

// Cek dan proses file Invitation jika diunggah
if (!empty($_FILES['invitation']['name'])) {
    $invitationFile = $_FILES['invitation'];
    $invitationExt = pathinfo($invitationFile['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($invitationExt), $allowedExt)) {
        $invitation_path = $uploadDir . "invitation_" . time() . "." . $invitationExt;
        if (!move_uploaded_file($invitationFile['tmp_name'], $invitation_path)) {
            echo json_encode(["success" => false, "message" => "Gagal mengunggah file Invitation"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Format file Invitation tidak valid"]);
        exit;
    }
}

// Cek dan proses file Documentation jika diunggah
if (!empty($_FILES['documentation']['name'])) {
    $documentationFile = $_FILES['documentation'];
    $documentationExt = pathinfo($documentationFile['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($documentationExt), $allowedExt)) {
        $documentation_path = $uploadDir . "documentation_" . time() . "." . $documentationExt;
        if (!move_uploaded_file($documentationFile['tmp_name'], $documentation_path)) {
            echo json_encode(["success" => false, "message" => "Gagal mengunggah file Documentation"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Format file Documentation tidak valid"]);
        exit;
    }
}

// Update data di database
$query = "UPDATE agenda SET date = ?, schedule_name = ?, description = ?, location = ?, invitation_letter = ?, documentation = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssi", $date, $schedule_name, $description, $location, $invitation_path, $documentation_path, $agendaId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Agenda berhasil diperbarui"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui agenda", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();