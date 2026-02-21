@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Dashboard</h1>
      <div class="mt-2 h-1 w-20 rounded-full bg-amber-400"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-indigo-800 to-indigo-700">
        <div class="text-sm font-semibold text-white/90">Total Akun</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['total_accounts'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-slate-600 to-slate-500">
        <div class="text-sm font-semibold text-white/90">Total Instansi</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['total_instansi'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-amber-400 to-orange-400">
        <div class="text-sm font-semibold text-white/90">Total Permohonan</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['total_submissions'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-emerald-700 to-emerald-600">
        <div class="text-sm font-semibold text-white/90">Total Penugasan</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['total_assignments'] }}</div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-xl font-extrabold text-slate-800">Akun Terbaru</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-4 py-3 text-left">Nama</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-left">Role</th>
              <th class="px-4 py-3 text-left">Instansi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentAccounts as $account)
              <tr class="border-t border-slate-100 text-slate-700">
                <td class="px-4 py-3">{{ $account->name }}</td>
                <td class="px-4 py-3">{{ $account->email }}</td>
                <td class="px-4 py-3">{{ $account->role?->label() ?? $account->role }}</td>
                <td class="px-4 py-3">{{ $account->instansi?->nama_instansi ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada data akun.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
