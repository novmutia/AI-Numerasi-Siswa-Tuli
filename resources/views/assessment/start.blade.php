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

        {{-- Form --}}
        <div class="card">
            <h2 class="font-bold text-slate-800 text-base mb-1">Data Siswa</h2>
            <p class="text-xs text-slate-400 mb-5">Isi data di bawah ini sebelum mengerjakan soal.</p>

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
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sekolah</label>
                    <select name="school_id" id="school_id"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition bg-white"
                        required>
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
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

        {{-- Petunjuk --}}
        <div class="card mt-4">
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
        // Auto-capitalize nama
        document.getElementById('student_name').addEventListener('input', function() {
            const pos = this.selectionStart;
            this.value = this.value.replace(/\b\w/g, c => c.toUpperCase());
            this.setSelectionRange(pos, pos);
        });

        // Loading state on submit
        document.getElementById('startForm').addEventListener('submit', function() {
            document.getElementById('btnText').textContent = 'Memuat soal...';
            document.getElementById('btnSpinner').classList.remove('hidden');
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
@endpush
