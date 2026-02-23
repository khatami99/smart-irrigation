💧 Smart Irrigation System
Sistem monitoring dan prediksi kebutuhan air irigasi rawa berbasis web, dilengkapi dengan model Machine Learning (Linear Regression) untuk memperkirakan kebutuhan air esok hari secara otomatis.

🚀 Fitur

Autentikasi — Login & logout dengan proteksi middleware
Dashboard Monitoring — Visualisasi tren kebutuhan air dengan Chart.js
Prediksi AI — Estimasi kebutuhan air 24 jam ke depan menggunakan Linear Regression (Python)
CRUD Data Irigasi — Tambah, edit, dan hapus data harian
Kalkulasi Otomatis — ETo dihitung otomatis dengan metode Penman-Monteith FAO-56, ETc dan kebutuhan air menyesuaikan
Preview Real-time — Hasil kalkulasi ETo, ETc, dan kebutuhan air langsung terlihat saat mengisi form
Pagination AJAX — Navigasi tabel tanpa reload halaman
Page Transition — Loading overlay dan loading bar saat berpindah halaman


🛠️ Tech Stack
LayerTeknologiBackendLaravel 12 (PHP)FrontendBlade + Tailwind CSSDatabaseMySQLMachine LearningPython (Linear Regression)ChartChart.jsServer LokalLaragon

⚙️ Instalasi
1. Clone repository
bashgit clone https://github.com/khatami99/smart-irrigation.git
cd smart-irrigation
2. Install dependencies
bashcomposer install
npm install
3. Konfigurasi environment
bashcp .env.example .env
php artisan key:generate
Edit file .env sesuaikan konfigurasi database:
envDB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_irrigation
DB_USERNAME=root
DB_PASSWORD=
4. Jalankan migration
bashphp artisan migrate
5. Buat user pertama
bashphp artisan tinker
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
]);
6. Jalankan aplikasi
bashphp artisan serve
Atau akses langsung via Laragon di http://smart-irrigation.test

📐 Kalkulasi ETo (Penman-Monteith FAO-56)
ETo dihitung otomatis dari data iklim yang diinput:
Parameter InputSatuanSuhu Maksimum°CSuhu Minimum°CKelembaban Udara%Kecepatan Anginm/sRadiasi MatahariMJ/m²/hariKoefisien Tanaman (Kc)-Curah Hujanmm
Rumus:
ETc  = ETo × Kc
Kebutuhan Air = ETc - (Curah Hujan × 0.8)

📁 Struktur Direktori Penting
app/
├── Http/Controllers/
│   ├── AuthController.php
│   └── IrrigationController.php
├── Services/
│   └── IrrigationDataService.php   ← kalkulasi ETo
└── Models/
    └── IrrigationData.php

resources/views/
├── layouts/
│   └── app.blade.php               ← layout utama
├── irrigation/
│   ├── index.blade.php             ← dashboard
│   ├── create.blade.php            ← form tambah data
│   ├── edit.blade.php              ← form edit data
│   └── partials/
│       └── table.blade.php         ← tabel AJAX
└── auth/
    └── login.blade.php

👤 Author
Khatami — Sistem Irigasi Rawa berbasis AI
github.com/khatami99

📄 Lisensi
Proyek ini dibuat untuk keperluan pengembangan sistem informasi irigasi rawa.
