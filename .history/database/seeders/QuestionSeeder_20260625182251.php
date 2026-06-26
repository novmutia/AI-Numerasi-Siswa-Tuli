<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Option;

class QuestionSeeder extends Seeder
{
    /**
     * 12 soal diagnostik numerasi untuk siswa tunarungu.
     *
     * Struktur tiap soal:
     * - question_text : teks soal (disertai deskripsi visual untuk guru)
     * - topic         : dimensi soal (Skills / Problem Solving / Literasi Konteks)
     * - difficulty    : easy (NSI) | medium (Basic) | hard (Proficient/Advanced)
     * - video_path    : path video BISINDO (diisi nanti saat ada file video)
     * - order         : urutan tampil
     *
     * Struktur tiap option:
     * - option_text  : teks jawaban yang dilihat siswa
     * - is_correct   : apakah ini jawaban benar
     * - order        : urutan A=1, B=2, C=3
     * - indicator    : kode indikator kemampuan (TIDAK ditampilkan ke siswa)
     * - level_value  : level yang diasosiasikan ke pilihan ini
     */
    public function run(): void
    {
        // Hapus data lama sebelum isi ulang
        Option::query()->delete();
        Question::query()->delete();

        $soal = [

            // ── SOAL 1 — NSI — Skills ─────────────────────────────────────
            [
                'question_text' => '3 Apel merah + 1 Apel merah. Berapa jumlah semua apel?',
                'topic'         => 'Skills',
                'difficulty'    => 'easy',
                'video_path'    => null,
                'order'         => 1,
                'options' => [
                    ['option_text' => 'A. 2',  'is_correct' => false, 'order' => 1, 'indicator' => 'tidak_memahami_operasi',  'level_value' => 'NSI'],
                    ['option_text' => 'B. 3',  'is_correct' => false, 'order' => 2, 'indicator' => 'salah_hitung_minor',       'level_value' => 'NSI'],
                    ['option_text' => 'C. 4',  'is_correct' => true,  'order' => 3, 'indicator' => 'operasi_dasar_benar',      'level_value' => 'Basic'],
                ],
            ],

            // ── SOAL 2 — NSI — Problem Solving ───────────────────────────
            [
                'question_text' => 'Tas berisi 3 buku dan 2 pensil. Berapa jumlah semua benda di dalam tas?',
                'topic'         => 'Problem Solving',
                'difficulty'    => 'easy',
                'video_path'    => null,
                'order'         => 2,
                'options' => [
                    ['option_text' => 'A. 5',  'is_correct' => true,  'order' => 1, 'indicator' => 'identifikasi_info_tepat',  'level_value' => 'Basic'],
                    ['option_text' => 'B. 3',  'is_correct' => false, 'order' => 2, 'indicator' => 'hanya_baca_sebagian',       'level_value' => 'NSI'],
                    ['option_text' => 'C. 6',  'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_semua',        'level_value' => 'NSI'],
                ],
            ],

            // ── SOAL 3 — NSI — Literasi Konteks ──────────────────────────
            [
                'question_text' => 'Permen seharga Rp1.000. Beli 2 permen. Berapa total harga yang harus dibayar?',
                'topic'         => 'Literasi Konteks',
                'difficulty'    => 'easy',
                'video_path'    => null,
                'order'         => 3,
                'options' => [
                    ['option_text' => 'A. Rp1.000', 'is_correct' => false, 'order' => 1, 'indicator' => 'tidak_pahami_konteks',      'level_value' => 'NSI'],
                    ['option_text' => 'B. Rp2.000', 'is_correct' => true,  'order' => 2, 'indicator' => 'konteks_sederhana_benar',    'level_value' => 'Basic'],
                    ['option_text' => 'C. Rp3.000', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_konteks',       'level_value' => 'NSI'],
                ],
            ],

            // ── SOAL 4 — Basic — Skills ───────────────────────────────────
            [
                'question_text' => 'Kebun ada 7 bunga, lalu 2 bunga dipetik. Berapa sisa bunga di kebun?',
                'topic'         => 'Skills',
                'difficulty'    => 'medium',
                'video_path'    => null,
                'order'         => 4,
                'options' => [
                    ['option_text' => 'A. 5', 'is_correct' => true,  'order' => 1, 'indicator' => 'operasi_konteks_benar',         'level_value' => 'Basic'],
                    ['option_text' => 'B. 9', 'is_correct' => false, 'order' => 2, 'indicator' => 'salah_operasi_tambah',           'level_value' => 'NSI'],
                    ['option_text' => 'C. 4', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_minor',             'level_value' => 'NSI'],
                ],
            ],

            // ── SOAL 5 — Basic — Skills ───────────────────────────────────
            [
                'question_text' => '7 + 2 = ?. Berapa hasilnya?',
                'topic'         => 'Skills',
                'difficulty'    => 'medium',
                'video_path'    => null,
                'order'         => 5,
                'options' => [
                    ['option_text' => 'A. 8',  'is_correct' => false, 'order' => 1, 'indicator' => 'salah_hitung_minor',            'level_value' => 'NSI'],
                    ['option_text' => 'B. 9',  'is_correct' => true,  'order' => 2, 'indicator' => 'operasi_rutin_benar',            'level_value' => 'Basic'],
                    ['option_text' => 'C. 10', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_lebih',             'level_value' => 'NSI'],
                ],
            ],

            // ── SOAL 6 — Basic — Literasi Konteks ────────────────────────
            [
                'question_text' => 'Air mineral Rp2.000 + Roti Rp3.000, Budi membeli keduanya. Berapa total harga?',
                'topic'         => 'Literasi Konteks',
                'difficulty'    => 'medium',
                'video_path'    => null,
                'order'         => 6,
                'options' => [
                    ['option_text' => 'A. Rp4.000', 'is_correct' => false, 'order' => 1, 'indicator' => 'salah_konteks_harga',      'level_value' => 'NSI'],
                    ['option_text' => 'B. Rp5.000', 'is_correct' => true,  'order' => 2, 'indicator' => 'konteks_sehari_benar',      'level_value' => 'Basic'],
                    ['option_text' => 'C. Rp6.000', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_konteks',      'level_value' => 'NSI'],
                ],
            ],

            // ── SOAL 7 — Proficient — Skills ─────────────────────────────
            [
                'question_text' => '10 Kue, 5 Kue dimakan. Berapa bagian kue yang dimakan? Tuliskan dalam bentuk pecahan.',
                'topic'         => 'Skills',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 7,
                'options' => [
                    ['option_text' => 'A. 1/2',  'is_correct' => true,  'order' => 1, 'indicator' => 'konsep_pecahan_benar',        'level_value' => 'Proficient'],
                    ['option_text' => 'B. 1/5',  'is_correct' => false, 'order' => 2, 'indicator' => 'terbalik_pembilang_penyebut', 'level_value' => 'Basic'],
                    ['option_text' => 'C. 5/10', 'is_correct' => false, 'order' => 3, 'indicator' => 'belum_sederhanakan',          'level_value' => 'Basic'],
                ],
            ],

            // ── SOAL 8 — Proficient — Problem Solving ─────────────────────
            [
                'question_text' => '12 Permen dibagikan sama rata kepada 3 anak. Berapa permen yang diterima setiap anak?',
                'topic'         => 'Problem Solving',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 8,
                'options' => [
                    ['option_text' => 'A. 3', 'is_correct' => false, 'order' => 1, 'indicator' => 'salah_strategi_bagi',            'level_value' => 'Basic'],
                    ['option_text' => 'B. 4', 'is_correct' => true,  'order' => 2, 'indicator' => 'strategi_pembagian_benar',       'level_value' => 'Proficient'],
                    ['option_text' => 'C. 6', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_operasi_bagi_dua',         'level_value' => 'Basic'],
                ],
            ],

            // ── SOAL 9 — Proficient — Literasi Konteks ────────────────────
            [
                'question_text' => 'Harga 1 buku = Rp4.000. Ani membeli 3 buku. Berapa total harga yang harus dibayar Ani?',
                'topic'         => 'Literasi Konteks',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 9,
                'options' => [
                    ['option_text' => 'A. Rp7.000',  'is_correct' => false, 'order' => 1, 'indicator' => 'salah_strategi_perkalian',  'level_value' => 'Basic'],
                    ['option_text' => 'B. Rp12.000', 'is_correct' => true,  'order' => 2, 'indicator' => 'konteks_perkalian_benar',    'level_value' => 'Proficient'],
                    ['option_text' => 'C. Rp8.000',  'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_konteks_perkalian', 'level_value' => 'Basic'],
                ],
            ],

            // ── SOAL 10 — Advanced — Skills ───────────────────────────────
            [
                'question_text' => 'Data nilai ulangan: 70, 80, 90. Berapa nilai rata-rata ketiga siswa tersebut?',
                'topic'         => 'Skills',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 10,
                'options' => [
                    ['option_text' => 'A. 80', 'is_correct' => true,  'order' => 1, 'indicator' => 'konsep_rata_rata_benar',         'level_value' => 'Advanced'],
                    ['option_text' => 'B. 70', 'is_correct' => false, 'order' => 2, 'indicator' => 'ambil_nilai_terkecil',            'level_value' => 'Basic'],
                    ['option_text' => 'C. 90', 'is_correct' => false, 'order' => 3, 'indicator' => 'ambil_nilai_terbesar',            'level_value' => 'Basic'],
                ],
            ],

            // ── SOAL 11 — Advanced — Problem Solving ──────────────────────
            [
                'question_text' => 'Pita panjang 20 cm dipotong menjadi 4 bagian sama panjang. Berapa panjang tiap bagian?',
                'topic'         => 'Problem Solving',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 11,
                'options' => [
                    ['option_text' => 'A. 4 cm', 'is_correct' => false, 'order' => 1, 'indicator' => 'salah_strategi_kompleks',      'level_value' => 'Basic'],
                    ['option_text' => 'B. 5 cm', 'is_correct' => true,  'order' => 2, 'indicator' => 'strategi_kompleks_benar',      'level_value' => 'Advanced'],
                    ['option_text' => 'C. 6 cm', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_pembagian',       'level_value' => 'Proficient'],
                ],
            ],

            // ── SOAL 12 — Advanced — Literasi Konteks ─────────────────────
            [
                'question_text' => 'Harga 1 minuman = Rp2.000. Uang kamu Rp10.000. Berapa minuman yang bisa dibeli?',
                'topic'         => 'Literasi Konteks',
                'difficulty'    => 'hard',
                'video_path'    => null,
                'order'         => 12,
                'options' => [
                    ['option_text' => 'A. 4', 'is_correct' => false, 'order' => 1, 'indicator' => 'salah_strategi_bagi_konteks',    'level_value' => 'Proficient'],
                    ['option_text' => 'B. 5', 'is_correct' => true,  'order' => 2, 'indicator' => 'konteks_bagi_kompleks_benar',    'level_value' => 'Advanced'],
                    ['option_text' => 'C. 6', 'is_correct' => false, 'order' => 3, 'indicator' => 'salah_hitung_bagi_konteks',      'level_value' => 'Basic'],
                ],
            ],

        ];

        foreach ($soal as $data) {
            $options = $data['options'];
            unset($data['options']);

            $question = Question::create($data);

            foreach ($options as $opt) {
                $question->options()->create($opt);
            }
        }

        $this->command->info('✓ QuestionSeeder: 12 soal + 36 pilihan jawaban berhasil diisi.');
    }
}
