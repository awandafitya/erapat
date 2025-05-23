<?php
session_start();
include 'koneksierapat.php';

// Pastikan parameter yang dibutuhkan ada
if (!isset($_GET['nip']) || !isset($_GET['schedule_id'])) {
    die("Parameter tidak valid!");
}

$nip = $_GET['nip'];
$schedule_id = $_GET['schedule_id'];

// Ambil data pegawai berdasarkan NIP
$sql = "SELECT nama FROM Profile WHERE nip = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Data tidak ditemukan!");
}

$row = $result->fetch_assoc();
$nama = $row['nama'];

// Proses form jika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $foto = $_FILES['foto']['name'];
    $signature = $_POST['signature']; // Tanda tangan dalam base64

    // Direktori upload
    $target_dir = "uploads/";
    $foto_path = $target_dir . basename($foto);

    // Pindahkan file yang diupload ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
        
        // Simpan ke database
        $insert_sql = "INSERT INTO presence (nip, name, schedule_id, photo, signature) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssiss", $nip, $nama, $schedule_id, $foto_path, $signature);

        if ($stmt->execute()) {
            echo "<script>alert('Absensi berhasil!'); window.location='presence.php';</script>";
        } else {
            echo "Gagal menyimpan absensi: " . $conn->error;
        }
    } else {
        echo "Gagal mengunggah file!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - E-Rapat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .absensi-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .readonly-label {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .readonly-input {
            font-size: 18px;
            font-weight: bold;
            background-color: #f3f3f3;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            text-align: center;
        }

        .file-input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .signature-container {
            border: 2px solid #000;
            border-radius: 5px;
            width: 100%;
            height: 150px;
            margin-top: 10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        canvas {
            border: none;
            width: 100%;
            height: 100%;
        }

        .button-group {
            margin-top: 10px;
        }

        .submit-btn, .clear-btn {
            margin-top: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .clear-btn {
            background-color: #ccc;
        }

        .clear-btn:hover {
            background-color: #999;
        }
    </style>
</head>
<body>

    <div class="absensi-container">
        <div class="absensi-title"><h2>Form Absensi</h2></div>

        <label class="readonly-label">NIP:</label>
        <input type="text" value="<?= htmlspecialchars($nip) ?>" readonly class="readonly-input">

        <label class="readonly-label">Nama:</label>
        <input type="text" value="<?= htmlspecialchars($nama) ?>" readonly class="readonly-input">

        <form action="" method="post" enctype="multipart/form-data" onsubmit="saveSignature()">
            <label class="readonly-label">Unggah Foto:</label>
            <input type="file" name="foto" required class="file-input">

            <label class="readonly-label">Tanda Tangan:</label>
            <div class="signature-container">
                <canvas id="signature-pad"></canvas>
            </div>

            <input type="hidden" name="signature" id="signature">

            <div class="button-group">
                <button type="button" class="clear-btn" onclick="clearCanvas()">Hapus Tanda Tangan</button>
                <button type="submit" class="submit-btn">Submit Absensi</button>
            </div>
        </form>
    </div>

    <script>
        let canvas = document.getElementById("signature-pad");
        let ctx = canvas.getContext("2d");

        function resizeCanvas() {
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = 150;
            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
        resizeCanvas();

        let drawing = false;

        canvas.addEventListener("mousedown", (e) => {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        });

        canvas.addEventListener("mousemove", (e) => {
            if (drawing) {
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
            }
        });

        canvas.addEventListener("mouseup", () => drawing = false);
        canvas.addEventListener("mouseleave", () => drawing = false);

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        function saveSignature() {
            document.getElementById("signature").value = canvas.toDataURL();
        }
    </script>

</body>
</html>