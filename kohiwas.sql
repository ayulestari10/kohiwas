-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 06 Jun 2017 pada 18.56
-- Versi Server: 10.1.21-MariaDB
-- PHP Version: 5.6.30

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
-- Struktur dari tabel `aktivitas`
--

CREATE TABLE `aktivitas` (
  `id_aktivitas` int(11) NOT NULL,
  `nama_aktivitas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `aktivitas`
--

INSERT INTO `aktivitas` (`id_aktivitas`, `nama_aktivitas`) VALUES
(1, 'Simpanan'),
(2, 'Pinjaman'),
(3, 'Angsuran'),
(4, 'Simpanan Sukarela');

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
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
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nlp`, `nama`, `tgl_mendaftar`, `alamat`, `simpanan_pokok`) VALUES
(2, '12345', 'Adinda', '2017-05-31', 'Palembang', 2000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `angsuran`
--

CREATE TABLE `angsuran` (
  `id_angsuran` int(11) NOT NULL,
  `id_pinjaman` int(11) NOT NULL,
  `tgl_angsuran` date NOT NULL,
  `jlh_dibayar` int(10) NOT NULL,
  `sisa_angsuran` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku_besar`
--

CREATE TABLE `buku_besar` (
  `id_buku_besar` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `ket` text NOT NULL,
  `ref` varchar(11) NOT NULL,
  `debit` int(11) NOT NULL,
  `kredit` int(11) NOT NULL,
  `saldo_debit` int(11) NOT NULL,
  `saldo_kredit` int(11) NOT NULL,
  `id_aktivitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `buku_besar`
--

INSERT INTO `buku_besar` (`id_buku_besar`, `tgl`, `ket`, `ref`, `debit`, `kredit`, `saldo_debit`, `saldo_kredit`, `id_aktivitas`) VALUES
(17, '2017-06-05', 'Simpanan Wajib', '103', 1000000, 0, 7000000, 0, 1),
(18, '2017-06-05', 'Simpanan Sukarela', '104', 1000000, 0, 8000000, 0, 4),
(19, '2017-06-04', 'Simpanan Wajib', '103', 2000000, 0, 4000000, 0, 1),
(20, '2017-06-04', 'Simpanan Sukarela', '104', 2000000, 0, 5000000, 0, 4),
(21, '2017-06-03', 'Simpanan Wajib', '103', 1000000, 0, 1000000, 0, 1),
(22, '2017-06-03', 'Simpanan Sukarela', '104', 1000000, 0, 2000000, 0, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_umum`
--

CREATE TABLE `jurnal_umum` (
  `id_jurnal` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `ket` text NOT NULL,
  `debit` int(11) NOT NULL,
  `kredit` int(11) NOT NULL,
  `id_aktivitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jurnal_umum`
--

INSERT INTO `jurnal_umum` (`id_jurnal`, `tgl`, `ket`, `debit`, `kredit`, `id_aktivitas`) VALUES
(19, '2017-06-05', 'Simpanan Wajib', 1000000, 1000000, 1),
(20, '2017-06-05', 'Simpanan Sukarela', 1000000, 1000000, 4),
(21, '2017-06-04', 'Simpanan Wajib', 2000000, 2000000, 1),
(22, '2017-06-04', 'Simpanan Sukarela', 2000000, 2000000, 4),
(23, '2017-06-03', 'Simpanan Wajib', 1000000, 1000000, 1),
(24, '2017-06-03', 'Simpanan Sukarela', 1000000, 1000000, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_login`
--

CREATE TABLE `log_login` (
  `id_login` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `log_login`
--

INSERT INTO `log_login` (`id_login`, `username`, `tanggal`, `waktu`) VALUES
(1, 'azhary', '2017-06-05', '15:36:22'),
(2, 'arliansyah', '2017-06-05', '15:37:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id_pinjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `tgl_pinjaman` date NOT NULL,
  `jlh_pinjaman` int(11) NOT NULL,
  `bunga` int(10) NOT NULL,
  `ttl_pinjaman` varchar(225) NOT NULL,
  `lama_pinjaman` int(5) NOT NULL,
  `angsuran` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(5) NOT NULL,
  `role` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `role`) VALUES
(1, 'Ketua Koperasi'),
(2, 'Admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `simpanan`
--

CREATE TABLE `simpanan` (
  `id_simpanan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `tgl_simpanan` date NOT NULL,
  `simpanan_wajib` int(20) NOT NULL,
  `simpanan_sukarela` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `simpanan`
--

INSERT INTO `simpanan` (`id_simpanan`, `id_anggota`, `tgl_simpanan`, `simpanan_wajib`, `simpanan_sukarela`) VALUES
(10, 2, '2017-06-05', 1000000, 1000000),
(11, 2, '2017-06-04', 1000000, 1000000),
(12, 2, '2017-06-03', 1000000, 1000000),
(13, 2, '2017-06-04', 1000000, 1000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `username` varchar(25) NOT NULL,
  `id_role` int(5) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`username`, `id_role`, `password`) VALUES
('admin', 2, '202cb962ac59075b964b07152d234b70'),
('arliansyah', 1, '985fabf8f96dc1c4c306341031569937'),
('azhary', 2, '985fabf8f96dc1c4c306341031569937'),
('ketua', 1, '202cb962ac59075b964b07152d234b70');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`id_aktivitas`);

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
  ADD PRIMARY KEY (`id_buku_besar`),
  ADD KEY `id_aktivitas` (`id_aktivitas`);

--
-- Indexes for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `id_aktivitas` (`id_aktivitas`);

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
-- AUTO_INCREMENT for table `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `id_aktivitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `angsuran`
--
ALTER TABLE `angsuran`
  MODIFY `id_angsuran` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `buku_besar`
--
ALTER TABLE `buku_besar`
  MODIFY `id_buku_besar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id_pinjaman` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id_simpanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  ADD CONSTRAINT `angsuran_id_pinjaman` FOREIGN KEY (`id_pinjaman`) REFERENCES `pinjaman` (`id_pinjaman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `buku_besar`
--
ALTER TABLE `buku_besar`
  ADD CONSTRAINT `buku_besar_ibfk_1` FOREIGN KEY (`id_aktivitas`) REFERENCES `aktivitas` (`id_aktivitas`);

--
-- Ketidakleluasaan untuk tabel `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD CONSTRAINT `jurnal_umum_ibfk_1` FOREIGN KEY (`id_aktivitas`) REFERENCES `aktivitas` (`id_aktivitas`);

--
-- Ketidakleluasaan untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_id_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `simpanan_id_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
