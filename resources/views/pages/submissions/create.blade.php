@extends('layouts.app')
@section('title', 'Tambah Permohonan')


@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Permohonan</h1>
      <p class="mt-1 text-sm text-slate-500">
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
          <label class="block text-sm font-medium text-slate-700">Nomor Surat
            <input
              type="text"
              name="nomor_surat"
              value="{{ old('nomor_surat') }}"
              placeholder="Masukkan Nomor Surat"
              required
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
          </label>


          <label class="block text-sm font-medium text-slate-700">Perihal
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


        <label class="block text-sm font-medium text-slate-700">Deskripsi Permohonan
          <textarea
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
                Anda dapat menambahkan lebih dari satu dokumen. Total ukuran file maksimal 20 MB.
            </p>

            <div id="file-container" class="mt-3 space-y-3">
                <div class="flex gap-2 items-center">
                    <input
                        type="file"
                        name="surat_permohonan[]"
                        required
                        class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">
                </div>
            </div>

            <button
                type="button"
                onclick="addFileInput()"
                class="mt-3 px-4 py-2 text-sm bg-slate-100 hover:bg-slate-200 rounded-lg">
                + Tambah Dokumen
            </button>

            @error('surat_permohonan.*')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-1">
        <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-4-4m4 4l4-4" />
          </svg>
          Simpan
        </button>
      </div>
      </form>
      <script>
        function addFileInput() {

            const container = document.getElementById('file-container');
            const totalInputs = container.querySelectorAll('input[type="file"]').length;

            if (totalInputs >= 5) {
                alert('Maksimal 5 dokumen.');
                return;
            }

            const div = document.createElement('div');
            div.classList.add('flex', 'gap-2', 'items-center');

            div.innerHTML = `
                <input
                    type="file"
                    name="surat_permohonan[]"
                    class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">

                <button type="button" onclick="removeFileInput(this)"
                    class="text-red-500 text-sm">
                    Hapus
                </button>
            `;

            container.appendChild(div);
        }

        function removeFileInput(button) {
            button.parentElement.remove();
        }
    </script>
    </div>
  </div>
@endsection
