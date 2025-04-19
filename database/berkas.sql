-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Apr 2025 pada 06.01
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
-- Database: `berkas`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `penerima` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `nop` varchar(255) NOT NULL,
  `kelurahan_objek_pajak` varchar(255) NOT NULL,
  `kecamatan_objek_pajak` varchar(255) NOT NULL,
  `alamat_wajib_pajak` text NOT NULL,
  `alamat_objek_pajak` text NOT NULL,
  `tipe_berkas` varchar(255) NOT NULL,
  `status` enum('pending','selesai') NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `current_handler` varchar(50) NOT NULL DEFAULT 'loket'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen`
--

INSERT INTO `dokumen` (`id`, `nama_dokumen`, `penerima`, `nama`, `nop`, `kelurahan_objek_pajak`, `kecamatan_objek_pajak`, `alamat_wajib_pajak`, `alamat_objek_pajak`, `tipe_berkas`, `status`, `tanggal_masuk`, `current_handler`) VALUES
(36, '', NULL, '1', '111111111111111111', '1', '1', '1', '1', 'BPHTB, Objek Pajak Baru, Mutasi Bagian, Mutasi Nama & Pembetulan', 'selesai', '0001-01-01 00:00:00', 'op_baru'),
(37, '', NULL, '2', '222222222222222222', '2', '2', '2', '2', 'BPHTB, Mutasi Nama & Pembetulan', 'selesai', '0002-02-02 00:00:00', 'mutasi2'),
(38, '', NULL, '3', '333333333333333333', '3', '3', '3', '3', 'Objek Pajak Baru, Mutasi Bagian', 'selesai', '0003-03-03 00:00:00', 'op_baru');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'loket', 'Petugas Loket'),
(2, 'verlap', 'Verifikasi Lapangan'),
(3, 'penetapan', 'Penetapan'),
(4, 'kabid', 'Kepala Bidang'),
(5, 'op_baru', 'Objek Pajak Baru'),
(6, 'mutasi1', 'Mutasi 1'),
(7, 'mutasi2', 'Mutasi 2'),
(8, 'bphtb', 'BPHTB'),
(9, 'monitoring', 'Pemantauan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`) VALUES
(1, 'petugas_loket', '$2y$10$ymnWFLSBeIfBNcewYyuCDO/OYvhD/tJcahV8j4vlFQrx1pY2I6N9m', 1),
(2, 'verlap', '$2y$10$9EN.cyY7Vy7kk19huufg7.dOC51t65k9GJlkPBSMb4/XjKXKHrKtG', 2),
(3, 'penetapan', '$2y$10$w6aZHbjqvJLkRZMehtDQlengWamQLFxXBVyfVWllVP0s6ZA6qC7FK', 3),
(4, 'kabid', '$2y$10$b9dMJe/hIV79s51xfN95ke628WjcQgaHGoRTQsZxJos.gkBDuQLD.', 4),
(5, 'op_baru', '$2y$10$azaz9pg4gsvCMwesz1BItuUrdjuVOemLwZClNQN9z3mcmJaz5Za2W', 5),
(6, 'mutasi1', '$2y$10$QdiMsbnrLSsncIt3ZrK4iuv1qGPBYbliMXQ20M3jbqwTo2ovw9rRq', 6),
(7, 'mutasi2', '$2y$10$SyNwNGhw8756gbLQBMJQaeqRjzVQsLpLh3qCOluN2afO1i2Z6Sh0u', 7),
(8, 'bphtb', '$2y$10$yWXfZuwKkJHfotBUi38LHeQcmxwtkxBcQ6osoGHPdNwW7trI.TPzW', 8),
(9, 'monitoring', '$2y$10$PuHsP3/kc/j7..49Khtguea1JYI6XsOd665DkpLcn6P1IBj4nz4tW', 9);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penerima` (`penerima`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`penerima`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
