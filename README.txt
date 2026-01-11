# INVESTMENT MANAGER - PANDUAN PENGEMBANGAN

## 1. Menjalankan Aplikasi
Perintah dasar untuk menyalakan server:
cd investment-manager
php artisan serve

## 2. Database & Migration
Konsep: Migration adalah "denah" gudang. Kita suruh tukang bangun dengan perintah ini:
php artisan migrate

## 3. Membuat Fitur Baru (Generator)
Membuat Model + Migration sekaligus:
php artisan make:model Product -m
php artisan make:model Transaction -m

Membuat Controller:
php artisan make:controller ProductController

## 4. Debugging & Cek Data
Masuk ke mode interaktif untuk cek database manual:
php artisan tinker

Contoh perintah dalam tinker:
App\Models\User::all();

## 5. CATATAN BUG / ERROR (To-Do List)
Status: [updateGoal masih error]
Masalah:
- Saat melakukan edit dan menambahkan produk, inputan ter-reset (hilang).
- Fitur hapus (delete) sudah berjalan normal.
- Fitur edit nominal masih bermasalah.