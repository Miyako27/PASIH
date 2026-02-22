@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Dashboard</h1>
      <div class="mt-2 h-1 w-20 rounded-full bg-amber-400"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #2B3056 0%, #3A4070 50%, #4A5080 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Total Akun</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 7a4 4 0 100-8 4 4 0 000 8zm11 14v-2a4 4 0 00-3-3.87M17 3.13a4 4 0 010 7.75" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none">{{ $stats['total_accounts'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #FFD82B 0%, #FFAB4A 50%, #FF9F2E 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Total Instansi</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 11h.01M12 11h.01M15 11h.01M9 15h.01M12 15h.01M15 15h.01" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none">{{ $stats['total_instansi'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #475569 0%, #64748B 50%, #94A3B8 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Total Permohonan</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l4 4v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2zm8 0v4h4M9 13h6M9 17h6M9 9h3" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none">{{ $stats['total_submissions'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #006A4E 0%, #1B7F5C 50%, #2A8F6A 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Total Penugasan</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0m-6 8l2 2 4-4" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none">{{ $stats['total_assignments'] }}</div>
      </div>
    </div>

    <div class="rounded-xl bg-white ring-1 ring-slate-200 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200">
        <h2 class="text-lg font-bold text-slate-800">Akun Terbaru</h2>
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

    <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Riwayat Aktivitas</h2>
          {{-- <p class="mt-1 text-[14px] text-slate-500">Jejak aksi akun Anda saat menggunakan sistem PASIH.</p> --}}
        </div>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
          {{ $recentActivities->count() }} aktivitas
        </span>
      </div>

      <div class="mt-5 max-h-[440px] overflow-y-auto pr-1 space-y-3">
        @forelse($recentActivities as $activity)
          @php
            $type = (string) ($activity['type'] ?? 'Aktivitas Sistem');
            [$dotColor, $chipClass] = match ($type) {
                'Autentikasi' => ['bg-emerald-500', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                'Permohonan' => ['bg-indigo-500', 'bg-indigo-50 text-indigo-700 ring-indigo-200'],
                'Penugasan' => ['bg-sky-500', 'bg-sky-50 text-sky-700 ring-sky-200'],
                'Hasil Analisis' => ['bg-amber-500', 'bg-amber-50 text-amber-700 ring-amber-200'],
                default => ['bg-slate-500', 'bg-slate-100 text-slate-700 ring-slate-200'],
            };
          @endphp
          <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
              <div class="flex gap-3 min-w-0 flex-1">
              <div class="pt-1">
                <span class="inline-block h-2.5 w-2.5 rounded-full {{ $dotColor }}"></span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold ring-1 {{ $chipClass }}">{{ $type }}</span>
                </div>
                <div class="mt-2 text-sm font-semibold text-slate-800">{{ $activity['title'] }}</div>
                <div class="mt-1 text-sm text-slate-600">{{ $activity['detail'] }}</div>
              </div>
              </div>
              <div class="shrink-0">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                  {{ optional($activity['time'])->format('d M Y H:i:s') ?: '-' }}
                </span>
              </div>
            </div>
          </div>
        @empty
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">
            Belum ada riwayat aktivitas.
          </div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
