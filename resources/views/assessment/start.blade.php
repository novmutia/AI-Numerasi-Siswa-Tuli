@extends('layouts.app')

@section('title', 'Mulai Asesmen — NumerasiTuli')
@section('page-title', 'Mulai Asesmen')
@section('page-subtitle', 'Isi data siswa sebelum memulai')

@section('content')
    <div class="max-w-lg mx-auto">

        {{-- Info strip --}}
        <div class="flex items-center gap-4 mb-6">
            @foreach ([['12 Soal'], ['±20 Menit'], ['Video BISINDO']] as [$text])
                <div class="flex-1 card flex flex-col items-center justify-center py-3 text-center gap-1">
                    <span class="text-xs font-semibold text-slate-600">{{ $text }}</span>
                </div>
            @endforeach
        </div>

        {{-- Tabs --}}
        <div id="tour-tabs" class="flex items-center gap-2 mb-4 p-1 bg-slate-100 rounded-xl">
            <button type="button" onclick="switchTab('baru')" id="tab_baru" class="flex-1 py-2 text-sm font-semibold rounded-lg bg-white text-violet-700 shadow-sm transition-all">Siswa Baru</button>
            <button type="button" onclick="switchTab('lama')" id="tab_lama" class="flex-1 py-2 text-sm font-semibold rounded-lg text-slate-500 hover:text-slate-700 transition-all">Sudah Pernah</button>
        </div>

        {{-- Form --}}
        <div class="card">
            
            {{-- Form Baru --}}
            <div id="form_baru">
                <h2 class="font-bold text-slate-800 text-base mb-1">Data Siswa Baru</h2>
                <p class="text-xs text-slate-400 mb-5">Isi data di bawah ini jika siswa belum pernah mengerjakan asesmen.</p>
    
                <form action="{{ route('assessment.store-student') }}" method="POST" id="startForm">
                    @csrf
    
                    {{-- Nama --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap Siswa</label>
                        <input type="text" name="student_name" id="student_name" placeholder="Contoh: Budi Santoso"
                            value="{{ old('student_name') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition"
                            autocomplete="off" required>
                        @error('student_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
    
                    {{-- Sekolah --}}
                    <div class="mb-6 relative" id="schoolDropdownContainer">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sekolah</label>
                        <input type="hidden" name="school_id" id="schoolInputBaru" value="{{ old('school_id') }}" required>
                        
                        @php
                            $oldSchoolName = '-- Pilih Sekolah --';
                            if (old('school_id')) {
                                foreach($schools as $sch) {
                                    if ($sch->id == old('school_id')) {
                                        $oldSchoolName = $sch->name;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        
                        <div class="relative">
                            <button type="button" onclick="toggleSchoolDropdown()" id="schoolDropdownTrigger"
                                    class="w-full flex items-center justify-between border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-white hover:border-violet-300 focus:outline-none focus:ring-2 focus:ring-violet-400 transition-all cursor-pointer shadow-sm">
                                <span id="schoolDropdownLabel" class="font-medium truncate mr-2 {{ old('school_id') ? 'text-slate-700' : 'text-slate-400' }}">{{ $oldSchoolName }}</span>
                                <svg id="schoolDropdownIcon" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
    
                            <div id="schoolDropdownMenu" class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-xl opacity-0 invisible -translate-y-2 transition-all duration-200 origin-top overflow-hidden">
                                <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5">
                                    @foreach($schools as $school)
                                    <button type="button" onclick="selectSchool('{{ $school->id }}', '{{ addslashes($school->name) }}')"
                                            class="school-option w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors flex items-center justify-between group {{ old('school_id') == $school->id ? 'bg-violet-50 text-violet-700' : '' }}" data-value="{{ $school->id }}">
                                        <span class="font-medium {{ old('school_id') == $school->id ? 'text-violet-700' : 'text-slate-700' }} group-hover:text-violet-700 school-text">{{ $school->name }}</span>
                                        <svg class="w-4 h-4 text-violet-600 school-check {{ old('school_id') == $school->id ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @error('school_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
    
                    {{-- Submit --}}
                    <button type="submit" id="submitBtn"
                        class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-md shadow-violet-200 flex items-center justify-center gap-2">
                        <span id="btnText">Mulai Asesmen</span>
                        <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4" />
                            <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Form Lama --}}
            <div id="form_lama" class="hidden">
                <h2 class="font-bold text-slate-800 text-base mb-1">Cari Siswa</h2>
                <p class="text-xs text-slate-400 mb-5">Ketik nama siswa yang sudah pernah mengerjakan asesmen sebelumnya.</p>
    
                <form action="{{ route('assessment.store-student') }}" method="POST" id="startFormLama">
                    @csrf
                    <input type="hidden" name="existing_student_id" id="existing_student_id">
    
                    {{-- Live Search --}}
                    <div class="mb-6 relative">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Siswa</label>
                        <div class="relative">
                            <input type="text" id="search_student" placeholder="Ketik nama siswa..."
                                class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition"
                                autocomplete="off">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        @error('existing_student_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        {{-- Dropdown --}}
                        <div id="searchResults" class="absolute z-50 w-full mt-1 bg-white border border-slate-100 rounded-xl shadow-xl overflow-hidden hidden">
                            <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5" id="searchResultsList">
                                <!-- Dimuat lewat JS -->
                            </div>
                        </div>
                    </div>
    
                    {{-- Submit --}}
                    <button type="submit" id="submitBtnLama" disabled
                        class="w-full bg-slate-300 text-white font-bold py-3 rounded-xl transition-colors text-sm flex items-center justify-center gap-2 cursor-not-allowed">
                        <span id="btnTextLama">Pilih Siswa Terlebih Dahulu</span>
                        <svg id="btnSpinnerLama" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4" />
                            <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8z" />
                        </svg>
                    </button>
                </form>
            </div>

        </div>

        {{-- Petunjuk --}}
        <div id="tour-petunjuk" class="card mt-4">
            <h3 class="font-bold text-slate-700 text-sm mb-3">Petunjuk Pengerjaan</h3>
            <ul class="space-y-2 text-xs text-slate-500">
                <li class="flex items-start gap-2"><span class="text-violet-400 font-bold mt-0.5">1.</span> Tonton video
                    BISINDO hingga selesai sebelum menjawab.</li>
                <li class="flex items-start gap-2"><span class="text-violet-400 font-bold mt-0.5">2.</span> Baca subtitle
                    soal yang tertera di bawah video.</li>
                <li class="flex items-start gap-2"><span class="text-violet-400 font-bold mt-0.5">3.</span> Pilih satu
                    jawaban yang paling tepat.</li>
                <li class="flex items-start gap-2"><span class="text-violet-400 font-bold mt-0.5">4.</span> Setelah semua
                    soal dijawab, klik tombol Selesai.</li>
                <li class="flex items-start gap-2"><span class="text-violet-400 font-bold mt-0.5">5.</span> Jawaban tidak
                    dapat diubah setelah soal disubmit.</li>
            </ul>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const studentsJS = @json($studentsJS ?? []);

        // Tab Switching Logic
        function switchTab(tab) {
            const formBaru = document.getElementById('form_baru');
            const formLama = document.getElementById('form_lama');
            const tabBaru = document.getElementById('tab_baru');
            const tabLama = document.getElementById('tab_lama');

            if (tab === 'baru') {
                formBaru.classList.remove('hidden');
                formLama.classList.add('hidden');
                
                tabBaru.className = "flex-1 py-2 text-sm font-semibold rounded-lg bg-white text-violet-700 shadow-sm transition-all";
                tabLama.className = "flex-1 py-2 text-sm font-semibold rounded-lg text-slate-500 hover:text-slate-700 transition-all";
            } else {
                formBaru.classList.add('hidden');
                formLama.classList.remove('hidden');
                
                tabLama.className = "flex-1 py-2 text-sm font-semibold rounded-lg bg-white text-violet-700 shadow-sm transition-all";
                tabBaru.className = "flex-1 py-2 text-sm font-semibold rounded-lg text-slate-500 hover:text-slate-700 transition-all";
            }
        }

        // Live Search Logic
        const searchInput = document.getElementById('search_student');
        const searchResults = document.getElementById('searchResults');
        const searchResultsList = document.getElementById('searchResultsList');
        const hiddenStudentId = document.getElementById('existing_student_id');
        const btnLama = document.getElementById('submitBtnLama');
        const btnTextLama = document.getElementById('btnTextLama');

        function renderResults(results) {
            if (results.length === 0) {
                searchResultsList.innerHTML = `<div class="px-3 py-3 text-sm text-center text-slate-500">Siswa tidak ditemukan</div>`;
            } else {
                searchResultsList.innerHTML = results.map(s => `
                    <button type="button" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-violet-50 hover:text-violet-700 transition-colors"
                        onclick="selectStudent('${s.id}', '${s.name.replace(/'/g, "\\'")}')">
                        <div class="font-medium">${s.name}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">${s.school}</div>
                    </button>
                `).join('');
            }
            searchResults.classList.remove('hidden');
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            // Reset selection if typing
            hiddenStudentId.value = '';
            btnLama.disabled = true;
            btnLama.className = "w-full bg-slate-300 text-white font-bold py-3 rounded-xl transition-colors text-sm flex items-center justify-center gap-2 cursor-not-allowed";
            btnTextLama.textContent = "Pilih Siswa Terlebih Dahulu";

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            const matched = studentsJS.filter(s => s.name.toLowerCase().includes(query)).slice(0, 50);
            renderResults(matched);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            // Live search
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
            
            // School dropdown
            const schoolDropdownContainer = document.getElementById('schoolDropdownContainer');
            if (schoolDropdownOpen && schoolDropdownContainer && !schoolDropdownContainer.contains(e.target)) {
                toggleSchoolDropdown();
            }
        });

        // Select student
        window.selectStudent = function(id, name) {
            searchInput.value = name;
            hiddenStudentId.value = id;
            searchResults.classList.add('hidden');
            
            // Enable button
            btnLama.disabled = false;
            btnLama.className = "w-full bg-violet-600 hover:bg-violet-700 shadow-md shadow-violet-200 text-white font-bold py-3 rounded-xl transition-colors text-sm flex items-center justify-center gap-2 cursor-pointer";
            btnTextLama.textContent = "Mulai Asesmen";
        };

        // Existing Auto-capitalize & Loading States
        document.getElementById('student_name').addEventListener('input', function() {
            const pos = this.selectionStart;
            this.value = this.value.replace(/\b\w/g, c => c.toUpperCase());
            this.setSelectionRange(pos, pos);
        });

        document.getElementById('startForm').addEventListener('submit', function() {
            document.getElementById('btnText').textContent = 'Memuat soal...';
            document.getElementById('btnSpinner').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;
        });

        document.getElementById('startFormLama').addEventListener('submit', function() {
            document.getElementById('btnTextLama').textContent = 'Memuat soal...';
            document.getElementById('btnSpinnerLama').classList.remove('hidden');
            document.getElementById('submitBtnLama').disabled = true;
        });

        // School Dropdown Logic
        let schoolDropdownOpen = false;
        
        function toggleSchoolDropdown() {
            const menu = document.getElementById('schoolDropdownMenu');
            const icon = document.getElementById('schoolDropdownIcon');
            const trigger = document.getElementById('schoolDropdownTrigger');
            
            schoolDropdownOpen = !schoolDropdownOpen;
            
            if (schoolDropdownOpen) {
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

        function selectSchool(id, name) {
            document.getElementById('schoolInputBaru').value = id;
            
            const label = document.getElementById('schoolDropdownLabel');
            label.textContent = name;
            label.classList.remove('text-slate-400');
            label.classList.add('text-slate-700');
            
            document.querySelectorAll('.school-option').forEach(btn => {
                const val = btn.dataset.value;
                const textEl = btn.querySelector('.school-text');
                const checkEl = btn.querySelector('.school-check');
                
                if (val === id) {
                    btn.classList.add('bg-violet-50', 'text-violet-700');
                    textEl.classList.remove('text-slate-700');
                    textEl.classList.add('text-violet-700');
                    checkEl.classList.remove('hidden');
                } else {
                    btn.classList.remove('bg-violet-50', 'text-violet-700');
                    textEl.classList.add('text-slate-700');
                    textEl.classList.remove('text-violet-700');
                    checkEl.classList.add('hidden');
                }
            });
            
            toggleSchoolDropdown();
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            // Live search
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
            
            // School dropdown
            const schoolDropdownContainer = document.getElementById('schoolDropdownContainer');
            if (schoolDropdownOpen && schoolDropdownContainer && !schoolDropdownContainer.contains(e.target)) {
                toggleSchoolDropdown();
            }
        });

    // Driver.js Tour Logic
    startTourWhenReady('tour_assessment_start', [
        { element: '#tour-tabs', popover: { title: 'Tipe Siswa', description: 'Gunakan tab ini untuk memilih apakah siswa baru pertama kali mendaftar, atau sudah pernah mengerjakan sebelumnya (Siswa Lama).' } },
        { element: '#schoolDropdownContainer', popover: { title: 'Pilih Sekolah', description: 'Daftar sekolah ini terhubung langsung ke database. Pastikan Anda memilih asal sekolah yang tepat.' } },
        { element: '#tour-petunjuk', popover: { title: 'Petunjuk Penting', description: 'Harap baca petunjuk ini baik-baik. Selalu pastikan siswa menonton video BISINDO secara penuh sebelum menjawab.', side: 'top', align: 'start' } }
    ]);
    </script>
@endpush
