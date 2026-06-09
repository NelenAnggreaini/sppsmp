<?php
/**
 * Halaman Riwayat Pembayaran + Form Input Pembayaran
 */
$page_title = "Transaksi Pembayaran";

require_once '../includes/header.php';

$pdo = koneksi();

// Filter berdasarkan bulan/tahun jika ada
$bulan_filter = $_GET['bulan'] ?? date('m');
$tahun_filter = $_GET['tahun'] ?? date('Y');
$nis_filter = $_GET['nis'] ?? '';

// Ambil daftar siswa untuk dropdown
$stmt_siswa = $pdo->query("SELECT nis, nama, kelas FROM siswa ORDER BY kelas, nama");
$siswa_list = $stmt_siswa->fetchAll();

// Ambil data pembayaran dengan filter
$query = "SELECT p.*, s.nama, s.kelas, pet.nama as petugas_nama 
          FROM pembayaran p 
          JOIN siswa s ON p.nis = s.nis 
          LEFT JOIN petugas pet ON p.petugas_id = pet.id 
          WHERE 1=1";
$params = [];

if ($bulan_filter) {
    $query .= " AND MONTH(tgl_bayar) = ? AND YEAR(tgl_bayar) = ?";
    $params[] = $bulan_filter;
    $params[] = $tahun_filter;
}

if ($nis_filter) {
    $query .= " AND p.nis = ?";
    $params[] = $nis_filter;
}

$query .= " ORDER BY p.tgl_bayar DESC, p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pembayaran_list = $stmt->fetchAll();

$total_pembayaran = 0;
foreach ($pembayaran_list as $p) {
    $total_pembayaran += $p['jumlah_bayar'];
}
?>

<?php if (isset($_GET['success'])): ?>
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
    <span style="letter-spacing: 0.5px;"><?= $_GET['success'] === 'delete' ? 'Pembayaran Berhasil Dihapus!' : 'Pembayaran Berhasil Disimpan!' ?></span>
</div>

<script>
    setTimeout(function() {
        var toast = document.getElementById('toastAlert');
        if(toast) {
            toast.style.transition = 'all 0.6s ease';
            toast.style.opacity = '0';
            toast.style.top = '0px';
            setTimeout(function() { 
                toast.remove();
                // Refresh halaman setelah toast hilang
                window.location.href = window.location.pathname;
            }, 600);
        }
    }, 3000);
</script>

