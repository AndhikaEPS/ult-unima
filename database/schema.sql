-- =====================================================================
-- SISTEM INFORMASI LAYANAN DIGITAL - UNIT LAYANAN TERPADU (ULT)
-- Universitas Negeri Manado
-- Database: MySQL 5.7+ / MariaDB 10.3+
-- =====================================================================

CREATE DATABASE IF NOT EXISTS ult_unima
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ult_unima;

-- ---------------------------------------------------------------------
-- TABEL: admin  -> akun petugas / admin ULT
-- ---------------------------------------------------------------------
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,      -- disimpan dengan password_hash()
  nama_lengkap VARCHAR(100) NOT NULL,
  role ENUM('super_admin','petugas') NOT NULL DEFAULT 'petugas',
  kategori_id INT NULL,                -- petugas bisa dikaitkan ke 1 kategori
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TABEL: kategori_layanan -> 5 kategori sesuai PDF + sub layanan (JSON)
-- ---------------------------------------------------------------------
CREATE TABLE kategori_layanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_prefix VARCHAR(2) NOT NULL UNIQUE,   -- huruf depan no. antrian, misal 'A'
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi VARCHAR(255) NULL,
  icon VARCHAR(50) DEFAULT 'bi-info-circle',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  urutan INT DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TABEL: sub_layanan -> daftar layanan detail per kategori (list bullet PDF)
-- ---------------------------------------------------------------------
CREATE TABLE sub_layanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT NOT NULL,
  nama_layanan VARCHAR(150) NOT NULL,
  wajib_upload TINYINT(1) NOT NULL DEFAULT 0, -- 1 = perlu unggah dokumen
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (kategori_id) REFERENCES kategori_layanan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TABEL: antrian -> data pemohon + tiket antrian
-- ---------------------------------------------------------------------
CREATE TABLE antrian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_tiket VARCHAR(10) NOT NULL,
  nama_lengkap VARCHAR(150) NOT NULL,
  nim_nip VARCHAR(50) NOT NULL,
  email VARCHAR(150) NULL,
  no_hp VARCHAR(20) NULL,
  fakultas_unit VARCHAR(150) NOT NULL,
  kategori_id INT NOT NULL,
  sub_layanan_id INT NULL,
  detail_keperluan TEXT NULL,
  file_path VARCHAR(255) NULL,          -- dokumen/foto yang diunggah
  file_original_name VARCHAR(255) NULL,
  status ENUM('menunggu','dipanggil','dilayani','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  dilayani_oleh INT NULL,               -- admin/petugas id
  tanggal_antrian DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  dipanggil_at DATETIME NULL,
  selesai_at DATETIME NULL,
  FOREIGN KEY (kategori_id) REFERENCES kategori_layanan(id),
  FOREIGN KEY (sub_layanan_id) REFERENCES sub_layanan(id),
  FOREIGN KEY (dilayani_oleh) REFERENCES admin(id),
  INDEX idx_status (status),
  INDEX idx_tanggal (tanggal_antrian)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TABEL: evaluasi -> kepuasan layanan (step 8 pada Panduan)
-- ---------------------------------------------------------------------
CREATE TABLE evaluasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  antrian_id INT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  komentar TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (antrian_id) REFERENCES antrian(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TABEL: pengaturan -> pengaturan umum (jam layanan dll), key-value
-- ---------------------------------------------------------------------
CREATE TABLE pengaturan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_setting VARCHAR(100) NOT NULL UNIQUE,
  nilai VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- =====================================================================
-- DATA AWAL (SEED DATA)
-- =====================================================================

-- Admin default -> username: admin | password: admin123
INSERT INTO admin (username, password, nama_lengkap, role) VALUES
('admin', '$2b$10$8gMMuz9YfFIjy5V450UyVOjouIa77WOHg5wSynlERtQco.x1dbiZ.', 'Administrator ULT', 'super_admin');
-- Password default di atas adalah: admin123  (WAJIB diganti setelah login pertama kali)

-- Kategori layanan sesuai PDF
INSERT INTO kategori_layanan (kode_prefix, nama_kategori, deskripsi, icon, urutan) VALUES
('U', 'Layanan Informasi Umum', 'Informasi umum seputar universitas dan prosedur layanan', 'bi-info-circle', 1),
('A', 'Layanan Akademik', 'KRS, KHS, Surat Aktif Kuliah, Cuti, Yudisium, Wisuda, dll', 'bi-mortarboard', 2),
('M', 'Layanan Kemahasiswaan', 'Beasiswa, organisasi, prestasi, surat rekomendasi', 'bi-people', 3),
('K', 'Layanan Keuangan dan UKT', 'Pembayaran UKT, status pembayaran, keringanan & cicilan', 'bi-cash-coin', 4),
('S', 'Layanan Sistem Informasi & IT', 'SIAKAD, akun mahasiswa, reset password, jaringan/wifi', 'bi-hdd-network', 5);

-- Sub layanan sesuai daftar bullet pada PDF
INSERT INTO sub_layanan (kategori_id, nama_layanan, wajib_upload) VALUES
(1, 'Informasi Layanan Universitas', 0),
(1, 'Informasi Prosedur Layanan', 0),
(1, 'Informasi Persyaratan', 0),
(1, 'Informasi Unit Kerja', 0),
(1, 'Informasi Jadwal Pelayanan', 0),

(2, 'KRS dan KHS', 0),
(2, 'Surat Keterangan Aktif Kuliah', 1),
(2, 'Cuti Akademik', 1),
(2, 'Aktif Kembali', 1),
(2, 'Yudisium', 1),
(2, 'Wisuda', 1),
(2, 'Transkrip Nilai', 0),
(2, 'Legalisir Dokumen Akademik', 1),

(3, 'Beasiswa', 1),
(3, 'Organisasi/Kegiatan Mahasiswa', 0),
(3, 'Prestasi Mahasiswa', 1),
(3, 'Surat/Rekomendasi Kemahasiswaan', 1),

(4, 'Pembayaran UKT', 1),
(4, 'Status Pembayaran', 0),
(4, 'Kendala Pembayaran', 1),
(4, 'Keringanan UKT', 1),
(4, 'Cicilan UKT', 1),

(5, 'SIAKAD', 0),
(5, 'Akun Mahasiswa', 0),
(5, 'Reset Password', 0),
(5, 'Email Mahasiswa', 0),
(5, 'Jaringan/Wi-Fi', 0),
(5, 'Sistem Informasi Universitas', 0);

-- Pengaturan umum
INSERT INTO pengaturan (nama_setting, nilai) VALUES
('jam_buka_senin_kamis', '08:00'),
('jam_tutup_senin_kamis', '16:00'),
('jam_buka_jumat', '08:00'),
('jam_tutup_jumat', '16:30'),
('nama_instansi', 'Unit Layanan Terpadu - Universitas Negeri Manado'),
('reset_nomor_harian', '1');
