@extends('layouts.app')

@section('title', 'Hasil Asesmen — NumerasiTuli')
@section('page-title', 'Hasil Diagnosis')
@section('page-subtitle', $result['student_name'] . ' · ' . $result['school_name'])

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ── HEADER RESULT ── --}}
    @php
        $levelConfig = [
            'NSI'       => ['bg'=>'bg-red-500',   'soft'=>'bg-red-50',   'text'=>'text-red-600',   'border'=>'border-red-200', 'desc'=>'Belum Cukup Indikator'],
            'Basic'     => ['bg'=>'bg-amber-500', 'soft'=>'bg-amber-50', 'text'=>'text-amber-600', 'border'=>'border-amber-200', 'desc'=>'Kemampuan Dasar'],
            'Proficient'=> ['bg'=>'bg-blue-500',  'soft'=>'bg-blue-50',  'text'=>'text-blue-600',  'border'=>'border-blue-200', 'desc'=>'Cakap'],
            'Advanced'  => ['bg'=>'bg-green-500', 'soft'=>'bg-green-50', 'text'=>'text-green-600', 'border'=>'border-green-200', 'desc'=>'Mahir'],
        ];
        $lc = $levelConfig[$result['level']] ?? $levelConfig['NSI'];
    @endphp

    <div class="card relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 {{ $lc['soft'] }} rounded-full -translate-y-1/2 translate-x-1/4 opacity-60"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div>
                <p class="text-xs text-slate-400 mb-1">Hasil Asesmen Numerasi</p>
                <h2 class="font-bold text-slate-800 text-xl mb-1">{{ $result['student_name'] }}</h2>
                <p class="text-sm text-slate-500">{{ $result['school_name'] }} · {{ now()->format('d M Y') }}</p>
            </div>
            <div class="flex flex-col items-center {{ $lc['soft'] }} border {{ $lc['border'] }} rounded-2xl px-7 py-4 text-center flex-shrink-0">
                <span class="font-bold text-2xl {{ $lc['text'] }}">{{ $result['level'] }}</span>
                <span class="text-xs text-slate-500 mt-0.5">{{ $lc['desc'] }}</span>
            </div>
        </div>
    </div>


    {{-- ── SKOR & AKURASI ── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card text-center">
            <p class="text-2xl font-bold text-slate-800">{{ $result['correct'] }}/{{ $result['total'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Jawaban Benar</p>
        </div>
        <div class="card text-center">
            <p class="text-2xl font-bold text-violet-600">{{ $result['accuracy'] }}%</p>
            <p class="text-xs text-slate-400 mt-1">Akurasi</p>
        </div>
        <div class="card text-center">
            <p class="text-2xl font-bold {{ $lc['text'] }}">{{ $result['level'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Level Diagnosa</p>
        </div>
    </div>


    {{-- ── PROBABILITAS PER TOPIK ── --}}
    <div class="card">
        <h3 class="font-bold text-slate-800 text-sm mb-4">Performa per Topik</h3>
        <div class="space-y-3">
            @foreach($result['topic_scores'] as $topic)
            @php
                $pct = $topic['score'];
                $barColor = $pct >= 80 ? 'bg-green-500' : ($pct >= 60 ? 'bg-blue-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-red-400'));
                $badge    = $pct >= 80 ? 'bg-green-100 text-green-700' : ($pct >= 60 ? 'bg-blue-100 text-blue-700' : ($pct >= 40 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600'));
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-slate-700">{{ $topic['name'] }}</span>
                        @if($topic['is_weak'])
                        <span class="text-[10px] bg-red-100 text-red-600 font-semibold px-1.5 py-0.5 rounded-full">Perlu Perhatian</span>
                        @endif
                    </div>
                    <span class="text-xs font-bold {{ $badge }} px-2 py-0.5 rounded-lg">{{ $pct }}%</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $topic['correct'] }} benar dari {{ $topic['total'] }} soal</p>
            </div>
            @endforeach
        </div>
    </div>


    {{-- ── KEKURANGAN & REKOMENDASI GURU ── --}}
    <div class="grid md:grid-cols-2 gap-4">

        {{-- Kekurangan --}}
        <div class="card border-l-4 border-red-400">
            <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <span class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center text-xs"></span>
                Area yang Perlu Ditingkatkan
            </h3>
            @if(count($result['weaknesses']) > 0)
            <ul class="space-y-2">
                @foreach($result['weaknesses'] as $w)
                <li class="flex items-start gap-2 text-sm text-slate-600">
                    <span class="text-red-400 mt-0.5 flex-shrink-0">•</span>
                    <span>{{ $w }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-green-600 font-medium">Tidak ada kelemahan signifikan</p>
            @endif
        </div>

        {{-- Rekomendasi --}}
        <div class="card border-l-4 border-violet-400">
            <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                <span class="w-6 h-6 bg-violet-100 rounded-lg flex items-center justify-center text-xs"></span>
                Rekomendasi untuk Guru
            </h3>
            <ul class="space-y-2">
                @foreach($result['recommendations'] as $rec)
                <li class="flex items-start gap-2 text-sm text-slate-600">
                    <span class="text-violet-400 mt-0.5 flex-shrink-0">→</span>
                    <span>{{ $rec }}</span>
                </li>
                @endforeach
            </ul>
        </div>

    </div>


    {{-- ── CATATAN AI ── --}}
    <div class="card bg-violet-50 border border-violet-100">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white text-sm"></span>
            </div>
            <div>
                <p class="font-bold text-violet-800 text-sm">Catatan Diagnosis AI</p>
                <p class="text-violet-700 text-sm mt-1 leading-relaxed">{{ $result['ai_note'] }}</p>
            </div>
        </div>
    </div>


    {{-- ── ACTIONS ── --}}
    <div class="flex flex-col sm:flex-row gap-3 pb-4">
        <a href="{{ route('assessment.start') }}"
           class="flex-1 flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-md shadow-violet-200">
            Asesmen Siswa Lain
        </a>
        <a href="{{ route('students.index') }}"
           class="flex-1 flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold py-3 rounded-xl transition-colors text-sm">
            Lihat Data Siswa
        </a>
        <button onclick="window.print()"
                class="flex-1 flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold py-3 rounded-xl transition-colors text-sm">
            Cetak Hasil
        </button>
    </div>

</div>
@endsection

@push('head')
<style>
    @media print {
        aside, header, footer, .no-print { display: none !important; }
        .main-area { margin: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
</style>
@endpush
