<?php
require_once 'config/config.php';
$petugas = getPetugas();

// Proteksi halaman
if (!$petugas) {
    header("Location: auth/login.php");
    exit;
}

$pdo = koneksi();

try {
    // Query aman: Hanya join ke tabel siswa
    $query = "SELECT pembayaran.*, siswa.nama as nama_siswa, siswa.kelas
              FROM pembayaran 
              JOIN siswa ON pembayaran.nis = siswa.nis 
              ORDER BY pembayaran.id DESC";
    
    $rekap = $pdo->query($query)->fetchAll();
    
    // Hitung statistik untuk print
    $total_uang = 0;
    $total_transaksi = count($rekap);
    foreach ($rekap as $row) {
        $total_uang += $row['jumlah_bayar'];
    }
    
    // Format tanggal cetak
    $tgl_cetak = date('d F Y');
    $bulan_tahun_sekarang = date('F Y');
} catch (PDOException $e) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; font-family: Quicksand;'>";
    echo "<h3>Aduh jirr, ada error database:</h3>" . $e->getMessage();
    echo "</div>";
    exit;
}

$page_title = "Laporan Rekapitulasi";
include 'includes/header.php';
?>

<style>
/* SCREEN VIEW STYLES */
.print-letterhead,
.print-title,
.print-periode,
.print-summary,
.print-footer {
    display: none !important;
}

.rekap-wrapper {
    max-width: 1140px !important;
    margin: 20px 40px !important;
    padding: 0 !important;
    display: block !important;
    text-align: left !important;
}

.rekap-wrapper .rekap-header {
    background: #EFF6FF !important;
    border-left: 5px solid #2563EB !important;
    padding: 20px 30px !important;
    border-radius: 20px 20px 0 0 !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-bottom: 0 !important;
}

.rekap-wrapper .rekap-header > div:first-child h2 {
    margin: 0 !important;
    color: #1E3A5F !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
}

.rekap-wrapper .rekap-header > div:first-child p {
    margin: 8px 0 0 0 !important;
    color: #64748B !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
}

.rekap-wrapper .btn-cetak {
    background: #2563EB !important;
    color: white !important;
    border: none !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: 0.3s !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
    font-size: 0.95rem !important;
}

.rekap-wrapper .btn-cetak:hover {
    background: #1D4ED8 !important;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3) !important;
}

.rekap-wrapper .rekap-table {
    background: #FFFFFF !important;
    border: 1px solid #BFDBFE !important;
    border-top: none !important;
    padding: 30px !important;
    border-radius: 0 0 20px 20px !important;
    box-shadow: 0 10px 30px rgba(30, 58, 95, 0.08) !important;
}

.rekap-wrapper table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 0.95rem !important;
}

.rekap-wrapper table thead {
    background: #1E3A5F !important;
    color: white !important;
}

.rekap-wrapper table thead th {
    padding: 15px 12px !important;
    text-align: left !important;
    font-weight: 600 !important;
    border: none !important;
}

.rekap-wrapper table tbody td {
    padding: 15px 12px !important;
    border-bottom: 1px solid #E2E8F0 !important;
    color: #334155 !important;
}

.rekap-wrapper table tbody tr:last-child td {
    border-bottom: none !important;
}

.rekap-wrapper .total-row td {
    background: #EFF6FF !important;
    color: #1E3A5F !important;
    font-weight: 800 !important;
    border-top: 2px solid #2563EB !important;
    border-bottom: none !important;
}

.rekap-wrapper .total-row td:last-child {
    color: #2563EB !important;
}

