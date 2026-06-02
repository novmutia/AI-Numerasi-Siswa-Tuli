@extends('layouts.app')

@section('title', 'Statistik — NumerasiTuli')
@section('page-title', 'Statistik')
@section('page-subtitle', 'Ringkasan hasil asesmen seluruh siswa')

@section('content')
    <div class="max-w-5xl mx-auto space-y-5">

        {{-- ── FILTER SEKOLAH ── --}}
        <div class="card !py-4">
            <form method="GET" action="{{ route('statistics') }}"
                class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <label class="text-xs font-semibold text-slate-500 flex-shrink-0">Filter Sekolah:</label>
                <select name="school_id" onchange="this.form.submit()"
                    class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
                    <option value="">Semua Sekolah</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @if ($schoolId)
                    <a href="{{ route('statistics') }}" class="text-xs text-slate-400 hover:text-slate-600 font-medium">
                        Reset filter ✕
                    </a>
                @endif
            </form>
        </div>


        {{-- ── STAT CARDS ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['value' => $totalSiswa, 'label' => 'Total Siswa', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'icon' => '👤'], ['value' => $totalAsesmen, 'label' => 'Total Asesmen', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => '📋'], ['value' => $distribusiLevel['NSI'] ?? 0, 'label' => 'Siswa NSI', 'bg' => 'bg-red-50', 'text' => 'text-red-600', 'icon' => '🔴'], ['value' => $distribusiLevel['Advanced'] ?? 0, 'label' => 'Siswa Advanced', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => '🏆']] as $s)
                <div class="fade-in card flex items-center gap-3">
                    <div
                        class="{{ $s['bg'] }} w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                        {{ $s['icon'] }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-xl leading-tight">{{ $s['value'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $s['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>


        {{-- ── DISTRIBUSI LEVEL + RATA AKURASI ── --}}
        <div class="grid md:grid-cols-2 gap-4">

            {{-- Distribusi Level --}}
            <div class="fade-in card">
                <h3 class="font-bold text-slate-800 text-sm mb-4">Distribusi Level Kemampuan</h3>
                @php
                    $totalDist = array_sum($distribusiLevel);
                    $levelConfig = [
                        'NSI' => [
                            'bar' => 'bg-red-400',
                            'badge' => 'bg-red-100 text-red-600',
                            'desc' => 'Perlu Intervensi',
                        ],
                        'Basic' => [
                            'bar' => 'bg-amber-400',
                            'badge' => 'bg-amber-100 text-amber-600',
                            'desc' => 'Pemahaman Dasar',
                        ],
                        'Proficient' => [
                            'bar' => 'bg-blue-400',
                            'badge' => 'bg-blue-100 text-blue-600',
                            'desc' => 'Cukup Mahir',
                        ],
                        'Advanced' => [
                            'bar' => 'bg-green-500',
                            'badge' => 'bg-green-100 text-green-600',
                            'desc' => 'Sangat Mahir',
                        ],
                    ];
                @endphp
                @if ($totalDist > 0)
                    <div class="space-y-3">
                        @foreach ($levelConfig as $level => $cfg)
                            @php
                                $jml = $distribusiLevel[$level] ?? 0;
                                $pct = $totalDist > 0 ? round(($jml / $totalDist) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $cfg['badge'] }} text-[10px] font-bold px-2 py-0.5 rounded-lg">
                                            {{ $level }}
                                        </span>
                                        <span class="text-xs text-slate-500">{{ $cfg['desc'] }}</span>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <span class="text-xs font-bold text-slate-700">{{ $jml }} siswa</span>
                                        <span class="text-[10px] text-slate-400 ml-1">({{ $pct }}%)</span>
                                    </div>
                                </div>
                                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="{{ $cfg['bar'] }} h-full rounded-full transition-all duration-700"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-slate-400 text-sm">Belum ada data asesmen</p>
                    </div>
                @endif
            </div>

            {{-- Rata Akurasi per Level --}}
            <div class="fade-in card">
                <h3 class="font-bold text-slate-800 text-sm mb-4">Rata-rata Akurasi per Level</h3>
                @if (count($rataAkurasi) > 0)
                    <div class="space-y-3">
                        @foreach ($levelConfig as $level => $cfg)
                            @php $rata = $rataAkurasi[$level] ?? null; @endphp
                            <div class="flex items-center gap-3">
                                <span
                                    class="{{ $cfg['badge'] }} text-[10px] font-bold px-2 py-0.5 rounded-lg w-20 text-center flex-shrink-0">
                                    {{ $level }}
                                </span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="{{ $cfg['bar'] }} h-full rounded-full"
                                        style="width: {{ $rata ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 w-10 text-right flex-shrink-0">
                                    {{ $rata ? $rata . '%' : '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-slate-400 text-sm">Belum ada data akurasi</p>
                    </div>
                @endif
            </div>
        </div>


        {{-- ── DIMENSI PALING LEMAH ── --}}
        @if (!empty($semuaWeakness))
            <div class="fade-in card">
                <h3 class="font-bold text-slate-800 text-sm mb-4">Dimensi yang Paling Banyak Lemah</h3>
                <div class="flex flex-wrap gap-3">
                    @php $maxW = max(array_values($semuaWeakness)); @endphp
                    @foreach ($semuaWeakness as $dimensi => $jml)
                        @php $pct = $maxW > 0 ? round(($jml / $maxW) * 100) : 0; @endphp
                        <div class="flex-1 min-w-[180px] bg-slate-50 rounded-xl p-3 border border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-700">{{ $dimensi }}</span>
                                <span class="text-xs font-bold text-red-500">{{ $jml }} siswa</span>
                            </div>
                            <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="bg-red-400 h-full rounded-full" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        {{-- ── STATISTIK PER SEKOLAH ── --}}
        <div class="fade-in card">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Ringkasan per Sekolah</h3>
            @if ($perSekolah->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2 pr-4">Sekolah</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2 px-3">Siswa</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2 px-3">Asesmen</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2 px-3">Rata Akurasi</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2 px-3">Level Dominan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($perSekolah as $s)
                                @php
                                    $lvlBadge =
                                        [
                                            'NSI' => 'bg-red-100 text-red-600',
                                            'Basic' => 'bg-amber-100 text-amber-600',
                                            'Proficient' => 'bg-blue-100 text-blue-600',
                                            'Advanced' => 'bg-green-100 text-green-600',
                                        ][$s['level_dominan']] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 pr-4">
                                        <p class="font-medium text-slate-700 text-xs">{{ $s['nama'] }}</p>
                                    </td>
                                    <td class="py-3 px-3 text-center text-xs text-slate-600 font-semibold">
                                        {{ $s['total_siswa'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center text-xs text-slate-600 font-semibold">
                                        {{ $s['total_asesmen'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            class="text-xs font-bold
                                {{ $s['rata_akurasi'] !== '-' && $s['rata_akurasi'] >= 70 ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $s['rata_akurasi'] !== '-' ? $s['rata_akurasi'] . '%' : '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if ($s['level_dominan'] !== '-')
                                            <span class="{{ $lvlBadge }} text-[10px] font-bold px-2 py-0.5 rounded-lg">
                                                {{ $s['level_dominan'] }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-3xl mb-2">🏫</p>
                    <p class="text-slate-500 text-sm font-medium">Belum ada data statistik sekolah</p>
                    <p class="text-xs text-slate-400 mt-1">Data akan muncul setelah ada asesmen selesai</p>
                </div>
            @endif
        </div>


        {{-- ── HASIL TERBARU ── --}}
        @if ($hasilTerbaru->count() > 0)
            <div class="fade-in card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">10 Hasil Asesmen Terbaru</h3>
                    <a href="{{ route('students.index') }}" class="text-xs text-violet-600 font-semibold hover:underline">
                        Lihat Semua →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2">Siswa</th>
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2">Sekolah</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2">Level</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2">Akurasi</th>
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2">Dimensi Lemah</th>
                                <th class="text-right text-xs font-semibold text-slate-400 pb-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($hasilTerbaru as $h)
                                @php
                                    $lb =
                                        [
                                            'NSI' => 'bg-red-100 text-red-600',
                                            'Basic' => 'bg-amber-100 text-amber-600',
                                            'Proficient' => 'bg-blue-100 text-blue-600',
                                            'Advanced' => 'bg-green-100 text-green-600',
                                        ][$h['level']] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-2.5 text-xs font-medium text-slate-700">
                                        {{ $h['nama'] }}
                                    </td>
                                    <td class="py-2.5 text-xs text-slate-500">{{ $h['sekolah'] }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="{{ $lb }} text-[10px] font-bold px-2 py-0.5 rounded-lg">
                                            {{ $h['level'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-center text-xs font-bold text-slate-600">
                                        {{ $h['akurasi'] }}%
                                    </td>
                                    <td class="py-2.5 text-xs text-slate-500">{{ $h['dimensi_lemah'] ?? '—' }}</td>
                                    <td class="py-2.5 text-right text-[11px] text-slate-400">{{ $h['tanggal'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection
