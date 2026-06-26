{{-- Video Card --}}
<div class="card !p-0 overflow-hidden">
    {{-- Video --}}
    <div class="relative bg-slate-900 aspect-video">
        @if ($question->video_path)
            <video id="questionVideo" class="w-full h-full object-contain" controls controlsList="nodownload"
                onended="onVideoEnded()"
                onplay="document.getElementById('videoCaptionBadge')?.classList.add('opacity-0')">
                <source src="{{ asset('storage/' . $question->video_path) }}" type="video/mp4">
                @if ($question->subtitle_path)
                    <track kind="subtitles" src="{{ asset('storage/' . $question->subtitle_path) }}" srclang="id"
                        label="Indonesia" default>
                @endif
            </video>
            <div id="videoCaptionBadge"
                class="absolute inset-0 flex items-center justify-center pointer-events-none transition-opacity duration-300">
                <span
                    class="bg-slate-800 text-white text-base font-bold px-5 py-2.5 rounded-full shadow-lg max-w-[85%] text-center leading-snug tracking-wide">
                    {{ $question->question_text }}
                </span>
            </div>
        @else
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-3">
                <div class="w-16 h-16 bg-slate-800 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm">Video BISINDO belum tersedia</p>
                <p class="text-xs text-slate-500">{{ $question->video_path ?? 'Tidak ada path video' }}</p>
            </div>
        @endif
        <button onclick="document.getElementById('questionVideo')?.play()"
            class="absolute top-3 right-3 bg-black/50 hover:bg-black/70 text-white text-xs px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Putar Ulang
        </button>
    </div>
</div>

{{-- Teks Soal Card --}}
<div class="card">
    <div class="flex items-start gap-2">
        <span
            class="bg-violet-100 text-violet-600 text-xs font-bold px-2 py-0.5 rounded-md flex-shrink-0 mt-0.5">Soal</span>
        <p class="text-slate-900 text-base font-bold leading-relaxed tracking-tight">
            {{ $question->question_text }}</p>
    </div>
    @if ($question->topic)
        <div class="mt-2 flex items-center gap-1">
            <span class="text-[10px] text-slate-400">Topik:</span>
            <span
                class="text-[10px] bg-violet-50 text-violet-500 font-semibold px-2 py-0.5 rounded-full">{{ $question->topic }}</span>
        </div>
    @endif
</div>
