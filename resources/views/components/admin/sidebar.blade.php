@php
  $user = auth()->user();
  $role = $user?->role?->value;

  $items = [];
  $defaultIcons = [
    'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8M5 10v10h14V10" />',
    'accounts' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8zm11 14v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />',
    'instansi' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M12 9h.01M15 9h.01M9 13h.01M12 13h.01M15 13h.01M9 17h.01M12 17h.01M15 17h.01" />',
    'pengajuan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l4 4v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2zm8 0v4h4M9 13h6M9 17h6M9 9h3" />',
    'penugasan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0m-6 8l2 2 4-4" />',
    'hasil_analisis' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l3-3 3 2 4-5" />',
  ];

  if ($role === 'admin') {
      $items = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard'], 'icon_key' => 'dashboard'],
        ['label' => 'Manajemen Akun', 'href' => route('admin.accounts.index'), 'active' => ['admin.accounts.*'], 'icon_key' => 'accounts'],
        ['label' => 'Manajemen Instansi', 'href' => route('admin.instansi.index'), 'active' => ['admin.instansi.*'], 'icon_key' => 'instansi'],
      ];
  } else {
      $items[] = ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard'], 'icon_key' => 'dashboard'];

      if (! in_array($role, ['analis_hukum', 'ketua_tim_analisis'], true)) {
          $items[] = ['label' => 'Pengajuan', 'href' => route('submissions.index'), 'active' => ['submissions.*'], 'icon_key' => 'pengajuan'];
      }

      if (in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true)) {
          $items[] = ['label' => 'Penugasan', 'href' => route('assignments.index'), 'active' => ['assignments.index', 'assignments.show', 'assignments.upload-hasil.*', 'assignments.assign-pic.*', 'assignments.approval.*'], 'icon_key' => 'penugasan'];
      }

      if (in_array($role, ['analis_hukum', 'ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'operator_pemda'], true)) {
          $items[] = ['label' => 'Hasil Analisis', 'href' => route('assignments.analysis-results'), 'active' => ['assignments.analysis-results*'], 'icon_key' => 'hasil_analisis'];
      }
  }
@endphp

<style>
  .sidebar-icon svg,
  .sidebar-icon svg * {
    width: 100%;
    height: 100%;
    stroke: currentColor !important;
    fill: currentColor !important;
  }
</style>

<aside class="fixed inset-y-0 left-0 z-30 w-[280px] hidden md:flex flex-col overflow-y-auto text-white" style="background: linear-gradient(180deg, #2B3056 0%, #3A4070 50%, #2B3056 100%);">
  <div class="px-6 py-6 border-b border-white/10
            flex flex-col items-center text-center">
    <img src="{{ asset('images/LogoInstansi.png') }}"
         alt="Logo PASIH"
         class="w-20 h-20 object-contain mb-4">
    <div class="font-extrabold tracking-tight text-xl">
        PASIH
    </div>
    <div class="text-xs text-white/70 mt-1 leading-relaxed">
        Pendampingan Analisa & Evaluasi Hukum Daerah
    </div>

</div>

  <nav class="px-4 py-5 space-y-2">
    @foreach($items as $item)
      @php
        $active = collect($item['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern));
        $iconColor = $active ? '#161616' : '#B9B9B9';
        $iconMarkup = $item['icon_svg'] ?? null;
        $fallbackIcon = $defaultIcons[$item['icon_key'] ?? 'dashboard'] ?? $defaultIcons['dashboard'];
      @endphp
      <a href="{{ $item['href'] }}"
         class="flex items-center gap-3 rounded-2xl px-4 py-3 font-semibold transition {{ $active ? 'text-slate-900' : 'text-white/85 hover:bg-white/10 hover:text-white' }}"
         @if($active) style="background: linear-gradient(90deg, #FFD82B 0%, #FFAB4A 100%);" @endif>
        @if($iconMarkup)
          <span class="sidebar-icon inline-flex h-5 w-5 shrink-0 items-center justify-center" style="color: {{ $iconColor }};">
            {!! $iconMarkup !!}
          </span>
        @else
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
               class="h-5 w-5 shrink-0" fill="none" stroke="{{ $iconColor }}" stroke-width="1.8">
            {!! $fallbackIcon !!}
          </svg>
        @endif
        <span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>

  {{-- <div class="mt-auto p-4 text-xs text-white/65">
    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
      <div class="font-semibold text-white">{{ $user?->name }}</div>
      <div class="mt-1">{{ $user?->role?->label() }}</div>
    </div>
  </div> --}}
</aside>
