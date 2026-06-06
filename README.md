# AI Numerasi Siswa Tuli

Aplikasi asesmen numerasi untuk siswa tunarungu berbasis Laravel 12.
Proyek ini menggabungkan soal numerasi terstruktur dengan diagnosis pola jawaban untuk menghasilkan level kemampuan, akurasi, dan rekomendasi pembelajaran.

## Ikhtisar

Aplikasi ini mendukung:

- pendaftaran siswa dan sekolah,
- sesi asesmen numerasi berbasis soal BISINDO,
- penyimpanan jawaban dan laporan diagnosis,
- dashboard statistik dan ringkasan hasil.

## Fitur Frontend & UI Terkini

- **Onboarding Tour Interaktif**: Menggunakan *Driver.js* untuk memandu pengguna baru di setiap halaman (Dashboard, Asesmen, Data Siswa, Statistik).
- **Client-Side Filtering & Live Search**: Pencarian siswa lama dan filter sekolah di halaman *Data Siswa* dan *Statistik* bekerja murni di sisi klien (JSON) tanpa memuat ulang halaman (*No-reload*).
- **Custom Dropdown**: Elemen *select* sekolah dimodifikasi secara khusus agar responsif, dapat diatur (*custom styling*), dan sesuai dengan panduan tema aplikasi.
- **Sistem Modal Pintar**: Komponen pop-up "Cara Kerja" dan detil metrik dirancang sedemikian rupa agar tidak tumpang-tindih dengan *Onboarding Tour*.

## Teknologi Utama

- Laravel 12
- PHP 8.2+
- Vite + Tailwind CSS
- Axios
- MySQL / SQLite / PostgreSQL (konfigurasi di `.env`)

## Struktur Database

Skema database utama dibuat oleh migrasi dalam `database/migrations`.

### Tabel Utama

- `schools`
    - `id`, `name`, `address`, `city`, `created_at`, `updated_at`
    - Menyimpan data sekolah asal siswa.

- `students`
    - `id`, `name`, `school_id`, `gender`, `birth_year`, `notes`, `created_at`, `updated_at`
    - Relasi: `student` belongs to `school`.

- `questions`
    - `id`, `question_text`, `topic`, `difficulty`, `video_path`, `subtitle_path`, `is_active`, `order`, `created_at`, `updated_at`
    - Menyimpan soal numerasi yang dilengkapi kategori topik dan tingkat kesulitan.

- `options`
    - `id`, `question_id`, `option_text`, `is_correct`, `order`, `indicator`, `level_value`, `created_at`, `updated_at`
    - Menyimpan pilihan jawaban untuk setiap soal.
    - `indicator` dan `level_value` dipakai untuk diagnosis internal, tidak ditampilkan ke siswa.

- `assessment_sessions`
    - `id`, `token`, `student_id`, `question_ids`, `started_at`, `finished_at`, `created_at`, `updated_at`
    - Menyimpan sesi asesmen yang sedang berjalan, termasuk urutan soal acak.

- `answers`
    - `id`, `assessment_session_id`, `question_id`, `option_id`, `created_at`, `updated_at`
    - Menyimpan jawaban siswa per soal.

- `diagnosis_results`
    - `id`, `assessment_session_id`, `student_id`, `level`, `accuracy`, `correct_count`, `total_questions`, `topic_scores`, `weaknesses`, `recommendations`, `ai_note`, `created_at`, `updated_at`
    - Menyimpan hasil diagnosis akhir untuk setiap sesi.

### Migrasi Penting

- `2024_01_01_000001_create_numerasi_tuli_tables.php`
    - Membuat semua tabel utama.
    - Menetapkan relasi foreign key dengan `cascadeOnDelete()`.
    - Menambahkan kolom `question_ids` di `assessment_sessions` untuk menyimpan urutan soal.

- `2024_01_02_000001_add_indicator_columns_to_options_table.php`
    - Menambahkan kolom `indicator` dan `level_value` di tabel `options`.
    - `indicator` digunakan oleh `DiagnosisService` untuk membaca pola kesalahan siswa.
    - `level_value` membantu menentukan distribusi level NSI/Basic/Proficient/Advanced.

## Seeders

Seeder utama ada di `database/seeders/DatabaseSeeder.php`.

### `SchoolSeeder`

- Mengisi data sekolah awal.
- Contoh sekolah: SLB Negeri Semarang, SLB ABC Swadaya Kendal, SLB B YRTRW Surakarta, dll.

### `QuestionSeeder`

- Mengisi 12 soal diagnostik numerasi.
- Setiap soal mempunyai:
    - teks soal (`question_text`),
    - topik (`topic`),
    - tingkat kesulitan (`difficulty`),
    - opsi jawaban dengan attribut `indicator`, `level_value`, dan `is_correct`.
