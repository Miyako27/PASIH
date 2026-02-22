@extends('layouts.app')
@section('title', 'Detail Pengajuan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Permohonan</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('submissions.index') }}" class="hover:text-slate-700 hover:underline">Permohonan</a>
        <span class="mx-1">/</span>
        <span>Detail Pengajuan</span>
      </p>
    </div>

    @php
      $statusTone = match($submission->status->value) {
        'accepted' => 'analisis-accepted',
        'rejected' => 'analisis-rejected',
        'revised' => 'analisis-revised',
        default => 'analisis-submitted',
      };
    @endphp

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Informasi Pengajuan</h2>
          <p class="text-sm text-slate-500 mt-1">Ringkasan data utama permohonan</p>
        </div>
        <x-ui.badge :tone="$statusTone">{{ $submission->status->label() }}</x-ui.badge>
      </div>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Nomor Surat</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->nomor_surat }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Pengajuan</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Perihal</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->perihal }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Instansi Pengaju</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->pemda_name }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Judul Perda</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->perda_title }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Deskripsi</div>
          <div class="mt-1 text-sm text-slate-700">{{ $submission->description ?: '-' }}</div>
        </div>
      </div>
    </div>

    @php
      $statusNote = $submission->revision_note ?: $submission->rejection_note;
      $latestDisposition = $submission->latestDisposition;
    @endphp

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Status dan Disposisi Pengajuan</h2>
          <p class="text-sm text-slate-500 mt-1">Status pengajuan serta riwayat disposisi terkait penugasan</p>
        </div>
        <x-ui.badge :tone="$statusTone">{{ $submission->status->label() }}</x-ui.badge>
      </div>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Status Pengajuan</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->status->label() }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Review Status</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($submission->reviewed_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Catatan Status</div>
          <div class="mt-1 text-sm text-slate-700">{{ $statusNote ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Disposisi Terakhir</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $latestDisposition?->toUser?->name ?? $submission->divisionOperator?->name ?? '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Disposisi</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($latestDisposition?->disposed_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Catatan Disposisi</div>
          <div class="mt-1 text-sm text-slate-700">{{ $latestDisposition?->note ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Dokumen Pengajuan</h2>
      <p class="text-sm text-slate-500 mt-1">Berkas yang diunggah di level pengajuan</p>

      <div class="mt-5 space-y-4">
        @forelse($submission->documents as $document)
          @php
            $fileUrl = !empty($document->file_path) ? asset('storage/'.$document->file_path) : null;
          @endphp
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div>
                <div class="text-sm font-semibold text-slate-800">{{ $document->file_name }}</div>
                <div class="text-xs text-slate-500">{{ str_replace('_', ' ', $document->document_type) }}</div>
              </div>
              @if($fileUrl)
                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                  Lihat
                </a>
              @else
                <span class="text-xs text-rose-600 font-semibold">File tidak tersedia</span>
              @endif
            </div>
          </div>
        @empty
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">Belum ada dokumen.</div>
        @endforelse
      </div>
    </div>

    @if($submission->submitter_id === auth()->id() && in_array($submission->status->value, ['submitted', 'revised'], true))
      <a href="{{ route('submissions.edit', $submission) }}" class="inline-flex items-center h-10 px-4 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
        Edit Pengajuan
      </a>
    @endif
  </div>
@endsection
