@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
  @php
    $maxBottleneck = max(max($bottleneck), 1);
    $onTime = $punctuality['on_time'];
    $late = $punctuality['late'];
    $total = max($punctuality['total'], 1);
    $onTimePercent = (int) round(($onTime / $total) * 100);
    $latePercent = 100 - $onTimePercent;
  @endphp

  <div class="space-y-5">
    <div>
      <h1 class="text-[32px] font-extrabold tracking-tight text-slate-800">Dashboard</h1>
      <div class="mt-2 h-1 w-20 rounded-full bg-amber-400"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-indigo-800 to-indigo-700">
        <div class="text-sm font-semibold text-white/90">Total Permohonan</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['total_submissions'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-amber-400 to-orange-400">
        <div class="text-sm font-semibold text-white/90">Sedang Diproses</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['in_progress'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-slate-600 to-slate-500">
        <div class="text-sm font-semibold text-white/90">Sedang Dianalisis</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['in_analysis'] }}</div>
      </div>

      <div class="rounded-2xl p-5 text-white bg-gradient-to-r from-emerald-700 to-emerald-600">
        <div class="text-sm font-semibold text-white/90">Selesai</div>
        <div class="mt-5 text-4xl font-extrabold">{{ $stats['completed'] }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
        <h2 class="text-[20px] font-extrabold tracking-tight text-slate-800">Grafik Bottleneck Proses</h2>
        <p class="mt-1 text-[14px] text-slate-500">Grafik ini menunjukkan distribusi permohonan untuk mendeteksi hambatan proses.</p>

        <div class="mt-6 space-y-4">
          @foreach($bottleneck as $label => $value)
            <div>
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="font-medium text-[14px]">{{ $label }}</span>
                <span class="font-semibold text-slate-800">{{ $value }}</span>
              </div>
              <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-slate-800" style="width: {{ ($value / $maxBottleneck) * 100 }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
        <h2 class="text-[20px] font-extrabold tracking-tight text-slate-800">Persentase Ketepatan Waktu Penugasan</h2>
        <p class="mt-1 text-[14px] text-slate-500">Selesai Tepat Waktu vs Terlambat</p>

        <div class="mt-6 flex flex-col md:flex-row md:items-center gap-6">
          <div class="relative h-44 w-44 rounded-full" style="background: conic-gradient(#1e3a8a 0 {{ $onTimePercent }}%, #dc2626 {{ $onTimePercent }}% 100%);">
            <div class="absolute inset-8 rounded-full bg-white"></div>
          </div>

          <div class="space-y-3 text-sm">
            <div class="flex items-center gap-2">
              <span class="inline-block h-3 w-3 rounded-full bg-blue-900"></span>
              <span class="text-slate-700">Selesai Tepat Waktu: <b>{{ $onTime }}</b></span>
            </div>
            <div class="flex items-center gap-2">
              <span class="inline-block h-3 w-3 rounded-full bg-red-600"></span>
              <span class="text-slate-700">Terlambat: <b>{{ $late }}</b></span>
            </div>
            <div class="pt-2 text-slate-500">Total selesai: {{ $punctuality['total'] }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
