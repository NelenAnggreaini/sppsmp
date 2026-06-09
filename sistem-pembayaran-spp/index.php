<?php
require_once 'config/config.php';

// Logika Multi-User: Cek Petugas atau Siswa
if (isset($_SESSION['petugas_id'])) {
    $petugas = getPetugas();
    $nama_user = strtoupper(escape($petugas['nama']));
    $level_user = ucfirst(escape($petugas['level']));
    $is_admin = ($petugas['level'] === 'admin');
    
    // Teks selamat datang khusus petugas/admin
    $welcome_display = "ADMINISTRATOR <span class='role-badge'>($level_user)</span>";
} elseif (isset($_SESSION['siswa_id'])) {
    $nama_user = strtoupper(escape($_SESSION['siswa_nama']));
    $level_user = "Siswa";
    $is_admin = false;
    
    // Teks selamat datang khusus siswa (Nama lengkap mereka)
    $welcome_display = $nama_user;
} else {
    header("Location: auth/login.php");
    exit;
}

$pdo = koneksi();
$total_siswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pembayaran SPP SMP</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ===== ANIMATION KEYFRAMES ===== */
        @keyframes fadeInPage {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUpCard {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInTitle {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInBadge {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== DASHBOARD FULL ===== */
        body.dashboard-full {
            background: #EFF6FF !important;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(96, 165, 250, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 90% 80%, rgba(30, 58, 95, 0.08) 0%, transparent 60%),
                linear-gradient(135deg, #EFF6FF 0%, #E0F2FE 100%) !important;
            background-attachment: fixed !important;
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Quicksand', sans-serif !important;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            animation: fadeInPage 0.75s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .main-dashboard-wrapper {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* ===== HEADER BAR ===== */
        .top-header {
            width: 100%;
            padding: 25px 40px;
            display: flex;
            justify-content: flex-end;
            box-sizing: border-box;
        }

        .status-badge {
            background: #FFFFFF !important;
            border: 1px solid #BFDBFE;
            color: #1E3A5F !important;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.06);
            animation: fadeInBadge 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            will-change: opacity, transform;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background-color: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10B981;
        }

        /* ===== HERO SECTION (BIAR GAK SEPI) ===== */
        .hero-title {
            text-align: center;
            padding: 20px 20px 50px 20px;
        }

        .hero-title h1 {
            color: #1E3A5F !important;
            font-size: 2.8rem;
            font-weight: 800;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
            animation: fadeInTitle 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.15s forwards;
            opacity: 0;
            will-change: opacity, transform;
        }

        .hero-title h1.animate {
            animation: fadeInTitle 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.15s forwards;
        }

        /* Teks Tambahan Biar Gak Polos */
        .hero-subtitle {
            color: #64748B;
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0 0 25px 0;
            letter-spacing: 0.5px;
        }

        /* Desain Baru Teks Selamat Datang (Tanpa Box Putus-putus) */
        .welcome-text-new {
            font-size: 1.25rem;
            color: #475569;
            font-weight: 600;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .welcome-text-new .user-highlight {
            color: #2563EB;
            font-weight: 800;
        }

        .welcome-text-new .role-badge {
            font-size: 1rem;
            color: #64748B;
            font-weight: 600;
        }

        /* ===== MENU GRID ===== */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            padding: 0 40px;
            max-width: 1400px;
            margin: 0 auto 60px auto;
            width: 100%;
            box-sizing: border-box;
        }

        .glass-card {
            background: #FFFFFF !important;
            border: 1px solid #BFDBFE !important;
            border-radius: 24px !important;
            padding: 30px 20px !important;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            opacity: 0;
            animation: slideUpCard 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            will-change: opacity, transform;
        }

        /* Stagger delays untuk setiap card */
        .glass-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .glass-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .glass-card:nth-child(3) {
            animation-delay: 0.4s;
        }

        .glass-card:nth-child(4) {
            animation-delay: 0.5s;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
            border-color: #2563EB !important;
        }

        .glass-card .card-icon {
            background: rgba(37, 99, 235, 0.1) !important;
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: 0.3s ease;
        }

        .glass-card:hover .card-icon {
            background: #2563EB !important;
        }

        .glass-card .card-icon i {
            font-size: 2rem !important;
            color: #2563EB !important;
            transition: 0.3s ease;
        }

        .glass-card:hover .card-icon i {
            color: #FFFFFF !important;
        }

        .glass-card .card-number { 
            font-size: 3rem !important; 
            font-weight: 800 !important; 
            color: #2563EB !important; 
            margin: 15px 0 10px 0 !important; 
        }
        
        .glass-card .card-label { 
            color: #64748B !important; 
            font-weight: 700 !important; 
            font-size: 0.9rem !important; 
            letter-spacing: 0.5px !important; 
        }
        
        .glass-card h3 { 
            color: #1E3A5F !important; 
            font-size: 1.15rem !important; 
            font-weight: 700 !important; 
            margin: 15px 0 10px 0 !important;
            line-height: 1.3;
        }
        
        .glass-card .card-desc { 
            color: #64748B !important; 
            font-size: 0.9rem !important; 
            font-weight: 500 !important;
            margin: 0 !important;
            line-height: 1.4;
        }

        /* ===== RESPONSIVE TABLET ===== */
        @media (max-width: 1024px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                padding: 0 30px;
            }

            .hero-title h1 {
                font-size: 2.2rem;
            }
        }

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 0 20px;
            }

            .hero-title h1 {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .welcome-text-new {
                font-size: 1rem;
            }

            .glass-card {
                padding: 25px 15px !important;
            }

            .glass-card .card-number {
                font-size: 2.5rem !important;
            }

            .glass-card h3 {
                font-size: 1rem !important;
            }

            .top-header {
                padding: 15px 20px;
            }
        }

        /* ===== LOGOUT SECTION ===== */
        .logout-section { 
            text-align: center; 
            margin: 50px auto 60px auto;
            animation: fadeInUp 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.6s forwards;
            opacity: 0;
        }
        
        .btn-logout {
            background: #EF4444 !important;
            color: white !important;
            padding: 14px 45px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.2);
            transition: 0.3s;
        }
        
        .btn-logout:hover { 
            background: #DC2626 !important; 
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 768px) {
            .top-header { justify-content: center; padding: 20px; }
            .hero-title h1 { font-size: 2rem; }
            .hero-subtitle { font-size: 0.95rem; }
            .welcome-text-new { font-size: 1.1rem; }
        }

        /* ===== GLOBAL FOOTER ===== */
        .global-footer {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 18px 20px;
            margin-top: 40px;
            border-top: 1px solid #d6e4ff;
            background: transparent;
            color: #5b6b8c;
            font-size: 14px;
            animation: fadeInUp 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.65s forwards;
            opacity: 0;
            font-weight: 500;
            box-sizing: border-box;
        }

        .global-footer span {
            margin: 0;
            padding: 0 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .global-footer {
                padding: 15px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="dashboard-full">

    <div class="main-dashboard-wrapper">
        <header class="top-header">
            <span class="status-badge">
                <span class="status-dot"></span> <?= strtoupper($level_user) ?> AKTIF
            </span>
        </header>

        <section class="hero-title">
            <h1>Sistem Informasi Pembayaran SPP SMP</h1>
            <p class="hero-subtitle">Kelola data pembayaran SPP dengan mudah, cepat, dan efisien.</p>
            
            <p class="welcome-text-new">
                Selamat Datang, <span class="user-highlight"><?= $welcome_display ?></span>
            </p>
        </section>

        <section class="menu-grid">
            <div class="glass-card">
                <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
                <h2 class="card-number"><?= number_format($total_siswa) ?></h2>
                <p class="card-label">TOTAL SISWA</p>
            </div>

            <?php if ($is_admin): ?>
            <a href="siswa/" class="glass-card">
                <div class="card-icon"><i class="fas fa-users-cog"></i></div>
                <h3>KELOLA DATA SISWA</h3>
                <p class="card-desc">Tambah, edit, dan hapus data profil serta akun siswa</p>
            </a>
            <?php endif; ?>

            <a href="pembayaran/" class="glass-card">
                <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <h3><?= $is_admin || $level_user == 'Petugas' ? 'TRANSAKSI PEMBAYARAN' : 'RIWAYAT PEMBAYARAN' ?></h3>
                <p class="card-desc">Proses iuran, cek tagihan, dan pantau histori pembayaran</p>
            </a>

            <a href="laporan_rekap.php" class="glass-card">
                <div class="card-icon"><i class="fas fa-file-invoice"></i></div>
                <h3>REKAP LAPORAN</h3>
                <p class="card-desc">Unduh hasil laporan rekapitulasi dalam format PDF atau Cetak</p>
            </a>
        </section>

        <section class="logout-section">
            <a href="auth/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> LOGOUT SISTEM
            </a>
        </section>
    </div>

    <footer class="global-footer">
        <span>&copy; 2026 Sistem Informasi Pembayaran SPP SMP | Version 1.0</span>
    </footer>

</body>
</html>

<script>
    // ===== DASHBOARD ANIMATION ENGINE =====
    // Animasi profesional yang berjalan saat halaman pertama kali dibuka
    
    (function() {
        'use strict';
        
        // Detect jika halaman di-load dari back button (jangan re-animate)
        if (performance.navigation.type === 2) {
            // Back button - gunakan cached state
            document.documentElement.classList.add('no-animations');
        }
        
        // Core animation initialization
        document.addEventListener('DOMContentLoaded', initDashboardAnimations);
        
        function initDashboardAnimations() {
            // Tunggu sedikit untuk memastikan rendering engine siap
            requestAnimationFrame(function() {
                // Trigger fade-in page (sudah otomatis dari CSS)
                document.body.classList.add('animate-ready');
                
                // Trigger animasi title
                const titleElement = document.querySelector('.hero-title h1');
                if (titleElement) {
                    titleElement.classList.add('animate');
                }
                
                // Ensure cards opacity dimulai dari 0
                const cards = document.querySelectorAll('.glass-card');
                cards.forEach(card => {
                    card.style.opacity = '0';
                });
                
                // Cleanup will-change setelah animasi selesai
                // Total animation time: 1.15s (title 0.6s delay 0.15s = 0.75s, 
                // cards max 0.6s delay 0.5s = 1.1s, logout 0.6s delay 0.6s = 1.2s)
                setTimeout(function() {
                    cleanupAnimationOptimizations();
                }, 1300);
            });
        }
        
        function cleanupAnimationOptimizations() {
            // Hapus will-change untuk menghemat memory
            const elementsWithWillChange = document.querySelectorAll('[style*="will-change"]');
            elementsWithWillChange.forEach(el => {
                el.style.willChange = 'auto';
            });
            
            // Hapus animation classes
            document.body.classList.remove('animate-ready');
        }
        
        // Prevent re-animation on page refresh
        window.addEventListener('beforeunload', function() {
            document.documentElement.classList.add('no-animations');
        });
    })();
</script>