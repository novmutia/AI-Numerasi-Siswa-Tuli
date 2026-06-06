@extends('layouts.app')

@section('title', 'Statistik — NumerasiTuli')
@section('page-title', 'Statistik')
@section('page-subtitle', 'Ringkasan hasil asesmen seluruh siswa')

@section('content')
    <div class="max-w-5xl mx-auto space-y-5">

        {{-- ── FILTER SEKOLAH ── --}}
        <div id="tour-statistics-filter" class="card !py-4">
            <form method="GET" action="{{ route('statistics') }}" id="filterForm"
                class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <label class="text-xs font-semibold text-slate-500 flex-shrink-0 mt-2 sm:mt-0">Filter Sekolah:</label>
                
                <input type="hidden" name="school_id" id="schoolInput" value="{{ $schoolId }}">
                
                @php
                    $selectedName = 'Semua Sekolah';
                    foreach ($schools as $school) {
                        if ($schoolId == $school->id) {
                            $selectedName = $school->name;
                            break;
                        }
                    }
                @endphp
    
                {{-- Custom Dropdown Select --}}
                <div class="relative w-full sm:max-w-xs" id="customDropdown">
                    <button type="button" onclick="toggleDropdown()" id="dropdownTrigger"
                            class="w-full flex items-center justify-between border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white hover:border-violet-300 focus:outline-none focus:ring-2 focus:ring-violet-400 transition-all cursor-pointer shadow-sm">
                        <span id="dropdownLabel" class="font-medium truncate mr-2">{{ $selectedName }}</span>
                        <svg id="dropdownIcon" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
    
                    <div id="dropdownMenu" class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-xl opacity-0 invisible -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5">
                            <button type="button" onclick="selectDropdown('')"
                                    class="dropdown-option w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors flex items-center justify-between group {{ empty($schoolId) ? 'bg-violet-50 text-violet-700' : '' }}" data-value="">
                                <span class="font-medium {{ empty($schoolId) ? 'text-violet-700' : 'text-slate-700' }} group-hover:text-violet-700 dropdown-text">Semua Sekolah</span>
                                <svg class="w-4 h-4 text-violet-600 dropdown-check {{ empty($schoolId) ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            
                            @foreach($schools as $school)
                            <button type="button" onclick="selectDropdown('{{ $school->id }}')"
                                    class="dropdown-option w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors flex items-center justify-between group {{ $schoolId == $school->id ? 'bg-violet-50 text-violet-700' : '' }}" data-value="{{ $school->id }}">
                                <span class="font-medium {{ $schoolId == $school->id ? 'text-violet-700' : 'text-slate-700' }} group-hover:text-violet-700 dropdown-text">{{ $school->name }}</span>
                                <svg class="w-4 h-4 text-violet-600 dropdown-check {{ $schoolId == $school->id ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
    
                <button type="button" onclick="selectDropdown('')" id="btnResetFilter" class="hidden inline-flex items-center gap-1 text-xs text-slate-400 hover:text-red-500 font-medium transition-colors ml-auto sm:ml-0">
                    Reset filter
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>


        {{-- ── STAT CARDS ── --}}
        <div id="tour-statistics-summary" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['id' => 'val_total_siswa', 'value' => $totalSiswa, 'label' => 'Total Siswa', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'],
                ['id' => 'val_total_asesmen', 'value' => $totalAsesmen, 'label' => 'Total Asesmen', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'],
                ['id' => 'val_siswa_nsi', 'value' => $distribusiLevel['NSI'] ?? 0, 'label' => 'Siswa NSI', 'bg' => 'bg-red-50', 'text' => 'text-red-600', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>'],
                ['id' => 'val_siswa_advanced', 'value' => $distribusiLevel['Advanced'] ?? 0, 'label' => 'Siswa Advanced', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>']
            ] as $s)
                <div id="tour-stat-{{ $s['id'] }}" class="fade-in card flex items-center gap-3">
                    <div
                        class="{{ $s['bg'] }} {{ $s['text'] }} w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0">
                        {!! $s['icon'] !!}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-xl leading-tight" id="{{ $s['id'] }}">{{ $s['value'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $s['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>


        {{-- ── DISTRIBUSI LEVEL + RATA AKURASI ── --}}
        <div id="tour-statistics-distribution" class="grid md:grid-cols-2 gap-4">

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
                                        <span class="text-xs font-bold text-slate-700" id="dist_val_{{ $level }}">{{ $jml }} siswa</span>
                                        <span class="text-[10px] text-slate-400 ml-1" id="dist_pct_{{ $level }}">({{ $pct }}%)</span>
                                    </div>
                                </div>
                                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="{{ $cfg['bar'] }} h-full rounded-full transition-all duration-700"
                                        id="dist_bar_{{ $level }}" style="width: {{ $pct }}%"></div>
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
                                    <div class="{{ $cfg['bar'] }} h-full rounded-full transition-all duration-700"
                                        id="rata_bar_{{ $level }}" style="width: {{ $rata ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 w-10 text-right flex-shrink-0" id="rata_val_{{ $level }}">
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
        <div class="fade-in card" id="weaknessCard" style="{{ empty($semuaWeakness) ? 'display:none;' : '' }}">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Dimensi yang Paling Banyak Lemah</h3>
            <div class="flex flex-wrap gap-3" id="weaknessContainer">
                @if (!empty($semuaWeakness))
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
                @endif
            </div>
        </div>


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
                        <tbody class="divide-y divide-slate-50" id="perSekolahTable">
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
                                <tr class="hover:bg-slate-50 transition-colors school-row" data-school-id="{{ $s['id'] }}">
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
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Belum ada data statistik sekolah</p>
                    <p class="text-xs text-slate-400 mt-1">Data akan muncul setelah ada asesmen selesai</p>
                </div>
            @endif
        </div>


        {{-- ── HASIL TERBARU ── --}}
        <div id="hasilTerbaruCard" style="{{ $hasilTerbaru->count() > 0 ? '' : 'display:none;' }}">
            <div class="fade-in card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">10 Hasil Asesmen Terbaru</h3>
                    <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 text-xs text-violet-600 font-semibold bg-violet-50 hover:bg-violet-100 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                        <tbody class="divide-y divide-slate-50" id="hasilTerbaruTable">
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
        </div>
        <div id="hasilTerbaruEmpty" style="{{ $hasilTerbaru->count() == 0 ? '' : 'display:none;' }}">
            <div class="fade-in card text-center py-8">
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="font-semibold text-slate-600 text-sm">Belum ada hasil asesmen terbaru</p>
                <p class="text-xs text-slate-400 mt-1">Data akan muncul di sini setelah ada siswa yang menyelesaikan asesmen.</p>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const allStudents = @json($allStudentsJS);
    const allResults = @json($allResultsJS);

    let dropdownOpen = false;
    
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu');
        const icon = document.getElementById('dropdownIcon');
        const trigger = document.getElementById('dropdownTrigger');
        
        dropdownOpen = !dropdownOpen;
        
        if (dropdownOpen) {
            menu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
            menu.classList.add('opacity-100', 'visible', 'translate-y-0');
            icon.classList.add('rotate-180');
            trigger.classList.add('border-violet-300', 'ring-2', 'ring-violet-100');
        } else {
            menu.classList.add('opacity-0', 'invisible', '-translate-y-2');
            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
            icon.classList.remove('rotate-180');
            trigger.classList.remove('border-violet-300', 'ring-2', 'ring-violet-100');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('customDropdown');
        if (dropdownOpen && dropdown && !dropdown.contains(e.target)) {
            toggleDropdown();
        }
    });

    function selectDropdown(id) {
        document.getElementById('schoolInput').value = id;
        
        // Update UI of the dropdown
        document.querySelectorAll('.dropdown-option').forEach(btn => {
            const val = btn.dataset.value;
            const textEl = btn.querySelector('.dropdown-text');
            const checkEl = btn.querySelector('.dropdown-check');
            
            if (val === id) {
                btn.classList.add('bg-violet-50', 'text-violet-700');
                textEl.classList.remove('text-slate-700');
                textEl.classList.add('text-violet-700');
                checkEl.classList.remove('hidden');
                document.getElementById('dropdownLabel').textContent = textEl.textContent;
            } else {
                btn.classList.remove('bg-violet-50', 'text-violet-700');
                textEl.classList.add('text-slate-700');
                textEl.classList.remove('text-violet-700');
                checkEl.classList.add('hidden');
            }
        });
        
        toggleDropdown();
        
        // Show/hide Reset filter button
        const btnReset = document.getElementById('btnResetFilter');
        if (id) {
            btnReset.classList.remove('hidden');
        } else {
            btnReset.classList.add('hidden');
        }
        
        // Apply Client-Side Filtering
        applyFilter(id);
    }
    
    function applyFilter(schoolId) {
        // Parse ID to string for comparison or empty
        const sId = schoolId ? String(schoolId) : '';
        
        // 1. Filter Data
        const filteredStudents = sId ? allStudents.filter(s => String(s.school_id) === sId) : allStudents;
        const filteredResults = sId ? allResults.filter(r => String(r.school_id) === sId) : allResults;
        
        // 2. Calculate Totals
        const totalSiswa = filteredStudents.length;
        const totalAsesmen = filteredResults.length;
        document.getElementById('val_total_siswa').textContent = totalSiswa;
        document.getElementById('val_total_asesmen').textContent = totalAsesmen;
        
        // 3. Calculate Distribution & Rata Akurasi
        const dist = { 'NSI': 0, 'Basic': 0, 'Proficient': 0, 'Advanced': 0 };
        const akurasiSum = { 'NSI': 0, 'Basic': 0, 'Proficient': 0, 'Advanced': 0 };
        
        filteredResults.forEach(r => {
            if (dist[r.level] !== undefined) {
                dist[r.level]++;
                akurasiSum[r.level] += parseFloat(r.accuracy);
            }
        });
        
        document.getElementById('val_siswa_nsi').textContent = dist['NSI'];
        document.getElementById('val_siswa_advanced').textContent = dist['Advanced'];
        
        const levels = ['NSI', 'Basic', 'Proficient', 'Advanced'];
        levels.forEach(lvl => {
            // Update Distribution
            const count = dist[lvl];
            const pct = totalAsesmen > 0 ? ((count / totalAsesmen) * 100).toFixed(1) : 0;
            const pctVal = Number.isInteger(Number(pct)) ? parseInt(pct) : pct; // Remove .0 if integer
            
            document.getElementById('dist_val_' + lvl).textContent = count + ' siswa';
            document.getElementById('dist_pct_' + lvl).textContent = '(' + pctVal + '%)';
            document.getElementById('dist_bar_' + lvl).style.width = pct + '%';
            
            // Update Rata Akurasi
            const sum = akurasiSum[lvl];
            const rata = count > 0 ? (sum / count).toFixed(1) : null;
            const rataVal = rata ? (Number.isInteger(Number(rata)) ? parseInt(rata) : rata) : null;
            
            document.getElementById('rata_val_' + lvl).textContent = rataVal ? rataVal + '%' : '—';
            document.getElementById('rata_bar_' + lvl).style.width = rataVal ? rataVal + '%' : '0%';
        });
        
        // 4. Update Dimensi Lemah
        const weaknessesCount = {};
        filteredResults.forEach(r => {
            r.weaknesses.forEach(w => {
                weaknessesCount[w] = (weaknessesCount[w] || 0) + 1;
            });
        });
        
        // Sort and take top 5
        const sortedWeaknesses = Object.keys(weaknessesCount).sort((a,b) => weaknessesCount[b] - weaknessesCount[a]).slice(0, 5);
        const maxW = sortedWeaknesses.length > 0 ? weaknessesCount[sortedWeaknesses[0]] : 0;
        
        const wContainer = document.getElementById('weaknessContainer');
        const wCard = document.getElementById('weaknessCard');
        
        if (sortedWeaknesses.length > 0) {
            wCard.style.display = '';
            wContainer.innerHTML = sortedWeaknesses.map(dimensi => {
                const jml = weaknessesCount[dimensi];
                const pct = maxW > 0 ? Math.round((jml / maxW) * 100) : 0;
                return `
                    <div class="flex-1 min-w-[180px] bg-slate-50 rounded-xl p-3 border border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-700">${dimensi}</span>
                            <span class="text-xs font-bold text-red-500">${jml} siswa</span>
                        </div>
                        <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="bg-red-400 h-full rounded-full" style="width:${pct}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            wCard.style.display = 'none';
            wContainer.innerHTML = '';
        }
        
        // 5. Update Hasil Terbaru
        const hContainer = document.getElementById('hasilTerbaruTable');
        const hCard = document.getElementById('hasilTerbaruCard');
        const hEmpty = document.getElementById('hasilTerbaruEmpty');
        
        const top10 = filteredResults.slice(0, 10);
        if (top10.length > 0) {
            hCard.style.display = '';
            hEmpty.style.display = 'none';
            
            const badges = {
                'NSI': 'bg-red-100 text-red-600',
                'Basic': 'bg-amber-100 text-amber-600',
                'Proficient': 'bg-blue-100 text-blue-600',
                'Advanced': 'bg-green-100 text-green-600'
            };
            
            hContainer.innerHTML = top10.map(h => {
                const badge = badges[h.level] || 'bg-slate-100 text-slate-500';
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-2.5 text-xs font-medium text-slate-700">${h.nama}</td>
                        <td class="py-2.5 text-xs text-slate-500">${h.sekolah}</td>
                        <td class="py-2.5 text-center">
                            <span class="${badge} text-[10px] font-bold px-2 py-0.5 rounded-lg">${h.level}</span>
                        </td>
                        <td class="py-2.5 text-center text-xs font-bold text-slate-600">${h.accuracy}%</td>
                        <td class="py-2.5 text-xs text-slate-500">${h.dimensi_lemah}</td>
                        <td class="py-2.5 text-right text-[11px] text-slate-400">${h.tanggal}</td>
                    </tr>
                `;
            }).join('');
        } else {
            hCard.style.display = 'none';
            hEmpty.style.display = '';
            hContainer.innerHTML = '';
        }
        
        // 6. Update Statistik Per Sekolah table
        document.querySelectorAll('.school-row').forEach(row => {
            if (sId === '' || row.dataset.schoolId === sId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Driver.js Tour Logic
    startTourWhenReady('tour_statistics', [
        { element: '#tour-statistics-filter', popover: { title: 'Sistem Filter', description: 'Gunakan filter ini. Saat Anda memilih sekolah, seluruh angka, grafik, dan tabel di bawah akan otomatis menyesuaikan!' } },
        { element: '#tour-stat-val_total_siswa', popover: { title: 'Total Siswa', description: 'Jumlah total siswa dari sekolah yang dipilih.' } },
        { element: '#tour-stat-val_total_asesmen', popover: { title: 'Total Asesmen', description: 'Total asesmen yang sudah diselesaikan oleh siswa dari sekolah tersebut.' } },
        { element: '#tour-stat-val_siswa_nsi', popover: { title: 'Siswa NSI', description: 'Jumlah siswa yang masuk kategori Needs Special Intervention (Perlu Intervensi Khusus).' } },
        { element: '#tour-stat-val_siswa_advanced', popover: { title: 'Siswa Advanced', description: 'Jumlah siswa yang masuk kategori Sangat Mahir.' } },
        { element: '#tour-statistics-distribution', popover: { title: 'Distribusi dan Akurasi', description: 'Lihat penyebaran level kemampuan siswa dan seberapa akurat rata-rata jawaban mereka per level.' } },
        { element: '#weaknessCard', popover: { title: 'Fokus Perbaikan', description: 'Sistem secara otomatis mengumpulkan 5 dimensi materi yang paling sering dijawab salah oleh siswa. Ini bisa jadi fokus pembelajaran berikutnya.' } }
    ]);
</script>
@endpush
