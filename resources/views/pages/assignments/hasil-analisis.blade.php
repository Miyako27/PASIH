@extends('layouts.app')
@section('title', 'Hasil Analisis')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Hasil Analisis</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <span>Hasil Analisis</span>
      </p>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-slate-700">
          <span>Tampil</span>
          <span class="h-8 px-2 inline-flex items-center rounded-md border border-slate-300">{{ $results->perPage() }}</span>
          <span>Data</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-700">
          <span>Cari:</span>
          <input type="text" class="h-8 w-40 rounded-md border border-[#B9B9B9] text-sm">
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-4 py-3 text-left">No</th>
              <th class="px-4 py-3 text-left">Nomor Surat</th>
              <th class="px-4 py-3 text-left">Perihal</th>
              <th class="px-4 py-3 text-left">Analis</th>
              <th class="px-4 py-3 text-left">Status Analisis</th>
              <th class="px-4 py-3 text-left">Dokumen Hasil</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($results as $item)
              @php
                $rowNumber = ($results->firstItem() ?? 1) + $loop->index;
                $doc = $item->latestAnalysisDocument;
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $item->submission->nomor_surat }}</td>
                <td class="px-4 py-3">{{ $item->submission->perihal }}</td>
                <td class="px-4 py-3">{{ $item->analyst?->name ?? '-' }}</td>
                <td class="px-4 py-3"><x-ui.badge tone="green">Selesai Analisis</x-ui.badge></td>
                <td class="px-4 py-3">
                  @if($doc)
                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-rose-600 hover:underline" title="Lihat Dokumen Hasil">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H14a1 1 0 01-1-1V3.5zM8 13h8v1.5H8V13zm0 3h8v1.5H8V16z"/></svg>
                      PDF
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
                <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada hasil analisis selesai.</td>
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
          {{ $results->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
