@extends('layouts.app')
@section('title', 'Detail Akun')

@section('content')
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[42px] font-extrabold tracking-tight text-slate-800 leading-none">Manajemen Akun</h1>
        <p class="mt-2 text-sm text-slate-500">
          <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <a href="{{ route('admin.accounts.index') }}" class="hover:text-slate-700 hover:underline">Manajemen Akun</a>
          <span class="mx-1">/</span>
          <span>Detail Akun</span>
        </p>
      </div>

      <a href="{{ route('admin.accounts.index') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        Kembali
      </a>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800">Detail Akun</h2>
      </div>

      <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-700">
        <div>
          <div class="text-slate-500">Nama</div>
          <div class="mt-1 font-semibold">{{ $account->name }}</div>
        </div>
        <div>
          <div class="text-slate-500">Email</div>
          <div class="mt-1 font-semibold">{{ $account->email }}</div>
        </div>
        <div>
          <div class="text-slate-500">Role</div>
          <div class="mt-1 font-semibold">{{ $account->role?->label() ?? $account->role }}</div>
        </div>
        <div>
          <div class="text-slate-500">Instansi</div>
          <div class="mt-1 font-semibold">{{ $account->instansi?->nama_instansi ?? '-' }}</div>
        </div>
      </div>
    </div>
  </div>
@endsection
