<?php
// Menginclude koneksi database
include('koneksierapat.php');

// Mendapatkan ID dari query string
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Jika ID ada, ambil data agenda berdasarkan ID
if ($id) {
    $sql = "SELECT * FROM agendas WHERE id = $id"; // Ganti `agendas` dengan nama tabel yang sesuai
} else {
    $sql = "SELECT * FROM agendas"; // Ambil semua agenda
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mengambil data dan mengubahnya menjadi array
    $agendas = [];
    while ($row = $result->fetch_assoc()) {
        $agendas[] = $row;
    }

    // Mengirimkan data dalam format JSON
    echo json_encode($agendas);
} else {
    echo json_encode(["error" => "Data tidak ditemukan."]);
}

// Menutup koneksi
$conn->close();
?>