<?php
include 'koneksierapat.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $queryAgenda = "SELECT date, location, documentation FROM agenda WHERE id = ?";
    $stmtAgenda = $conn->prepare($queryAgenda);
    $stmtAgenda->bind_param("i", $id);
    $stmtAgenda->execute();
    $resultAgenda = $stmtAgenda->get_result();
    $agenda = $resultAgenda->fetch_assoc();

    $queryNote = "SELECT pimpinan_rapat, peserta_rapat, notulen, perihal, pembahasan, kesimpulan, time FROM note WHERE id_agenda = ?";
    $stmtNote = $conn->prepare($queryNote);
    $stmtNote->bind_param("i", $id);
    $stmtNote->execute();
    $resultNote = $stmtNote->get_result();
    $note = $resultNote->fetch_assoc();

    $stmtAgenda->close();
    $stmtNote->close();
    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notulen Rapat</title>
        <style>
            @media print {
                button {
                    display: none; /* Sembunyikan semua tombol saat mode print */
                }
            }
            @page {
                size: A4 portrait;
                margin-top: 1cm;
                margin-bottom: 1cm;
                margin-left: 2cm;
                margin-right: 2cm;
            }
            body {
                font-family: "Times New Roman", serif;
                font-size: 12pt;
                line-height: 1.5;
                text-align: justify;
                margin: 0;
                padding: 40px;
            }
            .kop {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }
            .kop img {
                width: 112px;
                height: auto;
                margin-right: 20px;
            }
            .kop .text {
                flex-grow: 1;
                text-align: center;
            }
            .kop h1, .kop h2, .kop h3,.kop h4, .kop h5,.kop p {
                margin: 0;
            }
            .kop h1 {
                font-size: 16pt;
                font-weight: bold;
                text-decoration: underline;
            }
            .kop h2 {
                font-size: 16pt;
                font-weight: bold;
            }
            .kop h3 {
                font-size: 14pt;
                font-weight: bold;
            }
            .kop h4 {
                font-size: 12pt;
                font-weight: bold;
            }
            .kop h5 {
                font-size: 10pt;
                font-weight: normal;
            }
            .kop p {
                font-size: 12pt;
            }
            .judul {
                text-align: center;
                font-size: 12pt;
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 20px;
            }
            .part {
                text-align: left;
                font-size: 12pt;
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 10px;
            }
            .content {
                display: flex;
                flex-direction: column;
                gap: 5px; /* Jarak antar elemen */
            }

            .content .row {
                display: flex;
                align-items: baseline;
            }

            .content .row span {
                font-weight: bold;
                min-width: 150px; /* Lebar minimum agar label sejajar */
            }

            .content p {
                margin: 5px 0;
            }
            .content span {
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px; /* Tambahkan jarak agar tabel tidak terlalu ke atas */
            }
            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            ul, ol {
                margin-top: 0;
                margin-bottom: 0;
                padding-left: 15px; /* Supaya tetap rapi */
            }
            li {
                margin-bottom: 3px; /* Mengurangi jarak antar item */
            }
            .dokum {
                width: 200px;
                height: auto;
                /* margin-right: 20px; */
                text-align: center;
            }
            .signature {
                margin-top: 40px;
                text-align: right;
            }
        </style>
    </head>
    <body>
        <div class="kop">
            <img src="assets/icons/logo_surat.png" alt="Logo">
            <div class="text">
                <h3>PEMERINTAH KABUPATEN BOJONEGORO</h3>
                <h2>DINAS KOMUNIKASI DAN INFORMATIKA</h2>
                <p>Jl. P. Mas Tumapel No. 01 Telp. (0353) 881826</p>
                <h5>Website: www.dinkominfo.bojonegorokab.go.id</h5>
                <h5>E-mail: dinkominfo@bojonegorokab.go.id</h5>
                <h1>BOJONEGORO 62111</h1>
            </div>
        </div>
        <!-- <div class="garis"></div> -->

        <div class="judul">NOTULEN RAPAT</div>

        <div class="content">
        <div class="content">
        <div class="row"><span>Hari/Tanggal</span>: <?= htmlspecialchars($agenda['date'] ?? 'Data belum diinputkan'); ?></div>
        <div class="row"><span>Pukul</span>: <?= htmlspecialchars($note['time'] ?? 'Data belum diinputkan'); ?> WIB s/d selesai</div>
        <div class="row"><span>Tempat</span>: <?= htmlspecialchars($agenda['location'] ?? 'Data belum diinputkan'); ?></div>
        <div class="row"><span>Pimpinan Rapat</span>: <?= htmlspecialchars_decode($note['pimpinan_rapat'] ?? 'Data belum diinputkan'); ?></div>
        <div class="row"><span>Peserta Rapat</span>: <?= htmlspecialchars_decode($note['peserta_rapat'] ?? 'Data belum diinputkan'); ?></div>
        <div class="row"><span>Notulen</span>: <?= htmlspecialchars($note['notulen'] ?? 'Data belum diinputkan'); ?></div>
        <div class="row"><span>Perihal</span>: <?= htmlspecialchars_decode($note['perihal'] ?? 'Data belum diinputkan'); ?></div>
    </div>

        </div>

        <div class="part">PEMBAHASAN RAPAT</div>
        <div class="content">
            <p><?= htmlspecialchars_decode($note['pembahasan'] ?? 'Data belum diinputkan'); ?></p>
        </div>

        <div class="part">KESIMPULAN</div>
        <div class="content">
            <?= html_entity_decode($note['kesimpulan'] ?? 'Data belum diinputkan'); ?>
        </div>

        <div class="dokum">
            <?php
            if (!empty($agenda['documentation'])) {
                $file_ext = pathinfo($agenda['documentation'], PATHINFO_EXTENSION);
                if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])) {
                    // Jika file adalah gambar, tampilkan
                    echo '<img src="'.htmlspecialchars($agenda['documentation']).'" alt="Dokumentasi" width="200">';
                } else {
                    // Jika file adalah PDF atau format lain, tampilkan tautan
                    echo '<p><a href="'.htmlspecialchars($agenda['documentation']).'" target="_blank">Lihat Dokumentasi</a></p>';
                }
            } else {
                echo '<p>Dokumentasi tidak tersedia</p>';
            }
            ?>
        </div>

        <div class="signature">
            <p>Bojonegoro, <?= date("d F Y"); ?></p>
            <p><b>Notulen,</b></p>
            <br><br><br>
            <p> <?= htmlspecialchars($note['notulen'] ?? 'Data belum diinputkan'); ?> </p>
        </div>
        <form action="schedule.php" method="post">
        <button onclick="closePage()" style="
                background: grey; 
                color: white; 
                border: none; 
                padding: 5px 12px; 
                font-size: 14px; 
                border-radius: 5px; 
                cursor: pointer;
                font-weight: bold;">
                Kembali
            </button>
        </form>
        <script>
            window.print();
            setTimeout(() => window.close(), 1000);
        </script>

    </body>
    </html>
    <?php
}else {
    echo "ID agenda tidak ditemukan!";
}
?>