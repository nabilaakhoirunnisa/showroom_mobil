-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 07:41 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `showroom_mobil`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(1, 18, 'Mahesa Ibrahim', 'JL ARIA SANTIKA GG SAMAUN RT 3 RW 3 SUMUR PACING KARAWACI KOTA TANGERANG BANTEN', '089627912778', '1779804695_profil_18.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `administrasi_kendaraan`
--

CREATE TABLE `administrasi_kendaraan` (
  `id_administrasi` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `no_stnk` varchar(100) DEFAULT NULL,
  `tanggal_stnk` date DEFAULT NULL,
  `status_stnk` enum('aktif','hampir_habis','mati') DEFAULT 'aktif',
  `no_bpkb` varchar(100) DEFAULT NULL,
  `status_bpkb` enum('ada','proses','belum_ada') DEFAULT 'belum_ada',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(1, 20, 'Arjuna Meiureksa', 'Jl wisma mas bumi indah blok f no 22', '087527275275', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kwitansi`
--

CREATE TABLE `kwitansi` (
  `id_kwitansi` int(11) NOT NULL,
  `id_pembayaran` int(11) DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `periode` varchar(30) DEFAULT NULL,
  `total_penjualan` decimal(15,2) DEFAULT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `id_penjual` int(11) DEFAULT NULL,
  `nama_mobil` varchar(100) DEFAULT NULL,
  `harga` decimal(15,0) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `status` enum('tersedia','dipesan','terjual') DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `tahun` year(4) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `id_penjual`, `nama_mobil`, `harga`, `stok`, `status`, `deskripsi`, `tahun`, `foto`) VALUES
(16, 3, 'Honda Civic FL5', '425000000', 1, 'tersedia', 'Honda Civic 2021 – Sedan premium dengan desain sporty dan performa bertenaga yang siap pakai.\r\n\r\nSpesifikasi & Kondisi:\r\n\r\nTransmisi: Otomatis (CVT) halus dan responsif\r\n\r\nWarna: Hitam Metalik\r\n\r\nOdometer: 35.000 KM (Low KM, pemakaian apik)\r\n\r\nSurat: Lengkap (STNK, BPKB, Pajak hidup)\r\n\r\nKeunggulan:\r\n\r\nMesin turbo yang bertenaga namun tetap irit bahan bakar.\r\n\r\nDesain eksterior agresif dengan interior mewah berbahan premium.\r\n\r\nFitur canggih termasuk layar sentuh modern dan sistem keselamatan lengkap.\r\n\r\nKondisi istimewa, mesin kering, kaki-kaki senyap, serta bebas banjir dan tabrakan.', 2021, 'da5374831dd107eb09f9313668823d43.jpg'),
(20, 3, 'Alphard HEV', '2000000000', 0, 'terjual', 'Toyota Alphard HEV bekas kondisi sangat terawat dan siap pakai. Mengusung mesin hybrid yang irit bahan bakar namun tetap bertenaga, cocok untuk penggunaan harian maupun perjalanan jauh. Interior mewah dan kabin super lega memberikan kenyamanan maksimal untuk keluarga maupun kebutuhan bisnis.\r\n\r\nSpesifikasi singkat:\r\n\r\nMesin Hybrid Electric Vehicle (HEV)\r\nTransmisi otomatis\r\nInterior captain seat premium\r\nSunroof / moonroof\r\nHead unit modern & kamera parkir\r\nAC digital double blower\r\nDoor elektrik & smart key\r\nKilometer rendah dan terawat\r\n\r\nKondisi:\r\n\r\nMesin halus dan normal\r\nPajak hidup\r\nInterior bersih dan rapi\r\nKaki-kaki nyaman\r\nBody mulus\r\nSiap pakai tanpa PR', 2020, '1779376910_8169e7bb1301c57fe025a495139ea1cf.jpg'),
(21, 3, 'BYD Seal', '250000000', 0, 'terjual', 'BYD kondisi bekas terawat dan siap pakai. Hadir dengan desain modern dan teknologi canggih yang memberikan kenyamanan serta performa responsif saat berkendara. Cocok digunakan untuk harian maupun perjalanan jauh dengan konsumsi energi yang efisien.\r\n\r\nSpesifikasi singkat:\r\n\r\nMotor listrik bertenaga & responsif\r\nTransmisi otomatis\r\nInterior premium modern\r\nHead unit touchscreen\r\nKamera parkir & sensor\r\nAC digital\r\nSmart key & start stop button\r\nFitur keselamatan lengkap\r\n\r\nKondisi:\r\n\r\nBody mulus dan terawat\r\nInterior bersih\r\nMesin / baterai normal\r\nSuspensi nyaman\r\nPajak hidup\r\nSiap pakai tanpa PR', 2020, 'e9db5e1619a9da8f2c76380d788202ee.jpg'),
(22, 4, 'Pajero Sport', '500000000', 1, 'terjual', 'mobil ini bagus mantap sangat cocok untuk perjalanan jauh', 2020, '4995153399e03c2cab9a722888eeb559.jpg'),
(23, 5, 'Civic', '100000000', 1, 'tersedia', 'mantap hebat ngebut', 2015, 'da5374831dd107eb09f9313668823d43.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL,
  `metode_bayar` enum('tunai','transfer') DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','verifikasi','diterima') DEFAULT NULL,
  `bukti_pembayaran` varchar(255) NOT NULL,
  `jenis_pembayaran` enum('booking','dp','pelunasan') DEFAULT 'booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `metode_bayar`, `jumlah`, `status`, `bukti_pembayaran`, `jenis_pembayaran`) VALUES
(41, 38, 'tunai', '500000.00', 'diterima', '-', 'booking'),
(42, 38, '', '127000000.00', 'diterima', '-', 'dp'),
(43, 38, '', '297500000.00', 'diterima', '-', 'pelunasan'),
(44, 39, 'transfer', '500000.00', 'diterima', 'bukti_penawaran_1778953816_1852.jpg', 'booking'),
(45, 39, '', '127000000.00', 'diterima', '-', 'dp'),
(46, 40, 'transfer', '500000.00', 'diterima', 'bukti_booking_1779804940_9907.jpg', 'booking'),
(47, 40, '', '499500000.00', 'diterima', '-', 'pelunasan'),
(48, 41, '', '500000.00', 'diterima', '-', 'booking'),
(49, 41, '', '74500000.00', 'diterima', '-', 'dp'),
(50, 41, '', '175000000.00', 'diterima', '-', 'pelunasan'),
(51, 42, 'tunai', '500000.00', 'diterima', '-', 'booking'),
(52, 42, '', '149500000.00', 'diterima', '-', 'dp'),
(53, 42, '', '350000000.00', 'diterima', '-', 'pelunasan'),
(54, 43, 'tunai', '500000.00', 'diterima', '-', 'booking'),
(55, 43, '', '599500000.00', 'diterima', '-', 'dp'),
(56, 43, '', '1400000000.00', 'diterima', '-', 'pelunasan');

-- --------------------------------------------------------

--
-- Table structure for table `pembeli`
--

CREATE TABLE `pembeli` (
  `id_pembeli` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembeli`
--

INSERT INTO `pembeli` (`id_pembeli`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(8, 19, 'Mahesa Ibrahim', 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', '085693419679', '1778812854_profil_19.jpg'),
(10, 24, 'Nabila Khoirunnisa', 'taman kota permai 2', '081210104860', '1779805834_profil_24.jpg'),
(11, 25, 'fauzi', 'Pasar Kemis', '0834234234234', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
  `id_pembeli` int(11) DEFAULT NULL,
  `id_mobil` int(11) DEFAULT NULL,
  `tanggal_pesan` datetime DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `status` enum('booking','dp','lunas','batal') DEFAULT 'booking',
  `deadline_dp` date DEFAULT NULL,
  `foto_ktp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_pembeli`, `id_mobil`, `tanggal_pesan`, `total_harga`, `status`, `deadline_dp`, `foto_ktp`) VALUES
(38, 8, 16, '2026-05-21 17:15:36', '425000000.00', 'lunas', '2026-05-28', 'ktp_1779359328_3065.jpg'),
(39, 8, 16, '2026-05-21 21:33:54', '425000000.00', 'dp', '2026-05-28', 'ktp_1779379798_3365.jpg'),
(40, 8, 22, '2026-05-26 21:15:40', '500000000.00', 'lunas', '2026-06-02', 'ktp_1779805398_3883.jpg'),
(41, 10, 21, '2026-05-26 21:31:13', '250000000.00', 'lunas', '2026-06-02', 'ktp_1779805943_9752.jpg'),
(42, 11, 22, '2026-06-02 11:15:36', '500000000.00', 'lunas', '2026-06-09', 'ktp_1780373867_8375.jpg'),
(43, 8, 20, '2026-06-02 12:32:48', '2000000000.00', 'lunas', '2026-06-09', 'ktp_1780378413_7812.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `penawaran`
--

CREATE TABLE `penawaran` (
  `id_penawaran` int(11) NOT NULL,
  `id_penjual` int(11) DEFAULT NULL,
  `id_mobil` int(11) DEFAULT NULL,
  `harga_tawar` decimal(15,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `metode_pembayaran` enum('tunai','transfer') DEFAULT NULL,
  `tanggal_keputusan` datetime DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penawaran`
--

INSERT INTO `penawaran` (`id_penawaran`, `id_penjual`, `id_mobil`, `harga_tawar`, `tanggal`, `status`, `metode_pembayaran`, `tanggal_keputusan`, `catatan_admin`, `catatan`, `bukti_pembayaran`) VALUES
(4, 3, 16, '425000000.00', '2026-05-21', 'diterima', 'tunai', '2026-05-21 17:14:37', '', 'Honda Civic 2021 – Sedan premium dengan desain sporty dan performa bertenaga yang siap pakai.\r\n\r\nSpesifikasi & Kondisi:\r\n\r\nTransmisi: Otomatis (CVT) halus dan responsif\r\n\r\nWarna: Hitam Metalik\r\n\r\nOdometer: 35.000 KM (Low KM, pemakaian apik)\r\n\r\nSurat: Lengkap (STNK, BPKB, Pajak hidup)\r\n\r\nKeunggulan:\r\n\r\nMesin turbo yang bertenaga namun tetap irit bahan bakar.\r\n\r\nDesain eksterior agresif dengan interior mewah berbahan premium.\r\n\r\nFitur canggih termasuk layar sentuh modern dan sistem keselamatan lengkap.\r\n\r\nKondisi istimewa, mesin kering, kaki-kaki senyap, serta bebas banjir dan tabrakan.', NULL),
(6, 5, 23, '100000000.00', '2026-06-02', 'diterima', 'tunai', '2026-06-02 12:14:59', '.', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` int(11) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `alamat_kirim` text DEFAULT NULL,
  `status` enum('diproses','dikirim','selesai','terkirim') DEFAULT 'diproses',
  `bukti_pengiriman` varchar(255) DEFAULT NULL,
  `tanggal_terkirim` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_pemesanan`, `id_kurir`, `alamat_kirim`, `status`, `bukti_pengiriman`, `tanggal_terkirim`) VALUES
(6, 38, 1, 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', 'terkirim', '1778322141_Screenshot_8-5-2026_17547_www.bing.com.jpeg', '2026-05-26 12:20:50'),
(7, 42, 1, 'Pasar Kemis', 'terkirim', 'bukti_pengiriman_1780374155_8329.jpg', '2026-06-02 11:22:35'),
(8, 43, 1, 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', 'terkirim', 'bukti_pengiriman_1780378721_3543.jpg', '2026-06-02 12:38:41');

-- --------------------------------------------------------

--
-- Table structure for table `penjual`
--

CREATE TABLE `penjual` (
  `id_penjual` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penjual`
--

INSERT INTO `penjual` (`id_penjual`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(3, 21, 'Titania Najwa', 'Perumahan sukatani permai blok E no 9', '08582323232323', '1778953216_profil_21.jpg'),
(4, 23, 'Ibrahim Mahesa', 'Jl aria santika gg samaun rt 3 rw 3 sumur pacing kota tangerang banten', '085693410670', NULL),
(5, 26, 'maylita', 'kampung sondol', '081234567856', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id_suratjalan` int(11) NOT NULL,
  `id_pengiriman` int(11) DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `surat_jalan`
--

INSERT INTO `surat_jalan` (`id_suratjalan`, `id_pengiriman`, `tanggal_cetak`) VALUES
(4, 7, '2026-06-02 11:23:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','pembeli','penjual','kurir') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(18, 'admin123', '$2y$10$qSbxyfHQ3UpWqp9pXyYwvOGP6btKaP.WDkIdI2gVwF1RsfjrEzm0i', 'admin'),
(19, 'pembeli123', '$2y$10$kmQLqo5Eb17Pad/xN6fPwO3dfs03s5yhBQty2qEQlaiGc.R70.zUm', 'pembeli'),
(20, 'kurir123', '$2y$10$KdjjhVoqFfoW2CBAAM7zbOxFwVizbjEJjUhX/QuRXtfF01XSPY6de', 'kurir'),
(21, 'penjual123', '$2y$10$rinTb5y67a6DXPXHfVd7lOiKXWjz0jHJfUOTocMxC3ZGSqwyo/gyO', 'penjual'),
(23, 'penjual456', '$2y$10$n0SRFgoss765it57ASihhu4KSVFxdxFJ8tIVgMcYX6lWylzNtrBeu', 'penjual'),
(24, 'pembeli', '$2y$10$tV8FCmwI9zvj5VpJrDn33uuaGqdi5n0CL4MmZ0rECjeWNFMVW2fMa', 'pembeli'),
(25, 'fauzi123', '$2y$10$m9P3zHOuxuq1mVl4EG5F..r70mryFAffQjqSY3E4UMkLwQ5nNCoqS', 'pembeli'),
(26, 'lita', '$2y$10$lrenxNwkT388v/JtDO9N5.H1.NdpjIzVnSKQKVwjGxbkgSegEW.Te', 'penjual');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `administrasi_kendaraan`
--
ALTER TABLE `administrasi_kendaraan`
  ADD PRIMARY KEY (`id_administrasi`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD PRIMARY KEY (`id_kwitansi`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD KEY `id_penjual` (`id_penjual`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pembeli`
--
ALTER TABLE `pembeli`
  ADD PRIMARY KEY (`id_pembeli`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_pembeli` (`id_pembeli`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `penawaran`
--
ALTER TABLE `penawaran`
  ADD PRIMARY KEY (`id_penawaran`),
  ADD KEY `id_penjual` (`id_penjual`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pemesanan` (`id_pemesanan`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indexes for table `penjual`
--
ALTER TABLE `penjual`
  ADD PRIMARY KEY (`id_penjual`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id_suratjalan`),
  ADD KEY `id_pengiriman` (`id_pengiriman`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `administrasi_kendaraan`
--
ALTER TABLE `administrasi_kendaraan`
  MODIFY `id_administrasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kwitansi`
--
ALTER TABLE `kwitansi`
  MODIFY `id_kwitansi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `pembeli`
--
ALTER TABLE `pembeli`
  MODIFY `id_pembeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `penawaran`
--
ALTER TABLE `penawaran`
  MODIFY `id_penawaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `penjual`
--
ALTER TABLE `penjual`
  MODIFY `id_penjual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id_suratjalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `kurir`
--
ALTER TABLE `kurir`
  ADD CONSTRAINT `kurir_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD CONSTRAINT `kwitansi_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`);

--
-- Constraints for table `mobil`
--
ALTER TABLE `mobil`
  ADD CONSTRAINT `mobil_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Constraints for table `pembeli`
--
ALTER TABLE `pembeli`
  ADD CONSTRAINT `pembeli_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `fk_pembeli` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`);

--
-- Constraints for table `penawaran`
--
ALTER TABLE `penawaran`
  ADD CONSTRAINT `penawaran_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`),
  ADD CONSTRAINT `penawaran_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`) ON DELETE CASCADE;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`),
  ADD CONSTRAINT `pengiriman_ibfk_2` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`);

--
-- Constraints for table `penjual`
--
ALTER TABLE `penjual`
  ADD CONSTRAINT `penjual_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD CONSTRAINT `surat_jalan_ibfk_1` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id_pengiriman`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
