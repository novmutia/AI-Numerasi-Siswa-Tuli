<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NumerasiTuli')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Driver.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        violet: {
                            50:  '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0effe; }

        /* Driver.js Theme Overrides */
        .driver-popover, .driver-popover * {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .driver-popover {
            border-radius: 16px !important;
            padding: 20px !important;
            box-shadow: 0 20px 40px -10px rgba(109, 40, 217, 0.2) !important;
            border: 1px solid #ede9fe !important;
        }
        .driver-popover-title {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #5b21b6 !important;
            margin-bottom: 8px !important;
        }
        .driver-popover-description {
            font-size: 13px !important;
            color: #64748b !important;
            line-height: 1.5 !important;
        }
        .driver-popover-footer button {
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            text-shadow: none !important;
            transition: all 0.2s !important;
        }
        .driver-popover-next-btn {
            background-color: #7c3aed !important;
            color: white !important;
            border: none !important;
        }
        .driver-popover-next-btn:hover { background-color: #6d28d9 !important; }
        .driver-popover-prev-btn {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: none !important;
        }
        .driver-popover-prev-btn:hover { background-color: #e2e8f0 !important; }
        .driver-popover-close-btn { color: #94a3b8 !important; }
        .driver-popover-close-btn:hover { color: #ef4444 !important; }

        /* Sidebar */
        .sidebar { width: 220px; height: 100vh; position: fixed; top: 0; left: 0; flex-shrink: 0; overflow-y: auto; z-index: 40; }
        .main-area { margin-left: 220px; }
        @media (max-width: 768px) {
            .sidebar { left: -220px; z-index: 50; transition: left .3s ease; }
            .sidebar.open { left: 0; }
            .main-area { margin-left: 0 !important; }
        }

        /* Nav item */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 12px;
            font-size: 14px; font-weight: 500; color: #6b7280;
            transition: all .2s ease; cursor: pointer; text-decoration: none;
        }
        .nav-item:hover { background: #ede9fe; color: #6d28d9; }
        .nav-item.active { background: #7c3aed; color: #fff; box-shadow: 0 4px 14px rgba(124,58,237,.35); }
        .nav-item.active .nav-icon { filter: brightness(10); }

        /* Card */
        .card { background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 2px 12px rgba(109,40,217,.06); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 4px; }

        /* Fade in */
        .fade-in { animation: fadeIn .4s ease forwards; opacity: 0; }
        @keyframes fadeIn { to { opacity: 1; } }
        .fade-in:nth-child(1) { animation-delay: .05s; }
        .fade-in:nth-child(2) { animation-delay: .1s; }
        .fade-in:nth-child(3) { animation-delay: .15s; }
        .fade-in:nth-child(4) { animation-delay: .2s; }

        /* ── Cara Kerja Modal ── */
        .ck-overlay {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(15, 10, 40, .55);
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden;
            transition: opacity .35s ease, visibility .35s ease;
        }
        .ck-overlay.show { opacity: 1; visibility: visible; }

        .ck-modal {
            background: #fff; border-radius: 24px;
            width: 90%; max-width: 520px;
            padding: 0; overflow: hidden;
            box-shadow: 0 25px 60px rgba(124,58,237,.2), 0 0 0 1px rgba(124,58,237,.08);
            transform: translateY(30px) scale(.96);
            transition: transform .4s cubic-bezier(.16,1,.3,1);
        }
        .ck-overlay.show .ck-modal {
            transform: translateY(0) scale(1);
        }

        .ck-header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%);
            padding: 28px 32px 24px; position: relative; overflow: hidden;
        }
        .ck-header::before {
            content: ''; position: absolute; right: -30px; top: -30px;
            width: 120px; height: 120px; border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .ck-header::after {
            content: ''; position: absolute; right: 20px; bottom: -20px;
            width: 70px; height: 70px; border-radius: 50%;
            background: rgba(255,255,255,.05);
        }

        .ck-body { padding: 28px 32px 32px; }

        /* Progress dots */
        .ck-dots { display: flex; gap: 8px; justify-content: center; margin-bottom: 24px; }
        .ck-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: #e2e0f5; transition: all .3s ease; cursor: pointer;
        }
        .ck-dot.active {
            background: #7c3aed; width: 28px; border-radius: 5px;
            box-shadow: 0 2px 8px rgba(124,58,237,.35);
        }
        .ck-dot.done { background: #a78bfa; }

        /* Step slide */
        .ck-slides { position: relative; min-height: 160px; overflow: hidden; }
        .ck-step {
            position: absolute; inset: 0;
            opacity: 0; transform: translateX(40px);
            transition: opacity .35s ease, transform .35s ease;
            pointer-events: none;
        }
        .ck-step.active {
            opacity: 1; transform: translateX(0); pointer-events: auto;
            position: relative;
        }
        .ck-step.exit-left {
            opacity: 0; transform: translateX(-40px);
        }

        .ck-step-icon {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 800; flex-shrink: 0;
            margin-bottom: 16px;
        }

        /* Nav buttons */
        .ck-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; gap: 12px; }
        .ck-btn {
            border: none; border-radius: 12px; font-family: inherit;
            font-weight: 600; font-size: 14px; cursor: pointer;
            padding: 12px 24px; transition: all .2s ease;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .ck-btn-secondary {
            background: #f3f0ff; color: #7c3aed;
        }
        .ck-btn-secondary:hover { background: #ede9fe; }
        .ck-btn-primary {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #fff; box-shadow: 0 4px 14px rgba(124,58,237,.3);
        }
        .ck-btn-primary:hover {
            box-shadow: 0 6px 20px rgba(124,58,237,.45);
            transform: translateY(-1px);
        }

        /* Navbar help button */
        .btn-cara-kerja {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f3f0ff; color: #7c3aed;
            font-size: 13px; font-weight: 600; font-family: inherit;
            padding: 8px 14px; border-radius: 10px; border: 1px solid #e2ddf8;
            cursor: pointer; transition: all .2s ease;
        }
        .btn-cara-kerja:hover {
            background: #ede9fe; border-color: #c4b5fd;
            box-shadow: 0 2px 8px rgba(124,58,237,.12);
        }
    </style>
    @stack('head')
</head>
<body class="min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar bg-white flex flex-col py-6 px-4 border-r border-violet-100">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 mb-8">
            <div class="w-9 h-9 bg-violet-600 rounded-xl flex items-center justify-center shadow-md">
                <span class="text-white font-bold text-base">N</span>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm leading-tight">NumerasiTuli</p>
                <p class="text-xs text-slate-400">Asesmen Numerasi</p>
            </div>
        </a>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Beranda
            </a>
            <a href="{{ route('assessment.start') }}"
               class="nav-item {{ request()->routeIs('assessment.*') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Asesmen
            </a>
            <a href="{{ route('students.index') }}"
               class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Data Siswa
            </a>
            <a href="{{ route('statistics') }}"
               class="nav-item {{ request()->routeIs('statistics') ? 'active' : '' }}">
                <svg class="nav-icon w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Statistik
            </a>
        </nav>

        {{-- Start CTA --}}
        <a href="{{ route('assessment.start') }}"
           class="mt-4 flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold py-3 px-4 rounded-xl transition-colors shadow-md shadow-violet-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Mulai Asesmen
        </a>
    </aside>

    {{-- Overlay mobile --}}
    <div id="overlay" class="hidden fixed inset-0 bg-black/30 z-40 md:hidden" onclick="closeSidebar()"></div>

    {{-- ── MAIN ── --}}
    <div class="main-area flex flex-col h-screen overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white/70 backdrop-blur border-b border-violet-100 px-6 py-4 flex items-center justify-between flex-shrink-0 z-30">
            <div class="flex items-center gap-3">
                {{-- Burger mobile --}}
                <button id="burger" onclick="openSidebar()" class="md:hidden p-2 rounded-lg hover:bg-violet-50 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="font-bold text-slate-800 text-base leading-tight">@yield('page-title', 'Beranda')</h1>
                    <p class="text-xs text-slate-400">@yield('page-subtitle', 'Platform Asesmen Numerasi Siswa Tuli')</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button id="btnCaraKerja" onclick="openCaraKerja()" class="btn-cara-kerja">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cara Kerja
                </button>
                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                    <span class="text-violet-600 text-sm"></span>
                </div>
            </div>
        </header>

        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto">
            {{-- Flash --}}
            @if(session('success'))
            <div class="mx-6 mt-4">
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">✕</button>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-6 mt-4">
                <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">✕</button>
                </div>
            </div>
            @endif

            {{-- Content --}}
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ── CARA KERJA MODAL ── --}}
    <div id="ckOverlay" class="ck-overlay" onclick="if(event.target===this)closeCaraKerja()">
        <div class="ck-modal">
            {{-- Header --}}
            <div class="ck-header">
                <p class="text-violet-200 text-xs font-medium mb-1 relative z-10">Panduan Penggunaan</p>
                <h2 class="text-white text-xl font-bold leading-tight relative z-10">Cara Kerja Sistem</h2>
                <p class="text-violet-200 text-xs mt-1 relative z-10">Ikuti 4 langkah mudah berikut</p>
                <button onclick="closeCaraKerja()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-colors z-10" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="ck-body">
                {{-- Dots --}}
                <div class="ck-dots" id="ckDots"></div>

                {{-- Slides --}}
                <div class="ck-slides" id="ckSlides"></div>

                {{-- Navigation --}}
                <div class="ck-nav">
                    <button id="ckPrev" class="ck-btn ck-btn-secondary" onclick="ckNav(-1)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Sebelumnya</button>
                    <button id="ckNext" class="ck-btn ck-btn-primary" onclick="ckNav(1)">Selanjutnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.querySelector('.sidebar').classList.add('open');
            document.getElementById('overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('overlay').classList.add('hidden');
        }

        /* ── Cara Kerja Stepper ── */
        const ckSteps = [
            {
                icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                bg: 'bg-violet-100', color: 'text-violet-600',
                title: 'Isi Data Siswa',
                desc: 'Masukkan nama siswa dan pilih sekolah SLB tempat siswa belajar. Data ini digunakan untuk pencatatan hasil asesmen.'
            },
            {
                icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
                bg: 'bg-blue-100', color: 'text-blue-600',
                title: 'Kerjakan 12 Soal',
                desc: 'Siswa menjawab 12 soal pilihan ganda yang disajikan dalam video BISINDO. Setiap soal dirancang untuk mengukur kemampuan numerasi.'
            },
            {
                icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                bg: 'bg-amber-100', color: 'text-amber-600',
                title: 'Diagnosis Rule-Based',
                desc: 'Sistem secara otomatis membaca pola indikator dari setiap jawaban siswa menggunakan metode rule-based untuk menentukan level kemampuan.'
            },
            {
                icon: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                bg: 'bg-green-100', color: 'text-green-600',
                title: 'Lihat Hasil & Rekomendasi',
                desc: 'Dapatkan level kemampuan, probabilitas, dan strategi pembelajaran yang bisa digunakan guru untuk mendukung perkembangan siswa.'
            }
        ];
        let ckCurrent = 0;

        function ckRender() {
            const dots = document.getElementById('ckDots');
            const slides = document.getElementById('ckSlides');

            // Build dots
            dots.innerHTML = ckSteps.map((_, i) => {
                let cls = 'ck-dot';
                if (i === ckCurrent) cls += ' active';
                else if (i < ckCurrent) cls += ' done';
                return `<div class="${cls}" onclick="ckGoTo(${i})"></div>`;
            }).join('');

            // Build slides
            slides.innerHTML = ckSteps.map((s, i) => {
                let cls = 'ck-step';
                if (i === ckCurrent) cls += ' active';
                else if (i < ckCurrent) cls += ' exit-left';
                return `<div class="${cls}">
                    <div class="ck-step-icon ${s.bg} ${s.color}">${s.icon}</div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[11px] font-bold text-violet-500 bg-violet-50 px-2 py-0.5 rounded-md">Langkah ${i+1}</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">${s.title}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">${s.desc}</p>
                </div>`;
            }).join('');

            // Button states
            const prev = document.getElementById('ckPrev');
            const next = document.getElementById('ckNext');
            prev.style.visibility = ckCurrent === 0 ? 'hidden' : 'visible';
            if (ckCurrent === ckSteps.length - 1) {
                next.innerHTML = 'Mengerti! <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            } else {
                next.innerHTML = 'Selanjutnya <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
        }

        function ckNav(dir) {
            if (dir === 1 && ckCurrent === ckSteps.length - 1) {
                closeCaraKerja();
                return;
            }
            ckCurrent = Math.max(0, Math.min(ckSteps.length - 1, ckCurrent + dir));
            ckRender();
        }

        function ckGoTo(idx) {
            ckCurrent = idx;
            ckRender();
        }

        function openCaraKerja() {
            ckCurrent = 0;
            ckRender();
            document.getElementById('ckOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeCaraKerja() {
            document.getElementById('ckOverlay').classList.remove('show');
            document.body.style.overflow = '';
            localStorage.setItem('ck_seen', '1');
            window.dispatchEvent(new Event('caraKerjaClosed'));
        }

        // Auto-show on first visit
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('ck_seen')) {
                setTimeout(openCaraKerja, 600);
            }
        });

        // Global Tour helper
        function startTourWhenReady(tourKey, steps) {
            function runTour() {
                if (!localStorage.getItem(tourKey)) {
                    const driverObj = window.driver.js.driver({
                        showProgress: true,
                        doneBtnText: 'Selesai',
                        nextBtnText: 'Lanjut',
                        prevBtnText: 'Kembali',
                        steps: steps,
                        onDestroyStarted: () => {
                            localStorage.setItem(tourKey, 'true');
                            driverObj.destroy();
                        }
                    });
                    driverObj.drive();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                if (!localStorage.getItem('ck_seen')) {
                    window.addEventListener('caraKerjaClosed', runTour, {once: true});
                } else {
                    // Beri sedikit delay agar rendering halaman selesai
                    setTimeout(runTour, 300);
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
