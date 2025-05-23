<?php
session_start();
include 'koneksierapat.php';

if (!isset($_SESSION['username'])) {
    die("Anda harus login terlebih dahulu! <a href='login.php'>Login</a>");
}

$username = $_SESSION['username'];

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil daftar rapat
$sql_rapat = "SELECT id AS schedule_id, schedule_name AS nama_rapat FROM agenda";
$result_rapat = $conn->query($sql_rapat);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance list - E-Rapat</title>
    <link rel="stylesheet" href="style.css"> 

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .presence-table, .presence-table * {
                visibility: visible;
            }
            .presence-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <script>
        function getPresenceList() {
            let scheduleId = document.getElementById("rapat").value;

            fetch("get_presence.php?schedule_id=" + scheduleId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("presence-table-body").innerHTML = data;
                });
        }

        function printPage() {
            window.print();
        }

        function closePage() {
            window.location.href = "presence.php"; 
        }
    </script>
</head>
<body>
    <main class="presence-container">
        <h2>Attendance list</h2>

        <label for="rapat" class="no-print">Select Meeting:</label>
        <select id="rapat" name="rapat" class="no-print" onchange="getPresenceList()">
            <option value="">-- Select Meeting --</option>
            <?php while ($row = $result_rapat->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['schedule_id']); ?>"><?= htmlspecialchars($row['nama_rapat']); ?></option>
            <?php endwhile; ?>
        </select>

        <table class="presence-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody id="presence-table-body">
                <tr><td colspan='5' style='text-align:center;'>Please select a meeting first</td></tr>
            </tbody>
        </table>

        <div class="no-print" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
            <button onclick="printPage()" style="background: none; border: none; cursor: pointer;">
                <img src="assets/icons/ikonprint.png" alt="Print" width="24">
            </button>
            <button onclick="closePage()" style="
                background: red; 
                color: white; 
                border: none; 
                padding: 5px 12px; 
                font-size: 14px; 
                border-radius: 5px; 
                cursor: pointer;
                font-weight: bold;">
                Close
            </button>
        </div>
    </main>

    <?php 
    $conn->close(); 
    ?>
</body>
</html>