@extends('layouts.app')
@section('title', 'Tambah Permohonan')


@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Permohonan</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('submissions.index') }}" class="hover:text-slate-700 hover:underline">Permohonan</a>
        <span class="mx-1">/</span>
        <span>Tambah Permohonan</span>
      </p>
    </div>


    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif


    <div class="rounded-md bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Ajukan Surat Permohonan</h2>
      </div>


      <form method="POST" action="{{ route('submissions.store') }}" enctype="multipart/form-data" class="p-4 space-y-4">
        @csrf


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="block text-sm font-medium text-slate-700">Nomor Surat <span class="text-red-500">*</span>
            <input
              type="text"
              name="nomor_surat"
              value="{{ old('nomor_surat') }}"
              placeholder="Masukkan Nomor Surat"
              required
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>


          <label class="block text-sm font-medium text-slate-700">Perihal <span class="text-red-500">*</span>
            <input
              type="text"
              name="perihal"
              value="{{ old('perihal') }}"
              placeholder="Masukkan Perihal"
              required
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>
        </div>


        <label class="block text-sm font-medium text-slate-700">Deskripsi Permohonan <span class="text-red-500">*</span>
          <textarea
            required
            name="description"
            rows="4"
            placeholder="Masukkan Deskripsi Permohonan"
            class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
          >{{ old('description') }}</textarea>
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
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">
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
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">
            </label>
            @error('peraturan_daerah')
              <p class="text-red-500 text-sm -mt-2">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-medium text-slate-700">
              Peraturan Pelaksana Perda
              <input
                type="file"
                name="peraturan_pelaksana_perda"
                class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">
            </label>
            @error('peraturan_pelaksana_perda')
              <p class="text-red-500 text-sm -mt-2">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="pt-1">
        <button id="btn-submit" type="submit" disabled class="inline-flex items-center gap-2 h-10 px-4 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4" />
          </svg>
          Simpan
        </button>
      </div>
      </form>
      <script>
        const form = document.querySelector('form[action="{{ route('submissions.store') }}"]');
        const submitButton = document.getElementById('btn-submit');

        function toggleSubmitButton() {
          const requiredFields = form.querySelectorAll('[required]');
          let isValid = true;

          requiredFields.forEach((field) => {
            if (field.type === 'file') {
              if (!field.files || field.files.length === 0) {
                isValid = false;
              }
            } else if (!field.value || !field.value.trim()) {
              isValid = false;
            }
          });

          submitButton.disabled = !isValid;
        }

        form.addEventListener('input', toggleSubmitButton);
        form.addEventListener('change', toggleSubmitButton);
        toggleSubmitButton();
      </script>
    </div>
  </div>
@endsection
