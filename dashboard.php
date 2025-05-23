<?php
session_start();
include('koneksierapat.php');
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role']; // Ambil role dari session
// Ambil data profil user
$sql = "SELECT profile.nip, profile.nama, profile.foto, jabatan.nama_jabatan AS division 
        FROM profile 
        JOIN jabatan ON profile.jabatan_id = jabatan.id
        WHERE profile.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
// Path foto
$fotoPath = !empty($profile['foto']) ? "uploads/" . $profile['foto'] : "assets/icons/default-profile.png";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-Rapat</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
    <style>
        .profile-container {
            display: flex;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
            margin-left: 0px;
        }
        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
    </style>
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

    <!-- SIDEBAR -->
    <?php
    if ($role == 'admin') {
        include 'layout_admin.php';
    } elseif ($role == 'user') {
        include 'layout_user.php';
    } else {
        header("Location: unauthorized.php");
        exit();
    }
    ?>

    <!-- MAIN CONTENT -->
    <main class="content">
        <h1>Welcome to E-Rapat</h1>
        <p>Manage your meetings efficiently with our dashboard.</p>

        <!-- Profile -->
        <div class="profile-container">
            <img src="<?= htmlspecialchars($fotoPath) ?>" alt="Foto Profil" class="profile-photo">
            <div class="profile-info">
                <h2><?= htmlspecialchars($profile['nama'] ?? "Nama Tidak Ditemukan") ?></h2>
                <p><strong>NIP:</strong> <?= htmlspecialchars($profile['nip'] ?? "-") ?></p>
                <p><strong>Jabatan:</strong> <?= htmlspecialchars($profile['division'] ?? "-") ?></p>
            </div>
        </div>
    </main>

    <script>
    function toggleDropdown(id) {
        let dropdown = document.getElementById(id);
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    window.onclick = function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                dropdown.style.display = "none";
            });
        }
    };

    function logout() {
        alert("Anda telah keluar!");
        window.location.href = "login.php";
    }
    </script>

</body>
</html>
