@extends('layouts.app')
@section('title', 'Buku Panduan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Buku Panduan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <span>Buku Panduan</span>
      </p>
    </div>

    @if($latestGuide)
      @php
        $fileUrl = !empty($latestGuide->file_path) ? asset('storage/'.$latestGuide->file_path) : null;
        $fileName = strtolower($latestGuide->file_name ?? '');
        $filePath = strtolower($latestGuide->file_path ?? '');
        $isPdf = str_ends_with($fileName, '.pdf') || str_ends_with($filePath, '.pdf');
        $previewUrl = $isPdf ? route('documents.preview.guide', $latestGuide) : null;
        $previewDataUrl = $isPdf ? route('documents.preview.guide', ['document' => $latestGuide, 'base64' => 1]) : null;
      @endphp
      <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
        <h2 class="text-xl font-bold text-slate-800">Dokumen Buku Panduan</h2>
        <p class="text-sm text-slate-500 mt-1">Dokumen terbaru yang diunggah admin</p>

        <div class="mt-5 space-y-4">
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div class="min-w-0 flex-1">
                <div class="truncate text-sm text-slate-800" title="{{ $latestGuide->file_name }}"><span>{{ $latestGuide->file_name }}</span><span class="text-slate-500" data-pdf-page-info></span></div>
                <div class="text-xs text-slate-500">Diunggah : {{ optional($latestGuide->created_at)->format('d-m-Y H:i') ?: '-' }} oleh {{ $latestGuide->uploader?->name ?? 'Admin' }}</div>
              </div>
              @if($fileUrl)
                <a href="{{ ($isPdf && $previewUrl) ? $previewUrl : $fileUrl }}" target="_blank" class="inline-flex shrink-0 items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                  Lihat
                </a>
              @endif
            </div>

            @if($fileUrl && $isPdf && $previewUrl)
              <div class="bg-slate-100 p-3 md:p-4">
                <div
                  class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                  data-pdf-viewer
                  data-pdf-url="{{ $previewDataUrl }}"
                  data-pdf-name="{{ $latestGuide->file_name }}"
                >
                  <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                    <div class="flex flex-col items-center gap-3" data-pdf-pages>
                      <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                    </div>
                  </div>
                </div>
              </div>
            @elseif($fileUrl)
              <div class="px-4 py-3 text-sm text-slate-600 bg-white">
                Preview hanya tersedia untuk file PDF.
              </div>
            @else
              <div class="px-4 py-3 text-sm text-rose-600 bg-white">
                File tidak tersedia.
              </div>
            @endif
          </div>
        </div>
      </div>
    @else
      <div class="rounded-xl bg-white ring-1 ring-slate-200 px-4 py-6 text-center text-sm text-slate-500">
        Belum ada buku panduan yang diunggah admin.
      </div>
    @endif
  </div>
@endsection
