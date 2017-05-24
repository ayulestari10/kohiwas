-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2017 at 03:50 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kohiwas`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nlp` varchar(25) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `tgl_mendaftar` date NOT NULL,
  `alamat` text NOT NULL,
  `simpanan_pokok` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nlp`, `nama`, `tgl_mendaftar`, `alamat`, `simpanan_pokok`) VALUES
(1, '1234544', 'Antu', '2017-05-12', 'huehuehue', 7);

-- --------------------------------------------------------

--
-- Table structure for table `angsuran`
--

CREATE TABLE `angsuran` (
  `id_angsuran` int(11) NOT NULL,
  `id_pinjaman` int(11) NOT NULL,
  `tgl_angsuran` date NOT NULL,
  `jlh_dibayar` int(10) NOT NULL,
  `sisa_angsuran` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `angsuran`
--

INSERT INTO `angsuran` (`id_angsuran`, `id_pinjaman`, `tgl_angsuran`, `jlh_dibayar`, `sisa_angsuran`) VALUES
(1, 1, '2017-09-18', 200000, 120000),
(2, 2, '2017-05-03', 250000, 375000),
(3, 2, '2017-05-04', 400000, 350000),
(4, 6, '2017-05-17', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `buku_besar`
--

CREATE TABLE `buku_besar` (
  `id_buku_besar` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `ket` text NOT NULL,
  `ref` varchar(11) NOT NULL,
  `debit` int(11) NOT NULL,
  `kredit` int(11) NOT NULL,
  `saldo_debit` int(11) NOT NULL,
  `saldo_kredit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `buku_besar`
--

INSERT INTO `buku_besar` (`id_buku_besar`, `tgl`, `ket`, `ref`, `debit`, `kredit`, `saldo_debit`, `saldo_kredit`) VALUES
(11, '2017-05-01', 'Simpanan Wajib', '103', 10, 0, 10, 0),
(12, '2017-05-01', 'Simpanan Sukarela', '104', 5, 0, 5, 0),
(13, '2017-05-02', 'Simpanan Wajib', '103', 20, 0, 25, 0),
(14, '2017-05-02', 'Simpanan Sukarela', '104', 2, 0, 27, 0),
(15, '2017-05-08', 'Pinjaman', '105', 0, 6, 21, 0),
(16, '2017-05-17', 'Angsuran', '106', 5, 0, 26, 0);

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_umum`
--

CREATE TABLE `jurnal_umum` (
  `id_jurnal` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `ket` text NOT NULL,
  `debit` int(11) NOT NULL,
  `kredit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jurnal_umum`
--

INSERT INTO `jurnal_umum` (`id_jurnal`, `tgl`, `ket`, `debit`, `kredit`) VALUES
(36, '2017-05-12', 'Kas Simpanan Wajib', 123, 123),
(37, '2017-05-12', 'Kas Simpanan Sukarela', 321, 321),
(38, '2017-05-12', 'Kas Simpanan Wajib', 123, 123),
(39, '2017-05-12', 'Kas Simpanan Sukarela', 321, 321),
(40, '2017-05-12', 'Kas Simpanan Wajib', 123, 123),
(41, '2017-05-12', 'Kas Simpanan Sukarela', 321, 321),
(42, '2017-05-13', 'Kas Simpanan Wajib', 3211, 3211),
(43, '2017-05-13', 'Kas Simpanan Sukarela', 3211, 3211),
(44, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(45, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(46, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(47, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(48, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(49, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(50, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(51, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(52, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(53, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(54, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(55, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(56, '2017-05-09', 'Kas Simpanan Wajib', 111, 111),
(57, '2017-05-09', 'Kas Simpanan Sukarela', 111, 111),
(58, '2017-05-01', 'Kas Simpanan Wajib', 10, 10),
(59, '2017-05-01', 'Kas Simpanan Sukarela', 5, 5),
(60, '2017-05-02', 'Kas Simpanan Wajib', 20, 20),
(61, '2017-05-02', 'Kas Simpanan Sukarela', 2, 2),
(62, '2017-05-08', 'Pinjaman', 0, 6),
(63, '2017-05-17', 'Angsuran', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `id_login` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log_login`
--

INSERT INTO `log_login` (`id_login`, `username`, `tanggal`, `waktu`) VALUES
(1, 'ayu', '2017-05-10', '09:58:33'),
(2, 'ayu', '2017-05-10', '10:09:25'),
(3, 'ayu', '2017-05-10', '12:29:25'),
(4, 'ayu', '2017-05-16', '13:06:47'),
(5, 'admin', '2017-05-16', '13:14:01'),
(6, 'admin', '2017-05-16', '13:14:06'),
(7, 'ayu', '2017-05-16', '21:39:52'),
(8, 'ayu', '2017-05-16', '21:42:51'),
(9, 'ayu', '2017-05-16', '22:00:38'),
(10, 'ayu', '2017-05-17', '08:05:07'),
(11, 'ayu', '2017-05-17', '10:00:32'),
(12, 'ayu', '2017-05-21', '13:19:45'),
(13, 'ayu', '2017-05-22', '18:54:58'),
(14, 'admin', '2017-05-22', '20:35:23'),
(15, 'ayu', '2017-05-22', '21:20:45'),
(16, 'admin', '2017-05-22', '21:25:24'),
(17, 'admin', '2017-05-22', '21:26:46'),
(18, 'ayu', '2017-05-22', '22:46:30');

