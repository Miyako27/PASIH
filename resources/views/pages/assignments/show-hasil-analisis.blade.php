@extends('layouts.app')
@section('title', 'Detail Hasil Analisis')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Hasil Analisis</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.analysis-results') }}" class="hover:text-slate-700 hover:underline">Hasil Analisis</a>
        <span class="mx-1">/</span>
        <span>Detail</span>
      </p>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Informasi Penugasan</h2>
          <p class="text-sm text-slate-500 mt-1">Ringkasan data utama hasil analisis</p>
        </div>
        <x-ui.badge tone="permohonan-done">Selesai Analisis</x-ui.badge>
      </div>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Nomor Surat</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->submission->nomor_surat }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Perihal</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->submission->perihal }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Instansi Pengaju</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->submission->pemda_name }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Analis</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->analyst?->name ?? '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Selesai</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($assignment->completed_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Ringkasan Hasil Analisis</h2>
      <p class="text-sm text-slate-500 mt-1">Isi pokok hasil analisis dari analis</p>
      <div class="mt-5 space-y-4 text-sm">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Ringkasan Analisis</div>
          <div class="mt-1 text-slate-700">{{ $analysisFields['ringkasan_analisis'] ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Hasil Evaluasi</div>
          <div class="mt-1 text-slate-700">{{ $analysisFields['hasil_evaluasi'] ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Rekomendasi</div>
          <div class="mt-1 text-slate-700">{{ $analysisFields['rekomendasi'] ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Dokumen Hasil Analisis</h2>
      <p class="text-sm text-slate-500 mt-1">Lampiran dokumen pada proses analisis</p>
      <div class="mt-5 space-y-4">
        @forelse($assignment->documents->sortByDesc('id') as $doc)
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div>
              <div class="text-sm font-semibold text-slate-800">{{ $doc->file_name }}</div>
              <div class="text-xs text-slate-500">{{ str_replace('_', ' ', $doc->document_type) }}</div>
              </div>
              @if(!empty($doc->file_path))
                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="inline-flex items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                  Lihat
                </a>
              @else
                <span class="text-xs text-rose-600 font-semibold">File tidak tersedia</span>
              @endif
            </div>
          </div>
        @empty
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">Belum ada dokumen hasil.</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
