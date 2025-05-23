<?php
include('koneksierapat.php');

// Ambil NIP dari parameter GET (misalnya: ?nip=09020622022)
$nip = isset($_GET['nip']) ? $_GET['nip'] : null;


// Query untuk mengambil data profil berdasarkan NIP
$sql = "SELECT profile.nip, profile.nama, profile.jabatan AS division 
        FROM profile 
        WHERE profile.nip = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nip); 
$stmt->execute();


$conn->close();
?>