/* ===== PRINT STYLES ===== */
@media print {
    * {
        margin: 0;
        padding: 0;
    }

    body, html {
        background: white;
        font-family: Arial, sans-serif;
        width: 100%;
        height: 100%;
    }

    header, .header, nav, .back-nav, footer, .footer {
        display: none !important;
    }

    .rekap-wrapper {
        margin: 0 !important;
        padding: 25px 30px !important;
        max-width: 100% !important;
        width: 100%;
    }

    .rekap-wrapper .rekap-header {
        display: none !important;
    }

    .rekap-wrapper .rekap-table {
        background: white !important;
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    /* KOP SURAT */
    .print-letterhead {
        display: flex !important;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 3px solid #1E3A5F;
        page-break-inside: avoid;
    }

    .letterhead-left {
        flex: 1;
    }

    .letterhead-center {
        flex: 2;
        text-align: center;
    }

    .letterhead-right {
        flex: 1;
        text-align: right;
        font-size: 7.5pt;
        line-height: 1.3;
    }

    .letterhead-center .gov-text {
        font-size: 7pt;
        color: #1E3A5F;
        font-weight: bold;
    }

    .letterhead-center .school-name {
        font-size: 14pt;
        font-weight: 900;
        color: #1E3A5F;
        margin-top: 1px;
        margin-bottom: 2px;
    }

    .letterhead-center .school-addr {
        font-size: 7pt;
        color: #555;
        margin: 0.5px 0;
    }

    .letterhead-right .label {
        font-weight: bold;
        color: #1E3A5F;
        display: inline;
    }

    .letterhead-right .value {
        color: #333;
        display: inline;
    }

    /* MAIN TITLE */
    .print-title {
        text-align: center;
        margin: 10px 0 8px 0;
        display: block !important;
        page-break-inside: avoid;
    }

    .print-title h1 {
        font-size: 12pt;
        font-weight: 900;
        color: #1E3A5F;
        margin: 0;
        display: block !important;
        text-align: center;
    }

    /* PERIODE BADGE */
    .print-periode {
        text-align: center;
        margin: 0 0 10px 0;
        display: block !important;
        page-break-inside: avoid;
    }

    .periode-badge {
        background: #1E3A5F;
        color: white;
        padding: 3px 12px;
        border-radius: 20px;
        display: inline-block !important;
        font-weight: bold;
        font-size: 7.5pt;
    }

    /* TABLE CONTAINER */
    .print-table-wrapper {
        margin-bottom: 8px;
    }

    .print-table-wrapper table {
        width: 100% !important;
        border-collapse: collapse;
        font-size: 7pt;
        margin: 0 auto;
    }

    .print-table-wrapper thead {
        background: #1E3A5F;
        color: white;
    }

    .print-table-wrapper th {
        padding: 5px 3px !important;
        text-align: center;
        font-weight: bold;
        border: 1px solid #000;
        font-size: 6.5pt;
    }

    .print-table-wrapper td {
        padding: 4px 3px !important;
        border: 1px solid #999;
        font-size: 7pt;
        text-align: center;
    }

    .print-table-wrapper .no-col { text-align: center; }
    .print-table-wrapper .nis-col { text-align: center; }
    .print-table-wrapper .kelas-col { text-align: center; }
    .print-table-wrapper .jumlah-col { text-align: right; padding-right: 6px !important; }

    .print-table-wrapper tbody tr:nth-child(odd) {
        background: white;
    }

    .print-table-wrapper tbody tr:nth-child(even) {
        background: #f9f9f9;
    }

    .print-table-wrapper .total-row td {
        background: #f0f0f0 !important;
        font-weight: bold;
        border-top: 2px solid #1E3A5F;
        text-align: right;
        padding: 5px 3px !important;
    }

    /* SUMMARY BOXES */
    .print-summary {
        display: flex !important;
        gap: 10px;
        margin: 8px 0;
        page-break-inside: avoid;
        justify-content: center;
    }

    .summary-box {
        border: 2px solid #BFDBFE;
        border-radius: 4px;
        padding: 8px 10px;
        text-align: center;
        flex: 0 1 200px;
        background: white;
    }

    .summary-box-label {
        font-size: 6pt;
        color: #555;
        font-weight: bold;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }

    .summary-box-icon {
        font-size: 10pt;
        color: #2563EB;
    }

    .summary-box-value {
        font-size: 11pt;
        font-weight: 900;
        color: #1E3A5F;
    }

    /* FOOTER */
    .print-footer {
        margin-top: 8px;
        text-align: center;
        display: block !important;
        page-break-inside: avoid;
    }

    .footer-info {
        font-size: 7pt;
        color: #555;
        margin-bottom: 0;
        text-align: right;
    }

    .signature-section {
        margin-top: 12px;
        width: 160px;
        text-align: center;
        margin-left: auto;
        margin-right: 0;
    }

    .signature-space {
        height: 30px;
        border-bottom: 1px solid #333;
    }

    .signature-name {
        font-weight: bold;
        font-size: 7pt;
        margin-top: 1px;
    }
}
</style>

<div class="rekap-wrapper">
    <div class="rekap-header">
        <div>
            <h2>
                <i class="fas fa-file-invoice-dollar"></i>
                Rekapitulasi Pembayaran
            </h2>
            <p>Seluruh riwayat transaksi pembayaran siswa.</p>
        </div>
        <button onclick="window.print()" class="btn-cetak">
            <i class="fas fa-print"></i>
            Cetak Laporan
        </button>
    </div>

    <div class="rekap-table">
        <!-- PRINT LETTERHEAD -->
        <div class="print-letterhead">
            <div class="letterhead-left"></div>
            <div class="letterhead-center">
                <div class="gov-text">PEMERINTAH KABUPATEN LAMPUNG SELATAN</div>
                <div class="school-name">SMP NEGERI 1 LAMPUNG SELATAN</div>
                <div class="school-addr">Jl. Pendidikan No. 01, Kalianda, Lampung Selatan</div>
                <div class="school-addr">Telp. (0727) 1234567 | Email: smpn1ls@gmail.com</div>
            </div>
            <div class="letterhead-right">
                <div><span class="label">Tanggal Cetak</span> <span class="value">: <?= $tgl_cetak ?></span></div>
                <div><span class="label">Dicetak Oleh</span> <span class="value">: <?= escape($petugas['nama']) ?></span></div>
                <div><span class="label">Periode</span> <span class="value">: <?= $bulan_tahun_sekarang ?></span></div>
            </div>
        </div>

        <!-- PRINT TITLE -->
        <div class="print-title">
            <h1>Laporan Rekapitulasi Pembayaran SPP</h1>
        </div>

        <!-- PRINT PERIODE BADGE -->
        <div class="print-periode">
            <div class="periode-badge">Periode : <?= $bulan_tahun_sekarang ?></div>
        </div>

        <!-- PRINT TABLE WRAPPER -->
        <div class="print-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th class="nis-col">NIS</th>
                        <th>Nama Siswa</th>
                        <th class="kelas-col">Kelas</th>
                        <th>Bulan Dibayar</th>
                        <th>Tahun Dibayar</th>
                        <th class="jumlah-col">Jumlah Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_transaksi > 0): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($rekap as $row): ?>
                        <tr>
                            <td class="no-col"><?= $no++ ?></td>
                            <td class="nis-col"><?= escape($row['nis']) ?></td>
                            <td><strong><?= escape($row['nama_siswa']) ?></strong></td>
                            <td class="kelas-col"><?= escape($row['kelas']) ?></td>
                            <td><?= ucfirst(escape($row['bulan_dibayar'])) ?></td>
                            <td><?= escape($row['tahun_dibayar']) ?></td>
                            <td class="jumlah-col">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr class="total-row">
                            <td colspan="6" style="text-align: right;">TOTAL TRANSAKSI / PENDAPATAN :</td>
                            <td class="jumlah-col">Rp <?= number_format($total_uang, 0, ',', '.') ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 30px; text-align: center; color: #64748B; font-style: italic;">Belum ada riwayat transaksi pembayaran SPP yang tercatat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PRINT SUMMARY BOXES -->
        <div class="print-summary">
            <div class="summary-box">
                <div class="summary-box-label">
                    <i class="fas fa-file-alt summary-box-icon"></i>
                    Total Transaksi
                </div>
                <div class="summary-box-value"><?= $total_transaksi ?></div>
            </div>
            <div class="summary-box">
                <div class="summary-box-label">
                    <i class="fas fa-money-bill summary-box-icon"></i>
                    Total Pendapatan
                </div>
                <div class="summary-box-value">Rp <?= number_format($total_uang, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- PRINT FOOTER -->
        <div class="print-footer">
            <div class="footer-info">Lampung Selatan, <?= $tgl_cetak ?></div>
            <div class="footer-info" style="font-weight: 700; margin-top: 5px;">Administrator</div>
            <div class="signature-section">
                <div class="signature-space"></div>
                <div class="signature-name"><?= escape($petugas['nama']) ?></div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>