-- Script SQL untuk Sistem Informasi Pembayaran SPP SMP
-- Jalankan script ini di MySQL (phpMyAdmin atau command line)

-- 1. Buat database terlebih dahulu
CREATE DATABASE IF NOT EXISTS db_spp_smp;
USE db_spp_smp;

-- 2. Tabel SISWA
-- Menyimpan data siswa
CREATE TABLE siswa (
    nis VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel PETUGAS
-- Menyimpan data petugas dan admin (level: 'admin' atau 'petugas')
CREATE TABLE petugas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Gunakan password_hash() saat insert
    level ENUM('admin', 'petugas') DEFAULT 'petugas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel PEMBAYARAN
-- Menyimpan riwayat pembayaran SPP
CREATE TABLE pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nis VARCHAR(20),
    tgl_bayar DATE NOT NULL,
    bulan_dibayar INT NOT NULL CHECK (bulan_dibayar BETWEEN 1 AND 12),
    tahun_dibayar YEAR NOT NULL,
    jumlah_bayar DECIMAL(10,2) NOT NULL,
    petugas_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key untuk integritas data
    FOREIGN KEY (nis) REFERENCES siswa(nis) ON DELETE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES petugas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Insert data petugas default (password: admin123 di-hash)
INSERT INTO petugas (nama, username, password, level) VALUES 
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Petugas SPP', 'petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas');

-- Password default: admin123 (sudah di-hash menggunakan password_hash PHP)
-- Ganti password ini setelah login pertama kali!

-- 6. Insert data siswa contoh
INSERT INTO siswa (nis, nama, kelas) VALUES 
('001', 'Ahmad Santoso', '7A'),
('002', 'Siti Aisyah', '7B'),
('003', 'Budi Hartono', '8A');
