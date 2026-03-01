@extends('layouts.app')
@section('title', 'Penugasan')

@section('content')
  <div class="space-y-4 sm:space-y-5">
    <div>
      <h1 class="pasih-page-title">Permohonan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('submissions.index') }}" class="hover:text-slate-700 hover:underline">Permohonan</a>
        <span class="mx-1">/</span>
        <span>Penugasan</span>
      </p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-lg bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 sm:px-5 py-4 border-b border-slate-200">
        <h2 class="text-base sm:text-[18px] font-bold text-slate-800">Penugasan</h2>
      </div>

      <form method="POST" action="{{ route('submissions.penugasan.save', $submission) }}" class="p-4 sm:p-5 space-y-4 sm:space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
          <label class="block text-sm font-medium text-slate-700">
            Nomor Surat
            <input type="text" disabled value="{{ $submission->nomor_surat }}" class="mt-2 w-full h-11 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          </label>
          <label class="block text-sm font-medium text-slate-700">
            Perihal
            <input type="text" disabled value="{{ $submission->perihal }}" class="mt-2 w-full h-11 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          </label>
          <label class="block text-sm font-medium text-slate-700">
            Tanggal Pengajuan
            <input type="text" disabled value="{{ optional($submission->submitted_at)->format('d - m - Y') ?: '-' }}" class="mt-2 w-full h-11 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          </label>
          <label class="block text-sm font-medium text-slate-700">
            Instansi Pengaju
            <input type="text" disabled value="{{ $submission->submitter?->instansi?->nama_instansi ?? '-' }}" class="mt-2 w-full h-11 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          </label>
        </div>

        <label class="block text-sm font-medium text-slate-700">
          Catatan Penugasan
          <textarea name="instruction" rows="4" placeholder="Masukkan Catatan Untuk Penugasan" class="mt-2 w-full px-4 py-3 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">{{ old('instruction') }}</textarea>
        </label>

        <div>
          <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 h-11 px-5 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
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

