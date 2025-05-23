<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include koneksi database
include 'koneksierapat.php';

// Ambil ID agenda dari URL
if (isset($_GET['id'])) {
    $agendaId = intval($_GET['id']);

    // Ambil data agenda berdasarkan ID
    $query = "SELECT * FROM agenda WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $agendaId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika agenda ditemukan
    if ($result->num_rows > 0) {
        $agenda = $result->fetch_assoc();
    } else {
        echo "<script>alert('Agenda tidak ditemukan!'); window.location.href='schedule.php';</script>";
        exit;
    }
    $stmt->close();
} else {
    echo "<script>alert('ID agenda tidak ditemukan!'); window.location.href='schedule.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meeting Agenda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #4a79a5;
            color: white;
            font-family: Arial, sans-serif;
        }
        main {
            width: 50%;
            margin: 20px auto;
            background: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <main>
        <h1>Edit Meeting Agenda</h1>

        <!-- Form Edit Agenda -->
        <form id="edit-agenda-form">
            <input type="hidden" id="agenda-id" name="id" value="<?php echo $agenda['id']; ?>">

            <label for="edit-date">Date:</label>
            <input type="date" id="edit-date" name="date" value="<?php echo $agenda['date']; ?>" required><br><br>

            <label for="edit-schedule-name">Schedule Name:</label>
            <input type="text" id="edit-schedule-name" name="schedule_name" value="<?php echo $agenda['schedule_name']; ?>" required><br><br>

            <label for="edit-description">Description:</label>
            <textarea id="edit-description" name="description" required><?php echo $agenda['description']; ?></textarea><br><br>

            <label for="edit-location">Location:</label>
            <input type="text" id="edit-location" name="location" value="<?php echo $agenda['location']; ?>" required><br><br>
            
            <label for="edit-invitation">Invitation Letter:</label>
            <input type="file" id="edit-invitation" name="invitation"><br><br>
            
            <label for="edit-documentation">Documentation:</label>
            <input type="file" id="edit-documentation" name="documentation"><br><br>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-success">Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='schedule.php'">Cancel</button>
            </div>
        </form>
    </main>

    <script>
    document.getElementById("edit-agenda-form").addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        
        fetch("update_schedule.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text()) // Ambil response sebagai teks dulu
        .then(text => {
            console.log("Response:", text); // Debug di console
            return JSON.parse(text); // Coba parse JSON
        })
        .then(result => {
            if (result.success) {
                alert("Agenda berhasil diperbarui!");
                window.location.href = "schedule.php";
            } else {
                alert("Gagal: " + result.message);
                console.error(result.error);
            }
        })
        .catch(error => {
            console.error("Error parsing JSON:", error);
            alert("Terjadi kesalahan saat memproses data.");
        });
    });
    </script>
</body>
</html>