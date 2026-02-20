@extends('layouts.app')
@section('title', 'Detail Instansi')

@section('content')
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[42px] font-extrabold tracking-tight text-slate-800 leading-none">Manajemen Instansi</h1>
        <p class="mt-2 text-sm text-slate-500">Manajemen Instansi / Detail Instansi</p>
      </div>

      <a href="{{ route('admin.instansi.index') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        Kembali
      </a>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800">Detail Instansi</h2>
      </div>

      <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">
        <div>
          <div class="text-slate-500">Nama Instansi</div>
          <div class="mt-1 font-semibold">{{ $institution->nama_instansi }}</div>
        </div>
        <div>
          <div class="text-slate-500">Jenis</div>
          <div class="mt-1 font-semibold">{{ $institution->jenis_instansi }}</div>
        </div>
        <div class="md:col-span-2">
          <div class="text-slate-500">Alamat</div>
          <div class="mt-1 font-semibold">{{ $institution->alamat }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection
