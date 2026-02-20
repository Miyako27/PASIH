<header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <div>
      <div class="text-lg font-extrabold tracking-tight">Dashboard {{ auth()->user()?->role?->label() }}</div>

      <div class="text-xs text-slate-500">Kementerian Hukum dan HAM Wilayah Riau</div>
    </div>

    <div class="flex items-center gap-3">
      {{-- <div class="text-right">
        <div class="text-sm font-semibold">{{ auth()->user()?->name }}</div>
        <div class="text-xs text-slate-500">{{ auth()->user()?->role?->label() }}</div>
      </div> --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-ui.button variant="soft" size="sm">Logout</x-ui.button>
      </form>
    </div>
  </div>
</header>
