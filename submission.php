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
    
    <aside class="sidebar">
        <div class="logo-container">
            <a href="dashboard.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                <img src="assets/icons/logo.png" alt="Logo" class="logo">
                <h2>E - Rapat</h2>
            </a>
        </div>
    
        </div>
        <nav>
            <ul>
                <li><a href="schedule.php"><img src="assets/icons/ikonschedule.png" class="icon"> SCHEDULE</a></li>
                <li><a href="submission.php"><img src="assets/icons/ikonsubmission.png" class="icon"> SUBMISSION</a></li>
                <li><a href="presence.php"><img src="assets/icons/ikonpresence.png" class="icon"> PRESENCE</a></li>
                <li><a href="history.php"><img src="assets/icons/ikonhistory.png" class="icon"> HISTORY</a></li>
                <?php if ($can_edit): ?>
                    <li><a href="keloladata.php"><img src="assets/icons/ikonkeloladata.png" class="icon"> MANAGE DATA</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    
    <main>
        <h1>New Schedule</h1>
        <?php if ($can_edit): ?>
        <form action="submit.php" method="POST" enctype="multipart/form-data">
            <label>Schedule Name*</label>
            <input type="text" name="schedule_name" required>
        
            <label>Date*</label>
            <input type="date" name="date" required>
        
            <label>Description*</label>
            <textarea name="description" required></textarea>
        
            <label>Location*</label>
            <input type="text" name="location" required>
        
            <label>Invitation Letter</label>
            <input type="file" name="invitation_letter">
        
            <label>Documentation</label>
            <input type="file" name="documentation">
        
            <div class="d-flex gap-2 mt-3">
                <button type="submit">Create</button>
                <button type="button" onclick="window.location.href='schedule.php'">Cancel</button>
            </div>
        </form>
        <?php else: ?>
            <!-- Untuk user biasa -->
            <p style="color: gray; font-style: italic;">Silakan login sebagai admin untuk menambahkan jadwal rapat.</p>
        <?php endif; ?>        
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