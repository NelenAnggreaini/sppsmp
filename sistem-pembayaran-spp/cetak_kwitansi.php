<?php
/**
 * Halaman Cetak Kwitansi Pembayaran SPP
 * Menampilkan bukti pembayaran dalam format nota resmi ukuran A5
 */

require_once 'config/config.php';

// Keamanan: hanya petugas yang login
if (!isset($_SESSION['petugas_id'])) {
    die('<h3>Akses ditolak</h3><p>Silakan <a href="auth/login.php">login</a> terlebih dahulu.</p>');
}

// Validasi parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('<h3>Error</h3><p>ID pembayaran tidak ditemukan.</p>');
}

$id = intval($_GET['id']);
$pdo = koneksi();

// Ambil data pembayaran lengkap dengan join ke siswa dan petugas
$stmt = $pdo->prepare("
    SELECT 
        p.id,
        p.tgl_bayar,
        p.bulan_dibayar,
        p.tahun_dibayar,
        p.jumlah_bayar,
        s.nis,
        s.nama AS nama_siswa,
        s.kelas,
        pet.nama AS nama_petugas
    FROM pembayaran p
    JOIN siswa s ON p.nis = s.nis
    LEFT JOIN petugas pet ON p.petugas_id = pet.id
    WHERE p.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die('<h3>Error</h3><p>Data pembayaran tidak ditemukan.</p>');
}

// Helper format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Helper nama bulan Indonesia
$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$bulanText = $namaBulan[(int)$data['bulan_dibayar']] ?? '-';
$tglBayar = date('d', strtotime($data['tgl_bayar'])) . ' ' .
            $namaBulan[(int)date('m', strtotime($data['tgl_bayar']))] . ' ' .
            date('Y', strtotime($data['tgl_bayar']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran SPP - <?= escape($data['nama_siswa']) ?></title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            color: #222;
            padding: 20px;
        }

        /* Ukuran kertas A5 */
        .kwitansi {
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #bbb;
            padding: 25mm 15mm;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Kop Surat */
        .kop {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .kop h2 {
            font-size: 1.4rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .kop p {
            font-size: 0.85rem;
            color: #555;
        }

        /* Nomor Kwitansi */
        .no-kwitansi {
            text-align: right;
            font-size: 0.85rem;
            margin-bottom: 20px;
            color: #555;
        }

        /* Data Pembayaran */
        .data-section {
            margin-bottom: 30px;
        }

        .data-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-section td {
            padding: 8px 0;
            font-size: 0.95rem;
            vertical-align: top;
        }

        .data-section td:first-child {
            width: 38%;
            font-weight: 600;
            color: #444;
        }

        .data-section td:nth-child(2) {
            width: 2%;
        }

        .nominal-box {
            margin-top: 15px;
            padding: 12px 15px;
            border: 1px solid #2c3e50;
            background: #f9fafb;
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        /* Tanda Tangan */
        .ttd-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            font-size: 0.9rem;
        }

        .ttd-box p {
            margin-bottom: 60px;
        }

        .ttd-line {
            display: inline-block;
            width: 80%;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: 600;
        }

        /* Stempel (opsional visual) */
        .stamp {
            position: absolute;
            bottom: 30mm;
            right: 20mm;
            width: 80px;
            height: 80px;
            border: 3px solid #c0392b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c0392b;
            font-size: 0.7rem;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            opacity: 0.6;
            transform: rotate(-15deg);
        }

        /* Tombol cetak */
        .no-print {
            text-align: center;
            margin-top: 25px;
        }

        .no-print button {
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 5px;
            font-size: 0.95rem;
            cursor: pointer;
            margin: 0 5px;
            transition: background 0.2s;
        }

        .no-print button:hover {
            background: #1a252f;
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .kwitansi {
                border: 1px solid #000;
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
            }
            .no-print {
                display: none !important;
            }
            .stamp {
                opacity: 0.4;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            size: A5;
            margin: 0;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="kwitansi">
        <div class="kop">
            <h2>Bukti Pembayaran SPP</h2>
            <p>SMP Negeri 1 Example &mdash; Tahun Ajaran <?= escape($data['tahun_dibayar']) ?></p>
        </div>

        <div class="no-kwitansi">
            No. Transaksi: #<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?>
        </div>

        <div class="data-section">
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>:</td>
                    <td><?= escape($data['nama_siswa']) ?></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td><?= escape($data['nis']) ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?= escape($data['kelas']) ?></td>
                </tr>
                <tr>
                    <td>Bulan / Tahun Dibayar</td>
                    <td>:</td>
                    <td><?= $bulanText ?> <?= escape($data['tahun_dibayar']) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td>:</td>
                    <td><?= $tglBayar ?></td>
                </tr>
                    <td>Petugas</td>
                    <td>:</td>
                    <td><?= escape($data['nama_petugas'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <div class="ttd-section">
            <div class="ttd-box">
                <p>Penyetor,</p>
                <span class="ttd-line"><?= escape($data['nama_siswa']) ?></span>
            </div>
            <div class="ttd-box">
                <p>Petugas,</p>
                <span class="ttd-line"><?= escape($data['nama_petugas'] ?? '....................') ?></span>
            </div>
        </div>

        <div class="stamp">LUNAS<br>TERBAYAR</div>
    </div>

    <div class="no-print">
        <button type="button" onclick="window.print()">🖨️ Cetak Ulang</button>
        <button type="button" onclick="window.history.back()">← Kembali</button>
    </div>

</body>
</html>

