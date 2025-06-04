<?php
session_start();
include('koneksierapat.php');

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$sql = "SELECT profile.foto FROM profile WHERE profile.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$fotoPath = !empty($profile['foto']) ? "uploads/" . $profile['foto'] : "assets/icons/default-profile.png";
// Ambil data untuk edit (PINDAH ke atas)
$editData = null;
if (isset($_GET['edit'])) {
    $nipEdit = $_GET['edit'];
    $q = "SELECT profile.nip, profile.nama, profile.foto, profile.username, profile.jabatan_id, users.password, users.role
          FROM profile 
          JOIN users ON profile.username = users.username
          WHERE profile.nip = '$nipEdit'";
    $res = $conn->query($q);
    if ($res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}
// Proses tambah/edit data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $jabatan_id = $_POST['jabatan'];
    $foto = '';
    $editData = null;
    if (isset($_GET['edit'])) {
        $nipEdit = $_GET['edit'];
        $q = "SELECT profile.nip, profile.nama, profile.foto, profile.username, profile.jabatan_id, users.password, users.role
            FROM profile 
            JOIN users ON profile.username = users.username
            WHERE profile.nip = '$nipEdit'";
        $res = $conn->query($q);
        if ($res->num_rows > 0) {
            $editData = $res->fetch_assoc();
        }
    }

    // Upload foto
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        $foto = basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    }

    // Cek apakah nip sudah ada
    $cek = $conn->query("SELECT * FROM profile WHERE nip = '$nip'");
    if ($cek->num_rows > 0) {
        // Edit: update dulu di users
        $conn->query("UPDATE users SET password='$password', role='$role' WHERE username='$username'");
        
        // Baru update di profile
        $conn->query("UPDATE profile SET nama='$nama', jabatan_id='$jabatan_id'" .
            (!empty($foto) ? ", foto='$foto'" : "") . " WHERE nip='$nip'");
    } else {
        // Tambah baru: masukkan ke users DULU agar FK tidak error
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");

        // Baru insert ke profile
        $conn->query("INSERT INTO profile (nip, nama, jabatan_id, foto, username) 
                      VALUES ('$nip', '$nama', '$jabatan_id', '$foto', '$username')");
    }

    header("Location: keloladata.php");
    exit();
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $nip = $_GET['hapus'];
    // Ambil username berdasarkan NIP
    $getUser = $conn->query("SELECT username FROM profile WHERE nip='$nip'");
    if ($getUser->num_rows > 0) {
        $user = $getUser->fetch_assoc();
        $username = $user['username'];

        // Hapus dari profile dan users
        $conn->query("DELETE FROM profile WHERE nip='$nip'");
        $conn->query("DELETE FROM users WHERE username='$username'");
    }
    header("Location: keloladata.php");
    exit();
}

// Ambil data pegawai
$sql = "SELECT profile.nip, profile.nama, profile.foto, jabatan.nama_jabatan, users.username, users.password, users.role 
        FROM profile 
        JOIN jabatan ON profile.jabatan_id = jabatan.id
        JOIN users ON profile.username = users.username";
$result = $conn->query($sql);

// Ambil daftar jabatan
$jabatan = $conn->query("SELECT * FROM jabatan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data - E-Rapat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        img.profile-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        h2 {
            text-align: center;
        }
        form {
            width: 90%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 6px 0;
        }
        button {
            padding: 10px 20px;
            background-color: steelblue;
            border: none;
            color: white;
            width: 100%;
            font-weight: bold;
        }
    </style>
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
                    <button onclick="logout()">Keluar</button>
                </div>          
            </div>
        </div>
    </header>
<?php include 'layout_admin.php'; ?>
<main class="content">
    <h2>Kelola Data Pegawai</h2>

    <form method="POST" enctype="multipart/form-data">
    <h4><?= $editData ? 'Edit Pegawai' : 'Tambah Pegawai' ?></h4>
    <input type="text" name="nip" placeholder="NIP" required value="<?= $editData['nip'] ?? '' ?>" <?= $editData ? 'readonly' : '' ?>>
    <input type="text" name="nama" placeholder="Nama" required value="<?= $editData['nama'] ?? '' ?>">
    <input type="text" name="username" placeholder="Username" required value="<?= $editData['username'] ?? '' ?>">
    <input type="text" name="password" placeholder="Password" <?= $editData ? '' : 'required' ?>>
    <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin" <?= (isset($editData['role']) && $editData['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
        <option value="user" <?= (isset($editData['role']) && $editData['role'] === 'user') ? 'selected' : '' ?>>User</option>
    </select>
    <select name="jabatan" required>
        <option value="">-- Pilih Jabatan --</option>
        <?php
        $jabatan->data_seek(0); // reset pointer
        while ($j = $jabatan->fetch_assoc()) : ?>
            <option value="<?= $j['id'] ?>" <?= (isset($editData['jabatan_id']) && $editData['jabatan_id'] == $j['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($j['nama_jabatan']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <input type="file" name="foto">
    <?php if (!empty($editData['foto'])): ?>
        <img src="uploads/<?= $editData['foto'] ?>" width="60" style="margin-top: 10px;">
    <?php endif; ?>
    <button type="submit">Simpan</button>
</form>
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Username</th>
                <th>Password</th>
                <th>Role</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : 
                $fotoPath = !empty($row['foto']) ? "uploads/" . $row['foto'] : "assets/icons/default-profile.png";
            ?>
            <tr>
                <td><img src="<?= htmlspecialchars($fotoPath) ?>" class="profile-photo"></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['nip']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['password']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
                <td>
                    <a href="?edit=<?= $row['nip'] ?>">Edit</a> |
                    <a href="?hapus=<?= $row['nip'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>
<script>
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
</body>
</html>
