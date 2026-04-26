<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $maxBottleneck = max(max($bottleneck), 1);
    $onTime = $punctuality['on_time'];
    $late = $punctuality['late'];
    $total = max($punctuality['total'], 1);
    $onTimePercent = (int) round(($onTime / $total) * 100);
    $latePercent = 100 - $onTimePercent;

    $totalSubmissions = max((int) ($summaryStats['total_submissions'] ?? 0), 0);
    $completedSubmissions = max((int) ($summaryStats['completed_submissions'] ?? 0), 0);
    $summaryBase = max($totalSubmissions, 1);
    $completedSubmissionsPercent = (int) round(($completedSubmissions / $summaryBase) * 100);
  ?>

  <div class="space-y-5">
    <div>
      <h1 class="pasih-page-title">Dashboard</h1>
      <div class="mt-2 h-1 w-20 rounded-full bg-amber-400"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #2B3056 0%, #3A4070 50%, #4A5080 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Total Permohonan</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m-6-8h3m3-4l4 4m0 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2h10z" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none"><?php echo e($stats['total_submissions']); ?></div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #FFD82B 0%, #FFAB4A 50%, #FF9F2E 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Sedang Diproses</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none"><?php echo e($stats['in_progress']); ?></div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #475569 0%, #64748B 50%, #94A3B8 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Sedang Dianalisis</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none"><?php echo e($stats['in_analysis']); ?></div>
      </div>

      <div class="rounded-2xl p-5 text-white" style="background: linear-gradient(90deg, #006A4E 0%, #1B7F5C 50%, #2A8F6A 100%);">
        <div class="flex items-start justify-between gap-3">
          <div class="text-sm font-semibold text-white/90">Selesai</div>
          <div class="h-14 w-14 rounded-2xl border border-white/15 bg-white/10 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-2 text-5xl font-extrabold leading-none"><?php echo e($stats['completed']); ?></div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
        <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Grafik Bottleneck Proses</h2>
        <p class="mt-1 text-[14px] text-slate-500">Grafik ini menunjukkan distribusi permohonan untuk mendeteksi hambatan proses</p>

        <div class="mt-6 space-y-4">
          <?php $__currentLoopData = $bottleneck; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="font-medium text-[14px]"><?php echo e($label); ?></span>
                <span class="font-semibold text-slate-800"><?php echo e($value); ?></span>
              </div>
              <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-slate-800" style="width: <?php echo e(($value / $maxBottleneck) * 100); ?>%"></div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
        <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Notifikasi Tugas</h2>
        <p class="mt-1 text-[14px] text-slate-500">Berikut tugas yang menjadi tanggung jawab Anda</p>

        <div class="mt-6 space-y-3">
          <?php $__empty_1 = true; $__currentLoopData = $taskNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e($task['url']); ?>" class="block rounded-xl border border-slate-200 bg-white px-4 py-3 transition-colors hover:border-amber-300 hover:bg-amber-50/40">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <div class="text-sm font-semibold text-slate-800"><?php echo e($task['title']); ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?php echo e($task['description']); ?></div>
                </div>
                <span class="inline-flex min-w-[34px] justify-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">
                  <?php echo e($task['count']); ?>

                </span>
              </div>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">
              Tidak ada task khusus untuk role ini saat ini
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
      <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Ringkasan Permohonan</h2>
      <p class="mt-1 text-[14px] text-slate-500">Menampilkan total permohonan dan jumlah yang telah selesai</p>

      <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="grid grid-cols-1 md:grid-cols-[220px_1fr] items-center gap-5">
          <div class="flex justify-center">
            <div
              class="relative h-44 w-44 rounded-full ring-1 ring-slate-200"
              style="background: conic-gradient(#006A4E 0% <?php echo e($completedSubmissionsPercent); ?>%, #E2E8F0 <?php echo e($completedSubmissionsPercent); ?>% 100%);"
            >
              <div class="absolute inset-[18px] rounded-full bg-white flex flex-col items-center justify-center text-center px-2">
                <div class="text-[11px] font-medium tracking-wide text-slate-500">Selesai</div>
                <div class="mt-1 text-2xl font-extrabold leading-none text-slate-800"><?php echo e($completedSubmissionsPercent); ?>%</div>
              </div>
            </div>
          </div>

          <div class="space-y-3">
            <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
              <div class="text-xs font-semibold tracking-wide text-slate-500">Seluruh Permohonan</div>
              <div class="mt-1 text-3xl font-extrabold leading-none text-slate-900"><?php echo e($totalSubmissions); ?></div>
            </div>
            <div class="rounded-xl bg-white border border-slate-200 px-4 py-3">
              <div class="text-xs font-semibold tracking-wide text-slate-500">Permohonan Selesai</div>
              <div class="mt-1 text-3xl font-extrabold leading-none text-emerald-700"><?php echo e($completedSubmissions); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
      <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Jumlah Permohonan per Instansi</h2>
      <p class="mt-1 text-[14px] text-slate-500">Ringkasan jumlah permohonan dari setiap instansi yang terdaftar</p>

      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        <?php $__empty_1 = true; $__currentLoopData = $institutionSubmissionCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instansi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-colors hover:border-slate-300">
            <div class="mt-2 min-h-[44px] text-sm font-semibold leading-5 text-slate-800"><?php echo e($instansi->nama_instansi); ?></div>
            <div class="mt-4 flex items-end justify-between gap-3 border-t border-slate-100 pt-3">
              <div>
                <div class="text-[11px] font-medium text-slate-500">Total Permohonan</div>
                <div class="text-2xl font-extrabold leading-none text-slate-900"><?php echo e($instansi->total_permohonan); ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">
            Belum ada data instansi
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-5">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="text-[20px] font-bold tracking-tight text-slate-800">Riwayat Aktivitas</h2>
        </div>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
          <?php echo e($recentActivities->count()); ?> aktivitas
        </span>
      </div>

      <div class="mt-5 max-h-[440px] overflow-y-auto pr-1 space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $type = (string) ($activity['type'] ?? 'Aktivitas Sistem');
            [$dotColor, $chipClass] = match ($type) {
                'Autentikasi' => ['bg-emerald-500', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
                'Permohonan' => ['bg-indigo-500', 'bg-indigo-50 text-indigo-700 ring-indigo-200'],
                'Penugasan' => ['bg-sky-500', 'bg-sky-50 text-sky-700 ring-sky-200'],
                'Hasil Analisis' => ['bg-amber-500', 'bg-amber-50 text-amber-700 ring-amber-200'],
                default => ['bg-slate-500', 'bg-slate-100 text-slate-700 ring-slate-200'],
            };
          ?>
          <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
              <div class="flex gap-3 min-w-0 flex-1">
              <div class="pt-1">
                <span class="inline-block h-2.5 w-2.5 rounded-full <?php echo e($dotColor); ?>"></span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold ring-1 <?php echo e($chipClass); ?>"><?php echo e($type); ?></span>
                </div>
                <div class="mt-2 text-sm font-semibold text-slate-800"><?php echo e($activity['title']); ?></div>
                <div class="mt-1 text-sm text-slate-600"><?php echo e($activity['detail']); ?></div>
              </div>
              </div>
              <div class="shrink-0">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                  <?php echo e(optional($activity['time'])->format('d M Y H:i:s') ?: '-'); ?>

                </span>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="rounded-lg bg-slate-50 ring-1 ring-slate-200 px-4 py-3 text-sm text-slate-500">
            Belum ada riwayat aktivitas
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PASIH\resources\views/pages/dashboard.blade.php ENDPATH**/ ?>