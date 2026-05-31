<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            ['name' => 'SLB Negeri Semarang',           'address' => 'Jl. Elang Raya No.7, Semarang'],
            ['name' => 'SLB ABC Swadaya Kendal',         'address' => 'Jl. Soekarno Hatta, Kendal'],
            ['name' => 'SLB B YRTRW Surakarta',          'address' => 'Jl. Bibis Luhur No.1, Surakarta'],
            ['name' => 'SLB Negeri Salatiga',            'address' => 'Jl. Hasanudin No.83, Salatiga'],
            ['name' => 'SLB B Prima Bhakti Mulia Cirebon', 'address' => 'Jl. Perjuangan No.29, Cirebon'],
            ['name' => 'SLB Negeri Purwokerto',          'address' => 'Jl. Budi Utomo No.8, Purwokerto'],
            ['name' => 'SLB Marsudi Putra I Bantul',     'address' => 'Jl. Imogiri Barat Km.11, Bantul'],
            ['name' => 'SLB B Karnnamanohara Yogyakarta', 'address' => 'Jl. Pandean No.2, Yogyakarta'],
        ];

        foreach ($schools as $school) {
            School::firstOrCreate(['name' => $school['name']], $school);
        }

        $this->command->info('✓ SchoolSeeder: ' . count($schools) . ' sekolah berhasil diisi.');
    }
}
