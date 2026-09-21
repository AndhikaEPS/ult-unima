# Sistem Informasi Layanan Digital — Unit Layanan Terpadu (ULT) UNIMA

Website antrian & layanan digital untuk Unit Layanan Terpadu Universitas Negeri Manado.
Dibangun dengan **PHP native + MySQL/MariaDB**, tanpa framework, agar mudah di-deploy di hosting mana pun.

## Fitur

Sesuai desain UI/UX yang diberikan, ditambah beberapa penyempurnaan:

| Fitur | Keterangan |
|---|---|
| Beranda | Hero section, tombol "Ambil Nomor Antrian", statistik real-time (jam layanan, sedang dilayani, total menunggu) |
| Layanan | Daftar 5 kategori layanan beserta sub-layanan, diambil dinamis dari database |
| Panduan | 8 langkah alur penggunaan ULT |
| Tentang | Profil ULT & jam operasional |
| **Ambil Nomor Antrian** | Form biodata (nama, NIM/ID, fakultas/unit, email, no. HP) + pilih kategori & detail layanan + **unggah dokumen/foto (JPG/PNG/PDF, maks 2MB)** → nomor tiket otomatis (format `A-001`, dst, reset harian per kategori) |
| Tiket Antrian | Tampilan tiket digital, bisa dicetak, menampilkan estimasi posisi antrian |
| **Cek Status** | Pemohon dapat mengecek status tiketnya kapan saja (menunggu/dipanggil/dilayani/selesai) |
| **Papan Antrian Digital** | Layar monitor publik yang menampilkan nomor sedang dilayani + antrian berikutnya, auto-refresh |
| **Evaluasi Layanan** | Pemohon memberi rating & komentar setelah layanan selesai (sesuai langkah 8 pada Panduan) |
| **Panel Admin** | Login aman (bcrypt), dashboard kelola antrian (Panggil → Mulai Layani → Selesai/Batal), riwayat & filter, kelola kategori/sub-layanan, laporan evaluasi, kelola akun petugas per-kategori |

### Fitur tambahan (di luar PDF, sebagai penyempurnaan)
- Nomor tiket otomatis per-kategori dengan prefix huruf (A = Akademik, U = Umum, M = Kemahasiswaan, K = Keuangan, S = Sistem Informasi)
- Role admin: **super_admin** (kelola semua + akun) dan **petugas** (hanya melihat antrian kategori miliknya)
- Riwayat antrian dengan filter tanggal/status/kategori
- Statistik & papan antrian yang auto-update via AJAX (tanpa reload halaman)
- Proteksi upload (validasi tipe file, ukuran, dan folder `uploads/` tidak bisa mengeksekusi script)

## Struktur Folder

```
ult-unima/
├── admin/                  # Panel admin (login, dashboard, kelola data)
├── api/                    # Endpoint JSON untuk AJAX (stats, monitor, sub-layanan)
├── assets/                 # CSS, JS, gambar/logo
├── config/                 # Konfigurasi database & aplikasi
├── database/schema.sql     # Struktur + data awal database MySQL
├── includes/               # Header, footer, fungsi PHP bersama
├── uploads/                # Tempat file yang diunggah pemohon
├── index.php               # Beranda
├── layanan.php / panduan.php / tentang.php
├── ambil-antrian.php       # Form ambil nomor antrian
├── proses-antrian.php      # Proses simpan data + generate tiket
├── tiket.php               # Tampilan tiket
├── status.php              # Cek status antrian
├── papan-antrian.php       # Papan antrian publik
└── evaluasi.php            # Form evaluasi kepuasan
```

## Kebutuhan Server

- PHP **8.0 atau lebih baru**, dengan ekstensi: `pdo_mysql`, `mbstring`, `fileinfo`
- MySQL 5.7+ atau MariaDB 10.3+
- Web server Apache (mendukung `.htaccess`) atau Nginx

---

## 1. Instalasi & Menjalankan di Komputer Lokal

### Menggunakan XAMPP / Laragon (Windows/Mac/Linux)

