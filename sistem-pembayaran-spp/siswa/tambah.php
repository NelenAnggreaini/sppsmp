<?php
/**
 * Tambah data siswa baru
 */
$page_title = "Tambah Siswa Baru";

require_once '../includes/header.php';

$petugas = getPetugas();
if ($petugas['level'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$message = '';
$pdo = koneksi();

if ($_POST) {
    $nis = trim($_POST['nis']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    
    // Validasi
    $errors = [];
    if (empty($nis) || empty($nama) || empty($kelas)) {
        $errors[] = "Semua field harus diisi!";
    }
    if (strlen($nis) < 3 || strlen($nis) > 20) {
        $errors[] = "NIS harus 3-20 karakter!";
    }
    
    // Check NIS sudah ada
    $stmt = $pdo->prepare("SELECT nis FROM siswa WHERE nis = ?");
    $stmt->execute([$nis]);
    if ($stmt->fetch()) {
        $errors[] = "NIS $nis sudah terdaftar!";
    }
    
    if (empty($errors)) {
        // Insert data
        $stmt = $pdo->prepare("INSERT INTO siswa (nis, nama, kelas) VALUES (?, ?, ?)");
        if ($stmt->execute([$nis, $nama, $kelas])) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Siswa berhasil ditambahkan! <a href="index.php">Lihat daftar siswa</a></div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal menambah siswa!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . implode('<br>', $errors) . '</div>';
        // Populate form kembali
        $nis = $_POST['nis'];
        $nama = $_POST['nama'];
        $kelas = $_POST['kelas'];
    }
}
?>

<style>
.form-wrapper {
    max-width: 700px !important;
    margin: 40px auto !important;
    padding: 0 !important;
    display: block !important;
    text-align: left !important;
}

.form-wrapper .form-card-header {
    background: #EFF6FF !important;
    border-left: 5px solid #2563EB !important;
    padding: 25px 30px !important;
    border-radius: 20px 20px 0 0 !important;
}

.form-wrapper .form-card-header h2 {
    margin: 0 !important;
    color: #1E3A5F !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.form-wrapper .form-card-header h2 i {
    color: #2563EB !important;
    font-size: 1.8rem !important;
}

.form-wrapper .form-card-body {
    background: #FFFFFF !important;
    border: 1px solid #BFDBFE !important;
    border-top: none !important;
    padding: 30px !important;
    border-radius: 0 0 20px 20px !important;
    box-shadow: 0 10px 30px rgba(30, 58, 95, 0.08) !important;
}

.form-wrapper .form-intro {
    color: #64748B !important;
    font-size: 0.95rem !important;
    margin-bottom: 25px !important;
    font-weight: 500 !important;
}

.form-wrapper .form-group {
    margin-bottom: 20px !important;
    display: block !important;
}

.form-wrapper .form-group label {
    display: block !important;
    margin-bottom: 8px !important;
    color: #1E3A5F !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
}

.form-wrapper .form-group label i {
    color: #2563EB !important;
    margin-right: 6px !important;
}

.form-wrapper .form-group label .required {
    color: #EF4444 !important;
    font-weight: 800 !important;
    margin-left: 3px !important;
}

.form-wrapper input[type="text"],
.form-wrapper select {
    width: 100% !important;
    padding: 12px 16px !important;
    border: 1px solid #BFDBFE !important;
    border-radius: 10px !important;
    font-size: 0.95rem !important;
    font-family: 'Quicksand', sans-serif !important;
    color: #334155 !important;
    background: #FFFFFF !important;
    transition: 0.3s !important;
    box-sizing: border-box !important;
}

.form-wrapper input[type="text"]:focus,
.form-wrapper select:focus {
    border-color: #2563EB !important;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12) !important;
    outline: none !important;
}

.form-wrapper small {
    display: block !important;
    margin-top: 6px !important;
    color: #94A3B8 !important;
    font-size: 0.85rem !important;
}

.form-wrapper .form-actions {
    display: flex !important;
    gap: 12px !important;
    margin-top: 30px !important;
    padding-top: 25px !important;
    border-top: 1px solid #E2E8F0 !important;
    justify-content: flex-end !important;
}

.form-wrapper .btn-submit {
    background: #2563EB !important;
    color: white !important;
    border: none !important;
    padding: 12px 28px !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    font-size: 0.95rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: 0.3s !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
}

.form-wrapper .btn-submit:hover {
    background: #1D4ED8 !important;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3) !important;
    transform: translateY(-2px) !important;
}

.form-wrapper .btn-cancel {
    background: #F1F5F9 !important;
    color: #64748B !important;
    border: 1px solid #BFDBFE !important;
    padding: 12px 28px !important;
    border-radius: 10px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    font-size: 0.95rem !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: 0.3s !important;
}

.form-wrapper .btn-cancel:hover {
    background: #E2E8F0 !important;
    color: #334155 !important;
    border-color: #CBD5E1 !important;
}

.alert {
    padding: 16px 20px !important;
    border-radius: 12px !important;
    margin-bottom: 20px !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 12px !important;
    font-weight: 500 !important;
}

.alert-success {
    background: #DCFCE7 !important;
    color: #15803D !important;
    border: 1px solid #BBF7D0 !important;
}

.alert-success i {
    color: #22C55E !important;
    margin-top: 2px !important;
}

.alert-success a {
    color: #15803D !important;
    text-decoration: underline !important;
    font-weight: 700 !important;
}

.alert-danger {
    background: #EFF6FF !important;
    color: #1E3A5F !important;
    border: 1px solid #BFDBFE !important;
}

.alert-danger i {
    color: #EF4444 !important;
    margin-top: 2px !important;
}
</style>

<div class="form-wrapper">
    <div class="form-card-header">
        <h2>
            <i class="fas fa-user-plus"></i>
            Tambah Siswa Baru
        </h2>
    </div>

    <div class="form-card-body">
        <?php if ($message): ?>
            <?= $message ?>
        <?php else: ?>
            <p class="form-intro">
                <i class="fas fa-info-circle" style="color: #2563EB; margin-right: 8px;"></i>
                Isi form di bawah untuk menambahkan siswa baru ke dalam sistem.
            </p>
        <?php endif; ?>
        
        <form method="POST" style="margin-top: 20px;">
            <div class="form-group">
                <label for="nis">
                    <i class="fas fa-id-card"></i>
                    NIS
                    <span class="required">*</span>
                </label>
                <input type="text" id="nis" name="nis" value="<?= escape($nis ?? '') ?>" maxlength="20" required placeholder="Contoh: 001, S00123">
                <small><i class="fas fa-lightbulb" style="color: #2563EB;"></i> NIS harus unik untuk setiap siswa</small>
            </div>
            
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-user"></i>
                    Nama Lengkap
                    <span class="required">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="<?= escape($nama ?? '') ?>" required placeholder="Masukkan nama lengkap siswa">
            </div>
            
            <div class="form-group">
                <label for="kelas">
                    <i class="fas fa-graduation-cap"></i>
                    Kelas
                    <span class="required">*</span>
                </label>
                <select id="kelas" name="kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php 
                    $kelas_list = ['7A','7B','7C','8A','8B','8C','9A','9B','9C'];
                    foreach($kelas_list as $k): 
                    ?>
                        <option value="<?= $k ?>" <?= ($kelas ?? '') == $k ? 'selected' : '' ?>>Kelas <?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <a href="index.php" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Simpan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
