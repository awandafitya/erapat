-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Bulan Mei 2025 pada 10.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erapat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `schedule_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `invitation_letter` varchar(255) DEFAULT NULL,
  `documentation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `agenda`
--

INSERT INTO `agenda` (`id`, `schedule_name`, `date`, `description`, `location`, `invitation_letter`, `documentation`) VALUES
(1, 'Rapat Koordinasi', '2025-02-10', 'koordinasi peserta magang baru', 'Lantai 3 Gedung Pemkab', 'uploads/invitation_1741067480.pdf', 'uploads/documentation_1741074931.png'),
(2, 'Rapat Koordinasi', '2025-02-28', 'penting', 'Lantai 3 Gedung Pemkab', 'uploads/invitation_1741157849.pdf', 'uploads/documentation_1741157849.jpg'),
(3, 'Rapat Koordinasi', '2025-02-17', 'abcd', 'Lantai 3 Gedung Pemkab', 'uploads/invitation_1742356563.pdf', 'uploads/documentation_1746763902.jpeg'),
(6, 'Rapat istimewa', '2025-06-10', 'Dihadiri oleh peserta undangan khusus', 'malowopati', 'uploads/conto.pdf', 'uploads/1700558616893.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama_jabatan`) VALUES
(1, 'E-Goverment Devision'),
(2, 'Manager'),
(3, 'Staff');

-- --------------------------------------------------------

--
-- Struktur dari tabel `note`
--

CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `id_agenda` int(11) NOT NULL,
  `pimpinan_rapat` varchar(255) NOT NULL,
  `peserta_rapat` text NOT NULL,
  `notulen` varchar(255) NOT NULL,
  `perihal` text NOT NULL,
  `pembahasan` text NOT NULL,
  `kesimpulan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `note`
--

INSERT INTO `note` (`id`, `id_agenda`, `pimpinan_rapat`, `peserta_rapat`, `notulen`, `perihal`, `pembahasan`, `kesimpulan`, `created_at`, `time`) VALUES
(4, 1, 'Kepala Dinas Komunikasi dan Informatika', '<ol>\r\n<li>Inspektorat</li>\r\n<li>Badan Perencanaan dan Pembangunan Daerah</li>\r\n<li>Bagian Organisasi Setda</li>\r\n<li>Dinas Komunikasi dan Informatika</li>\r\n</ol>', 'Nur Rohmah Hidayatin', '<p>Rapat Persiapan Reviu Arsitektur dan Peta Rencana SPBE</p>', '<ol>\r\n<li>Rapat dibuke oleh Kepala Dinas Komunikasi dan Informatika Kabupaten Bojonegoro.</li>\r\n<li>Menentukan dasar untuk pembuatan Arsitektur dan Peta Rencana SPBE (RPD, RPJMD).</li>\r\n<li>Kebutuhan Reviu dan Input SIA SPBE mutakhir.</li>\r\n</ol>', '<ol>\r\n<li>Ada beberapa hal yang harus dibenahi agar indeks SPBE Kabupaten Bojonegoro belum mendapat predikat memuaskan ( <span style=\"text-decoration: underline;\">&gt;</span>4,2) termasuk melakukan Reviu Arsitektur dan Peta Rencana SPBE.</li>\r\n<li>Peta proses bisnis dan dokumen kebutuhan untuk kebutuhan input SIA SPBE Mutakhir diakomodir Ortala mengingat sebenarnya dokumen kebutuhan SIA SPBE juga merupakan dokumen SAKIP. Jika memungkinkan Pihak ke-3 membuat peta proses bisnis dengan pihak ke-3 input SIA SPBE dapat saling komunikasi.</li>\r\n<li>Mengingat usia RPD hanya sampai 2026 maka dasar pembuatan Arsitektur dan Peta Rencana SPBE dapat menggunakan Visi Misi, RPJMD Teknokratik dan RPJPD 2025-2045.</li>\r\n<li>Hal-hal yang masih kurang/tidak termuat di dokumen lama, melalui reviu ini dapat dilakukan evaluasi sehingga dapat dimuat di dokumen terbaru.</li>\r\n<li>OPD harus dikumpulkan dalam rangka menyiapkan dokumen yang dibutuhkan dalam persiapan SIA SPBE Mutakhir.</li>\r\n<li>Tugas dan Tanggung jawab pihak-pihak terkait</li>\r\n</ol>\r\n<table style=\"border-collapse: collapse; width: 100.072%;\" border=\"1\"><colgroup><col style=\"width: 49.8925%;\"><col style=\"width: 49.8925%;\"></colgroup>\r\n<tbody>\r\n<tr>\r\n<td style=\"text-align: center;\"><strong>PIC</strong></td>\r\n<td style=\"text-align: center;\"><strong>Tugas</strong></td>\r\n</tr>\r\n<tr>\r\n<td>Proses Bisnis Kabupaten</td>\r\n<td>Ortala Setda</td>\r\n</tr>\r\n<tr>\r\n<td>Visi Misi Pemimpin Baru, RPJMD, RPJPD</td>\r\n<td>BAPPEDA</td>\r\n</tr>\r\n<tr>\r\n<td>Reviu</td>\r\n<td>Inspektorat</td>\r\n</tr>\r\n<tr>\r\n<td>Arsitektur dan Peta Rencana SPBE</td>\r\n<td>Dinkominfo</td>\r\n</tr>\r\n<tr>\r\n<td>Proses Bisnis PD dan Dokumen SAKIP untuk input SIA SPBE</td>\r\n<td>OPD</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>', '2025-03-25 03:19:44', '09:30:00'),
(16, 2, 'Kepala Bagian E-Gov', '<ol>\r\n<li>Arimbi</li>\r\n<li>Tya</li>\r\n</ol>', 'eva', '<p>Demo Aplikasi MBKM</p>', '<p>Demo Project MBKM</p>', '<table style=\"border-collapse: collapse; width: 100.016%;\" border=\"1\"><colgroup><col style=\"width: 49.9603%;\"><col style=\"width: 49.9603%;\"></colgroup>\r\n<tbody>\r\n<tr>\r\n<td>weq</td>\r\n<td>trewte</td>\r\n</tr>\r\n<tr>\r\n<td>gfd</td>\r\n<td>tre</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>', '2025-04-28 07:13:53', '09:30:00'),
(18, 3, 'Kepala Bagian E-Gov', '<ol>\r\n<li>staff</li>\r\n<li>manager</li>\r\n</ol>', 'Nur Rohmah Hidayatin', '<ol>\r\n<li>jobdesk mbkm</li>\r\n<li>lokasi mbkm</li>\r\n</ol>', '<p>Komputer adalah perangkat elektronik yang dirancang untuk memproses dan mengolah data secara otomatis berdasarkan instruksi yang diberikan. Komputer dapat menerima input, memprosesnya, menyimpan data, dan menghasilkan output dalam bentuk informasi. Secara sederhana, komputer dapat diartikan sebagai mesin penghitung yang mampu melakukan berbagai perhitungan dan operasi logika.&nbsp;</p>', '<p>Komputer adalah perangkat elektronik yang dirancang untuk memproses dan mengolah data secara otomatis berdasarkan instruksi yang diberikan.</p>', '2025-05-07 03:02:57', '12:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presence`
--

CREATE TABLE `presence` (
  `nip` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `photo` varchar(100) DEFAULT NULL,
  `signature` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `presence`
--

INSERT INTO `presence` (`nip`, `name`, `schedule_id`, `photo`, `signature`) VALUES
('0', 'dummy', 2, '1745827158_123.png', 'signature_1745827158.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profile`
--

CREATE TABLE `profile` (
  `nip` varchar(20) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `jabatan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profile`
--

INSERT INTO `profile` (`nip`, `nama`, `foto`, `username`, `jabatan_id`) VALUES
('0', 'dummy', '123.png', '1', 3),
('12345678', 'Magang MBKM Sistem Informasi UINSA', 'self-employed.png', '0', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `username` varchar(30) NOT NULL,
  `password` varchar(40) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`username`, `password`, `role`) VALUES
('0', '0', 'admin'),
('1', '1', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_agenda` (`id_agenda`);

--
-- Indeks untuk tabel `presence`
--
ALTER TABLE `presence`
  ADD KEY `fk_presence_nip` (`nip`),
  ADD KEY `fk_presence_schedule` (`schedule_id`);

--
-- Indeks untuk tabel `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`nip`),
  ADD KEY `fk_profile_users` (`username`),
  ADD KEY `jabatan_id` (`jabatan_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `presence`
--
ALTER TABLE `presence`
  ADD CONSTRAINT `fk_presence_nip` FOREIGN KEY (`nip`) REFERENCES `profile` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `fk_profile_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
