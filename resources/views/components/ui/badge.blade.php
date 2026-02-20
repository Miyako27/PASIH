@props(['tone' => 'slate']) {{-- slate|green|amber|rose --}}

@php
  $tones = [
    'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
    'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'rose'  => 'bg-rose-50 text-rose-700 ring-rose-200',
  ][$tone] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $tones }}">
  {{ $slot }}
</span>
