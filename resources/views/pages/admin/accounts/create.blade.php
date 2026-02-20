@extends('layouts.app')
@section('title', 'Tambah Akun')

@section('content')
  <div class="space-y-5">
    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[42px] font-extrabold tracking-tight text-slate-800 leading-none">Manajemen Akun</h1>
        <p class="mt-2 text-sm text-slate-500">Manajemen Akun / Tambah Akun</p>
      </div>

      <a href="{{ route('admin.accounts.index') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        Kembali
      </a>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-2xl font-bold text-slate-800">Tambah Akun</h2>
      </div>

      <form method="POST" action="{{ route('admin.accounts.store') }}" class="p-4 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-semibold text-slate-700">Nama</label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama" class="mt-2 w-full h-11 rounded-xl border-slate-300">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan Alamat Email" class="mt-2 w-full h-11 rounded-xl border-slate-300">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Password</label>
            <input type="password" name="password" placeholder="Masukkan Password" class="mt-2 w-full h-11 rounded-xl border-slate-300">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Role</label>
            <select name="role" class="mt-2 w-full h-11 rounded-xl border-slate-300">
              <option value="">Pilih Role</option>
              @foreach($roles as $role)
                <option value="{{ $role->nama_role }}" @selected(old('role') === $role->nama_role)>{{ $role->nama_role }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Instansi</label>
            <select name="id_instansi" class="mt-2 w-full h-11 rounded-xl border-slate-300">
              <option value="">Pilih Instansi</option>
              @foreach($institutions as $institution)
                <option value="{{ $institution->id_instansi }}" @selected((string) old('id_instansi') === (string) $institution->id_instansi)>{{ $institution->nama_instansi }}</option>
              @endforeach
            </select>
          </div>
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
