@extends('layouts.app')
@section('title', 'Edit Hasil Analisis')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Hasil Analisis</h1>
      <p class="mt-1 pasih-page-breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.analysis-results') }}" class="hover:text-slate-700 hover:underline">Hasil Analisis</a>
        <span class="mx-1">/</span>
        <span>Edit Hasil Analisis</span>
      </p>
    </div>

    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="rounded-md bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Edit Hasil Analisis</h2>
      </div>

      <form method="POST" action="{{ route('assignments.upload-hasil.store', $assignment) }}" enctype="multipart/form-data" class="p-4 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="block text-sm font-medium text-slate-700">Nomor Surat
            <input type="text" disabled value="{{ $assignment->submission->nomor_surat }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>

          <label class="block text-sm font-medium text-slate-700">Perihal
            <input type="text" disabled value="{{ $assignment->submission->perihal }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>

          <label class="block text-sm font-medium text-slate-700">Ditugaskan Oleh
            <input type="text" disabled value="{{ $assignment->submission->divisionOperator?->name ?? 'P3H' }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>

          <label class="block text-sm font-medium text-slate-700">Tanggal Penugasan
            <input type="text" disabled value="{{ optional($assignment->assigned_at)->format('d - m - Y') ?: '-' }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <label class="block text-sm font-medium text-slate-700">Ringkasan Analisis
            <textarea name="ringkasan_analisis" rows="4" placeholder="Masukkan Ringkasan Analisis" class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">{{ old('ringkasan_analisis', $initialAnalysis['ringkasan_analisis'] ?? '') }}</textarea>
          </label>

          <label class="block text-sm font-medium text-slate-700">Hasil Evaluasi
            <textarea name="hasil_evaluasi" rows="4" placeholder="Masukkan Hasil Evaluasi" class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">{{ old('hasil_evaluasi', $initialAnalysis['hasil_evaluasi'] ?? '') }}</textarea>
          </label>
        </div>

        <label class="block text-sm font-medium text-slate-700">Rekomendasi
          <textarea name="rekomendasi" rows="4" placeholder="Masukkan Rekomendasi" class="mt-2 w-full px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">{{ old('rekomendasi', $initialAnalysis['rekomendasi'] ?? '') }}</textarea>
        </label>

        <label class="block text-sm font-medium text-slate-700">Upload Dokumen
          <input type="file" name="file" required class="mt-2 block w-full rounded-xl border border-[#B9B9B9] bg-white text-sm text-slate-700 file:mr-3 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-base file:text-slate-700">
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
@endsection