-- --------------------------------------------------------

--
-- Table structure for table `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id_pinjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `tgl_pinjaman` date NOT NULL,
  `jlh_pinjaman` int(11) NOT NULL,
  `bunga` int(10) NOT NULL,
  `ttl_pinjaman` varchar(225) NOT NULL,
  `lama_pinjaman` int(5) NOT NULL,
  `angsuran` int(10) NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pinjaman`
--

INSERT INTO `pinjaman` (`id_pinjaman`, `id_anggota`, `tgl_pinjaman`, `jlh_pinjaman`, `bunga`, `ttl_pinjaman`, `lama_pinjaman`, `angsuran`, `status`) VALUES
(1, 1, '2017-09-12', 3000000, 200, 'apaan', 1, 200000, 'ngutang'),
(2, 1, '2017-05-03', 3000000, 20, 'apa ini', 5, 200000, 'ngutang'),
(3, 1, '2017-05-08', 6, 1, 'apa ini', 1, 2, 'minjem e'),
(4, 1, '2017-05-08', 6, 1, 'apa ini', 1, 2, 'minjem e'),
(5, 1, '2017-05-08', 6, 1, 'apa ini', 1, 2, 'minjem e'),
(6, 1, '2017-05-08', 6, 1, 'apa ini', 1, 2, 'minjem e');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(5) NOT NULL,
  `role` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `role`) VALUES
(1, 'Ketua Koperasi'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `simpanan`
--

CREATE TABLE `simpanan` (
  `id_simpanan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `tgl_simpanan` date NOT NULL,
  `simpanan_wajib` int(20) NOT NULL,
  `simpanan_sukarela` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `simpanan`
--

INSERT INTO `simpanan` (`id_simpanan`, `id_anggota`, `tgl_simpanan`, `simpanan_wajib`, `simpanan_sukarela`) VALUES
(1, 1, '2017-05-01', 2000000, 200000),
(2, 1, '2017-05-03', 2000000, 200000),
(3, 1, '2017-05-05', 300000, 200000),
(4, 1, '2017-05-05', 300000, 200000),
(5, 1, '2017-05-05', 300000, 200000),
(6, 1, '2017-05-05', 300000, 200000),
(7, 1, '2017-05-05', 300000, 200000),
(8, 1, '2017-05-05', 300000, 200000),
(9, 1, '2017-05-05', 300000, 200000),
(10, 1, '2017-05-05', 300000, 200000),
(11, 1, '2017-05-05', 300000, 200000),
(12, 1, '2017-05-05', 300000, 200000),
(13, 1, '2017-05-05', 300000, 200000),
(14, 1, '2017-05-05', 300000, 200000),
(15, 1, '2017-05-05', 300000, 200000),
(16, 1, '2017-05-05', 300000, 200000),
(17, 1, '2017-05-05', 300000, 200000),
(18, 1, '2017-05-05', 300000, 200000),
(19, 1, '2017-05-06', 100000, 10000),
(20, 1, '2017-05-06', 100000, 10000),
(21, 1, '2017-05-10', 3500000, 350000),
(22, 1, '2017-05-12', 123, 321),
(23, 1, '2017-05-12', 123, 321),
(24, 1, '2017-05-12', 123, 321),
(25, 1, '2017-05-13', 3211, 3211),
(26, 1, '2017-05-09', 111, 111),
(27, 1, '2017-05-09', 111, 111),
(28, 1, '2017-05-09', 111, 111),
(29, 1, '2017-05-09', 111, 111),
(30, 1, '2017-05-09', 111, 111),
(31, 1, '2017-05-09', 111, 111),
(32, 1, '2017-05-09', 111, 111),
(33, 1, '2017-05-01', 10, 5),
(34, 1, '2017-05-02', 20, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(25) NOT NULL,
  `id_role` int(5) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `id_role`, `password`) VALUES
('admin', 2, '202cb962ac59075b964b07152d234b70'),
('ayu', 1, '202cb962ac59075b964b07152d234b70');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`);

--
-- Indexes for table `angsuran`
--
ALTER TABLE `angsuran`
  ADD PRIMARY KEY (`id_angsuran`),
  ADD KEY `id_pinjaman` (`id_pinjaman`);

--
-- Indexes for table `buku_besar`
--
ALTER TABLE `buku_besar`
  ADD PRIMARY KEY (`id_buku_besar`);

--
-- Indexes for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indexes for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id_pinjaman`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD PRIMARY KEY (`id_simpanan`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `angsuran`
--
ALTER TABLE `angsuran`
  MODIFY `id_angsuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `buku_besar`
--
ALTER TABLE `buku_besar`
  MODIFY `id_buku_besar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id_pinjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id_simpanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `angsuran`
--
ALTER TABLE `angsuran`
  ADD CONSTRAINT `angsuran_id_pinjaman` FOREIGN KEY (`id_pinjaman`) REFERENCES `pinjaman` (`id_pinjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_id_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `simpanan_id_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
