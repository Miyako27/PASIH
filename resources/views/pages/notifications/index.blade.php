@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-bold tracking-tight text-slate-800">Notifikasi</h1>
      <p class="mt-1 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 hover:underline">Dashboard</a>
        <span class="mx-1">/</span>
        <span>Notifikasi</span>
      </p>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200">
      <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-bold text-slate-800">Notifikasi Terbaru PASIH</h2>
          <p class="text-xs text-slate-500">Penugasan baru, perubahan status permohonan, dan status analisis.</p>
        </div>
        <span class="inline-flex h-7 items-center rounded-full bg-blue-50 px-3 text-xs font-semibold text-blue-700">
          {{ $notifications->count() }} Notifikasi
        </span>
      </div>

      <div class="divide-y divide-slate-100">
        @forelse($notifications as $notification)
          @php
            $badgeClass = match($notification['type']) {
              'Penugasan' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
              'Status Permohonan' => 'bg-violet-50 text-violet-700 ring-violet-200',
              'Status Analisis' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
              default => 'bg-slate-100 text-slate-700 ring-slate-200',
            };
          @endphp
          <div class="px-5 py-4 hover:bg-slate-50/60 transition">
            <div class="flex items-start justify-between gap-3">
              <div class="flex min-w-0 items-start gap-3">
                <div class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                  @if($notification['type'] === 'Penugasan')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0m-6 8l2 2 4-4" />
                    </svg>
                  @elseif($notification['type'] === 'Status Analisis')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                    </svg>
                  @elseif($notification['type'] === 'Status Permohonan')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                    </svg>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 {{ $badgeClass }}">
                    {{ $notification['type'] }}
                  </div>
                  <p class="mt-1 text-sm font-semibold text-slate-800 break-words">{{ $notification['title'] }}</p>
                  <p class="mt-0.5 text-xs text-slate-600">{{ $notification['detail'] }}</p>
                  <p class="mt-1 text-xs text-slate-500">Oleh: <span class="font-semibold text-slate-700">{{ $notification['user'] }}</span></p>
                </div>
              </div>
              <div class="shrink-0 text-right">
                <div class="text-xs font-semibold text-slate-600">{{ optional($notification['time'])->diffForHumans() }}</div>
                <div class="mt-1 text-[11px] text-slate-500">{{ optional($notification['time'])->format('d M Y, H:i') }}</div>
              </div>
            </div>
          </div>
        @empty
          <div class="px-5 py-12 text-center">
            <div class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
              </svg>
            </div>
            <p class="mt-3 text-sm font-medium text-slate-700">Belum ada notifikasi terbaru</p>
            <p class="mt-1 text-xs text-slate-500">Notifikasi akan muncul otomatis saat ada pembaruan data PASIH.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
