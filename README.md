# 💧 Smart Irrigation System

Sistem monitoring dan prediksi kebutuhan air irigasi rawa berbasis web, dilengkapi dengan model Machine Learning (Linear Regression) untuk memperkirakan kebutuhan air esok hari secara otomatis.

---

## 🚀 Fitur

- **Autentikasi** — Login & logout dengan proteksi middleware
- **Dashboard Monitoring** — Visualisasi tren kebutuhan air dengan Chart.js
- **Prediksi AI** — Estimasi kebutuhan air 24 jam ke depan menggunakan Linear Regression (Python)
- **CRUD Data Irigasi** — Tambah, edit, dan hapus data harian
- **Kalkulasi Otomatis** — ETo dihitung otomatis dengan metode **Penman-Monteith FAO-56**, ETc dan kebutuhan air menyesuaikan
- **Preview Real-time** — Hasil kalkulasi ETo, ETc, dan kebutuhan air langsung terlihat saat mengisi form
- **Pagination AJAX** — Navigasi tabel tanpa reload halaman
- **Page Transition** — Loading overlay dan loading bar saat berpindah halaman

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12 (PHP) |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL |
| Machine Learning | Python (Linear Regression) |
| Chart | Chart.js |
| Server Lokal | Laragon |

---

## ⚙️ Instalasi

### 1. Clone repository

```bash
git clone https://github.com/khatami99/smart-irrigation.git
cd smart-irrigation
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_irrigation
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan migration

```bash
php artisan migrate
```

### 5. Buat user pertama

```bash
php artisan tinker
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
]);
```

### 6. Jalankan aplikasi

```bash
php artisan serve
```

Atau akses langsung via Laragon di `http://smart-irrigation.test`

---

## 📐 Kalkulasi ETo (Penman-Monteith FAO-56)

ETo dihitung otomatis dari data iklim yang diinput:

| Parameter Input | Satuan |
|---|---|
| Suhu Maksimum | °C |
| Suhu Minimum | °C |
| Kelembaban Udara | % |
| Kecepatan Angin | m/s |
| Radiasi Matahari | MJ/m²/hari |
| Koefisien Tanaman (Kc) | - |
| Curah Hujan | mm |

**Rumus:**
```
ETc  = ETo × Kc
Kebutuhan Air = ETc - (Curah Hujan × 0.8)
```

---

## 📁 Struktur Direktori Penting

```
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
```

---

## 👤 Author

**Muhammad Sauqi Khatami** — Sistem Irigasi Rawa berbasis AI  
[github.com/khatami99](https://github.com/khatami99)

---

## 📄 Lisensi

MIT License

Copyright (c) 2026 Muhammad Sauqi Khatami

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

