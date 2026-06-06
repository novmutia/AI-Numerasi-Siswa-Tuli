@extends('layouts.app')

@section('title', 'Beranda — NumerasiTuli')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di NumerasiTuli')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- ── HERO CARD ── --}}
        <div id="tour-dashboard-hero" class="fade-in relative overflow-hidden rounded-2xl bg-violet-600 p-7 text-white shadow-lg shadow-violet-200">
            {{-- Decorative circles --}}
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -right-4 top-16 w-24 h-24 bg-white/5 rounded-full"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div>
                    <p class="text-violet-200 text-sm font-medium mb-1">Platform Asesmen</p>
                    <h2 class="text-2xl font-bold leading-tight mb-3">Ukur Kemampuan Numerasi<br>Siswa Tuli</h2>
                    <p class="text-violet-100 text-sm max-w-sm">Berbasis BISINDO dengan diagnosis AI — inklusif dan mudah
                        digunakan.</p>
                </div>
                <a href="{{ route('assessment.start') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 bg-white text-violet-700 font-bold text-sm px-5 py-3 rounded-xl hover:bg-violet-50 transition-colors shadow-md">
                    Mulai Asesmen
                </a>
            </div>
        </div>


        {{-- ── STAT CARDS ── --}}
        <div id="tour-dashboard-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['value' => $stats['total_siswa'], 'label' => 'Total Siswa', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'key' => 'total_siswa', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'],
                ['value' => $stats['total_sekolah'], 'label' => 'Sekolah', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'key' => 'total_sekolah', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'],
                ['value' => $stats['asesmen_selesai'], 'label' => 'Asesmen Selesai', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'key' => 'asesmen_selesai', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'],
                ['value' => $stats['rata_skor'], 'label' => 'Rata-rata Skor', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'key' => 'rata_skor', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>']
            ] as $s)
                <div id="tour-dash-{{ $s['key'] }}" class="fade-in card flex items-center gap-3 cursor-pointer hover:shadow-md hover:scale-[1.02] transition-all duration-200"
                     onclick="showDetail('{{ $s['key'] }}')">
                    <div
                        class="{{ $s['bg'] }} {{ $s['text'] }} w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0">
                        {!! $s['icon'] !!}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-xl leading-tight">{{ $s['value'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $s['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>


        {{-- ── LEVEL KEMAMPUAN ── --}}
        <div class="fade-in card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Level Kemampuan</h3>
                <span class="text-[10px] text-slate-400">Klik level untuk melihat detail</span>
            </div>
            <div class="grid md:grid-cols-2 gap-x-6 gap-y-3">
                @php
                    $levelInfo = [
                        [
                            'NSI',
                            'Perlu Intervensi Khusus',
                            'Pemahaman sangat terbatas',
                            'bg-red-100 text-red-600',
                            'bg-red-400',
                        ],
                        [
                            'Basic',
                            'Pemahaman Dasar',
                            'Mampu operasi dasar dalam konteks',
                            'bg-amber-100 text-amber-600',
                            'bg-amber-400',
                        ],
                        [
                            'Proficient',
                            'Cukup Mahir',
                            'Menerapkan strategi yang tepat',
                            'bg-blue-100 text-blue-600',
                            'bg-blue-400',
                        ],
                        [
                            'Advanced',
                            'Sangat Mahir',
                            'Berpikir kritis dan konteks kompleks',
                            'bg-green-100 text-green-600',
                            'bg-green-500',
                        ],
                    ];
                    $totalAsesmen = array_sum($distribusiLevel);
                @endphp
                @foreach ($levelInfo as [$label, $title, $desc, $badge, $bar])
                    @php
                        $jml = $distribusiLevel[$label] ?? 0;
                        $pct = $totalAsesmen > 0 ? round(($jml / $totalAsesmen) * 100) : 0;
                    @endphp
                    <div class="cursor-pointer rounded-xl p-2 -m-2 hover:bg-slate-50 transition-colors duration-200"
                         onclick="showLevelDetail('{{ $label }}')">
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="{{ $badge }} text-[10px] font-bold px-2 py-0.5 rounded-lg w-20 text-center flex-shrink-0">{{ $label }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-700 truncate">{{ $title }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ $desc }}</p>
                            </div>
                            <span class="text-xs font-bold text-slate-500 flex-shrink-0">
                                {{ $jml > 0 ? $jml . ' siswa' : '—' }}
                            </span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden ml-22">
                            <div class="{{ $bar }} h-full rounded-full transition-all duration-700"
                                style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- ── AKTIVITAS TERBARU (Timeline) ── --}}
        @if ($timeline->count() > 0)
            <div class="fade-in card">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-slate-800 text-sm">Aktivitas Terbaru</h3>
                    <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 text-xs text-violet-600 font-semibold bg-violet-50 hover:bg-violet-100 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="relative">
                    {{-- Timeline line --}}
                    <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-violet-100 rounded-full"></div>

                    <div class="space-y-1">
                        @foreach ($timeline as $item)
                            @php
                                $lvlConfig = [
                                    'NSI'        => ['badge' => 'bg-red-100 text-red-600',    'dot' => 'bg-red-400'],
                                    'Basic'      => ['badge' => 'bg-amber-100 text-amber-600','dot' => 'bg-amber-400'],
                                    'Proficient' => ['badge' => 'bg-blue-100 text-blue-600',  'dot' => 'bg-blue-400'],
                                    'Advanced'   => ['badge' => 'bg-green-100 text-green-600','dot' => 'bg-green-500'],
                                ];
                                $cfg = $lvlConfig[$item->level] ?? ['badge' => 'bg-slate-100 text-slate-600', 'dot' => 'bg-slate-400'];
                            @endphp
                            <div class="flex items-start gap-4 group relative pl-2 py-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                                {{-- Dot --}}
                                <div class="relative z-10 w-[18px] h-[18px] rounded-full border-[3px] border-white {{ $cfg['dot'] }} flex-shrink-0 mt-0.5 shadow-sm"></div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-slate-700">{{ $item->student->name ?? '—' }}</p>
                                        <span class="{{ $cfg['badge'] }} text-[10px] font-bold px-2 py-0.5 rounded-lg">{{ $item->level }}</span>
                                        <span class="text-[11px] font-semibold text-slate-500">{{ $item->accuracy }}%</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-slate-400">{{ $item->student->school->name ?? '—' }}</span>
                                        <span class="text-slate-200">·</span>
                                        <span class="text-[11px] text-slate-400">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                {{-- Akurasi visual --}}
                                <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                                    <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="{{ $cfg['dot'] }} h-full rounded-full" style="width:{{ $item->accuracy }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- Empty state kalau belum ada asesmen --}}
            <div class="fade-in card text-center py-8">
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="font-semibold text-slate-600 text-sm">Belum ada data asesmen</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Mulai asesmen pertama untuk melihat hasilnya di sini</p>
                <a href="{{ route('assessment.start') }}"
                    class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Mulai Asesmen Sekarang
                </a>
            </div>
        @endif

    </div>

    {{-- ── DETAIL POPUP MODAL ── --}}
    <div id="detailOverlay" class="ck-overlay" onclick="if(event.target===this)closeDetail()">
        <div class="ck-modal" style="max-width:560px">
            <div class="ck-header" id="detailHeader">
                <p class="text-violet-200 text-xs font-medium mb-1 relative z-10" id="detailSubtitle"></p>
                <h2 class="text-white text-xl font-bold leading-tight relative z-10" id="detailTitle"></h2>
                <button onclick="closeDetail()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-colors z-10" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="ck-body" style="max-height:400px;overflow-y:auto" id="detailBody"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const popupData = @json($popupData);

    const levelLabels = {
        'NSI': { title: 'Perlu Intervensi Khusus', color: '#ef4444' },
        'Basic': { title: 'Pemahaman Dasar', color: '#f59e0b' },
        'Proficient': { title: 'Cukup Mahir', color: '#3b82f6' },
        'Advanced': { title: 'Sangat Mahir', color: '#22c55e' },
    };

    const badgeClass = {
        'NSI': 'bg-red-100 text-red-600',
        'Basic': 'bg-amber-100 text-amber-600',
        'Proficient': 'bg-blue-100 text-blue-600',
        'Advanced': 'bg-green-100 text-green-600',
    };

    function showDetail(key) {
        const titles = {
            'total_siswa': ['Detail Siswa', 'Daftar siswa terdaftar'],
            'total_sekolah': ['Daftar Sekolah', 'Sekolah yang terdaftar'],
            'asesmen_selesai': ['Asesmen Selesai', 'Riwayat asesmen terbaru'],
            'rata_skor': ['Rata-rata Skor', 'Akurasi per level kemampuan'],
        };

        const [title, subtitle] = titles[key];
        document.getElementById('detailTitle').textContent = title;
        document.getElementById('detailSubtitle').textContent = subtitle;

        let html = '';
        const data = popupData[key];

        if (key === 'total_siswa') {
            if (data.length === 0) {
                html = emptyState('Belum ada siswa terdaftar');
            } else {
                html = '<div class="space-y-2">';
                data.forEach((s, i) => {
                    html += `<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-xs font-bold flex-shrink-0">${i+1}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate">${s.nama}</p>
                            <p class="text-[11px] text-slate-400">${s.sekolah}</p>
                        </div>
                    </div>`;
                });
                html += '</div>';
            }
        }

        if (key === 'total_sekolah') {
            if (data.length === 0) {
                html = emptyState('Belum ada sekolah terdaftar');
            } else {
                html = '<div class="space-y-2">';
                data.forEach(s => {
                    html += `<div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">${s.nama}</p>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded-md">${s.siswa} siswa</span>
                    </div>`;
                });
                html += '</div>';
            }
        }

        if (key === 'asesmen_selesai') {
            if (data.length === 0) {
                html = emptyState('Belum ada asesmen selesai');
            } else {
                html = '<div class="space-y-2">';
                data.forEach(r => {
                    const bc = badgeClass[r.level] || 'bg-slate-100 text-slate-600';
                    html += `<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-slate-700">${r.nama}</p>
                                <span class="${bc} text-[10px] font-bold px-2 py-0.5 rounded-lg">${r.level}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">${r.sekolah}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-slate-700">${r.akurasi}%</p>
                            <p class="text-[10px] text-slate-400">${r.tanggal}</p>
                        </div>
                    </div>`;
                });
                html += '</div>';
            }
        }

        if (key === 'rata_skor') {
            if (data.length === 0) {
                html = emptyState('Belum ada data akurasi');
            } else {
                html = '<div class="space-y-4">';
                data.forEach(r => {
                    const bc = badgeClass[r.level] || 'bg-slate-100 text-slate-600';
                    const info = levelLabels[r.level] || { title: r.level, color: '#94a3b8' };
                    html += `<div class="p-3 rounded-xl bg-slate-50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="${bc} text-[10px] font-bold px-2 py-0.5 rounded-lg">${r.level}</span>
                                <span class="text-xs text-slate-500">${info.title}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-slate-700">${r.rata}%</span>
                                <span class="text-[10px] text-slate-400 ml-1">(${r.jml} siswa)</span>
                            </div>
                        </div>
                        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" style="width:${r.rata}%;background:${info.color}"></div>
                        </div>
                    </div>`;
                });
                html += '</div>';
            }
        }

        document.getElementById('detailBody').innerHTML = html;
        document.getElementById('detailOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function showLevelDetail(level) {
        const info = levelLabels[level] || { title: level, color: '#94a3b8' };
        const bc = badgeClass[level] || 'bg-slate-100 text-slate-600';

        document.getElementById('detailTitle').textContent = 'Level ' + level;
        document.getElementById('detailSubtitle').textContent = info.title;

        const data = popupData.level[level] || [];
        let html = '';

        if (data.length === 0) {
            html = emptyState('Belum ada siswa di level ini');
        } else {
            html = `<p class="text-xs text-slate-500 mb-3">${data.length} siswa terakhir di level ini</p>`;
            html += '<div class="space-y-2">';
            data.forEach((r, i) => {
                html += `<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:${info.color}15;color:${info.color}">${i+1}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">${r.nama}</p>
                        <p class="text-[11px] text-slate-400">${r.sekolah}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700">${r.akurasi}%</p>
                        <p class="text-[10px] text-slate-400">${r.tanggal}</p>
                    </div>
                </div>`;
            });
            html += '</div>';
        }

        document.getElementById('detailBody').innerHTML = html;
        document.getElementById('detailOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeDetail() {
        document.getElementById('detailOverlay').classList.remove('show');
        document.body.style.overflow = '';
    }

    function emptyState(msg) {
        return `
            <div class="text-center py-6">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-xs font-medium text-slate-500">${msg}</p>
            </div>
            `;
    }

    document.getElementById('detailOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('detailOverlay').classList.contains('show')) {
            closeDetail();
        }
    });

    // Driver.js Tour Logic
    startTourWhenReady('tour_dashboard', [
        { element: '.sidebar', popover: { title: 'Navigasi Utama', description: 'Gunakan panel ini untuk berpindah ke berbagai halaman seperti Data Siswa dan Statistik.', side: "right", align: 'start' } },
        { element: '#tour-dashboard-hero', popover: { title: 'Mulai Asesmen AI', description: 'Klik tombol di dalam kartu ini untuk memulai proses asesmen kemampuan siswa.' } },
        { element: '#tour-dash-total_siswa', popover: { title: 'Total Siswa', description: 'Menampilkan seluruh siswa yang terdaftar. Anda bisa mengekliknya untuk melihat rincian datanya.' } },
        { element: '#tour-dash-total_sekolah', popover: { title: 'Total Sekolah', description: 'Jumlah sekolah yang sudah terdaftar di sistem. Klik untuk melihat detailnya.' } },
        { element: '#tour-dash-asesmen_selesai', popover: { title: 'Asesmen Selesai', description: 'Berapa banyak asesmen yang sudah dikerjakan siswa.' } },
        { element: '#tour-dash-rata_skor', popover: { title: 'Rata-rata Skor', description: 'Tingkat akurasi rata-rata dari seluruh siswa.' } }
    ]);
</script>
@endpush
