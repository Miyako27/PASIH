@extends('layouts.app')
@section('title', 'Tentukan PIC')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Tentukan PIC</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('assignments.index') }}" class="hover:text-slate-700 hover:underline">Penugasan</a>
        <span class="mx-1">/</span>
        <span>Tentukan PIC</span>
      </p>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <form method="POST" action="{{ route('assignments.assign-pic.store', $assignment) }}" class="p-5 space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <label class="block text-sm font-medium text-slate-700">
            Nomor Surat
            <input type="text" disabled value="{{ $assignment->submission->nomor_surat }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>
          <label class="block text-sm font-medium text-slate-700">
            Perihal
            <input type="text" disabled value="{{ $assignment->submission->perihal }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] bg-slate-100 text-sm text-slate-500">
          </label>
        </div>

        <label class="block text-sm font-medium text-slate-700">
          Pilih PIC (Analis)
          <select name="analyst_id" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm" required>
            <option value="">-- Pilih Analis --</option>
            @foreach($analysts as $analyst)
              <option value="{{ $analyst->id }}" @selected((string) old('analyst_id') === (string) $analyst->id)>{{ $analyst->name }}</option>
            @endforeach
          </select>
        </label>

        <label class="block text-sm font-medium text-slate-700">
          Deadline
          <input type="date" name="deadline_at" value="{{ old('deadline_at', optional($assignment->deadline_at)->format('Y-m-d')) }}" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm">
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
