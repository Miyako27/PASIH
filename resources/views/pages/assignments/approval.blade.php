@extends('layouts.app')
@section('title', 'ACC Penugasan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">ACC Penugasan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.index') }}" class="hover:text-slate-700 hover:underline">Penugasan</a>
        <span class="mx-1">/</span>
        <span>ACC</span>
      </p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <form method="POST" action="{{ route('assignments.approval.store', $assignment) }}" class="p-5 space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <label class="block text-sm font-medium text-slate-700">
            Nomor Surat
            <input type="text" disabled value="{{ $assignment->submission->nomor_surat }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>
          <label class="block text-sm font-medium text-slate-700">
            PIC
            <input type="text" disabled value="{{ $assignment->analyst?->name ?? '-' }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>
        </div>

        <label class="block text-sm font-medium text-slate-700">
          Keputusan
          <select name="decision" id="decision" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm focus:outline-none focus:ring-0 focus:border-[#B9B9B9]" required>
            <option value="">--Pilih Keputusan--</option>
            <option value="approve" @selected(old('decision') === 'approve')>ACC</option>
            <option value="revise" @selected(old('decision') === 'revise')>Tolak dan Minta Revisi</option>
          </select>
          @error('decision')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </label>

        <label class="block text-sm font-medium text-slate-700">
          Catatan Revisi
          <textarea name="revision_note" id="revision_note" rows="4" placeholder="Isi jika keputusan revisi" class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm">{{ old('revision_note') }}</textarea>
          @error('revision_note')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </label>

        <div class="pt-1">
          <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4" />
          </svg>
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      const decisionSelect = document.getElementById('decision');
      const revisionNote = document.getElementById('revision_note');

      if (!decisionSelect || !revisionNote) return;

      const syncRevisionRequirement = () => {
        revisionNote.required = decisionSelect.value === 'revise';
        revisionNote.setCustomValidity('');
      };

      syncRevisionRequirement();
      decisionSelect.addEventListener('change', syncRevisionRequirement);
      revisionNote.addEventListener('input', () => revisionNote.setCustomValidity(''));
      revisionNote.addEventListener('invalid', () => {
        if (decisionSelect.value === 'revise' && !revisionNote.value.trim()) {
          revisionNote.setCustomValidity('Catatan revisi wajib diisi saat memilih Tolak dan Minta Revisi.');
        } else {
          revisionNote.setCustomValidity('');
        }
      });
    })();
  </script>
@endsection

