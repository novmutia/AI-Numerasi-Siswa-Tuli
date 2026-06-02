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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['value' => $stats['total_siswa'], 'label' => 'Total Siswa', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700'], ['value' => $stats['total_sekolah'], 'label' => 'Sekolah', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700'], ['value' => $stats['asesmen_selesai'], 'label' => 'Asesmen Selesai', 'bg' => 'bg-green-50', 'text' => 'text-green-700'], ['value' => $stats['rata_skor'], 'label' => 'Rata-rata Skor', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700']] as $s)
                <div class="fade-in card flex items-center gap-3">
                    <div
                        class="{{ $s['bg'] }} w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
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

            {{-- Level — deskripsi sistem indikator (bukan range skor) --}}
            <div class="fade-in card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Level Kemampuan</h3>
                    <span class="text-[10px] text-slate-400">Berbasis pola indikator jawaban</span>
                </div>
                <div class="space-y-3">
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
                        <div>
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

            {{-- Cara Kerja --}}
            <div class="fade-in card">
                <h3 class="font-bold text-slate-800 mb-4 text-sm">Cara Kerja Sistem</h3>
                <div class="space-y-3">
                    @foreach ([['01', 'Isi Data Siswa', 'Input nama siswa dan pilih sekolah SLB'], ['02', 'Kerjakan 12 Soal', '12 soal pilihan ganda berbasis video BISINDO'], ['03', 'Diagnosis Rule-Based', 'Sistem baca pola indikator dari setiap jawaban'], ['04', 'Lihat Hasil & Rekomendasi', 'Level + probabilitas + strategi untuk guru']] as [$no, $title, $desc])
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0">
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


        {{-- ── HASIL ASESMEN TERBARU ── --}}
        @if ($hasilTerbaru->count() > 0)
            <div class="fade-in card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Hasil Asesmen Terbaru</h3>
                    <a href="{{ route('students.index') }}" class="text-xs text-violet-600 font-semibold hover:underline">
                        Lihat Semua →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2">Nama Siswa</th>
                                <th class="text-left text-xs font-semibold text-slate-400 pb-2">Sekolah</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2">Level</th>
                                <th class="text-center text-xs font-semibold text-slate-400 pb-2">Akurasi</th>
                                <th class="text-right text-xs font-semibold text-slate-400 pb-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($hasilTerbaru as $hasil)
                                @php
                                    $lvlBadge =
                                        [
                                            'NSI' => 'bg-red-100 text-red-600',
                                            'Basic' => 'bg-amber-100 text-amber-600',
                                            'Proficient' => 'bg-blue-100 text-blue-600',
                                            'Advanced' => 'bg-green-100 text-green-600',
                                        ][$hasil->level] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-2.5 font-medium text-slate-700 text-xs">
                                        {{ $hasil->student->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 text-slate-500 text-xs">
                                        {{ $hasil->student->school->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 text-center">
                                        <span class="{{ $lvlBadge }} text-[10px] font-bold px-2 py-0.5 rounded-lg">
                                            {{ $hasil->level }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-center text-xs font-semibold text-slate-600">
                                        {{ $hasil->accuracy }}%
                                    </td>
                                    <td class="py-2.5 text-right text-[11px] text-slate-400">
                                        {{ $hasil->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- Empty state kalau belum ada asesmen --}}
            <div class="fade-in card text-center py-8">
                <p class="text-3xl mb-2">📋</p>
                <p class="font-semibold text-slate-600 text-sm">Belum ada data asesmen</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Mulai asesmen pertama untuk melihat hasilnya di sini</p>
                <a href="{{ route('assessment.start') }}"
                    class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-colors">
                    Mulai Asesmen Sekarang
                </a>
            </div>
        @endif

    </div>

@endsection
