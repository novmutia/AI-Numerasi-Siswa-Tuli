# 🧠 AI Numerasi Siswa Tuli

Aplikasi Asesmen Numerasi Berbasis Web pertama untuk siswa tunarungu yang ditenagai oleh **Hybrid Neuro-Symbolic AI** (XGBoost Machine Learning + Rule-Based Expert System) dan dibangun di atas **Laravel 12**.

Proyek ini bertujuan untuk mendiagnosis pola jawaban siswa secara presisi untuk menghasilkan klasifikasi tingkat kemampuan (NSI, Basic, Proficient, Advanced) berdasarkan logika kecerdasan buatan, akurasi pengerjaan, dan riwayat asesmen.

---

## ✨ Fitur Utama

### 1. 🤖 Enterprise-Grade Hybrid AI Diagnosis
Aplikasi ini tidak sekadar menghitung skor Benar/Salah (1 atau 0), melainkan menggunakan model **XGBoost Classifier** untuk menganalisis "Kualitas Kesalahan" dari tiap opsi jawaban yang dipilih.
- **Microservice Python (Flask):** Menyediakan inferensi klasifikasi *real-time* dalam hitungan milidetik.
- **Fail-Safe Fallback:** Jika server AI *offline*, sistem Laravel akan otomatis (*seamless*) beralih ke penghitungan bobot manual berbasis *Rule-Based Expert System* tanpa memunculkan layar error 500 ke pengguna.

### 2. ⚡ Modern Frontend & UX
- **Live Search & Smart Caching:** Pencarian siswa lama menggunakan *real-time live search* tanpa memuat ulang halaman (*No-reload*).
- **Onboarding Tour Interaktif:** Memandu pengguna baru (Guru/Siswa) di setiap halaman (menggunakan *Driver.js*).
- **Glassmorphism & Micro-animations:** Tampilan *dashboard* premium, cantik, modern, dan dirancang khusus untuk kenyamanan visual (*Accessible*).
- **Pemisahan Entitas Valid:** Pilihan navigasi khusus bagi "Siswa Baru" dan "Siswa Lama" untuk memastikan riwayat *Pre-Test* dan *Post-Test* tersambung lurus di dalam tabel *database* tanpa duplikasi.

---

## 🛠️ Arsitektur Teknologi

- **Backend Utama:** Laravel 12 (PHP 8.2+)
- **Frontend:** HTML/Blade, Vanilla CSS/JS, Tailwind CSS
- **AI Microservice:** Python 3 (Flask, XGBoost, Pandas, Scikit-Learn)
- **Database:** MySQL / SQLite / PostgreSQL (Diatur di `.env`)

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Aplikasi ini memiliki 2 "mesin" yang saling terintegrasi dan berjalan secara berdampingan: **Mesin Web (Laravel)** dan **Mesin AI (Python)**.

### Langkah 1: Menjalankan Mesin AI (Python API)
Mesin AI ini bertugas layaknya "Otak" yang memberikan prediksi level dan klasifikasi asesmen secara *real-time*.

1. Buka terminal, masuk ke folder Machine Learning:
   ```bash
   cd ml_training
   ```
2. (Sangat Disarankan) Buat dan aktifkan *Virtual Environment* agar instalasi rapi dan terisolasi:
   ```bash
   python -m venv .venv
   
   # Untuk Pengguna Windows:
   .venv\Scripts\activate
   
   # Untuk Pengguna macOS / Linux:
   source .venv/bin/activate
   ```
3. Instal pustaka Python yang dibutuhkan:
   ```bash
   pip install -r requirements.txt
   ```
4. Jalankan server API (Flask):
   ```bash
   python app.py
   ```
   *Biarkan terminal ini tetap menyala di latar belakang (port 5000).*

*(Note: Untuk dokumentasi sangat mendetail terkait AI dan Model XGBoost, silakan baca [ml_training/README.md](ml_training/README.md))*

### Langkah 2: Menjalankan Mesin Web (Laravel)
Buka aplikasi Terminal atau Command Prompt **baru** (biarkan terminal API Python tetap terbuka).

1. Posisikan di *root* folder aplikasi, dan *Install Dependencies* PHP & Node:
   ```bash
   composer install
   npm install
   ```
2. Siapkan *Database*:
   Salin `.env.example` ke `.env`, sesuaikan konfigurasi DB Anda, buat *Application Key*, lalu jalankan migrasi & *seeder*:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
3. Jalankan Server Web Lokal (Laravel & Vite):
   ```bash
   npm run dev
   php artisan serve
   ```
Aplikasi yang cantik ini kini dapat diakses dengan membuka `http://127.0.0.1:8000` di Web Browser Anda!

---

## 🧪 Pengujian Sistem Otomatis
Sistem telah dilengkapi dengan *script* simulasi 100 siswa acak (Virtual Testing) yang dihubungkan ke *engine backend* untuk memastikan kedua "mesin" berkomunikasi dengan sempurna.
```bash
cd ml_training/testing
php test_hybrid.php
```

---

## 📂 Struktur Database Utama
- `schools`: Data institusi sekolah asal siswa.
- `students`: Data identitas siswa (berelasi dengan *school*).
- `questions`: Bank Soal Numerasi (dilengkapi kolom tingkat kesulitan `easy/medium/hard`).
- `options`: Opsi jawaban pilihan ganda, dilengkapi kolom penanda indikator diagnostik (`tepat/parsial/lemah`).
- `assessments` & `assessment_answers`: Rekam jejak waktu pengerjaan ujian siswa dan hasil akhirnya (Score, Metrik Level Dominan, Probability).
