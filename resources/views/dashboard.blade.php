@extends('layouts.app')

@section('title', 'Beranda — NumerasiTuli')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di NumerasiTuli')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ── HERO CARD ── --}}
    <div class="fade-in relative overflow-hidden rounded-2xl bg-violet-600 p-7 text-white shadow-lg shadow-violet-200">
        {{-- Decorative circles --}}
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -right-4 top-16 w-24 h-24 bg-white/5 rounded-full"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <p class="text-violet-200 text-sm font-medium mb-1">Platform Asesmen</p>
                <h2 class="text-2xl font-bold leading-tight mb-3">Ukur Kemampuan Numerasi<br>Siswa Tuli</h2>
                <p class="text-violet-100 text-sm max-w-sm">Berbasis BISINDO dengan diagnosis AI — inklusif dan mudah digunakan.</p>
            </div>
            <a href="{{ route('assessment.start') }}"
               class="flex-shrink-0 inline-flex items-center gap-2 bg-white text-violet-700 font-bold text-sm px-5 py-3 rounded-xl hover:bg-violet-50 transition-colors shadow-md">
                Mulai Asesmen
            </a>
        </div>
    </div>


    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['value'=>$stats['total_siswa'],     'label'=>'Total Siswa',      'bg'=>'bg-violet-50',  'text'=>'text-violet-700'],
            ['value'=>$stats['total_sekolah'],   'label'=>'Sekolah',          'bg'=>'bg-blue-50',    'text'=>'text-blue-700'],
            ['value'=>$stats['asesmen_selesai'], 'label'=>'Asesmen Selesai',  'bg'=>'bg-green-50',   'text'=>'text-green-700'],
            ['value'=>$stats['rata_skor'],       'label'=>'Rata-rata Skor',   'bg'=>'bg-amber-50',   'text'=>'text-amber-700'],
        ] as $s)
        <div class="fade-in card flex items-center gap-3">
            <div class="{{ $s['bg'] }} w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xl leading-tight">{{ $s['value'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>


    {{-- ── LEVEL KEMAMPUAN + CARA KERJA ── --}}
    <div class="grid md:grid-cols-2 gap-4">

        {{-- Level --}}
        <div class="fade-in card">
            <h3 class="font-bold text-slate-800 mb-4 text-sm">Level Kemampuan</h3>
            <div class="space-y-3">
                @foreach([
                    ['NSI',        '0–39',   'bg-red-100 text-red-600',    'bg-red-400',    20],
                    ['Basic',      '40–59',  'bg-amber-100 text-amber-600','bg-amber-400',  50],
                    ['Proficient', '60–79',  'bg-blue-100 text-blue-600',  'bg-blue-400',   75],
                    ['Advanced',   '80–100', 'bg-green-100 text-green-600','bg-green-500',  100],
                ] as [$label, $range, $badge, $bar, $pct])
                <div class="flex items-center gap-3">
                    <span class="{{ $badge }} text-xs font-bold px-2 py-0.5 rounded-lg w-20 text-center">{{ $label }}</span>
                    <div class="flex-1">
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="{{ $bar }} h-full rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400 w-12 text-right">{{ $range }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Cara Kerja --}}
        <div class="fade-in card">
            <h3 class="font-bold text-slate-800 mb-4 text-sm">Cara Kerja</h3>
            <div class="space-y-3">
                @foreach([
                    ['01', 'Isi Data Siswa',    'Input nama dan pilih sekolah'],
                    ['02',  'Kerjakan Soal',     'Soal video BISINDO interaktif'],
                    ['03',  'Lihat Hasil',       'Diagnosis AI & rekomendasi'],
                ] as [$no, $title, $desc])
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ $no }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm">{{ $title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

@endsection
