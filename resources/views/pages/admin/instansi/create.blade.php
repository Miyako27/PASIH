@extends('layouts.app')
@section('title', 'Tambah Instansi')

@section('content')
  <div class="space-y-5">
    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[42px] font-extrabold tracking-tight text-slate-800 leading-none">Manajemen Instansi</h1>
        <p class="mt-2 text-sm text-slate-500">Manajemen Instansi / Tambah Instansi</p>
      </div>

      <a href="{{ route('admin.instansi.index') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        Kembali
      </a>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800">Tambah Instansi</h2>
      </div>

      <form method="POST" action="{{ route('admin.instansi.store') }}" class="p-4 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Nama Instansi</label>
            <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}" placeholder="Masukkan Jenis Instansi" class="mt-2 w-full h-11 rounded-xl border-slate-300">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Jenis</label>
            <input type="text" name="jenis_instansi" value="{{ old('jenis_instansi') }}" placeholder="Masukkan Alamat Instansi" class="mt-2 w-full h-11 rounded-xl border-slate-300">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Alamat</label>
          <textarea name="alamat" rows="4" placeholder="Masukkan Alamat Instansi" class="mt-2 w-full rounded-xl border-slate-300">{{ old('alamat') }}</textarea>
        </div>

        <div class="pt-1">
          <button type="submit" class="h-10 px-5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
