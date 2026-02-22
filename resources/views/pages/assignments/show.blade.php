@extends('layouts.app')
@section('title', 'Detail Penugasan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Penugasan</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.index') }}" class="hover:text-slate-700 hover:underline">Penugasan</a>
        <span class="mx-1">/</span>
        <span>Detail Penugasan</span>
      </p>
    </div>

    @php
      $submission = $assignment->submission;

      $assignmentTone = match($assignment->status->value) {
        'completed' => 'permohonan-done',
        'in_progress' => 'permohonan-in-analysis',
        default => 'permohonan-available',
      };

      $submissionTone = match($submission->status->value) {
        'accepted' => 'analisis-accepted',
        'rejected' => 'analisis-rejected',
        'revised' => 'analisis-revised',
        default => 'analisis-submitted',
      };

      $statusNote = $submission->revision_note ?: $submission->rejection_note;

      $latestDisposition = $submission->latestDisposition;
    @endphp

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Informasi Penugasan</h2>
          <p class="text-sm text-slate-500 mt-1">Ringkasan data utama penugasan</p>
        </div>
        <x-ui.badge :tone="$assignmentTone">{{ $assignment->status->label() }}</x-ui.badge>
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
          <div class="text-xs uppercase tracking-wide text-slate-500">Pemberi Penugasan</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->assignedBy?->name ?? '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Analis</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $assignment->analyst?->name ?? 'Belum diambil' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Ditugaskan</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($assignment->assigned_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Deadline</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($assignment->deadline_at)->format('d-m-Y') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Mulai Analisis</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($assignment->started_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Selesai Analisis</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($assignment->completed_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Catatan Penugasan</div>
          <div class="mt-1 text-sm text-slate-700">{{ $assignment->instruction ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Status dan Disposisi Pengajuan</h2>
          <p class="text-sm text-slate-500 mt-1">Status pengajuan serta riwayat disposisi terkait penugasan</p>
        </div>
        <x-ui.badge :tone="$submissionTone">{{ $submission->status->label() }}</x-ui.badge>
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
      <h2 class="text-xl font-bold text-slate-800">Informasi Pengajuan Terkait</h2>
      <p class="text-sm text-slate-500 mt-1">Detail pengajuan yang menjadi dasar penugasan</p>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Akun Pengaju</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ $submission->submitter?->name ?? '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Pengajuan</div>
          <div class="mt-1 text-sm font-semibold text-slate-800">{{ optional($submission->submitted_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Deskripsi Pengajuan</div>
          <div class="mt-1 text-sm text-slate-700">{{ $submission->description ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Dokumen Pengajuan</h2>
      <p class="text-sm text-slate-500 mt-1">Surat Permohonan</p>

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
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">Belum ada dokumen pengajuan.</div>
        @endforelse
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Dokumen Penugasan (Hasil Analisis)</h2>
      <p class="text-sm text-slate-500 mt-1">Berkas yang diunggah selama proses penugasan</p>

      <div class="mt-5 space-y-4">
        @forelse($assignment->documents->sortByDesc('id') as $document)
          @php
            $fileUrl = !empty($document->file_path) ? asset('storage/'.$document->file_path) : null;
          @endphp
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div>
                <div class="text-sm font-semibold text-slate-800">{{ $document->file_name }}</div>
                <div class="text-xs text-slate-500">{{ str_replace('_', ' ', $document->document_type) }}</div>
                @if(!empty($document->notes))
                  <div class="mt-1 text-xs text-slate-600">{{ $document->notes }}</div>
                @endif
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
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">Belum ada dokumen penugasan.</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
