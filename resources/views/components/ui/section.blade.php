@props(['title' => null, 'subtitle' => null, 'action' => null])

<x-ui.card class="p-5 sm:p-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      @if($title)<div class="text-lg font-extrabold tracking-tight">{{ $title }}</div>@endif
      @if($subtitle)<div class="text-sm text-slate-500 mt-1">{{ $subtitle }}</div>@endif
    </div>
    @if($action)
      <div class="shrink-0">{!! $action !!}</div>
    @endif
  </div>

  <div class="mt-5">
    {{ $slot }}
  </div>
</x-ui.card>
