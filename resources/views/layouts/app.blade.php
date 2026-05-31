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

        /* Sidebar */
        .sidebar { width: 220px; min-height: 100vh; flex-shrink: 0; }
        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -220px; top: 0; z-index: 50; transition: left .3s ease; }
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
    </style>
    @stack('head')
</head>
<body class="flex min-h-screen">

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
    <div class="main-area flex-1 flex flex-col min-h-screen overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white/70 backdrop-blur border-b border-violet-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
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
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                    <span class="text-violet-600 text-sm"></span>
                </div>
            </div>
        </header>

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

    <script>
        function openSidebar() {
            document.querySelector('.sidebar').classList.add('open');
            document.getElementById('overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('overlay').classList.add('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
