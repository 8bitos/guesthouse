# Bagus Guest House - Reservation & Management System

Bagus Guest House adalah aplikasi berbasis web modern yang dirancang untuk mengelola pemesanan kamar (reservasi), profil tamu, laporan keuangan, dan layanan bantuan pelanggan (pengaduan/complaints) secara real-time. Aplikasi ini dibangun dengan menggunakan framework **Laravel 13**, **Tailwind CSS v4**, dan **MySQL** (dengan fallback SQLite untuk testing).

---

## Fitur Utama Sistem

1. **Sistem Reservasi Kamar & Proteksi Admin**:
   - Pemesanan kamar real-time dengan validasi ketersediaan kamar yang ketat.
   - Hak akses khusus: Administrator dapat mengelola pemesanan tetapi dibatasi dari melakukan checkout reservasi (dilengkapi custom glassmorphic alert dialog).

2. **Dashboard Interaktif & Analytics**:
   - Statistik ringkasan (Total Tamu, Kamar, Reservasi Aktif, dan Pendapatan).
   - Diagram tren okupansi & pendapatan interaktif berbasis SVG dinamis dengan filter range waktu (*Hari Ini, 7 Hari, 1 Bulan, 6 Bulan, 1 Tahun*).
   - Grafik asal negara tamu teratas (Top Guest Origins) dan kamar terpopuler.

3. **Manajemen Profil Pengguna (Tamu & Admin)**:
   - Pencarian dan pemilihan alamat rumah otomatis di seluruh dunia menggunakan integrasi **OpenStreetMap Nominatim API** (Autocomplete).
   - Pilihan nomor telepon internasional dengan dropdown kode negara (dilengkapi bendera emoji seperti 🇮🇩, 🇺🇸, 🇬🇧, dll.).
   - Validasi kata sandi lama sebelum mengizinkan penggantian kata sandi baru.

4. **Sistem Tiket Pengaduan (Complaints & Support Tickets)**:
   - Tamu dapat mengirim keluhan/feedback terkait reservasi mereka dari dashboard mereka.
   - Manajemen resolusi tiket oleh Administrator dari panel admin yang terintegrasi secara instan dengan popup modal detail keluhan di sisi tamu.

5. **Ekspor Laporan CSV**:
   - Fitur ekspor seluruh data pemesanan secara instan langsung ke format file CSV dengan BOM byte untuk pembacaan Microsoft Excel yang sempurna.

6. **Integrasi Email SMTP (Gmail)**:
   - Pengiriman email otomatis kepada tamu begitu reservasi mereka disetujui oleh admin.
   - Dilengkapi dengan portal pengujian pengiriman email di `/testemail`.

---

## Kebutuhan Sistem (Requirements)

Sebelum memulai instalasi, pastikan komputer Anda telah terpasang:
* **PHP >= 8.3** (lengkap dengan ekstensi PHP umum seperti `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`)
* **Composer** (Dependency manager untuk PHP)
* **Node.js & NPM** (Untuk kompilasi frontend Vite & Tailwind CSS)
* **MySQL Database Server** (atau gunakan SQLite jika hanya untuk development cepat)
* **Koneksi Internet** (diperlukan untuk meload peta OpenStreetMap Nominatim, Google Fonts, dan Material Symbols Icons)

---

## Langkah Instalasi & Konfigurasi

Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal Anda:

### 1. Clone Repository
Unduh kode sumber dari GitHub Anda:
```bash
git clone https://github.com/8bitos/guesthouse.git
cd guesthouse
```

### 2. Instal Dependensi PHP
Jalankan composer untuk mendownload library backend:
```bash
composer install
```

### 3. Instal Dependensi Frontend
Jalankan npm untuk mendownload library Tailwind CSS dan aset pendukung:
```bash
npm install
```

### 4. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` yang baru dibuat di text editor Anda, lalu sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=guesthouse
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Pengiriman Email SMTP (Jika menggunakan Gmail App Password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=bagusguesthouse01@gmail.com
MAIL_PASSWORD=hlwyvcfnrlyeqzrb
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="bagusguesthouse01@gmail.com"
MAIL_FROM_NAME="Bagus Guest House"
```

### 5. Generate Application Key
Buat kunci keamanan enkripsi Laravel:
```bash
php artisan key:generate
```

### 6. Jalankan Migrasi & Seeder Database
Buat tabel-tabel di database dan isi data dummy default (ini juga akan membuat akun admin default dan menyalin aset gambar kamar default):
```bash
php artisan migrate:fresh --seed
```
* **Akun Admin Default**:
  - Email: `admin@gmail.com`
  - Password: `password`
* **Akun Tamu Default**:
  - Email: `pelanggan@gmail.com`
  - Password: `password`

### 7. Buat Symbolic Link Storage
Hubungkan folder penyimpanan file upload ke folder public agar bisa diakses oleh browser:
```bash
php artisan storage:link
```

---

## Cara Menjalankan Aplikasi

Aplikasi berjalan menggunakan dua server lokal terpisah (back-end Laravel dan front-end Vite).

1. **Jalankan Laravel Development Server**:
   Jalankan perintah ini di terminal pertama Anda:
   ```bash
   php artisan serve
   ```
   Aplikasi Anda kini dapat diakses melalui browser di alamat: **[http://localhost:8000](http://localhost:8000)**.

2. **Jalankan Vite Frontend Server**:
   Jalankan perintah kompilasi aset frontend di terminal terpisah:
   * **Untuk Lingkungan Pengembangan (Development)**:
     ```bash
     npm run dev
     ```
   * **Untuk Lingkungan Produksi (Compile Aset Permanen)**:
     ```bash
     npm run build
     ```

---

## Cara Menjalankan Pengujian (Testing)

Aplikasi ini dilengkapi dengan suite pengujian otomatis lengkap menggunakan **Pest PHP**. Untuk memastikan semua fitur berjalan dengan benar tanpa ada error, jalankan perintah berikut:
```bash
php artisan test --compact
```
Semua 44 kasus pengujian (mulai dari alur reservasi, autentikasi, manajemen CRUD kamar, hingga pengaduan tiket) akan dieksekusi secara otomatis.
