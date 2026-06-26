@extends('layouts.app')

@section('title', 'Soal ' . $currentNumber . ' — NumerasiTuli')
@section('page-title', 'Asesmen Numerasi')
@section('page-subtitle', $studentName . ' · ' . $schoolName)

{{-- Custom CSS untuk styling --}}
@push('styles')
    <style>
        :root {
            --color-cream: #FDFCE9;
            --color-yellow: #FEBC28;
            --color-teal: #61D4D5;
            --color-teal-dark: #2F6974;
            --color-coral: #F79C99;
            --color-deep-blue: #235B6D;
        }

        body {
            background-color: var(--color-cream);
            color: var(--color-deep-blue);
            font-family: 'Inter', sans-serif;
            /* Pastikan font Inter sudah diload di layout */
        }

        .card-update {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Override custom style card if any in global */
        .card {
            background-color: white;
            border-radius: 20px;
            padding: 1rem 1.5rem;
        }

        /* Styling Progress Bar */
        .progress-bar-custom {
            height: 10px;
            background-color: #E2E8F0;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: var(--color-deep-blue);
            border-radius: 9999px;
            transition: width 0.5s ease;
        }

        /* Styling Dot Indicators */
        .dot-custom {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .dot-active {
            background-color: var(--color-deep-blue);
            color: white;
            box-shadow: 0 0 0 4px #BFDBFE;
            /* Ring style */
        }

        .dot-completed {
            background-color: #BFDBFE;
            color: var(--color-deep-blue);
        }

        .dot-inactive {
            background-color: #F1F5F9;
            color: #94A3B8;
            border: 2px solid #E2E8F0;
        }

        /* Styling Teks Soal */
        .question-text-custom {
            color: var(--color-deep-blue);
            font-size: 1.875rem;
            /* 30px */
            font-weight: 600;
            margin-bottom: 2rem;
        }

        /* Styling Opsi Jawaban */
        .option-label-custom {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            border-width: 2px;
            border-color: transparent;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
        }

        .option-label-custom:hover {
            background-color: #EFF6FF;
            border-color: #60A5FA;
        }

        .option-radio-custom {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .option-text-custom {
            font-size: 1.125rem;
            /* 18px */
            color: var(--color-deep-blue);
        }

        /* Warna Opsi Jawaban saat Dipilih */
        /* Akan ditambahkan di javascript styleOption() */
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- Progress bar & Dots --}}
        <div class="card-update">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-slate-600">Soal {{ $currentNumber }} dari
                    {{ $totalQuestions }}</span>
                <span
                    class="text-sm font-semibold text-violet-600">{{ round((($currentNumber - 1) / $totalQuestions) * 100) }}%
                    selesai</span>
            </div>
            <div class="progress-bar-custom mb-6">
                <div class="progress-bar-fill" style="width: {{ round((($currentNumber - 1) / $totalQuestions) * 100) }}%">
                </div>
            </div>

            {{-- Dot indicators --}}
            <div class="flex items-center gap-3 mt-4 flex-wrap justify-center">
                @for ($i = 1; $i <= $totalQuestions; $i++)
                    <div
                        class="dot-custom
                    {{ $i < $currentNumber ? 'dot-completed' : ($i == $currentNumber ? 'dot-active' : 'dot-inactive') }}">
                        {{ $i < $currentNumber ? '✓' : $i }}
                    </div>
                @endfor
            </div>
        </div>


        {{-- Tampilan Tengah: Video + Teks Soal --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            {{-- Video Card --}}
            <div class="card-update !p-0 overflow-hidden shadow-2xl rounded-xl">
                <div class="relative bg-black aspect-video">
                    @if ($question->video_path)
                        <video id="questionVideo" class="w-full h-full object-contain" controls controlsList="nodownload"
                            onended="onVideoEnded()">
                            <source src="{{ asset('storage/' . $question->video_path) }}" type="video/mp4">
                            @if ($question->subtitle_path)
                                <track kind="subtitles" src="{{ asset('storage/' . $question->subtitle_path) }}"
                                    srclang="id" label="Indonesia" default>
                            @endif
                        </video>
                    @else
                        {{-- Placeholder --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-3">
                            <svg class="w-16 h-16 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-lg">Video BISINDO tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teks Soal & Tanda BISINDO --}}
            <div class="flex flex-col justify-between">
                <h1 class="question-text-custom">{{ $question->question_text }}</h1>

                {{-- Keterangan BISINDO --}}
                <div class="card-update border-2 border-teal-100 !p-4 flex items-center gap-4">
                    <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <img src="{{ asset('images/icon_bisindo.svg') }}" alt="BISINDO" class="w-10 h-10">
                        {{-- Pastikan icon_bisindo.svg ada --}}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-teal-dark">Bahasa Isyarat</h4>
                        <p class="text-teal-dark text-opacity-80 text-sm">Dilengkapi Isyarat BISINDO</p>
                    </div>
                </div>
            </div>
        </div>


        {{-- Pilihan Jawaban --}}
        <form action="{{ route('assessment.answer') }}" method="POST" id="answerForm">
            @csrf
            <input type="hidden" name="session_token" value="{{ $sessionToken }}">
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            <input type="hidden" name="question_number" value="{{ $currentNumber }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 mb-8" id="optionList">
                @foreach ($question->options as $idx => $option)
                    <label class="option-label-custom card hover:border-blue-400" for="option_{{ $option->id }}"
                        data-option-type="{{ ['yellow', 'teal', 'deep-blue', 'coral'][$idx] }}">
                        <input type="radio" name="option_id" id="option_{{ $option->id }}" value="{{ $option->id }}"
                            class="option-radio-custom sr-only" onchange="selectOption(this)"
                            {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}>

                        <span class="option-text-custom flex-grow">
                            <span class="font-bold mr-3">{{ ['A.', 'B.', 'C.', 'D.'][$idx] }}</span>
                            {{ $option->option_text }}
                        </span>

                        {{-- Centang saat dipilih --}}
                        <div
                            class="option-check-icon w-7 h-7 rounded-full bg-green-100 border-2 border-green-500 items-center justify-center hidden">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Navigasi Tombol Gede di Bawah --}}
            <div class="flex items-center justify-end gap-4">
                {{-- Prev --}}
                @if ($currentNumber > 1)
                    <a href="{{ route('assessment.questions', ['token' => $sessionToken, 'no' => $currentNumber - 1]) }}"
                        class="px-8 py-3 rounded-xl border border-slate-300 bg-white text-deep-blue text-lg font-semibold hover:bg-slate-50 transition-colors">
                        Kembali
                    </a>
                @else
                    <div></div> {{-- Spacer --}}
                @endif

                {{-- Next / Finish --}}
                @if ($currentNumber < $totalQuestions)
                    <button type="submit" id="nextBtn" disabled
                        class="flex items-center gap-2 bg-deep-blue hover:bg-deep-blue/90 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-3 px-10 rounded-xl transition-all text-lg">
                        Selanjutnya
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <button type="submit" id="nextBtn" disabled
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-3 px-10 rounded-xl transition-all text-lg shadow-lg shadow-green-200">
                        Selesai & Lihat Hasil
                    </button>
                @endif
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        // Restore checked state on load
        document.querySelectorAll('input[name="option_id"]').forEach(input => {
            if (input.checked) styleOption(input.closest('label'), true);
        });

        // Enable next button if option already selected
        if (document.querySelector('input[name="option_id"]:checked')) {
            document.getElementById('nextBtn').disabled = false;
        }

        function selectOption(input) {
            // Reset all
            document.querySelectorAll('.option-label-custom').forEach(label => styleOption(label, false));
            // Style selected
            styleOption(input.closest('label'), true);
            // Enable next
            document.getElementById('nextBtn').disabled = false;
        }

        function styleOption(label, selected) {
            const type = label.dataset.optionType;
            const text = label.querySelector('.option-text-custom');
            const checkIcon = label.querySelector('.option-check-icon');

            if (selected) {
                // Styling label border based on type
                let borderColor = '#235B6D'; // default deep blue
                if (type === 'yellow') borderColor = '#FEBC28';
                if (type === 'teal') borderColor = '#61D4D5';
                if (type === 'coral') borderColor = '#F79C99';

                label.style.borderColor = borderColor;
                label.style.borderWidth = '4px'; // Thicker border

                checkIcon.classList.remove('hidden');
                checkIcon.classList.add('flex');

                text.classList.add('font-semibold');
            } else {
                label.style.borderColor = 'transparent';
                label.style.borderWidth = '2px';

                checkIcon.classList.add('hidden');
                checkIcon.classList.remove('flex');

                text.classList.remove('font-semibold');
            }
        }

        function onVideoEnded() {
            // Optional: highlight answer area after video ends
            // document.getElementById('optionList').classList.add('ring-2', 'ring-violet-200', 'rounded-2xl');
            // setTimeout(() => document.getElementById('optionList').classList.remove('ring-2','ring-violet-200','rounded-2xl'), 1500);
        }

        // Prevent double submit
        document.getElementById('answerForm').addEventListener('submit', function(e) {
            if (!document.querySelector('input[name="option_id"]:checked')) {
                e.preventDefault();
                alert('Pilih jawaban terlebih dahulu!');
                return;
            }
            document.getElementById('nextBtn').disabled = true;
            document.getElementById('nextBtn').innerHTML =
                '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8z"/></svg> Menyimpan...';
        });
    </script>
@endpush
