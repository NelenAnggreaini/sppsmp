# Sistem Informasi Pembayaran SPP SMP

## Struktur Folder
```
sistem-pembayaran-spp/
├── config/
│   └── config.php          # Koneksi database yang aman
├── database/
│   └── database.sql        # Script SQL untuk membuat tabel
├── includes/
│   ├── header.php          # Header umum (akan dibuat nanti)
│   └── footer.php          # Footer umum (akan dibuat nanti)
├── siswa/
│   ├── index.php           # Daftar siswa (CRUD)
│   ├── tambah.php          # Tambah siswa
│   ├── edit.php            # Edit siswa
│   └── hapus.php           # Hapus siswa
├── pembayaran/
│   ├── index.php           # Daftar pembayaran
│   └── proses_bayar.php    # Proses input pembayaran
├── auth/
│   ├── login.php           # Halaman login
│   └── proses_login.php    # Proses login petugas/admin
└── index.php               # Halaman utama / dashboard
```

