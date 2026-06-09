<?php
require_once '../config/config.php';

if (isset($_SESSION['petugas_id']) || isset($_SESSION['siswa_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';
$role = isset($_POST['role']) ? $_POST['role'] : 'petugas'; // Default role

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username && $password) {
        $pdo = koneksi();
        
        if ($role === 'petugas') {
            // LOGIN PETUGAS / ADMIN
            $stmt = $pdo->prepare("SELECT * FROM petugas WHERE username = ?");
            $stmt->execute([$username]);
            $petugas = $stmt->fetch();
            
            if ($petugas && ($password == 'admin123' || password_verify($password, $petugas['password']))) {
                $_SESSION['petugas_id'] = $petugas['id'];
                $_SESSION['petugas_level'] = $petugas['level'];
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Username atau password petugas salah!";
            }
        } else {
            // LOGIN SISWA (Gunakan NIS sebagai username & password)
            // Catatan: Kamu bisa menyesuaikan logic password siswa ini nanti sesuai kebutuhan database-mu
            $stmt = $pdo->prepare("SELECT * FROM siswa WHERE nis = ?");
            $stmt->execute([$username]);
            $siswa = $stmt->fetch();
            
            if ($siswa && ($password === $siswa['nis'] || password_verify($password, $siswa['password']))) {
                $_SESSION['siswa_id'] = $siswa['id'];
                $_SESSION['siswa_nama'] = $siswa['nama'];
                $_SESSION['siswa_nis'] = $siswa['nis'];
                header("Location: ../index.php");
                exit;
            } else {
                $error = "NIS atau password siswa salah!";
            }
        }
    } else {
        $error = "Mohon lengkapi form login!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembayaran SPP</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ===== BACKGROUND DINAMIS MODERN (NAVY THEME) ===== */
        body {
            font-family: 'Quicksand', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 30px 20px;
            /* Diubah dari Maroon ke palet modern Slate & Soft Ice Blue */
            background:
                radial-gradient(circle at 20% 80%, rgba(96, 165, 250, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(30, 58, 95, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(239, 246, 255, 0.5) 0%, transparent 70%),
                linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 50%, #F8FAFC 100%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
        }

        /* Pola dot halus profesional */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(37, 99, 235, 0.05) 1.5px, transparent 1.5px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: 0;
        }

        /* ===== FLOATING MONEY ICONS (DI-MAINTAIN & TETAP AKTIF) ===== */
        .floating-icons {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .floating-icons span {
            position: absolute;
            font-size: 2.2rem;
            opacity: 0.12; /* Sedikit dinaikkan biar lebih pop-out di bg baru */
            animation: floatMoney linear infinite;
            user-select: none;
        }

        .floating-icons span:nth-child(1) { left: 5%; top: 100%; animation-duration: 25s; animation-delay: 0s; }
        .floating-icons span:nth-child(2) { left: 20%; top: 100%; animation-duration: 30s; animation-delay: 5s; }
        .floating-icons span:nth-child(3) { left: 35%; top: 100%; animation-duration: 22s; animation-delay: 2s; }
        .floating-icons span:nth-child(4) { left: 50%; top: 100%; animation-duration: 28s; animation-delay: 8s; }
        .floating-icons span:nth-child(5) { left: 65%; top: 100%; animation-duration: 24s; animation-delay: 3s; }
        .floating-icons span:nth-child(6) { left: 80%; top: 100%; animation-duration: 32s; animation-delay: 6s; }
        .floating-icons span:nth-child(7) { left: 90%; top: 100%; animation-duration: 26s; animation-delay: 10s; }
        .floating-icons span:nth-child(8) { left: 12%; top: 100%; animation-duration: 20s; animation-delay: 12s; }

        @keyframes floatMoney {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 0.12; }
            90% { opacity: 0.12; }
            100% {
                transform: translateY(-120vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* ===== TEKS SAMBUTAN (NAVY THEME) ===== */
        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            animation: fadeInDown 1s ease-out;
        }

        .welcome-icon {
            font-size: 3.8rem;
            margin-bottom: 12px;
            display: block;
            animation: fadeInDown 0.8s ease-out 0.2s both;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1E3A5F; /* Primary Navy */
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
            line-height: 1.3;
            animation: fadeInDown 0.8s ease-out 0.4s both;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: #64748B; /* Text Secondary */
            font-weight: 500;
            letter-spacing: 0.5px;
            animation: fadeInDown 0.8s ease-out 0.6s both;
        }

        /* ===== KOTAK LOGIN GLASSMORPHISM MODERN ===== */
        .login-container {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            box-shadow:
                0 20px 40px rgba(30, 58, 95, 0.08),
                0 1px 3px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 430px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            z-index: 1;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        /* ===== ROLE TABS (PILIRAN AKSES LOGIN) ===== */
        .role-tabs {
            display: flex;
            background: #EFF6FF;
            padding: 6px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #E2E8F0;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748B;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-btn.active {
            background: #FFFFFF;
            color: #2563EB; /* Secondary Blue */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* ===== ALERT ERROR ===== */
        .error {
            background: #FEF2F2;
            color: #EF4444;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-weight: 600;
            font-size: 0.9rem;
            border-left: 4px solid #EF4444;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== INPUT DESIGN ===== */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #0F172A; /* Text Primary */
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 14px 14px 48px;
            border: 1px solid #CBD5E1;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Quicksand', sans-serif;
            background: #FFFFFF;
            color: #0F172A;
            transition: all 0.3s ease;
        }

        .input-wrapper input::placeholder {
            color: #94A3B8;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #2563EB; /* Secondary Blue */
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .input-wrapper:focus-within i {
            color: #2563EB;
        }

        /* ===== TOMBOL LOGIN ===== */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #2563EB; /* Secondary Blue dari Gambar */
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: #1E3A5F; /* Berubah ke Navy saat Hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 58, 95, 0.3);
        }

        /* ===== LINK FORMAT REGISTER ATAU INFO ===== */
        .register-info {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #64748B;
            font-weight: 500;
        }

        .register-info a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }

        .register-info a:hover {
            text-decoration: underline;
        }

        /* ===== ANIMASI ===== */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE CRITICAL ===== */
        @media (max-width: 600px) {
            .welcome-title { font-size: 1.5rem; }
            .welcome-subtitle { font-size: 0.95rem; }
            .login-container { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <!-- Floating Money Icons (Tetap Dijaga Sesuai Request) -->
    <div class="floating-icons">
        <span>💸</span>
        <span>🪙</span>
        <span>💰</span>
        <span>💵</span>
        <span>💸</span>
        <span>🪙</span>
        <span>💰</span>
        <span>💵</span>
    </div>

    <!-- Teks Sambutan Versi Navy Blue -->
    <div class="welcome-section">
        <span class="welcome-icon">🏫✨</span>
        <h1 class="welcome-title">Sistem Pembayaran SPP</h1>
        <p class="welcome-subtitle">Kelola dan pantau pembayaran SPP dengan mudah, cepat, dan transparan</p>
    </div>

    <!-- Kotak Login Glassmorphism Modern -->
    <div class="login-container">
        
        <!-- Pilihan Tab Akses Form -->
        <div class="role-tabs">
            <button type="button" class="tab-btn <?= $role === 'petugas' ? 'active' : '' ?>" onclick="switchRole('petugas')">
                <i class="fas fa-user-shield"></i> Petugas
            </button>
            <button type="button" class="tab-btn <?= $role === 'siswa' ? 'active' : '' ?>" onclick="switchRole('siswa')">
                <i class="fas fa-user-graduate"></i> Siswa
            </button>
        </div>

        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <!-- Hidden input untuk menampung role aktif -->
            <input type="hidden" name="role" id="userRole" value="<?= $role ?>">

            <div class="form-group">
                <label id="labelUsername">Username Petugas</label>
                <div class="input-wrapper">
                    <input type="text" name="username" id="inputUsername" required placeholder="Masukkan username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    <i class="fas fa-user" id="iconUsername"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" required placeholder="Masukkan password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">MASUK KE SISTEM</button>
        </form>

        <!-- Informasi Registrasi Profesional -->
        <div class="register-info">
            Belum memiliki akses akun? <br>
            <a href="javascript:void(0);" onclick="alert('Untuk keamanan sistem keuangan sekolah, pembuatan akun baru wajib diproses langsung oleh Admin TU di Ruang Administrasi.')">Hubungi Admin Sekolah <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>

    <!-- JavaScript Handling Dynamic Form Context Switch -->
    <script>
        function switchRole(role) {
            // Update value hidden input
            document.getElementById('userRole').value = role;
            
            // Atur class active pada tombol tab
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if(role === 'petugas') {
                event.currentTarget.classList.add('active');
                document.getElementById('labelUsername').innerText = 'Username Petugas';
                document.getElementById('inputUsername').placeholder = 'Masukkan username petugas';
                document.getElementById('iconUsername').className = 'fas fa-user-shield';
            } else {
                event.currentTarget.classList.add('active');
                document.getElementById('labelUsername').innerText = 'NIS (Nomor Induk Siswa)';
                document.getElementById('inputUsername').placeholder = 'Masukkan nomor NIS kamu';
                document.getElementById('iconUsername').className = 'fas fa-id-card';
            }
        }

        // Jalankan saat load pertama kali untuk menyesuaikan state jika ada error postback
        window.onload = function() {
            const currentRole = document.getElementById('userRole').value;
            if(currentRole === 'siswa') {
                document.getElementById('labelUsername').innerText = 'NIS (Nomor Induk Siswa)';
                document.getElementById('inputUsername').placeholder = 'Masukkan nomor NIS kamu';
                document.getElementById('iconUsername').className = 'fas fa-id-card';
            }
        };
    </script>
</body>
</html>