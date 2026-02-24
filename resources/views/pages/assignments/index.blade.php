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
              <th class="px-4 py-3 text-left">PIC</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($assignments as $assignment)
              @php
                $rowNumber = ($assignments->firstItem() ?? 1) + $loop->index;
                $submission = $assignment->submission;
                $statusTone = match($assignment->status->value) {
                    'assigned' => 'permohonan-available',
                    'in_progress' => 'permohonan-in-analysis',
                    'pending_kadiv_approval' => 'permohonan-awaiting-kadiv',
                    'pending_kakanwil_approval' => 'permohonan-awaiting-kakanwil',
                    'revision_by_pic' => 'permohonan-revision',
                    'completed' => 'permohonan-done',
                    default => 'permohonan-unassigned',
                };

                $userRole = auth()->user()->role->value;
                $isKetuaTim = $userRole === 'ketua_tim_analisis';
                $isKadiv = $userRole === 'kepala_divisi_p3h';
                $isKakanwil = $userRole === 'kakanwil';
                $isAnalystOwner = $userRole === 'analis_hukum' && $assignment->analyst_id === auth()->id();
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $submission->nomor_surat }}</td>
                <td class="px-4 py-3">{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}</td>
                <td class="px-4 py-3">{{ $submission->perihal }}</td>
                <td class="px-4 py-3">{{ $submission->pemda_name }}</td>
                <td class="px-4 py-3"><x-ui.badge :tone="$statusTone">{{ $assignment->status->label() }}</x-ui.badge></td>
                <td class="px-4 py-3">{{ $assignment->analyst?->name ?? 'Belum ada PIC' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1.5">
                    <a href="{{ route('assignments.show', $assignment) }}" class="h-8 w-8 rounded-md bg-blue-600 text-white inline-flex items-center justify-center" title="Detail">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><circle cx="12" cy="12" r="3" /></svg>
                    </a>

                    @if($isKetuaTim)
                      @if($assignment->status->value === 'assigned')
                        <a href="{{ route('assignments.assign-pic.form', $assignment) }}" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center" style="background-color:#06B6D4" title="Tentukan PIC">
                          <img src="{{ asset('icon/IC_Hand.svg') }}" alt="Ikon tangan" class="h-4 w-4" />
                        </a>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="PIC sudah ditentukan">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                            <path d="M16.6666 13.3332V7.08317C16.6666 6.39317 16.1325 5.83317 15.4333 5.83317C15 5.83317 14.1666 6.08317 14.1666 7.08317V4.58317M14.1666 4.58317C14.1666 3.89317 13.6325 3.33317 12.9333 3.33317C12.5108 3.33317 11.6666 3.58317 11.6666 4.58317M14.1666 4.58317V9.1665M11.6666 4.58317V2.9165C11.6666 2.2265 11.1325 1.6665 10.4333 1.6665C9.73329 1.6665 9.16663 2.2265 9.16663 2.9165V4.58317M11.6666 4.58317V9.1665M9.16663 4.58317C9.16663 3.58317 8.33913 3.33317 7.91663 3.33317C7.21663 3.33317 6.66663 3.909 6.66663 4.59984V11.6665M9.16663 4.58317V9.1665" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.6667 13.333C16.6667 16.6664 14.055 18.333 10.8333 18.333C7.61166 18.333 6.50333 17.4997 4.00333 13.333L2.69416 11.1622C2.2475 10.4389 2.605 9.50053 3.42833 9.23386C3.69758 9.14628 3.98722 9.14342 4.25815 9.22569C4.52907 9.30795 4.76823 9.47136 4.94333 9.69386L6.66666 11.6939" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </button>
                      @endif
                    @endif

                    @if($isAnalystOwner)
                      @if(in_array($assignment->status->value, ['in_progress', 'revision_by_pic'], true))
                        <a href="{{ route('assignments.upload-hasil.form', $assignment) }}" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center" style="background-color:#FB7C5A" title="Upload Hasil Analisis">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </a>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Upload dinonaktifkan">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        </button>
                      @endif
                    @endif

                    @if($isKadiv || $isKakanwil)
                      @php
                        $canApprove = ($isKadiv && $assignment->status->value === 'pending_kadiv_approval')
                            || ($isKakanwil && $assignment->status->value === 'pending_kakanwil_approval');
                      @endphp
                      @if($canApprove)
                        <a href="{{ route('assignments.approval.form', $assignment) }}" class="h-8 w-8 rounded-md bg-emerald-600 text-white inline-flex items-center justify-center" title="ACC / Revisi">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </a>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Belum bisa ACC">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
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
