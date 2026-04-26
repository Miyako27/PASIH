<?php
  $user = auth()->user();
  $role = $user?->role?->value;

  $items = [];
  $defaultIcons = [
    'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8M5 10v10h14V10" />',
    'accounts' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632z" />',
    'instansi' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M12 9h.01M15 9h.01M9 13h.01M12 13h.01M15 13h.01M9 17h.01M12 17h.01M15 17h.01" />',
    'guide' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13c-1.12-1.33-3.05-2-5.75-2A3.25 3.25 0 003 7.5v9.25A3.25 3.25 0 006.25 20c2.7 0 4.63.67 5.75 2m0-15.75c1.12-1.33 3.05-2 5.75-2A3.25 3.25 0 0121 7.5v9.25A3.25 3.25 0 0117.75 20c-2.7 0-4.63.67-5.75 2" />',
    'permohonan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l4 4v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2zm8 0v4h4M9 13h6M9 17h6M9 9h3" />',
    'penugasan' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0m-6 8l2 2 4-4" />',
    'hasil_analisis' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 15l3-3 3 2 4-5" />',
  ];

  if ($role === 'admin') {
      $items = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard'], 'icon_key' => 'dashboard'],
        ['label' => 'Manajemen Akun', 'href' => route('admin.accounts.index'), 'active' => ['admin.accounts.*'], 'icon_key' => 'accounts'],
        ['label' => 'Manajemen Instansi', 'href' => route('admin.instansi.index'), 'active' => ['admin.instansi.*'], 'icon_key' => 'instansi'],
        ['label' => 'Manajemen Buku Panduan', 'href' => route('admin.guides.index'), 'active' => ['admin.guides.*'], 'icon_key' => 'guide'],
      ];
  } else {
      $items[] = ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard'], 'icon_key' => 'dashboard'];

      if (! in_array($role, ['analis_hukum', 'ketua_tim_analisis'], true)) {
          $items[] = ['label' => 'Permohonan', 'href' => route('submissions.index'), 'active' => ['submissions.*'], 'icon_key' => 'permohonan'];
      }

      if (in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true)) {
          $items[] = ['label' => 'Penugasan', 'href' => route('assignments.index'), 'active' => ['assignments.index', 'assignments.show', 'assignments.upload-hasil.*', 'assignments.assign-pic.*', 'assignments.approval.*'], 'icon_key' => 'penugasan'];
      }

      if (in_array($role, ['analis_hukum', 'ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'operator_pemda'], true)) {
          $items[] = ['label' => 'Hasil Analisis', 'href' => route('assignments.analysis-results'), 'active' => ['assignments.analysis-results*'], 'icon_key' => 'hasil_analisis'];
      }

      $items[] = ['label' => 'Buku Panduan', 'href' => route('guides.index'), 'active' => ['guides.*'], 'icon_key' => 'guide'];
  }
?>

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
    <img src="<?php echo e(asset('images/LogoInstansi.png')); ?>"
         alt="Logo PASIH"
         class="w-20 h-20 object-contain mb-4">
    <div class="font-extrabold tracking-tight text-xl">
        PASIH
    </div>
    <div class="text-xs text-white/70 mt-1 leading-relaxed">
        Pendampingan Analisis &amp; Evaluasi Peraturan Daerah Kementerian Hukum Provinsi Riau
    </div>

</div>

  <nav class="px-4 py-5 space-y-2">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $active = collect($item['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern));
        $iconColor = $active ? '#161616' : '#B9B9B9';
        $iconMarkup = $item['icon_svg'] ?? null;
        $fallbackIcon = $defaultIcons[$item['icon_key'] ?? 'dashboard'] ?? $defaultIcons['dashboard'];
      ?>
      <a href="<?php echo e($item['href']); ?>"
         data-sidebar-link
         class="flex items-center gap-3 rounded-2xl px-4 py-3 font-semibold transition <?php echo e($active ? 'text-slate-900' : 'text-white/85 hover:bg-white/10 hover:text-white'); ?>"
         <?php if($active): ?> style="background: linear-gradient(90deg, #FFD82B 0%, #FFAB4A 100%);" <?php endif; ?>>
        <?php if($iconMarkup): ?>
          <span class="sidebar-icon inline-flex h-5 w-5 shrink-0 items-center justify-center" style="color: <?php echo e($iconColor); ?>;">
            <?php echo $iconMarkup; ?>

          </span>
        <?php else: ?>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
               class="h-5 w-5 shrink-0" fill="none" stroke="<?php echo e($iconColor); ?>" stroke-width="1.8">
            <?php echo $fallbackIcon; ?>

          </svg>
        <?php endif; ?>
        <span><?php echo e($item['label']); ?></span>
      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </nav>
</aside>

<div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-900/55 backdrop-blur-[1px] md:hidden"></div>

<aside
  data-sidebar-drawer
  class="fixed inset-y-0 left-0 z-40 flex w-[280px] -translate-x-full flex-col overflow-y-auto text-white shadow-2xl transition-transform duration-200 ease-out md:hidden"
  style="background: linear-gradient(180deg, #2B3056 0%, #3A4070 50%, #2B3056 100%);"
>
  <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
      <img src="<?php echo e(asset('images/LogoInstansi.png')); ?>" alt="Logo PASIH" class="w-11 h-11 object-contain">
      <div class="min-w-0">
        <div class="font-extrabold tracking-tight text-lg truncate">PASIH</div>
        <div class="text-[11px] text-white/70 leading-relaxed">Pendampingan Analisis &amp; Evaluasi Peraturan Daerah Kementerian Hukum Provinsi Riau</div>
      </div>
    </div>
    <button
      type="button"
      data-sidebar-close
      aria-label="Tutup menu"
      class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/25 bg-white/10 text-white"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  <nav class="px-4 py-5 space-y-2">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $active = collect($item['active'] ?? [])->contains(fn ($pattern) => request()->routeIs($pattern));
        $iconColor = $active ? '#161616' : '#B9B9B9';
        $iconMarkup = $item['icon_svg'] ?? null;
        $fallbackIcon = $defaultIcons[$item['icon_key'] ?? 'dashboard'] ?? $defaultIcons['dashboard'];
      ?>
      <a href="<?php echo e($item['href']); ?>"
         data-sidebar-link
         class="flex items-center gap-3 rounded-2xl px-4 py-3 font-semibold transition <?php echo e($active ? 'text-slate-900' : 'text-white/85 hover:bg-white/10 hover:text-white'); ?>"
         <?php if($active): ?> style="background: linear-gradient(90deg, #FFD82B 0%, #FFAB4A 100%);" <?php endif; ?>>
        <?php if($iconMarkup): ?>
          <span class="sidebar-icon inline-flex h-5 w-5 shrink-0 items-center justify-center" style="color: <?php echo e($iconColor); ?>;">
            <?php echo $iconMarkup; ?>

          </span>
        <?php else: ?>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
               class="h-5 w-5 shrink-0" fill="none" stroke="<?php echo e($iconColor); ?>" stroke-width="1.8">
            <?php echo $fallbackIcon; ?>

          </svg>
        <?php endif; ?>
        <span><?php echo e($item['label']); ?></span>
      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </nav>
</aside>
<?php /**PATH C:\laragon\www\PASIH\resources\views/components/admin/sidebar.blade.php ENDPATH**/ ?>