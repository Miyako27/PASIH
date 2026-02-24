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
        <h1 class="text-[32px] font-bold tracking-tight text-slate-800 leading-none">Manajemen Akun</h1>
        <p class="mt-2 text-sm text-slate-500">
          <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <a href="{{ route('admin.accounts.index') }}" class="hover:text-slate-700 hover:underline">Manajemen Akun</a>
          <span class="mx-1">/</span>
          <span>Tambah Akun</span>
        </p>
      </div>

      {{-- <a href="{{ route('admin.accounts.index') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        Kembali
      </a> --}}
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-[18px] font-bold text-slate-800">Tambah Akun</h2>
      </div>

      <form method="POST" action="{{ route('admin.accounts.store') }}" class="p-4 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-semibold text-slate-700">Nama</label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan Alamat Email" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Password</label>
            <input
              type="password"
              name="password"
              pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+"
              title="Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka."
              placeholder="Masukkan Password"
              class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]"
            >
            <span class="mt-1 block text-xs text-slate-500">
              Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">
                Role
            </label>
            <select name="role"
                class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-200">

                <option value="">Pilih Role</option>

                @foreach($roles as $role)
                    <option value="{{ $role->nama_role }}"
                        @selected(old('role') === $role->nama_role)>

                        {{ Str::title(str_replace('_', ' ', $role->nama_role)) }}

                    </option>
                @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Instansi</label>
            <select name="id_instansi" class="mt-2 w-full h-10 px-4 py-2 rounded-md border border-[#B9B9B9] text-sm placeholder:text-[14px]">
              <option value="">Pilih Instansi</option>
              @foreach($institutions as $institution)
                <option value="{{ $institution->id_instansi }}" @selected((string) old('id_instansi') === (string) $institution->id_instansi)>{{ $institution->nama_instansi }}</option>
              @endforeach
            </select>
          </div>
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
