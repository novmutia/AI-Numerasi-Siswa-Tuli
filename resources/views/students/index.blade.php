@extends('layouts.app')

@section('title', 'Data Siswa — NumerasiTuli')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola dan lihat hasil asesmen siswa')

@section('content')
<div class="space-y-5">

    {{-- ── RINGKASAN ATAS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @foreach([
            ['val_total_siswa', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>', $summary['total_siswa'], 'Total Siswa', 'bg-violet-50 text-violet-600'],
            ['val_sudah_asesmen', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', $summary['sudah_asesmen'], 'Sudah Asesmen', 'bg-green-50 text-green-600'],
            ['val_belum_asesmen', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', $summary['belum_asesmen'], 'Belum Asesmen', 'bg-amber-50 text-amber-600'],
        ] as [$id, $icon, $val, $label, $color])
        <div class="card flex items-center gap-3 !py-4 transition-all">
            <div class="{{ $color }} w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0">
                {!! $icon !!}
            </div>
            <div>
                <p class="font-bold text-slate-800 text-xl leading-tight" id="{{ $id }}">{{ $val }}</p>
                <p class="text-xs text-slate-400">{{ $label }}</p>
            </div>
        </div>
        @endforeach
    </div>


    {{-- ── FILTER & DAFTAR SISWA ── --}}
    <div class="card !p-0 overflow-hidden">
        {{-- Header Data & Filter --}}
        <div id="tour-students-filter" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-800 text-xl leading-tight" id="val_total_sekolah">{{ $summary['total_sekolah'] }}</p>
                    <p class="text-xs text-slate-400 font-medium">Total Sekolah</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 whitespace-nowrap">Filter Sekolah:</label>
                
                {{-- Custom Dropdown Select --}}
                <div class="relative min-w-[220px]" id="customDropdown">
                    <button type="button" onclick="toggleDropdown()" id="dropdownTrigger"
                            class="w-full flex items-center justify-between border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 bg-white hover:border-violet-300 focus:outline-none focus:ring-2 focus:ring-violet-400 transition-all cursor-pointer shadow-sm">
                        <span id="dropdownLabel" class="font-medium">Semua Sekolah</span>
                        <svg id="dropdownIcon" class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div id="dropdownMenu" class="absolute z-20 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-xl opacity-0 invisible -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5">
                            <button type="button" onclick="selectDropdown('all', 'Semua Sekolah')"
                                    class="dropdown-option w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors flex items-center justify-between"
                                    data-value="all">
                                <span class="font-medium text-slate-700 dropdown-text">Semua Sekolah</span>
                                <svg class="w-4 h-4 text-violet-600 hidden dropdown-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            
                            @foreach($schools as $school)
                            <button type="button" onclick="selectDropdown('{{ $school->id }}', '{{ $school->name }}')"
                                    class="dropdown-option w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors flex items-center justify-between group"
                                    data-value="{{ $school->id }}">
                                <span class="font-medium text-slate-700 dropdown-text group-hover:text-violet-700">{{ $school->name }}</span>
                                <svg class="w-4 h-4 text-violet-600 hidden dropdown-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Tab Scrollable --}}
        <div class="flex items-center gap-1 p-3 border-b border-slate-100 overflow-x-auto" id="schoolTabs">
            <button onclick="filterSchool('all')"
                    class="school-tab active-tab flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap"
                    data-id="all">
                Semua Sekolah
                <span class="tab-count bg-violet-100 text-violet-600 text-xs px-1.5 py-0.5 rounded-full">
                    {{ $summary['total_siswa'] }}
                </span>
            </button>
            @foreach($schools as $school)
            <button onclick="filterSchool('{{ $school->id }}')"
                    class="school-tab flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap text-slate-500 hover:bg-slate-50"
                    data-id="{{ $school->id }}">
                {{ $school->name }}
                <span class="tab-count bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-full">
                    {{ $school->students_count }}
                </span>
            </button>
            @endforeach
        </div>


        {{-- ── DAFTAR SISWA ── --}}
        <div class="p-4">

            {{-- Search --}}
            <div id="tour-students-search" class="relative mb-4">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Cari nama siswa..."
                       oninput="searchStudent(this.value)"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-300 transition">
            </div>

            {{-- Grid Siswa --}}
            <div id="studentGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($students as $student)
                @php
                    $latest   = $student->latestDiagnosis;
                    $levelCfg = [
                        'NSI'        => ['bg'=>'bg-red-100',   'text'=>'text-red-600',   'dot'=>'bg-red-400'],
                        'Basic'      => ['bg'=>'bg-amber-100', 'text'=>'text-amber-600', 'dot'=>'bg-amber-400'],
                        'Proficient' => ['bg'=>'bg-blue-100',  'text'=>'text-blue-600',  'dot'=>'bg-blue-400'],
                        'Advanced'   => ['bg'=>'bg-green-100', 'text'=>'text-green-600', 'dot'=>'bg-green-500'],
                        null         => ['bg'=>'bg-slate-100', 'text'=>'text-slate-400', 'dot'=>'bg-slate-300'],
                    ];
                    $lc = $levelCfg[$latest?->level] ?? $levelCfg[null];
                @endphp
                <div class="student-card cursor-pointer group"
                     data-school="{{ $student->school_id }}"
                     data-name="{{ strtolower($student->name) }}"
                     data-assessed="{{ $latest ? 'true' : 'false' }}"
                     onclick="openDetail({{ $student->id }})">
                    <div class="bg-white border border-slate-100 rounded-2xl p-4 hover:border-violet-300 hover:shadow-md transition-all">
                        {{-- Avatar --}}
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            @if($latest)
                            <span class="{{ $lc['bg'] }} {{ $lc['text'] }} text-xs font-bold px-2 py-0.5 rounded-lg">
                                {{ $latest->level }}
                            </span>
                            @else
                            <span class="bg-slate-100 text-slate-400 text-xs px-2 py-0.5 rounded-lg">Belum</span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <p class="font-semibold text-slate-800 text-sm leading-tight mb-0.5 group-hover:text-violet-700 transition-colors">
                            {{ $student->name }}
                        </p>
                        <p class="text-xs text-slate-400 mb-3">{{ $student->school->name }}</p>

                        {{-- Akurasi bar --}}
                        @if($latest)
                        <div>
                            <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                                <span>Akurasi</span>
                                <span class="font-semibold {{ $lc['text'] }}">{{ $latest->accuracy }}%</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="{{ $lc['dot'] }} h-full rounded-full" style="width:{{ $latest->accuracy }}%"></div>
                            </div>
                        </div>
                        @else
                        <p class="text-[11px] text-slate-400 italic">Belum mengikuti asesmen</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-slate-400">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-sm font-medium">Belum ada data siswa</p>
                    <p class="text-xs mt-1">Siswa akan muncul setelah mengikuti asesmen</p>
                </div>
                @endforelse
            </div>

            {{-- Empty state (untuk search/filter) --}}
            <div id="emptyState" class="hidden text-center py-12 text-slate-400">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-sm font-medium">Siswa tidak ditemukan</p>
            </div>

        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DETAIL SISWA
══════════════════════════════════════════════════════════ --}}
<div id="detailModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     onclick="closeDetailOutside(event)">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    {{-- Modal Box --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        {{-- Loading state --}}
        <div id="modalLoading" class="flex flex-col items-center justify-center py-20 gap-3">
            <svg class="w-8 h-8 animate-spin text-violet-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-sm text-slate-400">Memuat data siswa...</p>
        </div>

        {{-- Content (diisi via JS/AJAX) --}}
        <div id="modalContent" class="hidden"></div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const totalSekolahAsli = {{ $summary['total_sekolah'] }};

    // ── Custom Dropdown Logic ─────────────────────────────────
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
        if (dropdownOpen && !document.getElementById('customDropdown').contains(e.target)) {
            toggleDropdown();
        }
    });

    function selectDropdown(id, name) {
        filterSchool(id, name);
        if (dropdownOpen) toggleDropdown();
    }

    // ── Filter Sekolah Utama ──────────────────────────────────
    function filterSchool(schoolId, schoolName = null) {
        let visible = 0;
        let sudah = 0;
        let belum = 0;

        // Jika nama sekolah tidak diberikan (dari klik tab scroll), cari nama aslinya
        if (!schoolName) {
            document.querySelectorAll('.dropdown-option').forEach(opt => {
                if(opt.dataset.value === schoolId) schoolName = opt.querySelector('.dropdown-text').innerText;
            });
        }

        // Sync Dropdown Text & Checks
        document.getElementById('dropdownLabel').innerText = schoolName || 'Semua Sekolah';
        
        document.querySelectorAll('.dropdown-option').forEach(opt => {
            const isMatch = opt.dataset.value === schoolId;
            const text = opt.querySelector('.dropdown-text');
            const check = opt.querySelector('.dropdown-check');
            
            if (isMatch) {
                opt.classList.add('bg-violet-50');
                text.classList.replace('text-slate-700', 'text-violet-700');
                check.classList.remove('hidden');
            } else {
                opt.classList.remove('bg-violet-50');
                text.classList.replace('text-violet-700', 'text-slate-700');
                check.classList.add('hidden');
            }
        });

        // Sync Tabs
        document.querySelectorAll('.school-tab').forEach(t => {
            if (t.dataset.id === schoolId) {
                t.classList.add('active-tab');
                t.classList.remove('text-slate-500');
                t.querySelector('.tab-count').className = 'tab-count bg-violet-100 text-violet-600 text-xs px-1.5 py-0.5 rounded-full';
                // Auto scroll tab into view
                t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                t.classList.remove('active-tab');
                t.classList.add('text-slate-500');
                t.querySelector('.tab-count').className = 'tab-count bg-slate-100 text-slate-500 text-xs px-1.5 py-0.5 rounded-full';
            }
        });

        // Filter kartu dan hitung ulang berdasarkan sekolah terpilih
        document.querySelectorAll('.student-card').forEach(card => {
            const match = schoolId === 'all' || card.dataset.school === schoolId;
            card.style.display = match ? '' : 'none';
            if (match) {
                visible++;
                if (card.dataset.assessed === 'true') {
                    sudah++;
                } else {
                    belum++;
                }
            }
        });

        // Sinkronkan data di card ringkasan atas
        document.getElementById('val_total_siswa').innerText = visible;
        document.getElementById('val_sudah_asesmen').innerText = sudah;
        document.getElementById('val_belum_asesmen').innerText = belum;

        // Sinkronkan data Total Sekolah
        document.getElementById('val_total_sekolah').innerText = schoolId === 'all' ? totalSekolahAsli : 1;

        // Update UI state
        document.getElementById('emptyState').classList.toggle('hidden', visible > 0);

        // Reset search field
        document.getElementById('searchInput').value = '';
    }

    // ── Search Siswa ─────────────────────────────────────────
    function searchStudent(query) {
        const q = query.toLowerCase().trim();
        let visible = 0;
        document.querySelectorAll('.student-card').forEach(card => {
            const match = card.dataset.name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
    }

    // ── Modal Detail ─────────────────────────────────────────
    function openDetail(studentId) {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('modalLoading').classList.remove('hidden');
        document.getElementById('modalContent').classList.add('hidden');
        document.body.style.overflow = 'hidden';

        fetch(`/students/${studentId}/detail`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}: ${r.statusText}`);
                return r.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.message);
                document.getElementById('modalContent').innerHTML = buildModalHTML(data);
                document.getElementById('modalLoading').classList.add('hidden');
                document.getElementById('modalContent').classList.remove('hidden');
            })
            .catch(err => {
                document.getElementById('modalContent').innerHTML = `
                    <div class="p-8 text-center">
                        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-red-600 mb-1">Gagal memuat data siswa</p>
                        <p class="text-xs text-slate-400 bg-slate-50 rounded-lg px-3 py-2 mt-2 text-left font-mono">${err.message}</p>
                        <button onclick="closeDetail()" class="mt-4 bg-violet-600 text-white text-sm px-4 py-2 rounded-xl">Tutup</button>
                    </div>`;
                document.getElementById('modalLoading').classList.add('hidden');
                document.getElementById('modalContent').classList.remove('hidden');
            });
    }

    function closeDetail() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function closeDetailOutside(e) {
        if (e.target === document.getElementById('detailModal')) closeDetail();
    }

    // ── Build Modal HTML dari JSON ────────────────────────────
    function buildModalHTML(d) {
        const levelColor = {
            'NSI':        { bg: 'bg-red-50',   text: 'text-red-600',   bar: 'bg-red-400',   badge: 'bg-red-100 text-red-600'   },
            'Basic':      { bg: 'bg-amber-50',  text: 'text-amber-600', bar: 'bg-amber-400', badge: 'bg-amber-100 text-amber-600' },
            'Proficient': { bg: 'bg-blue-50',   text: 'text-blue-600',  bar: 'bg-blue-400',  badge: 'bg-blue-100 text-blue-600'  },
            'Advanced':   { bg: 'bg-green-50',  text: 'text-green-600', bar: 'bg-green-500', badge: 'bg-green-100 text-green-600' },
        };
        const lc = levelColor[d.level] || { bg:'bg-slate-50', text:'text-slate-500', bar:'bg-slate-300', badge:'bg-slate-100 text-slate-500' };

        // Topic scores rows
        const topicRows = d.topic_scores ? d.topic_scores.map(t => {
            const barColor = t.score >= 80 ? 'bg-green-500' : t.score >= 60 ? 'bg-blue-400' : t.score >= 40 ? 'bg-amber-400' : 'bg-red-400';
            const weak = t.is_weak ? `<span class="text-[10px] bg-red-100 text-red-500 font-semibold px-1.5 py-0.5 rounded-full ml-1">Perlu Perhatian</span>` : '';
            return `
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-slate-700 font-medium">${t.name}${weak}</span>
                        <span class="text-xs font-bold text-slate-500">${t.score}%</span>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="${barColor} h-full rounded-full" style="width:${t.score}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5">${t.correct} benar dari ${t.total} soal</p>
                </div>`;
        }).join('') : '<p class="text-sm text-slate-400 italic">Belum ada data topik</p>';

        // Weaknesses
        const weakList = d.weaknesses && d.weaknesses.length
            ? d.weaknesses.map(w => `<li class="flex items-start gap-2 text-sm text-slate-600"><span class="text-red-400 mt-0.5">•</span>${w}</li>`).join('')
            : '<li class="text-sm text-green-600 font-medium">Tidak ada kelemahan signifikan</li>';

        // Recommendations
        const recList = d.recommendations && d.recommendations.length
            ? d.recommendations.map(r => `<li class="flex items-start gap-2 text-sm text-slate-600"><span class="text-violet-400 mt-0.5">→</span>${r}</li>`).join('')
            : '<li class="text-sm text-slate-400 italic">Tidak ada rekomendasi</li>';

        // History rows
        const historyRows = d.history && d.history.length
            ? d.history.map(h => {
                const hc = levelColor[h.level] || {};
                return `<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="py-2.5 px-3 text-xs text-slate-500">${h.date}</td>
                    <td class="py-2.5 px-3"><span class="text-xs font-bold px-2 py-0.5 rounded-lg ${hc.badge || 'bg-slate-100 text-slate-500'}">${h.level}</span></td>
                    <td class="py-2.5 px-3 text-xs font-semibold text-slate-700">${h.accuracy}%</td>
                    <td class="py-2.5 px-3 text-xs text-slate-500">${h.correct}/${h.total} benar</td>
                </tr>`;
              }).join('')
            : `<tr><td colspan="4" class="py-6 text-center text-xs text-slate-400 italic">Belum ada riwayat asesmen</td></tr>`;

        return `
        <div class="p-5">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-400 to-violet-700 flex items-center justify-center text-white font-bold text-lg">
                        ${d.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800 text-lg leading-tight">${d.name}</h2>
                        <p class="text-sm text-slate-400">${d.school}</p>
                    </div>
                </div>
                <button onclick="closeDetail()" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-full flex items-center justify-center text-slate-500 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            ${d.level ? `
            {{-- Level & Akurasi --}}
            <div class="${lc.bg} border border-slate-100 rounded-2xl p-4 mb-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Hasil Diagnosis Terakhir</p>
                    <p class="font-bold text-2xl ${lc.text}">${d.level}</p>
                    <p class="text-xs text-slate-500 mt-0.5">${d.level === 'NSI' ? 'Belum Cukup Indikator' : d.level === 'Basic' ? 'Kemampuan Dasar' : d.level === 'Proficient' ? 'Cakap' : 'Mahir'}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-3xl ${lc.text}">${d.accuracy}%</p>
                    <p class="text-xs text-slate-400">${d.correct}/${d.total} benar</p>
                </div>
            </div>

            {{-- Performa Topik --}}
            <div class="bg-white border border-slate-100 rounded-2xl p-4 mb-4">
                <h3 class="font-bold text-slate-700 text-sm mb-3">Performa per Topik</h3>
                <div class="space-y-3">${topicRows}</div>
            </div>

            {{-- Kelemahan & Rekomendasi --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="border border-red-100 rounded-2xl p-3">
                    <h4 class="font-bold text-slate-700 text-xs mb-2 flex items-center gap-1">Area Lemah</h4>
                    <ul class="space-y-1">${weakList}</ul>
                </div>
                <div class="border border-violet-100 rounded-2xl p-3">
                    <h4 class="font-bold text-slate-700 text-xs mb-2 flex items-center gap-1">Rekomendasi Guru</h4>
                    <ul class="space-y-1">${recList}</ul>
                </div>
            </div>

            {{-- Catatan AI --}}
            <div class="bg-violet-50 border border-violet-100 rounded-2xl p-3 mb-4">
                <p class="text-xs font-bold text-violet-700 mb-1">Catatan Diagnosis AI</p>
                <p class="text-xs text-violet-600 leading-relaxed">${d.ai_note}</p>
            </div>
            ` : `
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-4 text-center">
                <div class="w-14 h-14 bg-amber-100/50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <p class="text-sm font-semibold text-amber-700">Siswa belum mengikuti asesmen</p>
                <a href="/assessment/start" class="inline-block mt-2 bg-violet-600 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-violet-700 transition-colors">Mulai Asesmen</a>
            </div>
            `}

            {{-- Riwayat Asesmen --}}
            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-50">
                    <h3 class="font-bold text-slate-700 text-sm">Riwayat Asesmen</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left text-xs text-slate-400 font-semibold px-3 py-2">Tanggal</th>
                            <th class="text-left text-xs text-slate-400 font-semibold px-3 py-2">Level</th>
                            <th class="text-left text-xs text-slate-400 font-semibold px-3 py-2">Akurasi</th>
                            <th class="text-left text-xs text-slate-400 font-semibold px-3 py-2">Skor</th>
                        </tr>
                    </thead>
                    <tbody>${historyRows}</tbody>
                </table>
            </div>

        </div>`;
    }

    // Driver.js Tour Logic
    startTourWhenReady('tour_students', [
        { element: '#tour-students-filter', popover: { title: 'Filter Asal Sekolah', description: 'Gunakan dropdown ini untuk memfilter data siswa berdasarkan sekolah tertentu secara instan.' } },
        { element: '#tour-students-search', popover: { title: 'Pencarian Instan', description: 'Ketik nama siswa di sini untuk menemukannya dengan cepat tanpa perlu memuat ulang halaman.' } },
        { element: '#studentGrid', popover: { title: 'Daftar Siswa', description: 'Ini adalah daftar seluruh siswa. Klik pada kartu siswa mana saja untuk melihat riwayat asesmen dan dimensi kelemahan mereka.' } }
    ]);
</script>
<style>
    .active-tab { background: #7c3aed !important; color: white !important; }
    .active-tab .tab-count { background: rgba(255,255,255,0.2) !important; color: white !important; }
</style>
@endpush
