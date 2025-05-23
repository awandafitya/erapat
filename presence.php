<?php
session_start();
include 'koneksierapat.php';

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

// Ambil foto profil pengguna
$sql = "SELECT profile.foto FROM profile WHERE profile.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$fotoPath = !empty($profile['foto']) ? "uploads/" . $profile['foto'] : "assets/icons/default-profile.png";

// Ambil daftar rapat
$sql_rapat = "SELECT id AS schedule_id, schedule_name AS nama_rapat FROM agenda";
$result_rapat = $conn->query($sql_rapat);

$selected_schedule = "";
$attendance_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $schedule_id = $_POST['schedule_id'];
    $signature = $_POST['signature'];
    $selected_schedule = $schedule_id;

    $target_dir = "uploads/";

    // **Upload foto peserta**
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = time() . "_" . basename($_FILES['foto']['name']);
        $foto_path = $target_dir . $foto_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
            $foto_db = $foto_name;
        } else {
            echo "Gagal mengunggah foto!";
            exit;
        }
    } else {
        echo "Foto harus diunggah!";
        exit;
    }

    // **Simpan signature sebagai file PNG**
    if (!empty($signature)) {
        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);
        $signatureData = base64_decode($signature);

        $signature_name = "signature_" . time() . ".png";
        $signature_path = $target_dir . $signature_name;

        if (file_put_contents($signature_path, $signatureData)) {
            $signature_db = $signature_name;
        } else {
            echo "Gagal menyimpan tanda tangan!";
            exit;
        }
    } else {
        echo "Tanda tangan harus diisi!";
        exit;
    }

    // **Simpan data ke database**
    $insert_sql = "INSERT INTO presence (nip, name, schedule_id, photo, signature) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssiss", $nip, $nama, $schedule_id, $foto_db, $signature_db);

    if ($stmt->execute()) {
        $attendance_success = true;
    } else {
        echo "Gagal menyimpan absensi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presence - E-Rapat</title>
    <link rel="stylesheet" href="style.css"> 
    <script>
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        function clearSignature() {
            var canvas = document.getElementById('signature-pad');
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('signature').value = "";
        }

        function saveSignature() {
            var canvas = document.getElementById('signature-pad');
            document.getElementById('signature').value = canvas.toDataURL();
        }

        window.onload = function() {
            var canvas = document.getElementById('signature-pad');
            var ctx = canvas.getContext('2d');
            var isDrawing = false;

            canvas.addEventListener('mousedown', function(e) {
                isDrawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });

            canvas.addEventListener('mousemove', function(e) {
                if (isDrawing) {
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', function() {
                isDrawing = false;
                saveSignature();
            });
        }
        function toggleDropdown(id) {
            let dropdown = document.getElementById(id);
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        document.addEventListener("click", function(event) {
            let dropdown = document.getElementById("profile-dropdown");
            let profileIcon = document.querySelector(".profile-icon");

            if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });

        function logout() {
            alert("Anda telah keluar!");
            window.location.href = "login.php";
        }
    </script>
</head>
<body>
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

    <main class="presence-container">
        <h2>Form Absensi</h2>

        <?php if ($attendance_success): ?>
            <p style="color: green; font-weight: bold;">Attendance was successful</p>
        <?php else: ?>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="schedule_id">Select Meeting:</label>
                <select id="schedule_id" name="schedule_id" required>
                    <option value="">-- Select Meeting --</option>
                    <?php while ($row = $result_rapat->fetch_assoc()): ?>
                        <option value="<?= $row['schedule_id']; ?>" <?= ($selected_schedule == $row['schedule_id']) ? 'selected' : ''; ?>>
                            <?= $row['nama_rapat']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="nip">NIP*:</label>
                <input type="text" id="nip" name="nip" required>

                <label for="nama">Name*:</label>
                <input type="text" id="nama" name="nama" required>

                <label for="foto">Upload Image*:</label>
                <input type="file" name="foto" required>

                <label for="signature">Signature*:</label>
                <div class="signature-container" style="display: flex; align-items: center; gap: 10px;">
                    <canvas id="signature-pad" width="400" height="150" style="border: 1px solid black;"></canvas>
                    <button type="button" onclick="clearSignature()">Delete Signature</button>
                </div>
                <input type="hidden" name="signature" id="signature">
                
                <button type="submit">Submit Absensi</button>
            </form>
        <?php endif; ?>

        <button onclick="window.location.href='presence_list.php'" style="margin-top: 10px;">See Attendance List</button>
    </main>
</body>
</html>