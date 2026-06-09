<?php
/**
 * Halaman CRUD Siswa - Hanya Admin yang bisa akses
 */
$page_title = "Kelola Data Siswa";

require_once '../includes/header.php';

$petugas = getPetugas();
if ($petugas['level'] !== 'admin') {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Akses ditolak! Hanya admin yang bisa mengelola data siswa.</div>';
    require_once '../includes/footer.php';
    exit;
}

// Inisialisasi variabel
$message = '';
$pdo = koneksi();

// Proses hapus
if (isset($_GET['hapus']) && $_GET['hapus']) {
    $stmt = $pdo->prepare("DELETE FROM siswa WHERE nis = ?");
    if ($stmt->execute([$_GET['hapus']])) {
        header("Location: index.php?success=delete");
        exit;
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal menghapus data!</div>';
    }
}

// Ambil semua data siswa
$stmt = $pdo->query("SELECT * FROM siswa ORDER BY kelas, nis");
$siswa_list = $stmt->fetchAll();
?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'delete'): ?>
<div id="toastAlert" style="
    position: fixed !important;
    top: 40px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background: #28a745;
    color: white;
    padding: 18px 35px;
    border-radius: 50px;
    box-shadow: 0 15px 40px rgba(40, 167, 69, 0.3);
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 9999999 !important;
    font-family: 'Quicksand', sans-serif;
    font-weight: 700;
    min-width: 300px;
    justify-content: center;
    border: 2px solid rgba(255,255,255,0.2);
    animation: slideDownCenter 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
">
    <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
    <span style="letter-spacing: 0.5px;">Data Siswa Berhasil Dihapus!</span>
</div>

<script>
    setTimeout(function() {
        var toast = document.getElementById('toastAlert');
        if(toast) {
            toast.style.transition = 'all 0.6s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(-100px)';
            setTimeout(function() {
                toast.remove();
                window.location.href = 'index.php';
            }, 600);
        }
    }, 3000);
</script>
<?php endif; ?>

