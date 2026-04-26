<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center sm:justify-end">
        <ul class="inline-flex items-center overflow-hidden rounded-lg border border-slate-300 bg-white text-sm">
            <?php if($paginator->onFirstPage()): ?>
                <li aria-disabled="true" aria-label="<?php echo app('translator')->get('pagination.previous'); ?>">
                    <span class="inline-flex h-10 w-10 items-center justify-center bg-slate-100 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.78 15.53a.75.75 0 0 1-1.06 0L7.22 11a1.5 1.5 0 0 1 0-2.12l4.5-4.5a.75.75 0 1 1 1.06 1.06L8.28 10l4.5 4.47a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            <?php else: ?>
                <li>
                    <a
                        href="<?php echo e($paginator->previousPageUrl()); ?>"
                        rel="prev"
                        aria-label="<?php echo app('translator')->get('pagination.previous'); ?>"
                        class="inline-flex h-10 w-10 items-center justify-center text-slate-600 hover:bg-slate-50"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.78 15.53a.75.75 0 0 1-1.06 0L7.22 11a1.5 1.5 0 0 1 0-2.12l4.5-4.5a.75.75 0 1 1 1.06 1.06L8.28 10l4.5 4.47a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            <?php endif; ?>

            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_string($element)): ?>
                    <li aria-disabled="true">
                        <span class="inline-flex h-10 min-w-10 items-center justify-center border-l border-slate-300 px-2 text-slate-400"><?php echo e($element); ?></span>
                    </li>
                <?php endif; ?>

                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li aria-current="page">
                                <span class="inline-flex h-10 min-w-10 items-center justify-center border-l border-slate-300 bg-slate-100 px-3 font-semibold text-slate-700"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li>
                                <a
                                    href="<?php echo e($url); ?>"
                                    aria-label="<?php echo app('translator')->get('Go to page :page', ['page' => $page]); ?>"
                                    class="inline-flex h-10 min-w-10 items-center justify-center border-l border-slate-300 px-3 text-slate-700 hover:bg-slate-50"
                                ><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($paginator->hasMorePages()): ?>
                <li>
                    <a
                        href="<?php echo e($paginator->nextPageUrl()); ?>"
                        rel="next"
                        aria-label="<?php echo app('translator')->get('pagination.next'); ?>"
                        class="inline-flex h-10 w-10 items-center justify-center border-l border-slate-300 text-slate-600 hover:bg-slate-50"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.22 4.47a.75.75 0 0 1 1.06 0l4.5 4.53a1.5 1.5 0 0 1 0 2.12l-4.5 4.5a.75.75 0 1 1-1.06-1.06L11.72 10l-4.5-4.47a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            <?php else: ?>
                <li aria-disabled="true" aria-label="<?php echo app('translator')->get('pagination.next'); ?>">
                    <span class="inline-flex h-10 w-10 items-center justify-center border-l border-slate-300 bg-slate-100 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.22 4.47a.75.75 0 0 1 1.06 0l4.5 4.53a1.5 1.5 0 0 1 0 2.12l-4.5 4.5a.75.75 0 1 1-1.06-1.06L11.72 10l-4.5-4.47a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH C:\laragon\www\PASIH\resources\views/vendor/pagination/pasih.blade.php ENDPATH**/ ?>