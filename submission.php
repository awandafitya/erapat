<?php
session_start();
include('koneksierapat.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Cek jika user hanya boleh melihat saja
$readonly = ($role === 'user'); // akan true jika user biasa

// Untuk contoh: jika admin
$can_edit = ($role === 'admin');

$username = $_SESSION['username'];
$sql = "SELECT profile.foto FROM profile WHERE profile.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$fotoPath = !empty($profile['foto']) ? "uploads/" . $profile['foto'] : "assets/icons/default-profile.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Agenda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
       <!-- HEADER -->
       <header class="header">
        <div class="header-icons" style="position: absolute; top: 10px; right: 20px; display: flex; gap: 15px;">
            <div class="dropdown">
                <span class="profile-icon" onclick="toggleDropdown('profile-dropdown')">
                    <img src="<?= htmlspecialchars($fotoPath) ?>" alt="Foto Profil" width="40" height="40" 
                         style="border-radius: 50%; object-fit: cover;" 
                         onerror="this.src='assets/icons/default-profile.png'">
                </span>
                <div id="profile-dropdown" class="dropdown-content">
                    <button onclick="logout()">Log Out</button>
                </div>           
            </div>
        </div>
    </header>
    
    <main>
        <h1>New Schedule</h1>
        
        <form action="submit.php" method="POST" enctype="multipart/form-data">
            <label>Nama Rapat*</label>
            <input type="text" name="schedule_name" required>
        
            <label>Tanggal*</label>
            <input type="date" name="date" required>
        
            <label>Deskripsi*</label>
            <textarea name="description" required></textarea>
        
            <label>Tempat*</label>
            <input type="text" name="location" required>
        
            <label>Undangan</label>
            <input type="file" name="invitation_letter">
        
            <label>Dokumentasi</label>
            <input type="file" name="documentation">
        
            <div class="d-flex gap-2 mt-3">
                <button type="submit">Buat</button>
                <button type="button" onclick="window.location.href='schedule.php'">Batal</button>
            </div>
        </form>
               
    </main>
    <script>
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }
        function logout() {
            alert("Anda telah keluar!");
            window.location.href = "login.php";
        }
        document.addEventListener("click", function(event) {
            let dropdown = document.getElementById("profile-dropdown");
            let profileIcon = document.querySelector(".profile-icon");

            if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    </script>
</body>
</html>