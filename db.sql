-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 01 Mei 2017 pada 17.55
-- Versi Server: 5.7.17-log
-- PHP Version: 7.0.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tirta_randik`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `no_pegawai` int(10) NOT NULL,
  `username` varchar(36) DEFAULT NULL,
  `nama` varchar(64) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id_bahan_baku` int(10) NOT NULL,
  `id_suplier` int(10) DEFAULT NULL,
  `nama_bahan` varchar(64) DEFAULT NULL,
  `jenis_bahan` varchar(36) DEFAULT NULL,
  `satuan` varchar(36) DEFAULT NULL,
  `harga` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_permintaan`
--

CREATE TABLE `detail_permintaan` (
  `id_detail_permintaan` bigint(20) NOT NULL,
  `id_permintaan` bigint(20) DEFAULT NULL,
  `id_bahan_baku` int(10) DEFAULT NULL,
  `jumlah_permintaan` int(10) DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_login`
--

CREATE TABLE `log_login` (
  `id_log` bigint(20) NOT NULL,
  `username` varchar(36) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `operator_unit`
--

CREATE TABLE `operator_unit` (
  `no_pegawai` int(10) NOT NULL,
  `username` varchar(36) DEFAULT NULL,
  `id_unit` int(10) DEFAULT NULL,
  `nama` varchar(64) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permintaan_bahan_baku`
--

CREATE TABLE `permintaan_bahan_baku` (
  `id_permintaan` bigint(20) NOT NULL,
  `id_unit` int(10) DEFAULT NULL,
  `tanggal_permintaan` date DEFAULT NULL,
  `batas_waktu` date DEFAULT NULL,
  `keterangan` text,
  `approved` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(2) NOT NULL,
  `role` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suplier`
--

CREATE TABLE `suplier` (
  `id_suplier` int(10) NOT NULL,
  `nama_suplier` varchar(64) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit`
--

CREATE TABLE `unit` (
  `id_unit` int(10) NOT NULL,
  `nama_unit` varchar(64) DEFAULT NULL,
  `alamat_kantor` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `username` varchar(36) NOT NULL,
  `id_role` int(2) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`no_pegawai`),
  ADD KEY `fk_user` (`username`);

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id_bahan_baku`),
  ADD KEY `fk_suplier` (`id_suplier`);

--
-- Indexes for table `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  ADD PRIMARY KEY (`id_detail_permintaan`),
  ADD KEY `fk_bahan` (`id_bahan_baku`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_user` (`username`);

--
-- Indexes for table `operator_unit`
--
ALTER TABLE `operator_unit`
  ADD PRIMARY KEY (`no_pegawai`),
  ADD KEY `fk_unit` (`id_unit`),
  ADD KEY `fk_user` (`username`);

--
-- Indexes for table `permintaan_bahan_baku`
--
ALTER TABLE `permintaan_bahan_baku`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `fk_unit` (`id_unit`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `suplier`
--
ALTER TABLE `suplier`
  ADD PRIMARY KEY (`id_suplier`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id_unit`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD KEY `fk_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `no_pegawai` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id_bahan_baku` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  MODIFY `id_detail_permintaan` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id_log` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `operator_unit`
--
ALTER TABLE `operator_unit`
  MODIFY `no_pegawai` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permintaan_bahan_baku`
--
ALTER TABLE `permintaan_bahan_baku`
  MODIFY `id_permintaan` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(2) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `suplier`
--
ALTER TABLE `suplier`
  MODIFY `id_suplier` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `id_unit` int(10) NOT NULL AUTO_INCREMENT;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD CONSTRAINT `bahan_baku_ibfk_1` FOREIGN KEY (`id_suplier`) REFERENCES `suplier` (`id_suplier`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_permintaan`
--
ALTER TABLE `detail_permintaan`
  ADD CONSTRAINT `detail_permintaan_ibfk_1` FOREIGN KEY (`id_bahan_baku`) REFERENCES `bahan_baku` (`id_bahan_baku`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `log_login`
--
ALTER TABLE `log_login`
  ADD CONSTRAINT `log_login_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `operator_unit`
--
ALTER TABLE `operator_unit`
  ADD CONSTRAINT `operator_unit_ibfk_1` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `operator_unit_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `permintaan_bahan_baku`
--
ALTER TABLE `permintaan_bahan_baku`
  ADD CONSTRAINT `permintaan_bahan_baku_ibfk_1` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id_unit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