<style>
    @keyframes slideDownCenter {
        from {
            transform: translateX(-50%) translateY(-100px);
            opacity: 0;
        }
        to {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }

    /* ==========================================================================
       1. BANTAI JUDUL NAVBAR ATAS YANG WARNA MAROON (image_01c27f.png)
       ========================================================================== */
    /* Kita tembak langsung semua elemen teks di dalam navbar atas agar berubah jadi putih bersih / light blue */
    header, .navbar, .topbar, [class*="nav"], [class*="header"] {
        color: #FFFFFF !important;
    }
    
    /* Paksa teks "Sistem Pembayaran SPP SMP" di navbar berubah menjadi putih bersinar */
    header h1, header h2, header h3, header span, header a,
    .navbar h1, .navbar h2, .navbar a, .navbar span,
    div[style*="color"] h1, div[style*="color"] h2,
    span[style*="color"], a[style*="color"],
    header *, .navbar * {
        /* Jika elemen tersebut adalah teks judul, paksa warnanya jadi putih, bukan maroon! */
        color: #FFFFFF !important; 
    }

    /* ==========================================================================
       2. HANCURKAN LAPISAN BACKGROUND & BAYANGAN MAROON/PINK (image_01cd0b.jpg)
       ========================================================================== */
    /* Menghilangkan paksa kotak/container luar yang memberikan efek bayangan pink kaku */
    main, .container, .main-content, .wrapper, div[class*="content"], .dashboard-full {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* Ubah Background halaman dasar menjadi soft blue clean */
    body, html {
        background: #EFF6FF !important;
        background-image: 
            radial-gradient(circle at 10% 20%, rgba(96, 165, 250, 0.08) 0%, transparent 50%),
            linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%) !important;
        background-attachment: fixed !important;
    }

    /* Card Box Utama (Hanya 1 lapisan putih bersih dengan border biru lembut) */
    .card, .glass-card {
        background: #FFFFFF !important;
        border: 1px solid #BFDBFE !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 30px rgba(30, 58, 95, 0.06) !important;
        padding: 25px !important;
        margin-top: 20px !important;
    }

    /* Header Box Dalam Card */
    .card-header {
        background: #EFF6FF !important;
        border-bottom: 1px solid #BFDBFE !important;
        border-left: 5px solid #2563EB !important;
        border-radius: 12px !important;
        padding: 15px 25px !important;
        margin-bottom: 20px !important;
    }

    .card-header h2 {
        color: #1E3A5F !important;
        font-weight: 700 !important;
        margin: 0 !important;
        font-size: 1.4rem !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header h2 i {
        color: #2563EB !important;
    }

    /* Tombol Tambah Siswa Baru */
    .btn-primary {
        background: #2563EB !important;
        border: none !important;
        color: white !important;
        padding: 12px 25px !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
        transition: 0.3s !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
    }

    .btn-primary:hover {
        background: #1D4ED8 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.3) !important;
    }

    /* Desain Tabel Modern */
    table {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        border: 1px solid #E2E8F0 !important;
    }

    th {
        background: #1E3A5F !important; /* Navy Blue Header */
        color: #FFFFFF !important;
        font-weight: 700 !important;
        padding: 16px !important;
        text-transform: uppercase !important;
        font-size: 0.85rem !important;
        letter-spacing: 0.5px !important;
        border: none !important;
    }

    th i {
        color: #93C5FD !important;
        margin-right: 5px;
    }

    td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #E2E8F0 !important;
        color: #334155 !important;
        background: #FFFFFF !important;
        font-weight: 500 !important;
    }

    tr:last-child td {
        border-bottom: none !important;
    }

    tr:hover td {
        background: #F8FAFC !important;
    }

    /* Badge Kelas */
    .badge {
        background: #E0F2FE !important;
        color: #0369A1 !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        border: 1px solid #BAE6FD !important;
    }

    /* Tombol Edit di Tabel */
    .btn-group .btn-primary {
        background: #FEF3C7 !important;
        color: #D97706 !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        font-size: 0.9rem !important;
    }
    .btn-group .btn-primary:hover {
        background: #FDE68A !important;
        color: #B45309 !important;
        transform: none !important;
    }

    /* Tombol Hapus di Tabel (Steril Tanpa Efek Shadow Maroon) */
    .btn-group .btn-danger {
        background: #F1F5F9 !important; 
        color: #64748B !important;      
        border: none !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
        font-size: 0.9rem !important;
        transition: 0.3s !important;
        cursor: pointer;
        box-shadow: none !important;
    }
    .btn-group .btn-danger:hover {
        background: #1E3A5F !important; 
        color: #FFFFFF !important;
    }
    
    /* Perbaikan Alert */
    .alert-success {
        background: #DCFCE7 !important;
        color: #15803D !important;
        border: 1px solid #BBF7D0 !important;
        border-radius: 12px !important;
        padding: 15px !important;
    }
    .alert-danger {
        background: #EFF6FF !important;
        color: #1E3A5F !important;
        border: 1px solid #BFDBFE !important;
        border-radius: 12px !important;
        padding: 15px !important;
    }

    /* ==========================================================================
       3. BANTAI FOOTER MAROON DI BAGIAN BAWAH SEKARANG JUGA
       ========================================================================== */
    footer, .footer, [class*="footer"], div[style*="background"], footer * {
        background: #DBEAFE !important; 
        background-color: #DBEAFE !important;
        color: #1E3A5F !important;
        border-top: 1px solid #BFDBFE !important;
    }
</style>

<div class="card" id="card-data-siswa" style="margin-bottom: 2rem; text-align: left !important;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #EFF6FF; border-left: 5px solid #2563EB;">
        <h2 style="margin: 0; color: #1E3A5F; font-weight: 700; font-size: 1.1rem;"><i class="fas fa-users"></i> Data Siswa (<?= count($siswa_list) ?> siswa)</h2>
        <a href="tambah.php" class="btn btn-primary" style="background: #2563EB; color: white; padding: 12px 25px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none;">
            <i class="fas fa-plus"></i> Tambah Siswa Baru
        </a>
    </div>
    <div class="card-body" style="text-align: left !important;">
        <style>
            #card-data-siswa {
                display: block !important;
                align-items: initial !important;
                flex-direction: initial !important;
            }
            
            #card-data-siswa .card-body {
                display: block !important;
                text-align: left !important;
            }
        </style>
        <?= $message ?>
        
        <?php if (empty($siswa_list)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Belum ada data siswa. <a href="tambah.php">Tambah siswa pertama</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="120"><i class="fas fa-id-card"></i> NIS</th>
                            <th><i class="fas fa-user"></i> Nama</th>
                            <th width="100"><i class="fas fa-graduation-cap"></i> Kelas</th>
                            <th width="200"><i class="fas fa-calendar"></i> Dibuat</th>
                            <th width="180"><i class="fas fa-cogs"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_list as $siswa): ?>
                        <tr>
                            <td><strong><?= escape($siswa['nis']) ?></strong></td>
                            <td><?= escape($siswa['nama']) ?></td>
                            <td><span class="badge"><?= escape($siswa['kelas']) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($siswa['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="edit.php?nis=<?= $siswa['nis'] ?>" class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" 
                                       class="btn btn-danger" 
                                       onclick="openDeleteModal('?hapus=<?= $siswa['nis'] ?>', '<?= escape($siswa['nama']) ?>')"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modern Warning Modal Delete -->
<div id="deleteWarningModal" class="warning-modal-overlay" style="display: none;">
    <div class="warning-modal">
        <button class="warning-modal-close" onclick="closeDeleteModal()">×</button>
        
        <div class="warning-modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        
        <h3 id="deleteStudentName">Hapus Siswa?</h3>
        
        <p class="warning-modal-text">
            Data siswa "<span id="deleteStudentNameText"></span>" dan semua informasi yang terkait akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!
        </p>
        
        <div class="warning-modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">
                Batal
            </button>
            <button class="btn-confirm" onclick="confirmDelete()">
                Ya, Hapus!
            </button>
        </div>
    </div>
</div>

<style>
.warning-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.warning-modal {
    background: #FFFFFF;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    max-width: 420px;
    width: 90%;
    padding: 40px 30px 30px;
    position: relative;
    animation: warningSlideIn 0.4s ease-out;
}

@keyframes warningSlideIn {
    from {
        transform: scale(0.85) translateY(-50px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.warning-modal-close {
    position: absolute;
    top: 12px;
    right: 12px;
    background: none;
    border: none;
    font-size: 2.2rem;
    color: #CBD5E1;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.warning-modal-close:hover {
    color: #1E3A5F;
}

.warning-modal-icon {
    width: 80px;
    height: 80px;
    background: #FEE2E2;
    border: 3px solid #FECACA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2.2rem;
    color: #DC2626;
}

.warning-modal h3 {
    text-align: center;
    color: #1E3A5F;
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0 0 12px 0;
}

.warning-modal-text {
    text-align: center;
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0 0 28px 0;
}

.warning-modal-text span {
    font-weight: 700;
    color: #1E3A5F;
}

.warning-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-cancel, .btn-confirm {
    padding: 12px 32px;
    border-radius: 8px;
    border: none;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
}

.btn-cancel {
    background: #F1F5F9;
    color: #64748B;
    border: 1px solid #BFDBFE;
}

.btn-cancel:hover {
    background: #E2E8F0;
    color: #334155;
    border-color: #CBD5E1;
}

.btn-confirm {
    background: #DC2626;
    color: white;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.btn-confirm:hover {
    background: #B91C1C;
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
    transform: translateY(-2px);
}
</style>

<script>
let deleteUrl = '';
let studentName = '';

function openDeleteModal(url, name) {
    deleteUrl = url;
    studentName = name;
    document.getElementById('deleteStudentName').textContent = 'Hapus ' + name + '?';
    document.getElementById('deleteStudentNameText').textContent = name;
    document.getElementById('deleteWarningModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteWarningModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    deleteUrl = '';
    studentName = '';
}

function confirmDelete() {
    if (deleteUrl) {
        window.location.href = deleteUrl;
    }
}

// Close modal saat tekan ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal saat klik di luar modal
document.getElementById('deleteWarningModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closeDeleteModal();
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>