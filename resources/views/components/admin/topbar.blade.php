@php
  $notificationCount = \App\Http\Controllers\NotificationController::buildNotifications(auth()->user(), 9)->count();
  $currentUser = auth()->user();
  $displayName = $currentUser?->name ?? 'Pengguna';
  $displayInstitution = $currentUser?->instansi?->nama_instansi ?? 'Kementerian Hukum Provinsi Riau';
@endphp

<header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <div class="min-w-0 flex items-center gap-2 sm:gap-3">
      <button
        type="button"
        data-sidebar-toggle
        aria-label="Buka menu"
        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 md:hidden"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <div class="min-w-0">
        <div class="truncate text-sm sm:text-base lg:text-lg font-bold tracking-tight">Dashboard {{ auth()->user()?->role?->label() }}</div>
        <div class="hidden sm:block text-xs text-slate-500 truncate">Kementerian Hukum Provinsi Riau</div>
      </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
      {{-- <div class="text-right">
        <div class="text-sm font-semibold">{{ auth()->user()?->name }}</div>
        <div class="text-xs text-slate-500">{{ auth()->user()?->role?->label() }}</div>
      </div> --}}
      <a
        href="{{ route('notifications.index') }}"
        title="Notifikasi aktivitas terbaru PASIH"
        class="relative inline-flex h-11 w-11 sm:h-10 sm:w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 18.75a2.25 2.25 0 01-4.5 0m8.25-2.25h-12c.88-.97 1.5-2.66 1.5-4.5V9a4.5 4.5 0 119 0v3c0 1.84.62 3.53 1.5 4.5z" />
        </svg>
        @if($notificationCount > 0)
          <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-[#C4132D] px-1 text-[10px] font-bold text-white">{{ $notificationCount }}</span>
        @endif
      </a>

      <div class="relative" data-profile-menu>
        <button
          type="button"
          title="Profil pengguna"
          aria-haspopup="true"
          aria-expanded="false"
          data-profile-toggle
          class="inline-flex h-11 w-11 sm:h-10 sm:w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632z" />
          </svg>
        </button>

        <div
          data-profile-panel
          class="hidden absolute right-0 top-12 w-[min(18rem,calc(100vw-1.5rem))] rounded-2xl border border-slate-200 bg-white p-3 shadow-xl"
        >
          <div class="rounded-xl bg-slate-50 px-4 py-3">
            <div class="truncate text-base font-bold text-slate-800">{{ $displayName }}</div>
            <div class="mt-0.5 truncate text-sm text-slate-500">{{ $displayInstitution }}</div>
          </div>

          <div class="my-3 mx-1 h-px bg-slate-300"></div>

          <div class="pt-1">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button
                type="submit"
                class="inline-flex w-full cursor-pointer items-center gap-2 rounded-xl border border-transparent bg-white px-3 py-2 text-sm font-semibold text-rose-600 transition duration-100 hover:bg-slate-100 active:bg-slate-200 active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3" />
                </svg>
                Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

@once
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const menu = document.querySelector('[data-profile-menu]');
        if (!menu) return;

        const toggle = menu.querySelector('[data-profile-toggle]');
        const panel = menu.querySelector('[data-profile-panel]');

        if (!toggle || !panel) return;

        const closePanel = () => {
          panel.classList.add('hidden');
          toggle.setAttribute('aria-expanded', 'false');
        };

        const openPanel = () => {
          panel.classList.remove('hidden');
          toggle.setAttribute('aria-expanded', 'true');
        };

        toggle.addEventListener('click', function () {
          const isOpen = !panel.classList.contains('hidden');
          if (isOpen) {
            closePanel();
            return;
          }
          openPanel();
        });

        document.addEventListener('click', function (event) {
          if (!menu.contains(event.target)) {
            closePanel();
          }
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            closePanel();
          }
        });
      });
    </script>
  @endpush
@endonce
