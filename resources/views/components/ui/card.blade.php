@props(['class' => ''])

<div {{ $attributes->merge([
  'class' => "rounded-3xl bg-white shadow-sm ring-1 ring-slate-200/70 {$class}"
]) }}>
  {{ $slot }}
</div>
