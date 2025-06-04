<?php
session_start();
include 'koneksierapat.php';

$error = ''; // Variabel untuk menyimpan pesan kesalahan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user dari database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if ($password === $user['password']) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");// Redirect ke halaman dashboard
            exit();
        }else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Tampilkan form login di bawah -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Rapat</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <img src="assets/icons/logo.png" alt="Logo" class="logo">
        <h2>E-Rapat</h2>
        <p>Silakan masukkan username dan password Anda untuk login.</p>

        <!-- Form login -->
        <form action="login.php" method="POST">
            <input type="text" class="input-field" name="username" placeholder="Username" required>
            <input type="password" class="input-field" name="password" placeholder="Password" required>
            <button type="submit" class="login-btn" name="submit">Masuk</button>
        </form>

        <!-- Tampilkan pesan error jika ada -->
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>