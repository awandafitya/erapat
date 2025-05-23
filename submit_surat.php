<?php
include 'koneksierapat.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_agenda = isset($_POST['id_agenda']) ? intval($_POST['id_agenda']) : 0;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $pimpinan_rapat = $_POST['pimpinan_rapat'];
    $peserta_rapat = $_POST['peserta_rapat'];
    $notulen = $_POST['notulen'];
    $perihal = $_POST['perihal'];
    $pembahasan = $_POST['pembahasan'];
    $kesimpulan = $_POST['kesimpulan'];

    // Cek apakah ID Agenda valid di tabel agenda
    $check_query = "SELECT id FROM agenda WHERE id = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("i", $id_agenda);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        die("Error: ID Agenda tidak ditemukan!");
    }
    $stmt_check->close();

    // Hapus data lama jika sudah ada
    $delete_query = "DELETE FROM note WHERE id_agenda = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("i", $id_agenda);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Masukkan data baru
    $sql = "INSERT INTO note (id_agenda, time, pimpinan_rapat, peserta_rapat, notulen, perihal, pembahasan, kesimpulan, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $id_agenda, $time, $pimpinan_rapat, $peserta_rapat, $notulen, $perihal, $pembahasan, $kesimpulan);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='history.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?><?php
include 'koneksierapat.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_agenda = isset($_POST['id_agenda']) ? intval($_POST['id_agenda']) : 0;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $pimpinan_rapat = $_POST['pimpinan_rapat'];
    $peserta_rapat = $_POST['peserta_rapat'];
    $notulen = $_POST['notulen'];
    $perihal = $_POST['perihal'];
    $pembahasan = $_POST['pembahasan'];
    $kesimpulan = $_POST['kesimpulan'];

    // Cek apakah ID Agenda valid di tabel agenda
    $check_query = "SELECT id FROM agenda WHERE id = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("i", $id_agenda);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        die("Error: ID Agenda tidak ditemukan!");
    }
    $stmt_check->close();

    // Hapus data lama jika sudah ada
    $delete_query = "DELETE FROM note WHERE id_agenda = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("i", $id_agenda);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Masukkan data baru
    $sql = "INSERT INTO note (id_agenda, time, pimpinan_rapat, peserta_rapat, notulen, perihal, pembahasan, kesimpulan, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $id_agenda, $time, $pimpinan_rapat, $peserta_rapat, $notulen, $perihal, $pembahasan, $kesimpulan);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='schedule.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>