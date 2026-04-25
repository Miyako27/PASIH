@extends('layouts.app')
@section('title', 'Detail Hasil Analisis')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Hasil Analisis</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.analysis-results') }}" class="hover:text-slate-700 hover:underline">Hasil Analisis</a>
        <span class="mx-1">/</span>
        <span>Detail</span>
      </p>
    </div>

    @php
      $submission = $assignment->submission;
      $analysisDocuments = $assignment->documents
        ->where('document_type', 'hasil_analisis')
        ->sortByDesc('id')
        ->values();
      $latestAnalysisDocument = $analysisDocuments->first();
      $formatDisplayFileName = function ($document, string $documentLabel) use ($submission): string {
        $fromDb = trim((string) ($document?->file_name ?? ''));
        if ($fromDb !== '') {
          return $fromDb;
        }

        $instansiName = (string) ($submission->submitter?->instansi?->nama_instansi ?? $submission->pemda_name ?? 'Instansi');
        $normalize = function (string $value): string {
          $parts = preg_split('/[^A-Za-z0-9]+/', trim($value)) ?: [];
          $parts = array_filter($parts, static fn ($part) => $part !== '');

          return $parts === [] ? 'Dokumen' : implode('', $parts);
        };
        $instansiPart = $normalize($instansiName);
        $jenisPart = $normalize($documentLabel);
        $timestampPart = optional($document?->created_at)->format('YmdHis') ?? '';

        return "{$instansiPart}_{$jenisPart}_{$timestampPart}";
      };
      $submissionDocuments = $submission->documents->whereIn('document_type', [
        'surat_permohonan',
        'peraturan_daerah',
        'peraturan_pelaksana_perda',
        'dokumen_pendukung',
      ]);
      $inferSubmissionDocumentType = function ($document): ?string {
        $documentType = (string) ($document->document_type ?? '');
        if (in_array($documentType, ['surat_permohonan', 'peraturan_daerah', 'peraturan_pelaksana_perda'], true)) {
          return $documentType;
        }

        $fileName = strtolower((string) ($document->file_name ?? ''));
        if (str_contains($fileName, '_suratpermohonan_')) {
          return 'surat_permohonan';
        }
        if (str_contains($fileName, '_peraturandaerah_')) {
          return 'peraturan_daerah';
        }
        if (str_contains($fileName, '_peraturanpelaksanaperda_')) {
          return 'peraturan_pelaksana_perda';
        }

        return null;
      };
      $latestSubmissionDocumentByType = function (string $type) use ($submissionDocuments, $inferSubmissionDocumentType) {
        return $submissionDocuments
          ->sortByDesc('id')
          ->first(fn ($document) => $inferSubmissionDocumentType($document) === $type);
      };
      $suratPermohonanDocument = $latestSubmissionDocumentByType('surat_permohonan');
      $peraturanDaerahDocument = $latestSubmissionDocumentByType('peraturan_daerah');
      $peraturanPelaksanaPerdaDocument = $latestSubmissionDocumentByType('peraturan_pelaksana_perda');

      $assignmentTone = match($assignment->status->value) {
        'completed' => 'permohonan-done',
        'in_progress' => 'permohonan-in-analysis',
        'pending_kadiv_approval' => 'permohonan-awaiting-kadiv',
        'pending_kakanwil_approval' => 'permohonan-awaiting-kakanwil',
        'revision_by_pic' => 'permohonan-revision',
        default => 'permohonan-available',
      };

      $assignmentStatusLabel = match($assignment->status->value) {
        'completed' => 'Selesai Analisis',
        'in_progress' => 'Dalam Analisis',
        'pending_kadiv_approval' => 'Menunggu ACC Kadiv',
        'pending_kakanwil_approval' => 'Menunggu ACC Kakanwil',
        'revision_by_pic' => 'Revisi oleh PIC',
        default => 'Belum ada PIC',
      };
    @endphp

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-bold text-slate-800">Informasi Penugasan</h2>
          <p class="text-sm text-slate-500 mt-1">Ringkasan data utama penugasan</p>
        </div>
        <x-ui.badge :tone="$assignmentTone">{{ $assignmentStatusLabel }}</x-ui.badge>
      </div>

      <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Nomor Surat</div>
          <div class="mt-1 text-sm text-slate-800">{{ $submission->nomor_surat }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Pengajuan</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Judul Perda</div>
          <div class="mt-1 text-sm text-slate-800">{{ $submission->perda_title ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Pemberi Penugasan</div>
          <div class="mt-1 text-sm text-slate-800">{{ $assignment->assignedBy?->name ?? '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Penanggung Jawab Analisis</div>
          <div class="mt-1 text-sm text-slate-800">{{ $assignment->analyst?->name ?? 'Belum ada PIC' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Tanggal Ditugaskan</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($assignment->assigned_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Deadline</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($assignment->deadline_at)->format('d-m-Y') ?: '-' }}</div>
        </div>
        <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Mulai Analisis</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($assignment->started_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Selesai Analisis</div>
          <div class="mt-1 text-sm text-slate-800">{{ optional($assignment->completed_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
        <div class="md:col-span-2 rounded-lg bg-slate-50 ring-1 ring-slate-200 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-500">Catatan Penugasan</div>
          <div class="mt-1 text-sm text-slate-700">{{ $assignment->instruction ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Dokumen Permohonan</h2>
      <p class="text-sm text-slate-500 mt-1">Berkas permohonan yang diunggah</p>

      <div class="mt-5 space-y-4">
        @foreach([
          ['label' => 'Surat Permohonan', 'document' => $suratPermohonanDocument],
          ['label' => 'Peraturan Daerah', 'document' => $peraturanDaerahDocument],
          ['label' => 'Peraturan Pelaksana Perda', 'document' => $peraturanPelaksanaPerdaDocument],
        ] as $docCard)
          @php
            $document = $docCard['document'];
          @endphp
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
              <div class="text-sm text-slate-800">{{ $docCard['label'] }}</div>
            </div>
            @if($document)
              @php
                $fileUrl = !empty($document->file_path) ? asset('storage/'.$document->file_path) : null;
                $fileName = strtolower($document->file_name ?? '');
                $filePath = strtolower($document->file_path ?? '');
                $isPdf = str_ends_with($fileName, '.pdf') || str_ends_with($filePath, '.pdf');
                $previewUrl = $isPdf ? route('documents.preview.submission', $document) : null;
                $previewDataUrl = $isPdf ? route('documents.preview.submission', ['document' => $document, 'base64' => 1]) : null;
                $displayFileName = $formatDisplayFileName($document, $docCard['label']);
              @endphp
              <div class="flex items-center justify-between gap-3 px-4 py-3 bg-white">
                <div class="min-w-0 flex-1">
                  <div class="truncate text-sm text-slate-800" title="{{ $displayFileName }}"><span>{{ $displayFileName }}</span><span class="text-slate-500" data-pdf-page-info></span></div>
                  <div class="text-xs text-slate-500">Diunggah : {{ optional($document->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
                </div>
                @if($fileUrl)
                  @php
                    $openUrl = ($isPdf && $previewUrl) ? $previewUrl : $fileUrl;
                  @endphp
                  <a href="{{ $openUrl }}" target="_blank" class="inline-flex shrink-0 items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                    Lihat
                  </a>
                @else
                  <span class="text-xs text-rose-600 font-semibold">File tidak tersedia</span>
                @endif
              </div>
              @if($fileUrl && $isPdf && $previewUrl)
                <div class="bg-slate-100 p-3 md:p-4">
                  <div
                    class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                    data-pdf-viewer
                    data-pdf-url="{{ $previewDataUrl }}"
                    data-pdf-name="{{ $displayFileName }}"
                  >

                    <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                      <div class="flex flex-col items-center gap-3" data-pdf-pages>
                        <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            @else
              <div class="px-4 py-3 text-sm text-slate-500 bg-white">-</div>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5 md:p-6">
      <h2 class="text-xl font-bold text-slate-800">Ringkasan Hasil Analisis</h2>
      <p class="text-sm text-slate-500 mt-1">Isi pokok hasil analisis dari analis hukum</p>
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
      <p class="text-sm text-slate-500 mt-1">Dokumen hasil analisis terbaru</p>
      <div class="mt-5 space-y-4">
        @if($latestAnalysisDocument)
          @php
            $fileUrl = !empty($latestAnalysisDocument->file_path) ? asset('storage/'.$latestAnalysisDocument->file_path) : null;
            $fileName = strtolower($latestAnalysisDocument->file_name ?? '');
            $filePath = strtolower($latestAnalysisDocument->file_path ?? '');
            $isPdf = str_ends_with($fileName, '.pdf') || str_ends_with($filePath, '.pdf');
            $previewUrl = $isPdf ? route('documents.preview.assignment', $latestAnalysisDocument) : null;
            $previewDataUrl = $isPdf ? route('documents.preview.assignment', ['document' => $latestAnalysisDocument, 'base64' => 1]) : null;
            $displayFileName = $formatDisplayFileName($latestAnalysisDocument, 'Hasil Analisis');
          @endphp
          <div class="rounded-xl ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50">
              <div class="min-w-0 flex-1">
                <div class="truncate text-sm text-slate-800" title="{{ $displayFileName }}"><span>{{ $displayFileName }}</span><span class="text-slate-500" data-pdf-page-info></span></div>
                <div class="text-xs text-slate-500">Diunggah : {{ optional($latestAnalysisDocument->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
              </div>
              @if($fileUrl)
                <a href="{{ ($isPdf && $previewUrl) ? $previewUrl : $fileUrl }}" target="_blank" class="inline-flex shrink-0 items-center h-8 px-3 rounded-lg bg-white text-slate-700 text-xs font-semibold ring-1 ring-slate-300 hover:bg-slate-100">
                  Lihat
                </a>
              @else
                <span class="text-xs text-rose-600 font-semibold">File tidak tersedia</span>
              @endif
            </div>
            @if($fileUrl && $isPdf && $previewUrl)
              <div class="bg-slate-100 p-3 md:p-4">
                <div
                  class="overflow-hidden rounded-lg ring-1 ring-slate-200 bg-slate-200"
                  data-pdf-viewer
                  data-pdf-url="{{ $previewDataUrl }}"
                  data-pdf-name="{{ $displayFileName }}"
                >

                  <div class="h-[58vh] min-h-[420px] max-h-[840px] overflow-auto p-3" data-pdf-scroll>
                    <div class="flex flex-col items-center gap-3" data-pdf-pages>
                      <div class="text-xs text-slate-500">Menyiapkan preview PDF...</div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        @else
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">_</div>
        @endif
      </div>
    </div>
  </div>
@endsection
