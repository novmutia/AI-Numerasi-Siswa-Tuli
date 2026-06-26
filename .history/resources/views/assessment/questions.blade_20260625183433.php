@extends('layouts.app')

@section('title', 'Soal ' . $currentNumber . ' — NumerasiTuli')
@section('page-title', 'Asesmen Numerasi')
@section('page-subtitle', $studentName . ' · ' . $schoolName)

@section('content')

    {{-- Custom styles untuk halaman soal --}}
    <style>
        /* Palet warna utama */
        :root {
            --sky: #77BEF0;
            --yellow: #FFCB61;
            --orange: #FF894F;
            --rose: #EA5B6F;
            --sky-soft: #EBF6FD;
            --yellow-soft: #FFF8E7;
            --orange-soft: #FFF1EB;
            --rose-soft: #FDEEF1;
        }

        /* Background gradient animasi halus */
        .assessment-bg {
            min-height: 100vh;
            background: linear-gradient(135deg,
                    #EBF6FD 0%,
                    #FFF8E7 30%,
                    #FFF1EB 65%,
                    #FDEEF1 100%);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Card glass */
        .glass-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(119, 190, 240, 0.10),
                0 1px 4px rgba(0, 0, 0, 0.04);
        }

        /* Progress dot warna per soal */
        .dot-done {
            background: var(--sky);
            color: #fff;
        }

        .dot-active {
            background: var(--rose);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(234, 91, 111, 0.25);
        }

        .dot-next {
            background: rgba(0, 0, 0, 0.06);
            color: #94a3b8;
        }

        /* Pilihan jawaban */
        .option-card {
            background: rgba(255, 255, 255, 0.85);
            border: 2px solid rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            transition: all 0.18s ease;
            cursor: pointer;
        }

        .option-card:hover {
            border-color: var(--sky);
            background: var(--sky-soft);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(119, 190, 240, 0.18);
        }

        .option-card.selected-A {
            border-color: var(--sky) !important;
            background: var(--sky-soft) !important;
            box-shadow: 0 4px 16px rgba(119, 190, 240, 0.25);
        }

        .option-card.selected-B {
            border-color: var(--yellow) !important;
            background: var(--yellow-soft) !important;
            box-shadow: 0 4px 16px rgba(255, 203, 97, 0.25);
        }

        .option-card.selected-C {
            border-color: var(--orange) !important;
            background: var(--orange-soft) !important;
            box-shadow: 0 4px 16px rgba(255, 137, 79, 0.25);
        }

        .option-card.selected-D {
            border-color: var(--rose) !important;
            background: var(--rose-soft) !important;
            box-shadow: 0 4px 16px rgba(234, 91, 111, 0.25);
        }

        /* Circle huruf A/B/C/D */
        .opt-circle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            flex-shrink: 0;
            transition: all 0.18s ease;
            background: rgba(0, 0, 0, 0.06);
            color: #64748b;
        }

        .circle-A {
            background: rgba(119, 190, 240, 0.18);
            color: #2a8dcf;
        }

        .circle-B {
            background: rgba(255, 203, 97, 0.22);
            color: #c8891a;
        }

        .circle-C {
            background: rgba(255, 137, 79, 0.18);
            color: #d45a1a;
        }

        .circle-D {
            background: rgba(234, 91, 111, 0.18);
            color: #c0334a;
        }

        .selected-A .circle-A {
            background: var(--sky);
            color: #fff;
        }

        .selected-B .circle-B {
            background: var(--yellow);
            color: #fff;
        }

        .selected-C .circle-C {
            background: var(--orange);
            color: #fff;
        }

        .selected-D .circle-D {
            background: var(--rose);
            color: #fff;
        }

        /* Tombol navigasi */
        .btn-next {
            background: linear-gradient(135deg, var(--rose) 0%, var(--orange) 100%);
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(234, 91, 111, 0.30);
        }

        .btn-next:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(234, 91, 111, 0.40);
        }

        .btn-next:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .btn-finish {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.30);
        }

        .btn-finish:hover:not(:disabled) {
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.40);
        }

        .btn-prev {
            background: rgba(255, 255, 255, 0.85);
            border: 1.5px solid rgba(0, 0, 0, 0.10);
            border-radius: 14px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.18s;
        }

        .btn-prev:hover {
            background: white;
            border-color: var(--sky);
            color: #2a8dcf;
        }

        /* Badge Soal */
        .soal-badge {
            background: linear-gradient(135deg, var(--rose) 0%, var(--orange) 100%);
            color: white;
            font-weight: 700;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 8px;
        }

        /* Nomor soal besar */
        .soal-number {
            font-size: 13px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--orange), var(--rose));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Video card accent border atas */
        .video-card-wrap {
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid transparent;
            background:
                linear-gradient(white, white) padding-box,
                linear-gradient(135deg, var(--sky), var(--yellow), var(--orange), var(--rose)) border-box;
            box-shadow: 0 8px 32px rgba(119, 190, 240, 0.15);
        }
    </style>

    <div class="assessment-bg -mx-4 -mt-4 px-4 pt-6 pb-10 min-h-screen">
        <div class="max-w-2xl mx-auto space-y-4">

            {{-- ── PROGRESS ── --}}
            <div class="glass-card px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="soal-number">Soal {{ $currentNumber }}</span>
                        <span class="text-slate-400 text-xs font-medium">dari {{ $totalQuestions }}</span>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background:var(--sky-soft);color:#2a8dcf">
                        {{ round((($currentNumber - 1) / $totalQuestions) * 100) }}% selesai
                    </span>
                </div>

                {{-- Progress bar warna --}}
                <div class="w-full h-2.5 rounded-full overflow-hidden" style="background:rgba(0,0,0,0.06)">
                    <div class="h-full rounded-full transition-all duration-700"
                        style="width: {{ round((($currentNumber - 1) / $totalQuestions) * 100) }}%;
                        background: linear-gradient(90deg, var(--sky), var(--yellow), var(--orange), var(--rose))">
                    </div>
                </div>

                {{-- Dot indicators --}}
                <div class="flex items-center gap-1 mt-3 flex-wrap">
                    @for ($i = 1; $i <= $totalQuestions; $i++)
                        <div
                            class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all
                    {{ $i < $currentNumber ? 'dot-done' : ($i == $currentNumber ? 'dot-active' : 'dot-next') }}">
                            {{ $i < $currentNumber ? '✓' : $i }}
                        </div>
                    @endfor
                </div>
            </div>


            {{-- ── VIDEO ── --}}
            <div class="video-card-wrap">
                <div class="relative bg-slate-900" style="aspect-ratio:16/9">
                    @if ($question->video_path)
                        <video id="questionVideo" class="w-full h-full object-contain" controls muted preload="metadata"
                            controlsList="nodownload" onended="onVideoEnded()"
                            onplay="document.getElementById('videoCaptionBadge')?.classList.add('opacity-0')">
                            <source src="{{ asset('storage/' . $question->video_path) }}" type="video/mp4">
                            @if ($question->subtitle_path)
                                <track kind="subtitles" src="{{ asset('storage/' . $question->subtitle_path) }}"
                                    srclang="id" label="Indonesia" default>
                            @endif
                        </video>

                        {{-- Caption overlay --}}
                        <div id="videoCaptionBadge"
                            class="absolute inset-0 flex items-center justify-center pointer-events-none transition-opacity duration-300">
                            <span
                                class="text-white text-sm font-bold px-5 py-2.5 rounded-2xl max-w-[85%] text-center leading-snug"
                                style="background:rgba(0,0,0,0.55);backdrop-filter:blur(4px)">
                                {{ $question->question_text }}
                            </span>
                        </div>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3" style="color:#94a3b8">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                                style="background:rgba(255,255,255,0.08)">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm">Video BISINDO belum tersedia</p>
                        </div>
                    @endif

                    {{-- Tombol Putar Ulang --}}
                    <button onclick="document.getElementById('questionVideo')?.play()"
                        class="absolute top-3 right-3 text-white text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5 font-semibold transition"
                        style="background:rgba(0,0,0,0.55);backdrop-filter:blur(4px)"
                        onmouseover="this.style.background='rgba(0,0,0,0.75)'"
                        onmouseout="this.style.background='rgba(0,0,0,0.55)'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Putar Ulang
                    </button>
                </div>
            </div>


            {{-- ── TEKS SOAL ── --}}
            <div class="glass-card px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="soal-badge flex-shrink-0 mt-0.5">Soal {{ $currentNumber }}</span>
                    <p class="text-slate-800 text-base font-bold leading-relaxed">
                        {{ $question->question_text }}
                    </p>
                </div>
            </div>


            {{-- ── PILIHAN JAWABAN ── --}}
            <form action="{{ route('assessment.answer') }}" method="POST" id="answerForm">
                @csrf
                <input type="hidden" name="session_token" value="{{ $sessionToken }}">
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="question_number" value="{{ $currentNumber }}">

                @php $optionLetters = ['A','B','C','D']; @endphp
                <div class="space-y-2.5" id="optionList">
                    @foreach ($question->options as $idx => $option)
                        @php $letter = $optionLetters[$idx] ?? 'D'; @endphp
                        <label class="option-label option-card flex items-center gap-4 px-4 py-3.5 select-none w-full"
                            for="option_{{ $option->id }}" data-letter="{{ $letter }}">
                            <input type="radio" name="option_id" id="option_{{ $option->id }}"
                                value="{{ $option->id }}" class="sr-only"
                                onchange="selectOption(this, '{{ $letter }}')"
                                {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}>
                            <div class="opt-circle circle-{{ $letter }} option-circle">{{ $letter }}</div>
                            <span class="option-text text-sm text-slate-700 font-medium leading-relaxed">
                                {{ $option->option_text }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- ── NAVIGASI ── --}}
                <div class="flex items-center justify-between mt-5 gap-3">
                    @if ($currentNumber > 1)
                        <a href="{{ route('assessment.questions', ['token' => $sessionToken, 'no' => $currentNumber - 1]) }}"
                            class="btn-prev">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if ($currentNumber < $totalQuestions)
                        <button type="submit" id="nextBtn" disabled class="btn-next">
                            Selanjutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <button type="submit" id="nextBtn" disabled class="btn-next btn-finish">
                            Selesai & Lihat Hasil 🎉
                        </button>
                    @endif
                </div>
            </form>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Restore state saat load
        document.querySelectorAll('input[name="option_id"]').forEach(input => {
            if (input.checked) {
                const letter = input.closest('label').dataset.letter;
                styleOption(input.closest('label'), true, letter);
            }
        });

        if (document.querySelector('input[name="option_id"]:checked')) {
            document.getElementById('nextBtn').disabled = false;
        }

        function selectOption(input, letter) {
            // Reset semua
            document.querySelectorAll('.option-label').forEach(label => {
                const l = label.dataset.letter;
                styleOption(label, false, l);
            });
            // Style yang dipilih
            styleOption(input.closest('label'), true, letter);
            document.getElementById('nextBtn').disabled = false;
        }

        function styleOption(label, selected, letter) {
            const circle = label.querySelector('.option-circle');
            const text = label.querySelector('.option-text');

            // Hapus semua kelas selected
            ['selected-A', 'selected-B', 'selected-C', 'selected-D'].forEach(c => label.classList.remove(c));

            if (selected) {
                label.classList.add('selected-' + letter);
                text.classList.add('font-semibold');
                text.style.color = letter === 'A' ? '#1a6fa0' :
                    letter === 'B' ? '#9a6300' :
                    letter === 'C' ? '#b84a0a' :
                    '#a02040';
            } else {
                text.classList.remove('font-semibold');
                text.style.color = '';
            }
        }

        function onVideoEnded() {
            // Highlight area jawaban setelah video selesai
            const list = document.getElementById('optionList');
            list.style.transition = 'all 0.3s';
            list.style.filter = 'drop-shadow(0 0 12px rgba(119,190,240,0.4))';
            setTimeout(() => {
                list.style.filter = '';
            }, 1800);
        }

        // Cegah double submit
        document.getElementById('answerForm').addEventListener('submit', function(e) {
            if (!document.querySelector('input[name="option_id"]:checked')) {
                e.preventDefault();
                alert('Pilih jawaban terlebih dahulu!');
                return;
            }
            const btn = document.getElementById('nextBtn');
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8z"/></svg> Menyimpan...';
        });
    </script>
@endpush
