cd investment-manager
php artisan serve

//Laravel sudah menyiapkan "denah" gudangnya secara otomatis (namanya Migration). 
Kita tinggal suruh tukangnya untuk bangun.
php artisan migrate
contoh:
php artisan make:model Product -m
php artisan make:model Transaction -m
php artisan make:controller ProductController


php artisan tinker //untuk cek database
App\Models\User::all();