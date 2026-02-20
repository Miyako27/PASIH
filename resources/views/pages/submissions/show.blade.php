@extends('layouts.app')
@section('title', 'Detail Pengajuan')

@section('content')
  <div class="space-y-6">
    <x-ui.section title="Detail Pengajuan" subtitle="Informasi utama permohonan">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold">Nomor Surat:</span> {{ $submission->nomor_surat }}</div>
        <div><span class="font-semibold">Status:</span> {{ $submission->status->label() }}</div>
        <div><span class="font-semibold">Perihal:</span> {{ $submission->perihal }}</div>
        <div><span class="font-semibold">Pemda:</span> {{ $submission->pemda_name }}</div>
        <div class="md:col-span-2"><span class="font-semibold">Judul Perda:</span> {{ $submission->perda_title }}</div>
        <div class="md:col-span-2"><span class="font-semibold">Deskripsi:</span> {{ $submission->description ?: '-' }}</div>
      </div>
    </x-ui.section>

    <x-ui.section title="Dokumen Pengajuan" subtitle="Berkas yang diunggah di level pengajuan">
      <div class="space-y-3 text-sm">
        @forelse($submission->documents as $document)
          <div class="flex items-center justify-between rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-3">
            <div>
              <div class="font-semibold">{{ $document->file_name }}</div>
              <div class="text-xs text-slate-500">{{ $document->document_type }}</div>
            </div>
            @if(!empty($document->file_path))
            <a href="{{ asset('storage/'.$document->file_path) }}"
                target="_blank"
                class="text-sm font-semibold hover:underline">
                Lihat
            </a>
            @else
            <span class="text-xs text-rose-500">File tidak tersedia</span>
            @endif
                </div>
                    @empty
                    <div class="text-slate-500">Belum ada dokumen.</div>
                    @endforelse
                </div>
    </x-ui.section>

    <x-ui.section title="Riwayat Disposisi" subtitle="Catatan alur disposisi permohonan">
      <div class="space-y-2 text-sm">
        @forelse($submission->dispositions as $disposition)
          <div class="rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3">
            <div class="font-semibold">Disposisi pada {{ optional($disposition->disposed_at)->format('d M Y H:i') }}</div>
            <div class="text-slate-600">{{ $disposition->note ?: '-' }}</div>
          </div>
        @empty
          <div class="text-slate-500">Belum ada disposisi.</div>
        @endforelse
      </div>
    </x-ui.section>

    @if($submission->submitter_id === auth()->id() && in_array($submission->status->value, ['submitted', 'revised'], true))
      <a href="{{ route('submissions.edit', $submission) }}" class="inline-flex items-center h-10 px-4 rounded-2xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">Edit Pengajuan</a>
    @endif
  </div>
@endsection