- Seed ini membuat total 12 soal dan 36 opsi jawaban.

## Model dan Relasi

- `App\Models\School`
    - hasMany `students`

- `App\Models\Student`
    - belongsTo `school`
    - hasMany `assessmentSessions`
    - hasMany `diagnosisResults`
    - hasOne latest diagnosis (`latestDiagnosis`)

- `App\Models\Question`
    - hasMany `options`

- `App\Models\Option`
    - belongsTo `question`

- `App\Models\AssessmentSession`
    - belongsTo `student`
    - hasMany `answers`
    - hasOne `diagnosisResult`

- `App\Models\Answer`
    - belongsTo `assessmentSession`
    - belongsTo `question`
    - belongsTo `option`

- `App\Models\DiagnosisResult`
    - belongsTo `assessmentSession`
    - belongsTo `student`

## Resource Utama

### Routes

Didefinisikan di `routes/web.php`.

- `/` → `DashboardController@index`
- `/assessment/start` → `AssessmentController@start`
- `/assessment/store-student` → `AssessmentController@storeStudent`
- `/assessment/{token}/soal/{no}` → `AssessmentController@questions`
- `/assessment/answer` → `AssessmentController@submitAnswer`
- `/assessment/result/{result}` → `AssessmentController@result`
- `/students` → `StudentController@index`
- `/students/{id}/detail` → `StudentController@detail`
- `/statistics` → `StatisticsController@index`

### Controller Utama

- `App\Http\Controllers\AssessmentController`
    - Mengelola alur asesmen, menyimpan siswa, membuat sesi, menampilkan soal, dan memproses hasil.

- `App\Http\Controllers\DashboardController`
    - Menyediakan statistik global: total siswa, total sekolah, asesmen selesai, rata-rata akurasi, distribusi level, dan hasil terbaru.

- `App\Http\Controllers\StudentController`
    - Menampilkan daftar siswa dan ringkasan status asesmen.
    - Mengembalikan detail riwayat diagnosa siswa dalam bentuk JSON.

- `App\Services\DiagnosisService`
    - Logika diagnosis utama yang membaca jawaban dan indikator opsi untuk menghitung level, akurasi, skor topik, kelemahan, dan rekomendasi.

### Views & Frontend

File view utama berada di `resources/views`.

- `resources/views/dashboard.blade.php`
    - Tampilan beranda dashboard.

- `resources/views/assessment/start.blade.php`
    - Form awal untuk input nama siswa dan memilih sekolah.

- `resources/views/assessment/questions.blade.php`
    - Halaman kuis soal numerasi.

- `resources/views/assessment/result.blade.php`
    - Halaman hasil diagnosis dan rekomendasi.

- `resources/views/students/index.blade.php`
    - Daftar siswa dan status asesmen.

- `resources/views/layouts/app.blade.php`
    - Layout umum, header, footer, dan struktur halaman.

Asset frontend:

- `resources/css/app.css`
- `resources/js/app.js`
- `resources/js/bootstrap.js`

## Setup dan Pengembangan Lokal

1. Copy file environment:

    ```bash
    cp .env.example .env
    ```

2. Install dependensi PHP:

    ```bash
    composer install
    ```

3. Buat dan konfigurasi database di `.env`.

4. Generate app key:

    ```bash
    php artisan key:generate
    ```

5. Jalankan migrasi dan seeder:

    ```bash
    php artisan migrate:fresh --seed
    ```

6. Install dependensi JavaScript:

    ```bash
    npm install
    ```

7. Jalankan Vite dalam mode development:

    ```bash
    npm run dev
    ```

8. Jalankan server lokal:

    ```bash
    php artisan serve
    ```

## Perintah Penting

- `php artisan migrate` — jalankan migrasi.
- `php artisan migrate:fresh --seed` — reset database dan isi ulang seed.
- `php artisan db:seed` — isi database dengan seed yang ada.
- `php artisan test` — jalankan unit/feature test.
- `npm run dev` — jalankan Vite development server.
- `npm run build` — build asset produksi.

## Catatan Khusus

- Soal diagnostik numerasi ditujukan untuk siswa tunarungu.
- Opsi jawaban menyertakan indikator internal (`indicator`, `level_value`) yang hanya dipakai untuk diagnosis.
- Nilai `question_ids` pada `assessment_sessions` menyimpan urutan soal yang diacak.
- `StudentController@detail` mengembalikan data JSON untuk tampilan riwayat siswa.

## Lisensi

Proyek ini mengikuti lisensi MIT.
