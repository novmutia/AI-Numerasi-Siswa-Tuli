<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini MENAMBAH 2 kolom ke tabel options yang sudah ada.
 * Jalankan dengan: php artisan migrate
 *
 * Tidak merusak data yang sudah ada karena semua kolom baru nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('options', function (Blueprint $table) {

            // Indikator pola kemampuan siswa dari pilihan ini
            // Contoh: 'tidak_memahami_operasi', 'strategi_tepat', 'salah_hitung_minor'
            // TIDAK ditampilkan ke siswa — hanya dibaca DiagnosisService
            $table->string('indicator')->nullable()->after('is_correct');

            // Level yang diasosiasikan ke pilihan ini
            // NSI / Basic / Proficient / Advanced
            // Dipakai untuk hitung distribusi level dari jawaban siswa
            $table->string('level_value')->nullable()->after('indicator');
        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn(['indicator', 'level_value']);
        });
    }
};
