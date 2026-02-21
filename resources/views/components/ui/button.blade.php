@props([
  'variant' => 'primary',
  'size' => 'md',
])

@php
  $base = "inline-flex items-center justify-center gap-2 font-semibold rounded-2xl transition focus:outline-none focus:ring-2 focus:ring-offset-2";
  $sizes = [
    'sm' => "h-9 px-3 text-sm",
    'md' => "h-10 px-4 text-sm",
  ][$size] ?? "h-10 px-4 text-sm";

  $variants = [
    'primary' => "bg-slate-900 text-white hover:bg-slate-800 focus:ring-slate-900",
    'soft'    => "bg-slate-100 text-slate-900 hover:bg-slate-200 focus:ring-slate-300",
    'ghost'   => "bg-transparent text-slate-700 hover:bg-slate-100 focus:ring-slate-300",
  ][$variant] ?? "bg-slate-900 text-white";
@endphp

<button {{ $attributes->merge(['class' => "$base $sizes $variants"]) }}>
  {{ $slot }}
</button>
