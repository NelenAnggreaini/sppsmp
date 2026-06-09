<?php
/**
 * Proses input pembayaran dari form & delete pembayaran
 * Redirect kembali dengan pesan sukses/error
 */

require_once '../config/config.php';
session_start();

if (!isset($_SESSION['petugas_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle DELETE
if ($_GET && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo = koneksi();
    $id = intval($_GET['id']);
    
    // Cek pembayaran ada
    $stmt = $pdo->prepare("SELECT id FROM pembayaran WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        // Hapus pembayaran
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: index.php?success=delete");
            exit;
        } else {
            header("Location: index.php?error=1&msg=Gagal+menghapus+pembayaran");
            exit;
        }
    } else {
        header("Location: index.php?error=1&msg=Pembayaran+tidak+ditemukan");
        exit;
    }
}

if ($_POST) {
    $pdo = koneksi();
    $petugas_id = $_SESSION['petugas_id'];
    
    $nis = trim($_POST['nis']);
    $jumlah_bayar = floatval($_POST['jumlah_bayar']);
    $bulan_dibayar = intval($_POST['bulan_dibayar']);
    $tahun_dibayar = intval($_POST['tahun_dibayar']);
    $tgl_bayar = date('Y-m-d'); // Tanggal hari ini
    
    // Validasi
    $errors = [];
    if (empty($nis)) $errors[] = 'NIS wajib dipilih';
    if ($jumlah_bayar <= 0) $errors[] = 'Jumlah bayar harus lebih dari 0';
    if ($bulan_dibayar < 1 || $bulan_dibayar > 12) $errors[] = 'Bulan tidak valid';
    if ($tahun_dibayar < 2000 || $tahun_dibayar > 2030) $errors[] = 'Tahun tidak valid';
    
    // Cek siswa ada
    $stmt = $pdo->prepare("SELECT nis FROM siswa WHERE nis = ?");
    $stmt->execute([$nis]);
    if (!$stmt->fetch()) {
        $errors[] = 'Siswa dengan NIS tersebut tidak ditemukan';
    }
    
    if (empty($errors)) {
        // Insert pembayaran
        $stmt = $pdo->prepare("
            INSERT INTO pembayaran (nis, tgl_bayar, bulan_dibayar, tahun_dibayar, jumlah_bayar, petugas_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$nis, $tgl_bayar, $bulan_dibayar, $tahun_dibayar, $jumlah_bayar, $petugas_id])) {
            // Sukses - redirect dengan pesan
            header("Location: index.php?success=1&siswa=$nis");
            exit;
        } else {
            $errors[] = 'Gagal menyimpan pembayaran';
        }
    }
}

// Error - redirect dengan pesan error
$redirect_url = 'index.php';
if (!empty($errors)) {
    $error_msg = urlencode(implode('; ', $errors));
    $redirect_url .= "?error=1&msg=$error_msg";
}
header("Location: $redirect_url");
exit;
?>

