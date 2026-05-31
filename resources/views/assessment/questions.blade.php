@extends('layouts.app')

@section('title', 'Soal ' . $currentNumber . ' — NumerasiTuli')
@section('page-title', 'Asesmen Numerasi')
@section('page-subtitle', $studentName . ' · ' . $schoolName)

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Progress bar --}}
    <div class="card !py-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-500">Soal {{ $currentNumber }} dari {{ $totalQuestions }}</span>
            <span class="text-xs font-semibold text-violet-600">{{ round(($currentNumber - 1) / $totalQuestions * 100) }}% selesai</span>
        </div>
        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-violet-500 rounded-full transition-all duration-500"
                 style="width: {{ round(($currentNumber - 1) / $totalQuestions * 100) }}%"></div>
        </div>
        {{-- Dot indicators --}}
        <div class="flex items-center gap-1 mt-3 flex-wrap">
            @for($i = 1; $i <= $totalQuestions; $i++)
                <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all
                    {{ $i < $currentNumber ? 'bg-violet-600 text-white' :
                       ($i == $currentNumber ? 'bg-violet-200 text-violet-700 ring-2 ring-violet-400' :
                       'bg-slate-100 text-slate-400') }}">
                    {{ $i < $currentNumber ? '✓' : $i }}
                </div>
            @endfor
        </div>
    </div>


    {{-- Video Card --}}
    <div class="card !p-0 overflow-hidden">
        {{-- Video --}}
        <div class="relative bg-slate-900 aspect-video">
            @if($question->video_path)
                <video id="questionVideo"
                       class="w-full h-full object-contain"
                       controls
                       controlsList="nodownload"
                       onended="onVideoEnded()">
                    <source src="{{ asset('storage/' . $question->video_path) }}" type="video/mp4">
                    {{-- Subtitle track --}}
                    @if($question->subtitle_path)
                    <track kind="subtitles" src="{{ asset('storage/' . $question->subtitle_path) }}"
                           srclang="id" label="Indonesia" default>
                    @endif
                </video>
            @else
                {{-- Placeholder saat video belum ada --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-3">
                    <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center text-3xl"></div>
                    <p class="text-sm">Video BISINDO belum tersedia</p>
                    <p class="text-xs text-slate-500">{{ $question->video_path ?? 'Tidak ada path video' }}</p>
                </div>
            @endif

            {{-- Replay badge --}}
            <button onclick="document.getElementById('questionVideo')?.play()"
                    class="absolute top-3 right-3 bg-black/50 hover:bg-black/70 text-white text-xs px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Putar Ulang
            </button>
        </div>

        {{-- Subtitle / Teks soal --}}
        <div class="bg-slate-50 border-t border-slate-100 px-5 py-4">
            <div class="flex items-start gap-2">
                <span class="bg-violet-100 text-violet-600 text-xs font-bold px-2 py-0.5 rounded-md flex-shrink-0 mt-0.5">Soal</span>
                <p class="text-slate-800 text-sm font-semibold leading-relaxed">{{ $question->question_text }}</p>
            </div>
            @if($question->topic)
            <div class="mt-2 flex items-center gap-1">
                <span class="text-[10px] text-slate-400">Topik:</span>
                <span class="text-[10px] bg-violet-50 text-violet-500 font-semibold px-2 py-0.5 rounded-full">{{ $question->topic }}</span>
            </div>
            @endif
        </div>
    </div>


    {{-- Pilihan Jawaban --}}
    <form action="{{ route('assessment.answer') }}" method="POST" id="answerForm">
        @csrf
        <input type="hidden" name="session_token" value="{{ $sessionToken }}">
        <input type="hidden" name="question_id" value="{{ $question->id }}">
        <input type="hidden" name="question_number" value="{{ $currentNumber }}">

        <div class="space-y-2.5" id="optionList">
            @foreach($question->options as $idx => $option)
            <label class="option-label flex items-center gap-3 card !py-3.5 cursor-pointer hover:border-violet-300 border-2 border-transparent transition-all select-none"
                   for="option_{{ $option->id }}">
                <input type="radio" name="option_id" id="option_{{ $option->id }}"
                       value="{{ $option->id }}"
                       class="sr-only"
                       onchange="selectOption(this)"
                       {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'checked' : '' }}>
                <div class="option-circle w-9 h-9 rounded-xl border-2 border-slate-200 flex items-center justify-center font-bold text-sm text-slate-400 flex-shrink-0 transition-all">
                    {{ ['A','B','C','D'][$idx] }}
                </div>
                <span class="option-text text-sm text-slate-700 leading-relaxed">{{ $option->option_text }}</span>
            </label>
            @endforeach
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between mt-5 gap-3">
            {{-- Prev --}}
            @if($currentNumber > 1)
            <a href="{{ route('assessment.questions', ['token' => $sessionToken, 'no' => $currentNumber - 1]) }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </a>
            @else
            <div></div>
            @endif

            {{-- Next / Finish --}}
            @if($currentNumber < $totalQuestions)
            <button type="submit" id="nextBtn" disabled
                    class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-2.5 px-5 rounded-xl transition-all text-sm">
                Selanjutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            @else
            <button type="submit" id="nextBtn" disabled
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-2.5 px-6 rounded-xl transition-all text-sm shadow-md shadow-green-200">
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
        document.querySelectorAll('.option-label').forEach(label => styleOption(label, false));
        // Style selected
        styleOption(input.closest('label'), true);
        // Enable next
        document.getElementById('nextBtn').disabled = false;
    }

    function styleOption(label, selected) {
        const circle = label.querySelector('.option-circle');
        const text   = label.querySelector('.option-text');
        if (selected) {
            label.classList.add('border-violet-500', 'bg-violet-50');
            label.classList.remove('border-transparent');
            circle.classList.add('bg-violet-600', 'border-violet-600', 'text-white');
            circle.classList.remove('border-slate-200', 'text-slate-400');
            text.classList.add('text-violet-700', 'font-semibold');
        } else {
            label.classList.remove('border-violet-500', 'bg-violet-50');
            label.classList.add('border-transparent');
            circle.classList.remove('bg-violet-600', 'border-violet-600', 'text-white');
            circle.classList.add('border-slate-200', 'text-slate-400');
            text.classList.remove('text-violet-700', 'font-semibold');
        }
    }

    function onVideoEnded() {
        // Optional: highlight answer area after video ends
        document.getElementById('optionList').classList.add('ring-2', 'ring-violet-200', 'rounded-2xl');
        setTimeout(() => document.getElementById('optionList').classList.remove('ring-2','ring-violet-200','rounded-2xl'), 1500);
    }

    // Prevent double submit
    document.getElementById('answerForm').addEventListener('submit', function(e) {
        if (!document.querySelector('input[name="option_id"]:checked')) {
            e.preventDefault();
            alert('Pilih jawaban terlebih dahulu!');
            return;
        }
        document.getElementById('nextBtn').disabled = true;
        document.getElementById('nextBtn').innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8z"/></svg> Menyimpan...';
    });
</script>
@endpush
