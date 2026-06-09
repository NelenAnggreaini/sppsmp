<?php
/**
 * Edit data siswa
 */
$page_title = "Edit Data Siswa";

require_once '../includes/header.php';

$petugas = getPetugas();
if ($petugas['level'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$message = '';
$pdo = koneksi();
$nis = $_GET['nis'] ?? '';

if (!$nis) {
    header("Location: index.php");
    exit;
}

// Ambil data siswa yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->execute([$nis]);
$siswa = $stmt->fetch();

if (!$siswa) {
    $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Siswa tidak ditemukan!</div>';
} elseif ($_POST) {
    $new_nis = trim($_POST['nis']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    
    // Validasi
    $errors = [];
    if (empty($new_nis) || empty($nama) || empty($kelas)) {
        $errors[] = "Semua field harus diisi!";
    }
    
    // Check NIS baru sudah ada (kecuali NIS lama)
    if ($new_nis !== $nis) {
        $stmt_check = $pdo->prepare("SELECT nis FROM siswa WHERE nis = ? AND nis != ?");
        $stmt_check->execute([$new_nis, $nis]);
        if ($stmt_check->fetch()) {
            $errors[] = "NIS $new_nis sudah digunakan siswa lain!";
        }
    }
    
    if (empty($errors)) {
        // Update data
        $stmt = $pdo->prepare("UPDATE siswa SET nis = ?, nama = ?, kelas = ? WHERE nis = ?");
        if ($stmt->execute([$new_nis, $nama, $kelas, $nis])) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Data siswa berhasil diupdate! <a href="index.php">Lihat daftar siswa</a></div>';
            $nis = $new_nis; // Update NIS untuk form jika sukses
            $siswa['nis'] = $new_nis;
            $siswa['nama'] = $nama;
            $siswa['kelas'] = $kelas;
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal mengupdate data!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . implode('<br>', $errors) . '</div>';
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-user-edit"></i> Edit Siswa: <?= escape($siswa['nama'] ?? 'Tidak Ditemukan') ?></h2>
    </div>
    <div class="card-body">
        <?= $message ?: '<p class="text-muted">Edit data siswa di bawah ini.</p>' ?>
        
        <?php if ($siswa): ?>
        <form method="POST">
            <div class="form-group">
                <label for="nis"><i class="fas fa-id-card"></i> NIS <span style="color:red;">*</span></label>
                <input type="text" id="nis" name="nis" value="<?= escape($siswa['nis']) ?>" maxlength="20" required>
                <small><?= $nis === $siswa['nis'] ? '' : '<small class="text-warning">NIS lama: ' . escape($nis) . '</small>' ?></small>
            </div>
            
            <div class="form-group">
                <label for="nama"><i class="fas fa-user"></i> Nama Lengkap <span style="color:red;">*</span></label>
                <input type="text" id="nama" name="nama" value="<?= escape($siswa['nama']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kelas"><i class="fas fa-graduation-cap"></i> Kelas <span style="color:red;">*</span></label>
                <select id="kelas" name="kelas" required>
                    <option value="">Pilih Kelas</option>
                    <?php 
                    $kelas_list = ['7A','7B','7C','8A','8B','8C','9A','9B','9C'];
                    foreach($kelas_list as $k): 
                    ?>
                        <option value="<?= $k ?>" <?= ($siswa['kelas'] ?? '') == $k ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="text-align: right; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #BFDBFE;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Siswa
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>

<?php require_once '../includes/footer.php'; ?>
