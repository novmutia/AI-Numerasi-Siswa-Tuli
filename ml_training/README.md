# 🧠 Modul AI: XGBoost Numerasi Diagnosis

Folder ini merupakan **Jantung Kecerdasan Buatan (Machine Learning)** dari aplikasi *AI Numerasi Siswa Tuli*. 

Di sinilah kita mentransformasi logika *Rule-Based Expert System* kuno (menghitung bobot secara manual kaku) menjadi sistem klasifikasi prediktif modern berbasis algoritma **XGBoost Classifier**.

---

## 📁 Struktur Direktori & Fungsi File

- `app.py`: Server *Microservice* berbasis **Flask**. Bertugas menerima API Request JSON dari backend Laravel, mengubah array jawaban (A/B/C) menjadi susunan fitur biner (*One-Hot Encoding*), dan mengembalikan prediksi *Level Kemampuan* beserta persentase *Probabilitas Keyakinan*.
- `generate_dataset.py`: *Script Python* pembuat data sintetis. Bertugas menciptakan simulasi dataset 1.500 pola jawaban siswa yang merepresentasikan skenario probabilitas dari dunia nyata (berdasarkan struktur soal di sistem).
- `numeracy_dataset.csv`: *Dataset* tabular (*Comma-Separated Values*) hasil *generate* yang digunakan untuk melatih (*training*) AI. Bebas diganti/ditimpa jika Anda sudah memiliki ribuan data nyata hasil pre-test siswa di lapangan.
- `training_xgboost.ipynb`: Buku kerja (*Jupyter Notebook*) berisi seluruh tahapan utuh *Data Science*. Mulai dari *Loading Data*, pembuatan *Class Weights* (untuk mengatasi data tidak seimbang/*Imbalanced Data*), proses *Training* XGBoost, pencetakan matriks evaluasi, hingga menyimpan model final.
- `xgboost_numerasi.json`: "Otak Beku" (*Frozen Brain*). File ini adalah model matematis (tersusun dari ribuan Pohon Keputusan / *Decision Trees*) yang dihasilkan oleh algoritma dan langsung siap di-*deploy* (*Production-Ready*).
- `requirements.txt`: Daftar pustaka/modul Python yang wajib diinstal.
- `check_importance.py`: Script bedah model untuk menguji dan membuktikan bahwa AI secara matematis berhasil menyadari "Tingkat Kesulitan Soal" dengan memberikan poin *Feature Importance* (Daya Pembeda) tinggi pada soal *Hard/Easy*.
- `testing/`: Folder laboratorium yang berisi alat penguji (*Hybrid Fallback Test PHP* & *Manual Scenario ML Python*).

---

## ⚙️ Cara Kerja Model

1. **Input Bute (Blind Input):** Model **sama sekali tidak** membaca kolom *Difficulty* (Tingkat Kesulitan: *easy, medium, hard*) dari *Database*. Input murni yang dimasukkan hanyalah matriks dari 36 fitur One-Hot Encoding (12 Soal × 3 Opsi Jawaban).
2. **Menimbang Kualitas Kesalahan:** Model belajar bahwa anak yang salah menjawab dengan memilih opsi `12_B` (kesalahan perhitungan minor) kemampuannya lebih tinggi (*Proficient*) dibandingkan anak yang salah dengan memilih opsi `1_A` (kebingungan fundamental/*NSI*).
3. **Pemisahan Kelas (Class Decision Boundary):** XGBoost secara otomatis mengalkulasi dan mendeteksi soal mana yang pantas digunakan sebagai patokan untuk anak masuk ke level *Advanced*, dan soal mana yang jika salah membuktikan sang anak terjun ke level *NSI*.

---

## 🚀 Panduan Menjalankan Server API AI (Inference Engine)

Agar Web Laravel bisa meminta (melakukan *Hit*) prediksi AI secara *real-time* ketika siswa menekan tombol Selesai Ujian, Anda **wajib** menyalakan server Flask yang ada di folder ini.

1. Buka Terminal/Command Prompt, lalu masuk ke folder ini:
   ```bash
   cd ml_training
   ```
2. (Sangat Disarankan) Buat dan aktifkan Lingkungan Virtual (*Virtual Environment*):
   ```bash
   python -m venv .venv
   
   # Untuk Pengguna Windows:
   .venv\Scripts\activate
   
   # Untuk Pengguna macOS / Linux:
   source .venv/bin/activate
   ```
3. Instal prasyarat *library* Python (Hanya perlu dilakukan 1 kali pada komputer/server baru):
   ```bash
   pip install -r requirements.txt
   ```
4. Nyalakan pelayanan (*Server Runtime*):
   ```bash
   python app.py
   ```
*(Terminal akan menampilkan konfirmasi bahwa server berjalan secara lokal pada alamat `http://127.0.0.1:5000/predict`. Biarkan terminal ini tetap terbuka).*

---

## 🧪 Pengujian Mandiri Ekstrem (Laboratorium)

Jika Anda ingin bereksperimen, membongkar "apa yang dipikirkan AI", atau mengetes model prediksi tanpa perlu menekan tombol di Web Laravel, jalankan pengujian khusus di folder `testing/`:

```bash
cd testing
python test_scenario.py
```
*Script* ini akan mengirimkan kasus-kasus khusus ekstrem (Contoh: Jawaban "Anak Sangat Pintar", Jawaban "Anak Cukup Pintar", Jawaban "Anak Ngawur") yang di-input secara manual ke AI, dan mencetak probabilitas finalnya untuk membuktikan model berjalan secara empiris!