<style>
@keyframes slideDownCenter {
    from { transform: translate(-50%, -100px); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
}
</style>
<?php endif; ?>

<div class="card" id="card-input-pembayaran" style="margin-bottom: 2rem; text-align: left !important;">
    <div class="card-header" style="border-left: 5px solid #2563EB; padding: 20px; background: #EFF6FF; text-align: center;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px; justify-content: center;">
            <i class="fas fa-credit-card" style="font-size: 1.3rem; color: #2563EB;"></i>
            <h3 style="margin: 0; color: #1E3A5F; font-weight: 700; font-size: 1.1rem;">Input Pembayaran Baru</h3>
        </div>
        <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Silakan lengkapi data pembayaran siswa.</p>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" style="margin-top: 12px; border-radius: 8px; padding: 10px 14px;">
                <i class="fas fa-exclamation-triangle"></i> <?= urldecode($_GET['msg'] ?? 'Terjadi kesalahan') ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body" style="padding: 30px; text-align: left !important;">
        <style>
            #card-input-pembayaran {
                display: block !important;
                align-items: initial !important;
                flex-direction: initial !important;
            }
            
            #form-input-pembayaran { 
                text-align: left !important; 
                display: grid !important;
                align-items: start !important;
            }
            #form-input-pembayaran .form-group { 
                text-align: left !important; 
                align-items: start !important;
                display: block !important;
            }
            #form-input-pembayaran label { 
                text-align: left !important; 
                display: block !important;
                align-items: start !important;
            }
        </style>
        <form id="form-input-pembayaran" method="POST" action="proses_bayar.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; width: 100%; margin: 0; padding: 0; text-align: left !important;">
            <div class="form-group" style="width: 100%; text-align: left !important;">
                <label style="display: block; margin-bottom: 8px; color: #1E3A5F; font-weight: 600; font-size: 0.95rem; text-align: left !important;">Siswa <span style="color:#EF4444;">*</span></label>
                <select name="nis" required style="width: 100%; padding: 11px 14px; font-size: 14px; border: 1px solid #BFDBFE; border-radius: 10px; background: white; color: #334155; box-sizing: border-box;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach ($siswa_list as $s): ?>
                        <option value="<?= $s['nis'] ?>"><?= escape($s['nis']) ?> - <?= escape($s['nama']) ?> (<?= $s['kelas'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="width: 100%; text-align: left !important;">
                <label style="display: block; margin-bottom: 8px; color: #1E3A5F; font-weight: 600; font-size: 0.95rem; text-align: left !important;">Jumlah Bayar (Rp) <span style="color:#EF4444;">*</span></label>
                <input type="number" name="jumlah_bayar" min="0" step="5000" required placeholder="150000" style="width: 100%; padding: 11px 14px; font-size: 14px; box-sizing: border-box; border: 1px solid #BFDBFE; border-radius: 10px; background: white; color: #334155;">
            </div>

            <div class="form-group" style="width: 100%; text-align: left !important;">
                <label style="display: block; margin-bottom: 8px; color: #1E3A5F; font-weight: 600; font-size: 0.95rem; text-align: left !important;">Bulan Dibayar <span style="color:#EF4444;">*</span></label>
                <select name="bulan_dibayar" required style="width: 100%; padding: 11px 14px; font-size: 14px; border: 1px solid #BFDBFE; border-radius: 10px; background: white; color: #334155; box-sizing: border-box;">
                    <?php for ($b = 1; $b <= 12; $b++): ?>
                        <option value="<?= $b ?>"><?= date('F', mktime(0, 0, 0, $b)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group" style="width: 100%; text-align: left !important;">
                <label style="display: block; margin-bottom: 8px; color: #1E3A5F; font-weight: 600; font-size: 0.95rem; text-align: left !important;">Tahun Dibayar <span style="color:#EF4444;">*</span></label>
                <input type="number" name="tahun_dibayar" min="2020" max="<?= date('Y') + 1 ?>" value="<?= date('Y') ?>" required style="width: 100%; padding: 11px 14px; font-size: 14px; box-sizing: border-box; border: 1px solid #BFDBFE; border-radius: 10px; background: white; color: #334155;">
            </div>

            <div style="grid-column: span 2; display: flex; justify-content: center; margin-top: 1rem; width: 100%; text-align: center;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 15px; border-radius: 10px; cursor: pointer; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); background: #2563EB; border: none; color: white;">
                    <i class="fas fa-lock"></i> Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" id="card-riwayat-pembayaran" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: flex-start; padding: 20px; background: #EFF6FF; border-left: 5px solid #2563EB;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-history" style="font-size: 1.2rem; color: #2563EB;"></i>
            <h3 style="margin: 0; color: #1E3A5F; font-weight: 700; font-size: 1.1rem;">Riwayat Pembayaran</h3>
        </div>
        <div style="text-align: right; padding-top: 2px;">
            <div style="color: #1E3A5F; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px;">TOTAL:</div>
            <div style="color: #2563EB; font-size: 1.2rem; font-weight: 800; margin-top: 4px;">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="card-body" style="text-align: left !important;">
        <style>
            #card-riwayat-pembayaran {
                display: block !important;
                align-items: initial !important;
                flex-direction: initial !important;
            }
            
            #card-riwayat-pembayaran .card-body {
                display: block !important;
                text-align: left !important;
            }
        </style>
        <?php if (empty($pembayaran_list)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada riwayat pembayaran. Mulai input pembayaran di atas!
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="100">Tanggal</th>
                            <th width="130">NIS</th>
                            <th>Siswa</th>
                            <th>Bulan/Tahun</th>
                            <th width="130">Jumlah</th>
                            <th width="120">Petugas</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembayaran_list as $bayar): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($bayar['tgl_bayar'])) ?></td>
                                <td><strong><?= escape($bayar['nis']) ?></strong></td>
                                <td><?= escape($bayar['nama']) ?> <small>(<?= $bayar['kelas'] ?>)</small></td>
                                <td><?= date('F Y', mktime(0, 0, 0, $bayar['bulan_dibayar'], 1, $bayar['tahun_dibayar'])) ?></td>
                                <td><strong>Rp <?= number_format($bayar['jumlah_bayar'], 0, ',', '.') ?></strong></td>
                                <td><?= escape($bayar['petugas_nama'] ?? 'Manual') ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="../cetak_kwitansi.php?id=<?= $bayar['id']; ?>" target="_blank" class="btn btn-sm" style="background: #F59E0B; color: white; padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; border: none; font-weight: 600;">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="openPaymentDeleteModal('proses_bayar.php?action=delete&id=<?= $bayar['id']; ?>', '<?= escape($bayar['nama']) ?>', '<?= date('F Y', mktime(0, 0, 0, $bayar['bulan_dibayar'], 1)) ?>')" class="btn btn-sm" style="background: #EF4444; color: white; padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; border: none; font-weight: 600;">
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

<!-- Modern Warning Modal Delete Pembayaran -->
<div id="deletePaymentModal" class="warning-modal-overlay" style="display: none;">
    <div class="warning-modal">
        <button class="warning-modal-close" onclick="closePaymentDeleteModal()">×</button>
        
        <div class="warning-modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        
        <h3>Hapus Pembayaran?</h3>
        
        <p class="warning-modal-text">
            Pembayaran dari "<span id="deletePaymentStudent"></span>" untuk bulan <span id="deletePaymentMonth"></span> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!
        </p>
        
        <div class="warning-modal-actions">
            <button class="btn-cancel" onclick="closePaymentDeleteModal()">
                Batal
            </button>
            <button class="btn-confirm" onclick="confirmPaymentDelete()">
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
let deletePaymentUrl = '';

function openPaymentDeleteModal(url, studentName, month) {
    deletePaymentUrl = url;
    document.getElementById('deletePaymentStudent').textContent = studentName;
    document.getElementById('deletePaymentMonth').textContent = month;
    document.getElementById('deletePaymentModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePaymentDeleteModal() {
    document.getElementById('deletePaymentModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    deletePaymentUrl = '';
}

function confirmPaymentDelete() {
    if (deletePaymentUrl) {
        window.location.href = deletePaymentUrl;
    }
}

// Close modal saat tekan ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePaymentDeleteModal();
    }
});

// Close modal saat klik di luar modal
document.getElementById('deletePaymentModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closePaymentDeleteModal();
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>