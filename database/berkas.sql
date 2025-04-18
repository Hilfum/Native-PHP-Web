-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Apr 2025 pada 02.29
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
(2, '', NULL, 'muflih', '124173256786329826', 'kadia', 'kadia', 'perdos', 'perdos', 'BPHTB', 'pending', '0000-00-00 00:00:00', 'loket'),
(3, '', NULL, 'yayak', '356946783684715734', 'lippo', 'lipppo', 'lippo', 'lippo', 'Objek Pajak Baru', 'pending', '0000-00-00 00:00:00', 'loket'),
(4, '', NULL, 'bir', '318658658963956498', 'mengapa', 'mengapa', 'mengapa', 'mengapa', 'Mutasi Bagian', 'pending', '0000-00-00 00:00:00', 'loket'),
(5, '', NULL, 'cikong', '813259817429864386', 'rumha', 'rumah', 'rumah', 'rumah', 'Mutasi Nama & Pembetulan', 'pending', '0000-00-00 00:00:00', 'loket');

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
(8, 'bphtb', 'BPHTB');

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
(1, 'petugas_loket', '$2y$10$oVc92/QQOrt3BSQ53Aoytu3xrPIybdN5TlGmGxYnDb2i/AtKshvwS', 1),
(2, 'verlap', '$2y$10$RCjepqLqY.psCmu/irwRO.e.fQqoCB.vI7QfmhZbFE5yUyKq.DkV6', 2),
(3, 'penetapan', '$2y$10$rg8g/jB01jsHJFjz72O4neae6TjE5Plq8fU1YsKWhuNItdb5R/fsy', 3),
(4, 'kabid', '$2y$10$HbF2QYqfC8r38iSQxrnVTOOXxNi12nTeDPSiVlbKSLMwXuvRnK3X6', 4),
(5, 'op_baru', '$2y$10$KlddM1ZAf93dvjMxfGRr9eAFd6DUfzdUFXj7EzNGScivnIFsd9LWq', 5),
(6, 'mutasi1', '$2y$10$GKzvPbYjv0SApL5DlZRvS.SjMX3F4m8tdg.a676Hus0f8jI8FNd/2', 6),
(7, 'mutasi2', '$2y$10$iIDohyCOsVrQNgD6Un2ZbeB4azBcpOZILZGTghgpW3bvKhh5UhY6m', 7),
(8, 'bphtb', '$2y$10$zR.m8/aVm3uqZUa18iXtU.ddwtQKBcMhyKIlRpEOnELZgzZSsJB2K', 8);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
