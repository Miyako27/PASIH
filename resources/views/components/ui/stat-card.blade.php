@props([
  'title' => 'Total',
  'value' => '0',
  'delta' => '+0%',
  'deltaNote' => 'dari bulan lalu',
  'tone' => 'slate', // slate|amber|green|indigo
  'icon' => '📄',
])

@php
  $toneClass = [
    'slate'  => 'from-slate-700 to-slate-600',
    'amber'  => 'from-amber-400 to-orange-400',
    'green'  => 'from-emerald-700 to-emerald-600',
    'indigo' => 'from-indigo-700 to-indigo-600',
  ][$tone] ?? 'from-slate-700 to-slate-600';
@endphp

<div class="rounded-3xl bg-gradient-to-r {{ $toneClass }} text-white p-5 sm:p-6 shadow-sm">
  <div class="flex items-start justify-between gap-3">
    <div>
      <div class="text-sm font-semibold text-white/85">{{ $title }}</div>
      <div class="mt-2 text-3xl font-extrabold tracking-tight">{{ $value }}</div>
      <div class="mt-2 text-sm text-white/85">
        <span class="font-semibold">{{ $delta }}</span> {{ $deltaNote }}
      </div>
    </div>

    <div class="h-12 w-12 rounded-2xl bg-white/15 ring-1 ring-white/20 flex items-center justify-center text-xl">
      {{ $icon }}
    </div>
  </div>
</div>
