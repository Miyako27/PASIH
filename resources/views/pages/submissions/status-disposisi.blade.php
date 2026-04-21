@extends('layouts.app')
@section('title', 'Ubah Status & Disposisi')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Permohonan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('submissions.index') }}" class="hover:text-slate-700 hover:underline">Permohonan</a>
        <span class="mx-1">/</span>
        <span>Ubah Status & Disposisi</span>
      </p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-lg bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Ubah Status & Tetapkan Disposisi</h2>
      </div>

      <form id="status-disposition-form" method="POST" action="{{ route('submissions.status-disposisi.save', $submission) }}" class="p-5 space-y-5" novalidate>
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <label class="block text-sm font-medium text-slate-700">
            Status <span class="text-red-500">*</span>
            <select id="status" name="status" required class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px] focus:outline-none focus:ring-0 focus:border-[#B9B9B9]">
              <option value="">Pilih Status</option>
              <option value="accepted" @selected(old('status', $submission->status->value) === 'accepted')>Diterima</option>
              <option value="revised" @selected(old('status', $submission->status->value) === 'revised')>Perlu Revisi</option>
              <option value="rejected" @selected(old('status', $submission->status->value) === 'rejected')>Ditolak</option>
            </select>
          </label>

          <label class="block text-sm font-medium text-slate-700">
            Disposisi <span class="text-red-500">*</span> <span class="text-xs font-normal text-slate-500">(wajib untuk status Diterima)</span>
            <select id="to_user_id" name="to_user_id" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px] focus:outline-none focus:ring-0 focus:border-[#B9B9B9]">
              <option value="">Pilih Disposisi</option>
              @if($kadivUser)
                <option value="{{ $kadivUser->id }}" @selected((int) old('to_user_id') === $kadivUser->id)>
                  Kepala Divisi P3H
                </option>
              @endif
            </select>
          </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <label class="block text-sm font-medium text-slate-700">
            Catatan Status <span class="text-red-500">*</span>
            <textarea
              id="status_note"
              required
              name="status_note"
              rows="4"
              placeholder="Masukkan Catatan Untuk Status"
              class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >{{ old('status_note') }}</textarea>
          </label>

          <label class="block text-sm font-medium text-slate-700">
            Catatan Disposisi <span class="text-red-500">*</span> <span class="text-xs font-normal text-slate-500">(wajib untuk status Diterima)</span>
            <textarea
              id="disposition_note"
              name="disposition_note"
              rows="4"
              placeholder="Masukkan Catatan Untuk Disposisi"
              class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >{{ old('disposition_note') }}</textarea>
          </label>

        </div>

        <div>
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
    const form = document.getElementById('status-disposition-form');
    const statusSelect = document.getElementById('status');
    const dispositionSelect = document.getElementById('to_user_id');
    const statusNoteInput = document.getElementById('status_note');
    const dispositionNoteInput = document.getElementById('disposition_note');

    function applyStatusRules() {
      const status = statusSelect.value;
      const isRejected = status === 'rejected';
      const isRevised = status === 'revised';
      const isAccepted = status === 'accepted';
      const needsStatusNote = true;
      const disableDisposition = isRejected || isRevised;

      dispositionSelect.disabled = disableDisposition;
      dispositionNoteInput.disabled = disableDisposition;

      if (disableDisposition) {
        dispositionSelect.value = '';
        dispositionNoteInput.value = '';
      }

      statusNoteInput.required = needsStatusNote;
      dispositionSelect.required = isAccepted;
      dispositionNoteInput.required = isAccepted;
      statusNoteInput.setCustomValidity('');
      dispositionSelect.setCustomValidity('');
      dispositionNoteInput.setCustomValidity('');
    }

    statusSelect.addEventListener('change', function () {
      statusSelect.setCustomValidity('');
      applyStatusRules();
    });
    statusNoteInput.addEventListener('input', function () { statusNoteInput.setCustomValidity(''); });
    dispositionSelect.addEventListener('change', function () { dispositionSelect.setCustomValidity(''); });
    dispositionNoteInput.addEventListener('input', function () { dispositionNoteInput.setCustomValidity(''); });

    form.addEventListener('submit', function (event) {
      applyStatusRules();

      const status = statusSelect.value;
      const needsDisposition = status === 'accepted';
      const needsStatusNote = true;

      statusSelect.setCustomValidity('');
      statusNoteInput.setCustomValidity('');
      dispositionSelect.setCustomValidity('');
      dispositionNoteInput.setCustomValidity('');

      if (status === '') {
        event.preventDefault();
        statusSelect.setCustomValidity('Silakan pilih status terlebih dahulu.');
        statusSelect.reportValidity();
        statusSelect.focus();
        return;
      }

      if (needsStatusNote && statusNoteInput.value.trim() === '') {
        event.preventDefault();
        statusNoteInput.setCustomValidity('Silakan isi catatan status terlebih dahulu.');
        statusNoteInput.reportValidity();
        statusNoteInput.focus();
        return;
      }

      if (needsDisposition && dispositionSelect.value === '') {
        event.preventDefault();
        dispositionSelect.setCustomValidity('Silakan pilih disposisi terlebih dahulu.');
        dispositionSelect.reportValidity();
        dispositionSelect.focus();
        return;
      }

      if (needsDisposition && dispositionNoteInput.value.trim() === '') {
        event.preventDefault();
        dispositionNoteInput.setCustomValidity('Silakan isi catatan disposisi terlebih dahulu.');
        dispositionNoteInput.reportValidity();
        dispositionNoteInput.focus();
      }
    });

    applyStatusRules();
  </script>
@endsection
