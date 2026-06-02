<?php
// FILE: database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Question;
use App\Models\Option;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Sekolah dummy ─────────────────────────────────────────────────
        $schools = [
            ['name' => 'SLB Negeri 1 Semarang',    'city' => 'Semarang'],
            ['name' => 'SLB Negeri 1 Ungaran',      'city' => 'Semarang'],
            ['name' => 'SLB B Karya Mulia Surabaya','city' => 'Surabaya'],
            ['name' => 'SLB Negeri 1 Jakarta',      'city' => 'Jakarta'],
            ['name' => 'SLB B YRTRW Surakarta',     'city' => 'Solo'],
        ];
        foreach ($schools as $s) School::firstOrCreate(['name' => $s['name']], $s);

        // ── Soal dummy (5 topik × 4 soal = 20 soal) ──────────────────────
        $questions = [

            // BILANGAN BULAT
            ['text' => 'Berapa hasil dari 25 + 37?',              'topic' => 'bilangan_bulat', 'difficulty' => 'easy',
             'options' => [['52',false],['62',true],['72',false],['82',false]]],

            ['text' => 'Budi memiliki 84 kelereng. Ia memberikan 29 kepada temannya. Berapa sisa kelereng Budi?',
             'topic' => 'bilangan_bulat', 'difficulty' => 'easy',
             'options' => [['45',false],['55',true],['65',false],['75',false]]],

            ['text' => 'Hasil dari 6 × 8 adalah …',              'topic' => 'bilangan_bulat', 'difficulty' => 'easy',
             'options' => [['42',false],['48',true],['54',false],['56',false]]],

            ['text' => '144 dibagi 12 hasilnya adalah …',        'topic' => 'bilangan_bulat', 'difficulty' => 'medium',
             'options' => [['10',false],['11',false],['12',true],['13',false]]],

            // PECAHAN
            ['text' => 'Manakah pecahan yang sama nilainya dengan ½?', 'topic' => 'pecahan', 'difficulty' => 'easy',
             'options' => [['2/3',false],['3/4',false],['4/8',true],['3/5',false]]],

            ['text' => '¾ + ¼ = …',                              'topic' => 'pecahan', 'difficulty' => 'easy',
             'options' => [['4/8',false],['4/4 (1)',true],['2/4',false],['3/8',false]]],

            ['text' => 'Ubahlah 0,75 ke bentuk pecahan paling sederhana.',
             'topic' => 'pecahan', 'difficulty' => 'medium',
             'options' => [['75/100',false],['7/10',false],['3/4',true],['4/5',false]]],

            ['text' => 'Ani memiliki ⅔ potong kue. Budi memiliki ⅓ potong kue. Berapa jumlah kue mereka?',
             'topic' => 'pecahan', 'difficulty' => 'medium',
             'options' => [['2/6',false],['3/6',false],['1 (satu)',true],['5/6',false]]],

            // GEOMETRI
            ['text' => 'Sebuah persegi panjang memiliki panjang 8 cm dan lebar 5 cm. Berapakah luasnya?',
             'topic' => 'geometri', 'difficulty' => 'easy',
             'options' => [['26 cm²',false],['30 cm²',false],['40 cm²',true],['45 cm²',false]]],

            ['text' => 'Keliling sebuah persegi dengan sisi 7 cm adalah …',
             'topic' => 'geometri', 'difficulty' => 'easy',
             'options' => [['14 cm',false],['21 cm',false],['28 cm',true],['49 cm',false]]],

            ['text' => 'Sebuah segitiga memiliki alas 10 cm dan tinggi 6 cm. Berapa luasnya?',
             'topic' => 'geometri', 'difficulty' => 'medium',
             'options' => [['16 cm²',false],['30 cm²',true],['60 cm²',false],['120 cm²',false]]],

            ['text' => 'Sudut siku-siku besarnya …',
             'topic' => 'geometri', 'difficulty' => 'easy',
             'options' => [['45°',false],['60°',false],['90°',true],['180°',false]]],

            // STATISTIKA
            ['text' => 'Data nilai ujian: 70, 80, 90, 60, 80. Berapakah nilai rata-ratanya?',
             'topic' => 'statistika', 'difficulty' => 'medium',
             'options' => [['72',false],['76',true],['80',false],['82',false]]],

            ['text' => 'Dari data: 5, 3, 8, 3, 7, 3, 9 — berapakah modusnya?',
             'topic' => 'statistika', 'difficulty' => 'easy',
             'options' => [['5',false],['7',false],['3',true],['9',false]]],

            ['text' => 'Data: 10, 20, 30, 40, 50. Berapakah mediannya?',
             'topic' => 'statistika', 'difficulty' => 'easy',
             'options' => [['20',false],['25',false],['30',true],['40',false]]],

            ['text' => 'Diagram batang menunjukkan: Senin=5, Selasa=8, Rabu=6. Berapa total siswa 3 hari tersebut?',
             'topic' => 'statistika', 'difficulty' => 'easy',
             'options' => [['17',false],['18',false],['19',true],['20',false]]],

            // ALJABAR / POLA
            ['text' => 'Pola bilangan: 2, 4, 6, 8, … Bilangan berikutnya adalah?',
             'topic' => 'aljabar', 'difficulty' => 'easy',
             'options' => [['9',false],['10',true],['11',false],['12',false]]],

            ['text' => 'Jika x + 5 = 12, maka x = …',           'topic' => 'aljabar', 'difficulty' => 'easy',
             'options' => [['5',false],['6',false],['7',true],['8',false]]],

            ['text' => 'Pola: 1, 4, 9, 16, … Bilangan berikutnya adalah?',
             'topic' => 'aljabar', 'difficulty' => 'medium',
             'options' => [['20',false],['24',false],['25',true],['36',false]]],

            ['text' => 'Jika 3y = 21, maka y = …',               'topic' => 'aljabar', 'difficulty' => 'easy',
             'options' => [['6',false],['7',true],['8',false],['9',false]]],
        ];

        foreach ($questions as $i => $q) {
            $question = Question::firstOrCreate(
                ['question_text' => $q['text']],
                [
                    'topic'       => $q['topic'],
                    'difficulty'  => $q['difficulty'],
                    'is_active'   => true,
                    'order'       => $i + 1,
                    'video_path'  => null, // isi dengan path video saat video sudah tersedia
                    'subtitle_path' => null,
                ]
            );

            if ($question->wasRecentlyCreated) {
                foreach ($q['options'] as $j => [$text, $correct]) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $text,
                        'is_correct'  => $correct,
                        'order'       => $j,
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder selesai: ' . count($schools) . ' sekolah, ' . count($questions) . ' soal.');
    }
}
