@extends('layouts.app')
@section('title', 'Manajemen Instansi')

@section('content')
  <div class="space-y-5">
    @if($errors->any())
      <div class="rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-200 px-4 py-3 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-[32px] font-bold tracking-tight text-slate-800 leading-none">Manajemen Instansi</h1>
        <p class="mt-2 text-sm text-slate-500">
          <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <span>Manajemen Instansi</span>
        </p>
      </div>

      <a href="{{ route('admin.instansi.create') }}" class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-blue-950 text-white text-sm font-semibold hover:bg-blue-900">
        <span class="text-base">+</span> Tambah Data
      </a>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('admin.instansi.index') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <span>Tampil</span>
          <select name="per_page" class="h-8 rounded-md border-slate-300 text-sm focus:outline-none focus:ring-0 focus:border-slate-300" onchange="this.form.submit()">
            <option value="5" @selected($perPage === 5)>5</option>
            <option value="10" @selected($perPage === 10)>10</option>
            <option value="25" @selected($perPage === 25)>25</option>
          </select>
          <span>Data</span>
          <input type="hidden" name="q" value="{{ $search }}">
        </form>

        <form method="GET" action="{{ route('admin.instansi.index') }}" class="flex items-center gap-2 text-sm text-slate-700">
          <label for="q">Cari:</label>
          <input id="q" type="text" name="q" value="{{ $search }}" class="h-8 w-40 rounded-md border border-[#B9B9B9] text-sm">
          <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-4 py-3 text-left">No</th>
              <th class="px-4 py-3 text-left">Nama Instansi</th>
              <th class="px-4 py-3 text-left">Jenis</th>
              <th class="px-4 py-3 text-left">Alamat</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($institutions as $institution)
              @php
                $rowNumber = ($institutions->firstItem() ?? 1) + $loop->index;
              @endphp
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $rowNumber }}</td>
                <td class="px-4 py-3">{{ $institution->nama_instansi }}</td>
                <td class="px-4 py-3">{{ $institution->jenis_instansi }}</td>
                <td class="px-4 py-3">{{ $institution->alamat }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.instansi.show', $institution) }}" class="h-8 w-8 rounded-md bg-blue-600 text-white inline-flex items-center justify-center" title="Detail">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><circle cx="12" cy="12" r="3" /></svg>
                    </a>
                    <a href="{{ route('admin.instansi.edit', $institution) }}" class="h-8 w-8 rounded-md bg-amber-400 text-white inline-flex items-center justify-center" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L12 14l-4 1 1-4 7.5-7.5z" /></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.instansi.destroy', $institution) }}" data-confirm-type="delete" data-confirm-message="Apakah Anda yakin ingin menghapus data ini?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="h-8 w-8 rounded-md bg-rose-600 text-white inline-flex items-center justify-center" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" /></svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada data instansi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">
        <div>
          Menampilkan {{ $institutions->firstItem() ?? 0 }} - {{ $institutions->lastItem() ?? 0 }} dari {{ $institutions->total() }} data
        </div>
        <div>
          {{ $institutions->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
