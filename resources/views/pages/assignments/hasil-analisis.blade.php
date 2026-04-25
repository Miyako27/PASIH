@extends('layouts.app')
@section('title', 'Hasil Analisis')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Hasil Analisis</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <span>Hasil Analisis</span>
      </p>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('assignments.analysis-results') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <span>Tampil</span>
          <select name="per_page" class="h-8 rounded-md border-slate-300 text-sm focus:outline-none focus:ring-0 focus:border-slate-300" onchange="this.form.submit()">
            <option value="5" @selected($perPage === 5)>5</option>
            <option value="10" @selected($perPage === 10)>10</option>
            <option value="25" @selected($perPage === 25)>25</option>
          </select>
          <span>Data</span>
          <input type="hidden" name="q" value="{{ $search }}">
        </form>
        <form method="GET" action="{{ route('assignments.analysis-results') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <label for="q">Cari:</label>
          <input id="q" type="text" name="q" value="{{ $search }}" class="h-8 w-40 rounded-md border border-[#B9B9B9] text-sm">
          <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-4 py-3 text-center">No</th>
              <th class="px-4 py-3 text-left">Judul Perda</th>
              <th class="px-4 py-3 text-left">Instansi Pengaju</th>
              <th class="px-4 py-3 text-center">Tahun</th>
              <th class="px-4 py-3 text-left">Analis Hukum</th>
              <th class="px-4 py-3 text-center">Peraturan Daerah</th>
              <th class="px-4 py-3 text-center">Dokumen Hasil Analisis</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($results as $item)
              @php
                $rowNumber = ($results->firstItem() ?? 1) + $loop->index;
                $submission = $item->submission;
                $yearCompleted = optional($item->completed_at)->format('Y') ?: '-';
                $perdaDocument = $submission?->documents?->where('document_type', 'peraturan_daerah')->sortByDesc('id')->first();
                $analysisDocument = $item->latestAnalysisDocument;
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3 text-center">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $submission?->pemda_title ?: $submission?->perda_title ?: '-' }}</td>
                <td class="px-4 py-3">{{ $submission?->submitter?->instansi?->nama_instansi ?? '-' }}</td>
                <td class="px-4 py-3 text-center">{{ $yearCompleted }}</td>
                <td class="px-4 py-3">{{ $item->analyst?->name ?? $item->latestPicUpdate?->analyst?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                  @if($perdaDocument && !empty($perdaDocument->file_path))
                    <a href="{{ asset('storage/'.$perdaDocument->file_path) }}" target="_blank" class="inline-flex w-full items-center justify-center text-rose-600 hover:underline" title="Lihat Dokumen Peraturan Daerah" aria-label="Lihat Dokumen Peraturan Daerah">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H14a1 1 0 01-1-1V3.5zM8 13h8v1.5H8V13zm0 3h8v1.5H8V16z"/></svg>
                    </a>
                  @else
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-center">
                  @if($analysisDocument && !empty($analysisDocument->file_path))
                    <a href="{{ asset('storage/'.$analysisDocument->file_path) }}" target="_blank" class="inline-flex w-full items-center justify-center text-rose-600 hover:underline" title="Lihat Dokumen Hasil Analisis" aria-label="Lihat Dokumen Hasil Analisis">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H14a1 1 0 01-1-1V3.5zM8 13h8v1.5H8V13zm0 3h8v1.5H8V16z"/></svg>
                    </a>
                  @else
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1.5">
                    <a href="{{ route('assignments.analysis-results.show', $item) }}" class="h-8 w-8 rounded-md bg-blue-600 text-white inline-flex items-center justify-center" title="Detail Hasil Analisis">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><circle cx="12" cy="12" r="3" /></svg>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-6 text-center text-slate-500">Belum ada hasil analisis selesai.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">
        <div>
          Menampilkan {{ $results->firstItem() ?? 0 }} - {{ $results->lastItem() ?? 0 }} dari {{ $results->total() }} data
        </div>
        <div>
          {{ $results->onEachSide(1)->links('vendor.pagination.pasih') }}
        </div>
      </div>
    </div>
  </div>
@endsection
