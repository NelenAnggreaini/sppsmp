<?php
/**
 * File konfigurasi koneksi database
 * File ini berisi pengaturan koneksi MySQL yang aman
 */

// Pengaturan database - UBAH SESUAI KONFIGURASI SERVER ANDA
define('DB_HOST', 'localhost');     // Host database (biasanya localhost)
define('DB_NAME', 'db_spp_smp');    // Nama database yang dibuat dari database.sql
define('DB_USER', 'root');          // Username database (default MySQL: root)
define('DB_PASS', '');              // Password database (default MySQL kosong)

// Fungsi untuk membuat koneksi database yang aman
function koneksi() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // Jika koneksi gagal, tampilkan error untuk debugging (hapus di production)
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// Fungsi helper untuk prevent SQL Injection
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk mendapatkan data petugas yang login (session)
function getPetugas() {
    if (isset($_SESSION['petugas_id'])) {
        $pdo = koneksi();
        $stmt = $pdo->prepare("SELECT * FROM petugas WHERE id = ?");
        $stmt->execute([$_SESSION['petugas_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Start session untuk semua halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

