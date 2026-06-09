<?php
// Pastikan config dimuat
if (!defined('DB_HOST')) {
    require_once '../config/config.php';
} else {
    require_once 'config/config.php';
}
$petugas = getPetugas();

if (!$petugas) {
    header("Location: " . (basename($_SERVER['PHP_SELF']) === 'login.php' ? '../index.php' : 'auth/login.php'));
    exit;
}

// Hitung kedalaman folder agar path CSS selalu benar
$script_dir = dirname($_SERVER['PHP_SELF']);
$depth = max(0, substr_count(trim($script_dir, '/'), '/'));
$css_path = str_repeat('../', $depth) . 'assets/css/style.css';

// Tentukan URL kembali berdasarkan halaman saat ini
$current_file = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

$back_url = 'index.php'; // Default balik ke dashboard utama jika berada di folder luar

if ($current_file === 'index.php' && ($current_dir === 'siswa' || $current_dir === 'pembayaran')) {
    $back_url = '../index.php';
} elseif ($current_file === 'tambah.php' || $current_file === 'edit.php') {
    $back_url = 'index.php';
} elseif ($current_file === 'login.php') {
    $back_url = '../index.php';
} elseif ($current_file === 'laporan_rekap.php') {
    $back_url = 'index.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Sistem Pembayaran SPP SMP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $css_path ?>" rel="stylesheet">

    <style>
    /* ==========================================================================
       FLEXBOX GLOBAL LAYOUT
       ========================================================================== */
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
    }

    .main-content {
        flex: 1;
    }

    /* ==========================================================================
       OVERRIDE COLOR: PAKSA WARNA TEXT JUDUL JADI PUTIH MURNI
       ========================================================================== */
    header h1, 
    .header h1, 
    .logo h1,
    .logo h1 span,
    header h1 a {
        color: #FFFFFF !important;
        -webkit-text-fill-color: #FFFFFF !important;
    }

    /* Paksa Icon Sekolah Jadi Biru Langit Cerah */
    header h1 i, 
    .header h1 i, 
    .logo h1 i {
        color: #38BDF8 !important;
        -webkit-text-fill-color: #38BDF8 !important;
    }

    /* ===================================
       GLOBAL NAVY PROFESSIONAL THEME
       =================================== */
    .card{
        background:#FFFFFF !important;
        border:1px solid #BFDBFE !important;
        border-radius:20px !important;
        box-shadow:0 10px 30px rgba(30,58,95,.08) !important;
    }

    .card-header{
        background:#EFF6FF !important;
        border-left:5px solid #2563EB !important;
        border-bottom:1px solid #BFDBFE !important;
        padding:20px !important;
    }

    .card-header h1,
    .card-header h2,
    .card-header h3{
        color:#1E3A5F !important;
    }

    .card-body{
        background:#FFFFFF !important;
    }

    .form-group label{
        color:#1E3A5F !important;
        font-weight:700 !important;
    }

    input, select, textarea{
        background:#FFFFFF !important;
        border:1px solid #BFDBFE !important;
        color:#334155 !important;
        border-radius:12px !important;
    }

    input:focus, select:focus, textarea:focus{
        border-color:#2563EB !important;
        box-shadow:0 0 0 4px rgba(37,99,235,.12) !important;
    }

    .btn-primary{
        background:#2563EB !important;
        border:none !important;
        color:#FFFFFF !important;
    }

    .btn-primary:hover{
        background:#1D4ED8 !important;
    }

    .alert-danger{
        background:#EFF6FF !important;
        border:1px solid #BFDBFE !important;
        color:#1E3A5F !important;
    }

    .alert-success{
        background:#DCFCE7 !important;
        border:1px solid #BBF7D0 !important;
        color:#166534 !important;
    }

    table{ background:#FFFFFF !important; }
    th{ background:#1E3A5F !important; color:#FFFFFF !important; }
    td{ background:#FFFFFF !important; color:#334155 !important; }

    /* Base Background reset */
    body, html {
        background: #EFF6FF !important;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(96, 165, 250, 0.08) 0%, transparent 50%),
            linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%) !important;
        background-attachment: fixed !important;
        font-family: 'Quicksand', sans-serif !important;
    }

    /* Top Header Topbar Navy */
    .header, header {
        background: #1E3A5F !important;
        box-shadow: 0 4px 20px rgba(30, 58, 95, 0.15) !important;
        border-bottom: 3px solid #2563EB !important;
        padding: 15px 30px !important;
    }

    /* Badge Info User */
    .user-name-badge {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
        padding: 8px 16px !important;
        border-radius: 30px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .user-name-badge i { color: #38BDF8 !important; }
    .user-level { color: #93C5FD !important; font-weight: 700; }

    /* ===== RE-FIX CONTAINER BIAR BALIK KOTAK DI TENGAH ===== */
    .container, main.container {
        max-width: 1140px !important;
        margin: 40px auto !important;
        background: #FFFFFF !important;
        border: 1px solid #BFDBFE !important;
        border-radius: 24px !important;
        padding: 35px !important;
        box-sizing: border-box !important;
        box-shadow: 0 10px 30px rgba(30,58,95,.08) !important;
        display: block !important; /* Paksa balik ke layout block standar */
    }

    .menu-grid{ background:transparent !important; border:none !important; box-shadow:none !important; }
    *[style*="#f8e7ef"], *[style*="#f3dce8"], *[style*="#e7c7d5"]{ background:#FFFFFF !important; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div class="logo">
                <h1><i class="fas fa-school"></i> Sistem Pembayaran SPP SMP</h1>
            </div>
            <div class="user-info">
                <span class="user-name-badge">
                    <i class="fas fa-user-circle"></i>
                    <span class="user-name-text"><?= escape($petugas['nama']) ?></span>
                    <span class="user-level">(<?= ucfirst($petugas['level']) ?>)</span>
                </span>
            </div>
        </div>
    </header>
    
    <div class="main-content">
    <main class="container">