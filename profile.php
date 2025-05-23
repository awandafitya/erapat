<?php
session_start();
include('koneksierapat.php');

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

$username = $_SESSION['username'];

// Query untuk mengambil data profil berdasarkan username
$sql = "SELECT profile.nip, profile.nama, profile.jabatan AS division, profile.foto 
        FROM profile 
        WHERE profile.username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    $profile = ["error" => "Data tidak ditemukan."];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .profile-card h2 {
            margin: 10px 0;
            font-size: 20px;
            color: #333;
        }
        .profile-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card">
        <img src="uploads/<?= $profile['foto'] ?: 'default.jpg' ?>" alt="Foto Profil">
        <h2><?= $profile['nama'] ?? "Nama Tidak Ditemukan" ?></h2>
        <p><strong>NIP:</strong> <?= $profile['nip'] ?? "-" ?></p>
        <p><strong>Jabatan:</strong> <?= $profile['division'] ?? "-" ?></p>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

</body>
</html>
