@extends('layouts.app')
@section('title', 'Ubah Status & Disposisi')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-4xl font-extrabold tracking-tight text-slate-800">Permohonan</h1>
      <p class="mt-1 text-sm text-slate-500">
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
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Ubah Status & Tetapkan Disposisi</h2>
      </div>

      <form method="POST" action="{{ route('submissions.status-disposisi.save', $submission) }}" class="p-5 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <label class="block text-lg font-medium text-slate-700">
            Status
            <select name="status" class="mt-2 w-full h-12 rounded-xl border-slate-300 text-base">
              <option value="">Pilih Status</option>
              <option value="accepted" @selected(old('status', $submission->status->value) === 'accepted')>Diterima</option>
              <option value="revised" @selected(old('status', $submission->status->value) === 'revised')>Perlu Revisi</option>
              <option value="rejected" @selected(old('status', $submission->status->value) === 'rejected')>Ditolak</option>
            </select>
          </label>

          <label class="block text-lg font-medium text-slate-700">
            Disposisi
            <select name="to_user_id" class="mt-2 w-full h-12 rounded-xl border-slate-300 text-base">
              <option value="">Pilih Disposisi</option>
              @foreach($divisionUsers as $divisionUser)
                <option value="{{ $divisionUser->id }}" @selected((int) old('to_user_id') === $divisionUser->id)>
                  {{ $divisionUser->name }}
                </option>
              @endforeach
            </select>
          </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <label class="block text-lg font-medium text-slate-700">
            Catatan Status
            <textarea
              name="status_note"
              rows="4"
              placeholder="Masukkan Catatan Untuk Status"
              class="mt-2 w-full rounded-xl border-slate-300 text-base placeholder:text-slate-400"
            >{{ old('status_note') }}</textarea>
          </label>

          <label class="block text-lg font-medium text-slate-700">
            Catatan Disposisi
            <textarea
              name="disposition_note"
              rows="4"
              placeholder="Masukkan Catatan Untuk Disposisi"
              class="mt-2 w-full rounded-xl border-slate-300 text-base placeholder:text-slate-400"
            >{{ old('disposition_note') }}</textarea>
          </label>
        </div>

        <div>
          <button type="submit" class="inline-flex items-center gap-2 h-12 px-6 rounded-lg bg-emerald-600 text-white text-lg font-semibold hover:bg-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4" />
            </svg>
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
