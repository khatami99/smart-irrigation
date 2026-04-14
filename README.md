# 💧 Smart Irrigation Management System

> Sistem manajemen irigasi berbasis web yang mengintegrasikan standar teknis KP-01/FAO-56 dengan prediksi kebutuhan air menggunakan Machine Learning.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.x-3776AB?style=flat&logo=python&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

---

## 📋 Tentang Proyek

Smart Irrigation Management System adalah aplikasi web untuk mendukung **perencanaan dan operasional irigasi** pada Daerah Irigasi Permukaan (DIP) dan Daerah Irigasi Rawa (DIR). Sistem ini mengimplementasikan standar teknis resmi Indonesia (KP-01, Permen PU No. 32/PRT/M/2007) dan metodologi FAO-56 untuk perhitungan kebutuhan air irigasi.

Proyek ini dibangun sebagai **portfolio project** yang merepresentasikan sistem nyata yang digunakan oleh dinas irigasi/PUPR di lapangan.

---

## ✨ Fitur Utama

### 🗂️ Master Data
- **Daerah Irigasi** — CRUD DIP (permukaan) dan DIR (rawa) lengkap dengan atribut teknis (SKA, faktor tersier, pct kehilangan air)
- **Petak Irigasi** — Manajemen petak tersier dengan koordinat GPS dan map picker interaktif
- **Saluran** — Data jaringan saluran irigasi

### 📅 Perencanaan
- **Musim Tanam** — Manajemen periode tanam dengan status berjalan/selesai
- **Luas Tanam O-01 (DIP)** — Blangko usulan luas tanam per DI per MT, CRUD lengkap
- **Jadwal Tanam (RTT)** — Rencana Tata Tanam per petak dengan tracking fase pertumbuhan otomatis (FAO)

### 🌾 Blangko Operasional
| Kode | Nama | Jenis | Fitur |
|------|------|-------|-------|
| O-01 | Usulan Luas Tanam per DI | DIP | CRUD + otomatis hitung KP-01 |
| O-05 | Rencana Kebutuhan Air di Pintu | DIP | View + Download PDF |
| O-09 | Rencana/Realisasi Tanaman per Petak Tersier | DIR | CRUD + Download PDF |

### 🌦️ Data Iklim
- Input manual data iklim harian (suhu, kelembaban, angin, radiasi, curah hujan)
- **Import CSV BMKG** dengan auto-deteksi kolom (mendukung berbagai format ekspor BMKG)
- Kalkulasi otomatis ETo (FAO-56 Penman-Monteith), ETc, dan kebutuhan air

### 🤖 Prediksi AI
- **Linear Regression** dengan feature engineering (lag, rolling average, musiman)
- Training terjadwal via **Laravel Scheduler** (tiap malam jam 00:00)
- Evaluasi model: R² score dan RMSE
- Threshold adaptif berbasis distribusi historis (Normal/Tinggi/Rendah)

### 📊 Analisis & Laporan
- **Kebutuhan Air KP-01** — Rekap hasil perhitungan per DI per MT per dekade
- **Grafik** — Visualisasi tren kebutuhan air 30 hari terakhir
- **Laporan** — Export PDF dan Excel (data iklim, blangko OP, RTT, rekap)

### 🗺️ Peta Interaktif
- Visualisasi Daerah Irigasi dengan **Leaflet.js**
- Import GeoJSON, tambah/edit layer dan feature polygon
- Status RTT per DI ditampilkan dengan kode warna (rencana/berjalan/selesai/terlambat)

### 🔐 Autentikasi & Otorisasi
- Role-based access control menggunakan **Spatie Laravel Permission**
- Permission granular per fitur (view/create/edit/delete)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.4 |
| Frontend | Blade, Vanilla JS, Chart.js |
| Database | MySQL 8 |
| Maps | Leaflet.js |
| PDF | barryvdh/laravel-dompdf |
| Permission | Spatie Laravel Permission |
| AI/ML | Python 3, scikit-learn, pandas, SQLAlchemy |
| Dev Environment | Laragon |

---

## 📐 Standar Teknis yang Diimplementasi

- **KP-01** — Kriteria Perencanaan Irigasi (perhitungan kebutuhan air per dekade)
- **FAO-56** — Penman-Monteith untuk kalkulasi ETo
- **Permen PU No. 32/PRT/M/2007** — Format blangko operasional irigasi

---

## 🚀 Cara Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL
- Python 3 + pip
- Node.js (opsional, untuk asset)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/khatami99/smart-irrigation.git
cd smart-irrigation

# 2. Install dependencies PHP
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=smart_irrigation
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migration dan seeder
php artisan migrate --seed

# 7. Install dependencies Python (untuk fitur AI)
pip install pandas sqlalchemy scikit-learn numpy mysql-connector-python

# 8. Jalankan prediksi AI pertama kali (manual)
php artisan ai:prediksi

# 9. Jalankan server
php artisan serve
```

### Setup Scheduler (Production)
Tambahkan cron job berikut di server untuk mengaktifkan Laravel Scheduler:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```
Scheduler akan otomatis menjalankan training AI setiap malam pukul 00:00.

---

## 📁 Struktur Modul

```
app/
├── Console/Commands/
│   └── PrediksiAI.php          # Artisan command training AI
├── Http/Controllers/
│   ├── BlangkoDipController.php # Blangko DIP (O-01, O-05)
│   ├── BlangkoDirController.php # Blangko DIR (O-09)
│   ├── IrrigationController.php # Dashboard + Data Iklim
│   └── ...
├── Models/
│   ├── DaerahIrigasi.php
│   ├── Petak.php
│   ├── RttDir.php
│   ├── AiPrediction.php
│   └── ...
├── Services/
│   ├── KpSatuService.php        # Kalkulasi KP-01
│   └── IrrigationDataService.php # ETo/ETc FAO-56
predict.py                       # Script Linear Regression (Python)
```

---

## 🔮 Roadmap

- [ ] Blangko DIP: O-02, O-04, O-07, O-09, O-10
- [ ] Blangko DIR: O-01, O-03, O-05, O-06, O-07, O-08, O-12
- [ ] BMKG API auto-fetch (realtime climate data)
- [ ] Leaflet Draw — digitasi polygon petak langsung di peta
- [ ] Dashboard command center terintegrasi semua modul
- [ ] Upgrade model AI (Random Forest / XGBoost)

---

## 👤 Author

**Muhammad Sauqi Khatami**
- GitHub: [@khatami99](https://github.com/khatami99)
- Email: sauqikhatami084@gmail.com

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).

---

> *Dibangun dengan harapan dan semangat membantu modernisasi pengelolaan irigasi Indonesia.*
