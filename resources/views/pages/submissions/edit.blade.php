@extends('layouts.app')
@section('title', 'Edit Permohonan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Permohonan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('submissions.index') }}" class="hover:text-slate-700 hover:underline">Permohonan</a>
        <span class="mx-1">/</span>
        <span>Edit Data</span>
      </p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-md bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Ubah Surat Permohonan</h2>
      </div>

      <form id="edit-submission-form" method="POST" action="{{ route('submissions.update', $submission) }}" enctype="multipart/form-data" class="p-4 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block text-sm font-medium text-slate-700">Tanggal Pengajuan
            <input
              type="text"
              value="{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}"
              disabled
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500"
            >
          </label>


          <label class="block text-sm font-medium text-slate-700">Instansi Pengaju
            <input
              type="text"
              value="{{ $submission->submitter?->instansi?->nama_instansi ?? '-' }}"
              disabled
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500"
            >
          </label>
          <label class="block text-sm font-medium text-slate-700">Nama Perda <span class="text-red-500">*</span>
            <input
                type="text"
                name="perda_title"
                value="{{ old('perda_title', $submission->perda_title) }}"
                placeholder="Masukkan Nama Perda"
                required
                class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>
          <label class="block text-sm font-medium text-slate-700">Nomor Surat <span class="text-red-500">*</span>
            <input
              type="text"
              name="nomor_surat"
              value="{{ old('nomor_surat', $submission->nomor_surat) }}"
              placeholder="Masukkan Nomor Surat"
              required
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>
        </div>

        <label class="block text-sm font-medium text-slate-700">Perihal <span class="text-red-500">*</span>
            <input
              type="text"
              name="perihal"
              value="{{ old('perihal', $submission->perihal) }}"
              placeholder="Masukkan Perihal"
              required
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>


        <label class="block text-sm font-medium text-slate-700">Deskripsi Permohonan <span class="text-red-500">*</span>
          <textarea
            name="description"
            rows="4"
            required
            placeholder="Masukkan Deskripsi Permohonan"
            class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
          >{{ old('description', $submission->description) }}</textarea>
        </label>

        <div>
          <label class="block text-sm font-medium text-slate-700">
            Upload Dokumen
          </label>

          <p class="text-xs text-slate-500 mt-1">
            Maksimal ukuran tiap file 20 MB.
          </p>

          <div class="mt-3 space-y-3">
            <label class="block text-sm font-medium text-slate-700">
              Surat Permohonan <span class="text-red-500">*</span>
              <input
                type="file"
                name="surat_permohonan"
                required
                oninvalid="this.setCustomValidity('Silakan unggah Surat Permohonan terlebih dahulu.')"
                oninput="this.setCustomValidity('')"
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700"
              >
            </label>
            @error('surat_permohonan')
              <p class="text-red-500 text-sm -mt-2">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium text-slate-700">
              Peraturan Daerah <span class="text-red-500">*</span>
              <input
                type="file"
                name="peraturan_daerah"
                required
                oninvalid="this.setCustomValidity('Silakan unggah Peraturan Daerah terlebih dahulu.')"
                oninput="this.setCustomValidity('')"
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700"
              >
            </label>
            @error('peraturan_daerah')
              <p class="text-red-500 text-sm -mt-2">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium text-slate-700">
              Peraturan Pelaksana Perda
              <input
                type="file"
                name="peraturan_pelaksana_perda"
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700"
              >
            </label>
            @error('peraturan_pelaksana_perda')
              <p class="text-red-500 text-sm -mt-2">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="pt-1 flex items-center gap-2">
          <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4" />
            </svg>
            Simpan Perubahan
          </button>
          {{-- <a href="{{ route('submissions.index') }}" class="inline-flex items-center h-10 px-4 rounded-md bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</a> --}}
        </div>
      </form>
    </div>
  </div>
@endsection
