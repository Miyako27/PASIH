@php
  $notificationCount = \App\Http\Controllers\NotificationController::buildNotifications(auth()->user(), 9)->count();
@endphp

<header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <div>
      <div class="text-lg font-bold tracking-tight">Dashboard {{ auth()->user()?->role?->label() }}</div>

      <div class="text-xs text-slate-500">Kementerian Hukum Provinsi Riau</div>
    </div>

    <div class="flex items-center gap-4">
      {{-- <div class="text-right">
        <div class="text-sm font-semibold">{{ auth()->user()?->name }}</div>
        <div class="text-xs text-slate-500">{{ auth()->user()?->role?->label() }}</div>
      </div> --}}
      <a
        href="{{ route('notifications.index') }}"
        title="Notifikasi aktivitas terbaru PASIH"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-[#6D72A0] shadow-sm transition hover:bg-slate-50"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082A23.85 23.85 0 0112 17.25a23.847 23.847 0 01-2.857-.168m5.714 0a8.966 8.966 0 003.714-2.7 8.967 8.967 0 001.429-5.032V9a6 6 0 00-12 0v.35a8.967 8.967 0 001.429 5.032 8.966 8.966 0 003.714 2.7m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($notificationCount > 0)
          <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-[#C4132D] px-1 text-[10px] font-bold text-white">{{ $notificationCount }}</span>
        @endif
      </a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#DC2626] px-4 text-[14px] font-semibold text-white shadow-sm transition hover:bg-[#D8191D]">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3" />
          </svg>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </div>
</header>
