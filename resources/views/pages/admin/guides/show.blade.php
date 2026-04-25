@extends('layouts.app')
@section('title', 'Detail Buku Panduan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Manajemen Buku Panduan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('admin.guides.index') }}" class="hover:text-slate-700 hover:underline">Manajemen Buku Panduan</a>
        <span class="mx-1">/</span>
        <span>Detail</span>
      </p>
    </div>

    @php
      $fileUrl = !empty($guide->file_path) ? asset('storage/'.$guide->file_path) : null;
      $fileName = strtolower($guide->file_name ?? '');
      $filePath = strtolower($guide->file_path ?? '');
      $isPdf = str_ends_with($fileName, '.pdf') || str_ends_with($filePath, '.pdf');
      $previewUrl = $isPdf ? route('documents.preview.guide', $guide) : null;
      $previewDataUrl = $isPdf ? route('documents.preview.guide', ['document' => $guide, 'base64' => 1]) : null;
    @endphp

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Informasi Buku Panduan</h2>
          <p class="text-sm text-slate-500 mt-1">Dokumen buku panduan yang diunggah admin</p>
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Judul Dokumen</div>
          <div class="mt-1 text-sm text-slate-800">{{ $guide->document_title ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Diunggah Oleh</div>
          <div class="mt-1 text-sm text-slate-800">{{ $guide->uploader?->name ?? '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Upload</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($guide->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Buku Panduan</h2>
      <p class="text-sm text-slate-500 mt-1">Dokumen terbaru buku panduan</p>

      <div class="mt-5 space-y-4">
        @if($fileUrl && $isPdf && $previewUrl)
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div class="min-w-0 flex-1">
                <div class="truncate text-sm text-slate-800" title="{{ $guide->file_name }}"><span>{{ $guide->file_name }}</span><span class="text-slate-500" data-pdf-page-info></span></div>
                <div class="text-xs text-slate-500">Diunggah : {{ optional($guide->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
              </div>
              <a href="{{ $previewUrl }}" target="_blank" class="inline-flex shrink-0 items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                Lihat
              </a>
            </div>
            <div class="bg-slate-100 p-3 md:p-4">
              <div
                class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                data-pdf-viewer
                data-pdf-url="{{ $previewDataUrl }}"
                data-pdf-name="{{ $guide->file_name }}"
              >
                <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                  <div class="flex flex-col items-center gap-3" data-pdf-pages>
                    <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @elseif($fileUrl)
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-600">
            Preview hanya tersedia untuk file PDF.
            <a href="{{ $fileUrl }}" target="_blank" class="ml-2 text-blue-600 hover:underline">Buka Dokumen</a>
          </div>
        @else
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">File tidak tersedia.</div>
        @endif
      </div>
    </div>
  </div>
@endsection
