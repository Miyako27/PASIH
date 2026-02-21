@extends('layouts.app')
@section('title', 'Permohonan')

@section('content')
  <div class="space-y-5">
    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    @if(session('success'))
      <div class="rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[32px] font-extrabold tracking-tight text-slate-800">Permohonan</h1>
        <p class="mt-1 text-sm text-slate-500">Dashboard / Permohonan</p>
      </div>

      @if($canCreate)
        <a href="{{ route('submissions.create') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
          <span class="text-base">+</span> Tambah Data
        </a>
      @endif
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('submissions.index') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <span>Tampil</span>
          <select name="per_page" class="h-8 rounded-md border-slate-300 text-sm" onchange="this.form.submit()">
            <option value="5" @selected($perPage === 5)>5</option>
            <option value="10" @selected($perPage === 10)>10</option>
            <option value="25" @selected($perPage === 25)>25</option>
          </select>
          <span>Data</span>
          <input type="hidden" name="q" value="{{ $search }}">
        </form>

        <form method="GET" action="{{ route('submissions.index') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <label for="q">Cari:</label>
          <input id="q" type="text" name="q" value="{{ $search }}" class="h-8 w-40 rounded-md border-slate-300 text-sm">
          <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
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
              <th class="px-4 py-3 text-left">Disposisi</th>
              <th class="px-4 py-3 text-left">Status Permohonan</th>
              <th class="px-4 py-3 text-left">Status Analisis</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($submissions as $submission)
              @php
                $rowNumber = ($submissions->firstItem() ?? 1) + $loop->index;
                $assignment = $submission->assignments->sortByDesc('id')->first();

                $statusTone = match($submission->status->value) {
                  'accepted' => 'analisis-accepted',
                  'rejected' => 'analisis-rejected',
                  'revised' => 'analisis-revised',
                  default => 'analisis-submitted',
                };

                $analysisText = 'Belum Ditugaskan';
                $analysisTone = 'permohonan-unassigned';

                if ($assignment) {
                    if ($assignment->status->value === 'completed') {
                        $analysisText = 'Selesai Analisis';
                        $analysisTone = 'permohonan-done';
                    } elseif ($assignment->status->value === 'in_progress') {
                        $analysisText = 'Dalam Analisis';
                        $analysisTone = 'permohonan-in-analysis';
                    } else {
                        $analysisText = 'Tersedia';
                        $analysisTone = 'permohonan-available';
                    }
                }
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $submission->nomor_surat }}</td>
                <td class="px-4 py-3">{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}</td>
                <td class="px-4 py-3">{{ $submission->perihal }}</td>
                <td class="px-4 py-3">{{ $submission->pemda_name }}</td>
                <td class="px-4 py-3">{{ $submission->divisionOperator?->name ?? $submission->latestDisposition?->toUser?->name ?? '-' }}</td>
                <td class="px-4 py-3"><x-ui.badge :tone="$statusTone">{{ $submission->status->label() }}</x-ui.badge></td>
                <td class="px-4 py-3"><x-ui.badge :tone="$analysisTone">{{ $analysisText }}</x-ui.badge></td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1.5">
                    <a href="{{ route('submissions.show', $submission) }}" class="h-8 w-8 rounded-md bg-blue-600 text-white inline-flex items-center justify-center" title="Detail">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><circle cx="12" cy="12" r="3" /></svg>
                    </a>

                    @if($canReview)
                      @php
                        $isReviewerRole = in_array(auth()->user()->role->value, ['operator_kanwil', 'operator_divisi_p3h'], true);
                        $isStatusDispositionDone = !is_null($submission->reviewed_at);
                      @endphp

                      @if($isReviewerRole && $isStatusDispositionDone)
                        <button
                          type="button"
                          class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed"
                          style="background-color:#B9B9B9"
                          title="Status-Disposisi sudah disimpan"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </button>
                      @else
                        <a
                          href="{{ route('submissions.status-disposisi.form', $submission) }}"
                          class="h-8 w-8 rounded-md bg-emerald-600 text-white inline-flex items-center justify-center"
                          title="Aksi Status & Disposisi"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </a>
                      @endif
                    @endif

                    @if(in_array(auth()->user()->role->value, ['operator_divisi_p3h', 'kakanwil', 'kepala_divisi_p3h'], true) && in_array($submission->status->value, ['accepted', 'disposed', 'assigned'], true))
                      @if($assignment)
                        <button
                          type="button"
                          class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed"
                          style="background-color:#B9B9B9"
                          title="Permohonan sudah ditugaskan"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                          </svg>
                        </button>
                      @else
                        <a
                          href="{{ route('submissions.penugasan.form', $submission) }}"
                          class="h-8 w-8 rounded-md bg-violet-600 text-white inline-flex items-center justify-center"
                          title="Penugasan"
                        >
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                          </svg>
                        </a>
                      @endif
                    @endif

                    @if($canCreate && $submission->submitter_id === auth()->id())
                      @php
                        $canModifyPemda = in_array($submission->status->value, ['submitted', 'revised'], true);
                      @endphp

                      @if($canModifyPemda)
                        <a href="{{ route('submissions.edit', $submission) }}" class="h-8 w-8 rounded-md bg-amber-400 text-white inline-flex items-center justify-center" title="Edit">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L12 14l-4 1 1-4 7.5-7.5z" /></svg>
                        </a>

                        <form method="POST" action="{{ route('submissions.destroy', $submission) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="h-8 w-8 rounded-md bg-rose-600 text-white inline-flex items-center justify-center" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" /></svg>
                          </button>
                        </form>
                      @else
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Edit dinonaktifkan">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L12 14l-4 1 1-4 7.5-7.5z" /></svg>
                        </button>
                        <button type="button" class="h-8 w-8 rounded-md text-white inline-flex items-center justify-center cursor-not-allowed" style="background-color:#B9B9B9" title="Hapus dinonaktifkan">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" /></svg>
                        </button>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-6 text-center text-slate-500">Belum ada data permohonan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">
        <div>
          Menampilkan {{ $submissions->firstItem() ?? 0 }} - {{ $submissions->lastItem() ?? 0 }} dari {{ $submissions->total() }} data
        </div>
        <div>
          {{ $submissions->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
