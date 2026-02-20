@php
  $user = auth()->user();
  $role = $user?->role?->value;

  $items = [];

  if ($role === 'admin') {
      $items = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard']],
        ['label' => 'Manajemen Akun', 'href' => route('admin.accounts.index'), 'active' => ['admin.accounts.*']],
        ['label' => 'Manajemen Instansi', 'href' => route('admin.instansi.index'), 'active' => ['admin.instansi.*']],
      ];
  } else {
      $items[] = ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard']];

      if ($role !== 'analis_hukum') {
          $items[] = ['label' => 'Pengajuan', 'href' => route('submissions.index'), 'active' => ['submissions.*']];
      }

      if (in_array($role, ['operator_divisi_p3h', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true)) {
          $items[] = ['label' => 'Penugasan', 'href' => route('assignments.index'), 'active' => ['assignments.index', 'assignments.upload-hasil.*']];
      }

      if (in_array($role, ['analis_hukum', 'operator_divisi_p3h', 'operator_pemda'], true)) {
          $items[] = ['label' => 'Hasil Analisis', 'href' => route('assignments.analysis-results'), 'active' => ['assignments.analysis-results']];
      }
  }
@endphp

<aside class="w-[280px] hidden md:flex flex-col bg-gradient-to-b from-slate-900 to-slate-800 text-white">
  <div class="px-6 py-6 border-b border-white/10
            flex flex-col items-center text-center">
    <img src="{{ asset('images/LogoInstansi.png') }}"
         alt="Logo PASIH"
         class="w-20 h-20 object-contain mb-4">
    <div class="font-extrabold tracking-tight text-xl">
        PASIH
    </div>
    <div class="text-xs text-white/70 mt-1 leading-relaxed">
        Analisis & Evaluasi Hukum Daerah
    </div>

</div>

  <nav class="px-4 py-5 space-y-2">
    @foreach($items as $item)
      @php
        $active = collect($item['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern));
      @endphp
      <a href="{{ $item['href'] }}"
         class="block rounded-2xl px-4 py-3 font-semibold transition {{ $active ? 'bg-amber-300 text-slate-900' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
         {{ $item['label'] }}
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