1. Install [XAMPP](https://www.apachefriends.org/) atau [Laragon](https://laragon.org/).
2. Ekstrak folder proyek ini ke dalam `htdocs` (XAMPP) atau `www` (Laragon), misalnya menjadi `htdocs/ult-unima`.
3. Jalankan Apache dan MySQL dari control panel XAMPP/Laragon.
4. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database baru bernama `ult_unima`, lalu klik tab **Import** dan pilih file `database/schema.sql`. Atau jalankan lewat terminal:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
5. Buka `config/database.php`, sesuaikan bila perlu (default sudah cocok untuk XAMPP: user `root`, password kosong):
   ```php
   define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
   define('DB_NAME', getenv('DB_NAME') ?: 'ult_unima');
   define('DB_USER', getenv('DB_USER') ?: 'root');
   define('DB_PASS', getenv('DB_PASS') ?: '');
   ```
6. Akses di browser: `http://localhost/ult-unima/index.php`

### Login Admin Default

```
URL      : /admin/login.php
Username : admin
Password : admin123
```
> ⚠️ **Wajib segera ganti password** setelah login pertama (lewat menu Kelola Akun, buat akun baru dengan password kuat, lalu nonaktifkan akun default ini).

---

## 2. Upload Proyek ke GitHub

Langkah-langkah dari nol (asumsikan Git sudah terinstall dan Anda sudah punya akun GitHub):

```bash
# 1. Masuk ke folder proyek
cd ult-unima

# 2. Inisialisasi git
git init

# 3. Tambahkan semua file
git add .

# 4. Commit pertama
git commit -m "Initial commit: Sistem Informasi Layanan Digital ULT UNIMA"

# 5. Buat repository baru di GitHub (lewat website github.com -> New Repository)
#    Beri nama misalnya "ult-unima", JANGAN centang "Initialize with README"

# 6. Hubungkan repo lokal ke GitHub (ganti USERNAME dan NAMA_REPO)
git remote add origin https://github.com/USERNAME/NAMA_REPO.git
git branch -M main
git push -u origin main
```

Setelah ini, kode Anda akan tersimpan di `https://github.com/USERNAME/NAMA_REPO`.

---

## 3. Menghubungkan ke Domain — Penting Dibaca

**Catatan penting:** GitHub Pages (domain `username.github.io` atau domain custom yang dihubungkan ke GitHub Pages) **hanya bisa menampilkan file statis** (HTML/CSS/JS murni). GitHub Pages **tidak bisa menjalankan PHP maupun MySQL**. Karena website ini butuh PHP untuk logika antrian dan MySQL untuk database, **website ini tidak bisa langsung online lewat GitHub Pages**.

Berikut opsi yang bisa Anda pakai:

### Opsi A — Hosting PHP + MySQL biasa (paling umum & disarankan)
1. Sewa hosting yang mendukung PHP & MySQL, misalnya **Niagahoster, Hostinger, DomaiNesia** (berbayar, cocok untuk instansi), atau **InfinityFree/000webhost** (gratis, untuk uji coba).
2. Upload seluruh isi folder proyek ke `public_html` lewat File Manager atau FTP (bisa juga `git clone` langsung di hosting jika mendukung SSH/Git).
3. Buat database MySQL dari cPanel, import `database/schema.sql` lewat phpMyAdmin.
4. Edit `config/database.php` dengan kredensial database dari hosting Anda.
5. Arahkan domain Anda (beli di Niagahoster/Rumahweb/dst, atau domain kampus) ke nameserver hosting tersebut. Website akan tampil di domain Anda secara langsung — **tidak perlu GitHub Pages sama sekali** untuk bagian ini; GitHub cukup dipakai sebagai penyimpanan kode (version control).
6. Setiap ada perubahan kode, cukup `git pull` di server (jika pakai SSH) atau upload ulang file yang berubah.

### Opsi B — Platform modern dengan dukungan PHP & MySQL (Railway, Render, dll)
1. Daftar di [Railway](https://railway.app) atau [Render](https://render.com).
2. Hubungkan akun GitHub Anda, pilih repository `ult-unima` yang sudah di-push.
3. Tambahkan **MySQL add-on/plugin** di platform tersebut, lalu salin kredensialnya (host, user, password, nama database) ke Environment Variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) — `config/database.php` sudah otomatis membaca dari environment variable ini.
4. Jalankan import `database/schema.sql` lewat fitur "Run Command"/console yang disediakan platform (atau via MySQL client dari komputer Anda dengan kredensial yang diberikan).
5. Platform akan memberi domain otomatis (`namaapp.up.railway.app` dsb); Anda bisa menghubungkan domain custom dari menu **Settings → Domains** di platform tersebut — arahkan DNS domain Anda (CNAME) sesuai instruksi yang diberikan.

### Jika Anda tetap ingin memakai GitHub Pages
GitHub Pages hanya cocok jika Anda ingin membuat **halaman informasi statis** (bukan sistem antrian yang butuh database). Jika ini yang dimaksud, beri tahu saya dan saya bisa buatkan versi statis (tanpa backend) khusus untuk itu.

---

## 4. Keamanan Sebelum Digunakan Secara Resmi

- [ ] Ganti password admin default (`admin123`)
- [ ] Set `DB_PASS` dengan password database yang kuat (jangan kosong di server produksi)
- [ ] Pastikan folder `uploads/.htaccess` ikut ter-upload (mencegah file berbahaya dieksekusi)
- [ ] Aktifkan HTTPS (SSL) di hosting/domain Anda
- [ ] Backup database secara berkala (`mysqldump`)

## 5. Struktur Database

Lihat detail lengkap di `database/schema.sql`. Ringkasan tabel:

- `admin` — akun admin/petugas
- `kategori_layanan` — 5 kategori layanan (Informasi Umum, Akademik, Kemahasiswaan, Keuangan/UKT, Sistem Informasi/IT)
- `sub_layanan` — daftar layanan detail per kategori
- `antrian` — data pemohon & tiket antrian
- `evaluasi` — rating & komentar kepuasan
- `pengaturan` — pengaturan umum (jam layanan, dsb)

---

Dibuat berdasarkan desain UI/UX ULT Universitas Negeri Manado.
