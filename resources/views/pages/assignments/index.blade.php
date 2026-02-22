@extends('layouts.app')
@section('title', 'Penugasan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Penugasan</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <span>Penugasan</span>
      </p>
    </div>

    @if($canAssign)
      <x-ui.section title="Buat Penugasan" subtitle="Tetapkan analis untuk permohonan yang telah didisposisi">
        <form method="POST" action="{{ route('assignments.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @csrf
          <label class="text-sm font-semibold">Pilih Pengajuan
            <select name="submission_id" class="mt-2 w-full h-10 rounded-xl border-slate-300" required>
              @foreach($submissions as $submission)
                <option value="{{ $submission->id }}">{{ $submission->nomor_surat }} - {{ $submission->pemda_name }}</option>
              @endforeach
            </select>
          </label>

          <label class="text-sm font-semibold">Pilih Analis
            <select name="analyst_id" class="mt-2 w-full h-10 rounded-xl border-slate-300" required>
              @foreach($analysts as $analyst)
                <option value="{{ $analyst->id }}">{{ $analyst->name }}</option>
              @endforeach
            </select>
          </label>

          <label class="text-sm font-semibold">Instruksi
            <input type="text" name="instruction" class="mt-2 w-full h-10 rounded-xl border-slate-300" placeholder="Catatan penugasan">
          </label>

          <div class="md:col-span-3">
            <x-ui.button>Simpan Penugasan</x-ui.button>
          </div>
        </form>
      </x-ui.section>
    @endif

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-2 text-sm text-slate-700">
          <span>Tampil</span>
          <span class="h-8 px-2 inline-flex items-center rounded-md border border-slate-300">{{ $assignments->perPage() }}</span>
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
              <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
              <th class="px-4 py-3 text-left">Perihal</th>
              <th class="px-4 py-3 text-left">Instansi Pengaju</th>
              <th class="px-4 py-3 text-left">Status Analisis</th>
              <th class="px-4 py-3 text-left">Diambil Oleh</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($assignments as $assignment)
              @php
                $rowNumber = ($assignments->firstItem() ?? 1) + $loop->index;
                $submission = $assignment->submission;

                $statusLabel = 'Belum Ditugaskan';
                $statusTone = 'permohonan-unassigned';

                if ($assignment->status->value === 'assigned') {
                    $statusLabel = 'Tersedia';
                    $statusTone = 'permohonan-available';
                } elseif ($assignment->status->value === 'in_progress') {
                    $statusLabel = 'Dalam Analisis';
                    $statusTone = 'permohonan-in-analysis';
                } elseif ($assignment->status->value === 'completed') {
                    $statusLabel = 'Selesai Analisis';
                    $statusTone = 'permohonan-done';
                }

                $isAnalystRole = auth()->user()->role->value === 'analis_hukum';
                $isDivisiRole = auth()->user()->role->value === 'operator_divisi_p3h';
                $isAnalystStyleRole = in_array(auth()->user()->role->value, ['analis_hukum', 'operator_divisi_p3h'], true);
                $isAnalystOwner = $isAnalystRole && $assignment->analyst_id === auth()->id();
                $isAvailableForAnalyst = $isAnalystRole && $assignment->status->value === 'assigned' && $assignment->analyst_id === null;
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $submission->nomor_surat }}</td>
                <td class="px-4 py-3">{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}</td>
                <td class="px-4 py-3">{{ $submission->perihal }}</td>
                <td class="px-4 py-3">{{ $submission->pemda_name }}</td>
                <td class="px-4 py-3"><x-ui.badge :tone="$statusTone">{{ $statusLabel }}</x-ui.badge></td>
                <td class="px-4 py-3">{{ $assignment->analyst?->name ?? 'Belum diambil' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1.5">
                    <a href="{{ route('submissions.show', $submission) }}" class="h-8 w-8 rounded-md bg-blue-600 text-white inline-flex items-center justify-center" title="Detail">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><circle cx="12" cy="12" r="3" /></svg>
                    </a>

                    @if($isAnalystStyleRole)
                      @if($isAvailableForAnalyst)
                        <form method="POST" action="{{ route('assignments.take', $assignment) }}" data-confirm-type="take-assignment" data-confirm-message="Apakah Anda yakin ingin mengambil penugasan ini?">
                          @csrf
                          <button type="submit" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center" style="background-color:#06B6D4" title="Ambil Penugasan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4m-2 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m10 0H7" /></svg>
                          </button>
                        </form>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Sudah Diambil">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4m-2 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m10 0H7" /></svg>
                        </button>
                      @endif

                      @if($isAnalystOwner && $assignment->status->value === 'in_progress')
                        <a href="{{ route('assignments.upload-hasil.form', $assignment) }}" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center" style="background-color:#FB7C5A" title="Upload Hasil Analisis">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </a>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Upload Dinonaktifkan">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </button>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-6 text-center text-slate-500">Belum ada penugasan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">
        <div>
          Menampilkan {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} dari {{ $assignments->total() }} data
        </div>
        <div>
          {{ $assignments->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
