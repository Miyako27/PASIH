@extends('layouts.app')
@section('title', 'Tambah Buku Panduan')

@section('content')
  <div class="space-y-5">
    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="pasih-page-title">Manajemen Buku Panduan</h1>
        <p class="mt-2 pasih-page-breadcrumb">
          <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <a href="{{ route('admin.guides.index') }}" class="hover:text-slate-700 hover:underline">Manajemen Buku Panduan</a>
          <span class="mx-1">/</span>
          <span>Tambah Buku Panduan</span>
        </p>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Tambah Buku Panduan</h2>
      </div>

      <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data" class="p-4 space-y-4">
        @csrf

        <label class="block text-sm font-semibold text-slate-700">
          Judul Dokumen <span class="text-red-500">*</span>
          <input type="text" name="document_title" value="{{ old('document_title') }}" required placeholder="Masukkan Judul Dokumen" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          @error('document_title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </label>

        <div>
          <label class="block text-sm font-semibold text-slate-700">
            Upload Buku Panduan <span class="text-red-500">*</span>
          </label>
          <p class="mt-1 block text-xs text-slate-500">
            Format: PDF/DOC/DOCX, maksimal ukuran tiap file 5 MB.
          </p>
          <input
            type="file"
            name="file"
            required
            accept=".pdf,.doc,.docx"
            class="mt-2 block w-full rounded-md border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-md file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:text-slate-700">
          @error('file')
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
    </div>
  </div>
@endsection
