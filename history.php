<?php
session_start();
include('koneksierapat.php');

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
$username = $_SESSION['username'];
$sql = "SELECT profile.foto FROM profile WHERE profile.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$fotoPath = !empty($profile['foto']) ? "uploads/" . $profile['foto'] : "assets/icons/default-profile.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Rapat</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <style>
        #calendar {
            max-width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .fc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fc-button {
            background-color: #749BC2 !important;
            border: none !important;
            color: white !important;
            padding: 8px 12px;
            border-radius: 4px;
            text-shadow: none !important;
            box-shadow: none !important;
        }

        .fc-event {
            background-color: #749BC2 !important;
            border: none !important;
            color: white !important;
            padding: 5px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #749BC2;
            color: white;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
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

    <aside class="sidebar">
        <div class="logo-container">
            <a href="dashboard.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                <img src="assets/icons/logo.png" alt="Logo" class="logo">
                <h2>E - Rapat</h2>
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="schedule.php"><img src="assets/icons/ikonschedule.png" class="icon"> JADWAL</a></li>
                <li><a href="presence.php"><img src="assets/icons/ikonpresence.png" class="icon"> ABSENSI</a></li>
                <li><a href="history.php"><img src="assets/icons/ikonhistory.png" class="icon"> RIWAYAT</a></li>
                <?php if ($can_edit): ?>
                    <li><a href="keloladata.php"><img src="assets/icons/ikonkeloladata.png" class="icon"> KELOLA DATA</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <main>
        <h1>Riwayat Rapat</h1>
        <div id="calendar"></div>
        
        <table id="history-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Rapat</th>
                    <th>Deskripsi</th>
                    <th>Tempat</th>
                </tr>
            </thead>
            <tbody></tbody>
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

        $(document).ready(function() {
            const today = moment().format("YYYY-MM-DD");
            const historyTableBody = $("#history-table tbody");
            historyTableBody.html('');

            $.getJSON('get_schedule.php', function(agenda) {
                const pastEvents = agenda.filter(event => moment(event.date).isBefore(today));

                if (pastEvents.length > 0) {
                    $('#calendar').fullCalendar({
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay,listWeek'
                        },
                        defaultView: 'month',
                        editable: false,
                        eventLimit: true,
                        events: pastEvents.map(event => ({
                            title: event.schedule_name,
                            start: event.date,
                            description: event.description,
                            color: "#749BC2" 
                        })),
                        eventClick: function(event) {
                            alert("Agenda: " + event.title + "\nDeskripsi: " + event.description);
                        }
                    });

                    pastEvents.forEach(event => {
                        let row = `<tr>
                            <td>${event.date}</td>
                            <td>${event.schedule_name}</td>
                            <td>${event.description}</td>
                            <td>${event.location}</td>
                        </tr>`;
                        historyTableBody.append(row);
                    });
                }
            }).fail(function() {
                console.error("Error loading history data");
            });
        });
    </script>
</body>
</html>