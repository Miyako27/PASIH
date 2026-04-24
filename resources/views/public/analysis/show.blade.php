<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Hasil Analisis Publik</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/loginlogo2.png') }}" alt="Logo" class="h-11 w-11 rounded-lg object-cover">
                <div>
                    <div class="text-xl font-bold text-blue-950">PASIH</div>
                    <div class="text-xs text-slate-500">Hasil Analisis Peraturan Daerah</div>
                </div>
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center h-10 px-5 rounded-full bg-blue-950 text-white font-semibold hover:bg-blue-900">Masuk</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 space-y-5">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-blue-950">Hasil Analisis</h1>
            <p class="mt-2 text-sm text-slate-600">
                <a href="{{ route('public.analysis.index') }}" class="hover:underline">Daftar Hasil Analisis</a>
                <span class="mx-1">/</span>
                <span>Detail</span>
            </p>
        </div>

        @php
            $submission = $assignment->submission;
            $analysisDoc = $latestAnalysisDocument;
            $perdaDoc = $perdaDocument;
            $yearCompleted = optional($assignment->completed_at)->format('Y') ?: '-';
        @endphp

        <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
            <h2 class="text-xl font-bold text-slate-800">Informasi Perda</h2>
            <p class="text-sm text-slate-500 mt-1">Ringkasan informasi perda hasil analisis</p>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
                    <div class="text-xs uppercase tracking-wide text-slate-500">Judul Perda</div>
                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission?->perda_title ?: '-' }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
                    <div class="text-xs uppercase tracking-wide text-slate-500">Tahun Selesai Analisis</div>
                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $yearCompleted }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
                    <div class="text-xs uppercase tracking-wide text-slate-500">Instansi</div>
                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission?->submitter?->instansi?->nama_instansi ?? '-' }}</div>
                </div>
                <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
                    <div class="text-xs uppercase tracking-wide text-slate-500">Nomor Surat</div>
                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission?->nomor_surat ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
            <h2 class="text-xl font-bold text-slate-800">Dokumen Perda</h2>
            <p class="text-sm text-slate-500 mt-1">Dokumen peraturan daerah yang dianalisis</p>

            <div class="mt-5 rounded-xl ring-1 ring-slate-200 overflow-hidden">
                @if($perdaDoc && !empty($perdaDoc->file_path))
                    @php
                        $perdaFileUrl = asset('storage/'.$perdaDoc->file_path);
                        $perdaFileName = strtolower($perdaDoc->file_name ?? '');
                        $perdaFilePath = strtolower($perdaDoc->file_path ?? '');
                        $perdaIsPdf = str_ends_with($perdaFileName, '.pdf') || str_ends_with($perdaFilePath, '.pdf');
                        $perdaPreviewUrl = $perdaIsPdf ? route('public.documents.preview.submission', $perdaDoc) : null;
                        $perdaPreviewDataUrl = $perdaIsPdf ? route('public.documents.preview.submission', ['document' => $perdaDoc, 'base64' => 1]) : null;
                        $perdaOpenUrl = $perdaIsPdf ? $perdaPreviewUrl : $perdaFileUrl;
                    @endphp
                    <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-slate-800">{{ $perdaDoc->file_name ?? 'Dokumen Perda' }}</div>
                            <div class="text-xs text-slate-500">{{ optional($perdaDoc->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
                        </div>
                        <a href="{{ $perdaOpenUrl }}" target="_blank" class="inline-flex items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">Lihat</a>
                    </div>
                    @if($perdaIsPdf)
                        <div class="bg-slate-100 p-3 md:p-4">
                            <div
                                class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                                data-pdf-viewer
                                data-pdf-url="{{ $perdaPreviewDataUrl }}"
                                data-pdf-name="{{ $perdaDoc->file_name ?? 'Dokumen Perda' }}"
                            >
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200 bg-white px-3 py-2">
                                    <div class="truncate text-xs font-semibold text-slate-600" data-pdf-meta>Memuat dokumen...</div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="inline-flex items-center h-7 px-2 rounded-md text-xs font-semibold text-white bg-slate-700 hover:bg-slate-800" data-pdf-action="load">Tampilkan</button>
                                    </div>
                                </div>
                                <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                                    <div class="flex flex-col items-center gap-3" data-pdf-pages>
                                        <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="px-4 py-3 text-sm text-slate-500 bg-white">Dokumen perda belum tersedia.</div>
                @endif
            </div>
        </div>

        <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
            <h2 class="text-xl font-bold text-slate-800">Dokumen Hasil Analisis Terbaru</h2>
            <p class="text-sm text-slate-500 mt-1">Jika ada revisi, dokumen terbaru ditampilkan di sini</p>

            <div class="mt-5 rounded-xl ring-1 ring-slate-200 overflow-hidden">
                @if($analysisDoc && !empty($analysisDoc->file_path))
                    @php
                        $analysisFileUrl = asset('storage/'.$analysisDoc->file_path);
                        $analysisFileName = strtolower($analysisDoc->file_name ?? '');
                        $analysisFilePath = strtolower($analysisDoc->file_path ?? '');
                        $analysisIsPdf = str_ends_with($analysisFileName, '.pdf') || str_ends_with($analysisFilePath, '.pdf');
                        $analysisPreviewUrl = $analysisIsPdf ? route('public.documents.preview.assignment', $analysisDoc) : null;
                        $analysisPreviewDataUrl = $analysisIsPdf ? route('public.documents.preview.assignment', ['document' => $analysisDoc, 'base64' => 1]) : null;
                        $analysisOpenUrl = $analysisIsPdf ? $analysisPreviewUrl : $analysisFileUrl;
                    @endphp
                    <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-slate-800">{{ $analysisDoc->file_name ?? 'Dokumen Hasil Analisis' }}</div>
                            <div class="text-xs text-slate-500">{{ optional($analysisDoc->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
                        </div>
                        <a href="{{ $analysisOpenUrl }}" target="_blank" class="inline-flex items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">Lihat</a>
                    </div>
                    @if($analysisIsPdf)
                        <div class="bg-slate-100 p-3 md:p-4">
                            <div
                                class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                                data-pdf-viewer
                                data-pdf-url="{{ $analysisPreviewDataUrl }}"
                                data-pdf-name="{{ $analysisDoc->file_name ?? 'Dokumen Hasil Analisis' }}"
                            >
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200 bg-white px-3 py-2">
                                    <div class="truncate text-xs font-semibold text-slate-600" data-pdf-meta>Memuat dokumen...</div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="inline-flex items-center h-7 px-2 rounded-md text-xs font-semibold text-white bg-slate-700 hover:bg-slate-800" data-pdf-action="load">Tampilkan</button>
                                    </div>
                                </div>
                                <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                                    <div class="flex flex-col items-center gap-3" data-pdf-pages>
                                        <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="px-4 py-3 text-sm text-slate-500 bg-white">Dokumen hasil analisis belum tersedia.</div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
