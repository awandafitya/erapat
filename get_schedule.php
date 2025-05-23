<?php
// Menginclude koneksi database
include('koneksierapat.php');

// Mendapatkan ID dari query string
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Ambil data berdasarkan ID
    $sql = "SELECT * FROM agenda WHERE id = $id"; // Ganti `agenda` dengan nama tabel yang sesuai
} else {
    // Ambil semua data jika ID tidak ditemukan
    $sql = "SELECT * FROM agenda";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $agenda = [];
    while ($row = $result->fetch_assoc()) {
        $agenda[] = $row;
    }
    // Mengembalikan data dalam format JSON
    echo json_encode($agenda);
} else {
    // Mengembalikan error jika data tidak ditemukan
    echo json_encode(["error" => "Data tidak ditemukan."]);
}

// Menutup koneksi
$conn->close();
?>