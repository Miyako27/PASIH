@props([
  'label' => 'Item',
  'note' => null,
  'value' => 0,
  'tone' => 'green',
])

@php
  $bar = [
    'green'  => 'bg-emerald-700',
    'amber'  => 'bg-amber-400',
    'slate'  => 'bg-slate-700',
    'orange' => 'bg-orange-500',
    'rose'   => 'bg-rose-600',
  ][$tone] ?? 'bg-emerald-700';
@endphp

<div class="py-3">
  <div class="flex items-start justify-between gap-3">
    <div>
      <div class="font-bold text-slate-900">{{ $label }}</div>
      @if($note)<div class="text-sm text-slate-500">{{ $note }}</div>@endif
    </div>
    <div class="text-sm font-extrabold text-slate-700">{{ $value }}%</div>
  </div>

  <div class="mt-2 h-2.5 w-full rounded-full bg-slate-100 ring-1 ring-slate-200 overflow-hidden">
    <div class="h-full {{ $bar }}" style="width: {{ max(0,min(100,$value)) }}%"></div>
  </div>
</div>
