# 📈 Porto Tracking - Personal Investment Dashboard

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-F5788D?style=for-the-badge&logo=chartdotjs&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)

**Porto Tracking untuk Rupiah Aset** adalah aplikasi web manajemen portofolio investasi pribadi yang dibangun menggunakan Framework Laravel. Aplikasi ini membantu investor memantau kekayaan bersih (_Net Worth_), riwayat transaksi, dan performa aset di berbagai akun RDN/Sekuritas dalam satu dasbor terpusat.

## 🌟 Fitur Utama

### 1. 📊 Dashboard Komprehensif

-   **Net Worth Real-time:** Menghitung total aset + kas secara otomatis.
-   **Analisis Profit/Loss:** Memantau keuntungan/kerugian dari modal tertahan.
-   **Komposisi Aset:** Grafik Donut Chart untuk melihat diversifikasi aset.
-   **Grafik Tren Nilai Portofolio:** Visualisasi pertumbuhan kekayaan dengan filter waktu (1 Bulan, 1 Tahun, Semua).

### 2. 🏦 Manajemen Multi-RDN (Rekening Dana Nasabah)

-   Mencatat berbagai akun sekuritas (misal: Bibit, Ajaib, BCA Sekuritas).
-   Pemisahan saldo kas per akun RDN.
-   Riwayat transaksi spesifik per RDN.

### 3. 📝 Pencatatan Transaksi Lengkap

Mendukung berbagai jenis transaksi investasi:

-   **Buy/Sell:** Menghitung _Average Price_ dan _Realized/Unrealized Gain_.
-   **Top Up/Withdraw:** Manajemen arus kas masuk/keluar.
-   **Dividen:** Mencatat pemasukan pasif (Tunai & Unit).
-   **Biaya (Fee):** Perhitungan biaya broker/admin yang akurat.

### 4. 🎯 Financial Goals & Watchlist

-   **Goals:** Menetapkan target keuangan (misal: "Beli Rumah") dan menghubungkannya dengan aset tertentu untuk melacak progres.
-   **Watchlist:** Memantau saham incaran dengan indikator harga "Mahal/Murah" berdasarkan target harga beli.

---

## 🚀 Technical Highlights

Salah satu fitur unggulan teknis dalam proyek ini adalah algoritma **Reverse Calculation** pada grafik performa.

-   **Masalah:** Menyimpan _snapshot_ saldo harian di database memakan memori dan sulit dikelola jika ada perubahan data historis.
-   **Solusi:** Grafik dihitung secara _on-the-fly_ menggunakan logika mundur:
    1.  Ambil posisi _Net Worth_ saat ini.
    2.  Lakukan _looping_ mundur ke masa lalu berdasarkan riwayat transaksi.
    3.  Balikkan efek transaksi (misal: Topup dianggap pengurang saldo masa lalu).
-   **Hasil:** Grafik yang 100% akurat, ringan, dan tidak memerlukan tabel _snapshot_ harian yang besar.

---

## 🛠️ Instalasi

Ikuti langkah ini untuk menjalankan proyek di komputer lokal:

### Prasyarat

-   PHP >= 8.1
-   Composer
-   MySQL (via XAMPP/Laragon)

### Langkah-langkah

1.  **Clone Repository**

    ```bash
    git clone [https://github.com/username/porto-tracking.git](https://github.com/username/porto-tracking.git)
    cd porto-tracking
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    ```

3.  **Setup Environment**

    -   Duplikat file `.env.example` menjadi `.env`.
    -   Atur konfigurasi database di file `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_kamu
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Generate Key & Migrasi**

    ```bash
    php artisan key:generate
    php artisan migrate
    ```

5.  **Jalankan Server**
    ```bash
    php artisan serve
    ```
    Buka browser dan akses: `http://localhost:8000`

---

## 📸 Screenshots

---

## 👨‍💻 Author

Dibuat dengan ❤️ Yudi (@eternalsunshine09) oleh **Oja**.
Proyek ini dikembangkan untuk keperluan manajemen investasi pribadi dan pembelajaran Web Development.
