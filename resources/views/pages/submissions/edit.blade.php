@extends('layouts.app')
@section('title', 'Edit Permohonan')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-4xl font-extrabold tracking-tight text-slate-800">Permohonan</h1>
      <p class="mt-1 text-sm text-slate-500">Dashboard / Permohonan / Edit Data</p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-xl bg-white ring-1 ring-slate-200 p-5">
      <form method="POST" action="{{ route('submissions.update', $submission) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        <label class="block text-sm font-semibold text-slate-700">Nomor Surat
          <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $submission->nomor_surat) }}" required class="mt-2 w-full h-10 rounded-lg border-slate-300 text-sm">
        </label>

        <label class="block text-sm font-semibold text-slate-700">Tanggal Pengajuan
          <input type="text" value="{{ optional($submission->submitted_at)->format('d-m-Y') ?: '-' }}" disabled class="mt-2 w-full h-10 rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500">
        </label>

        <label class="block text-sm font-semibold text-slate-700">Perihal
          <input type="text" name="perihal" value="{{ old('perihal', $submission->perihal) }}" required class="mt-2 w-full h-10 rounded-lg border-slate-300 text-sm">
        </label>

        <label class="block text-sm font-semibold text-slate-700">Instansi Pengaju
          <input type="text" name="pemda_name" value="{{ old('pemda_name', $submission->pemda_name) }}" required class="mt-2 w-full h-10 rounded-lg border-slate-300 text-sm">
        </label>

        <label class="block text-sm font-semibold text-slate-700 md:col-span-2">Judul Perda
          <input type="text" name="perda_title" value="{{ old('perda_title', $submission->perda_title) }}" required class="mt-2 w-full h-10 rounded-lg border-slate-300 text-sm">
        </label>

        <label class="block text-sm font-semibold text-slate-700 md:col-span-2">Keterangan
          <textarea name="description" rows="4" class="mt-2 w-full rounded-lg border-slate-300 text-sm">{{ old('description', $submission->description) }}</textarea>
        </label>

        <label class="block text-sm font-semibold text-slate-700 md:col-span-2">Dokumen Pendukung Tambahan
          <input type="file" name="dokumen_pendukung" class="mt-2 w-full text-sm">
        </label>

        <div class="md:col-span-2 flex items-center gap-2 pt-2">
          <x-ui.button>Simpan Perubahan</x-ui.button>
          <a href="{{ route('submissions.index') }}" class="inline-flex items-center h-10 px-4 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Batal</a>
        </div>
      </form>
    </div>
  </div>
@endsection
