<?php $__env->startSection('title', 'Rate Limits'); ?>
<?php $__env->startSection('heading', 'Rate Limit Settings'); ?>
<?php $__env->startSection('subtitle', 'Per-client minute, hour, and day limits with live usage'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                /** @var \App\Models\ApiClient $client */
                $client = $row['model'];
                $usage = $row['usage'];
            ?>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900"><?php echo e($client->name); ?></h2>
                        <p class="text-sm text-slate-500">
                            <?php echo e($client->is_active ? 'Active client' : 'Inactive client'); ?>

                        </p>
                    </div>
                    <a href="<?php echo e(route('dashboard.clients.edit', $client)); ?>" class="text-sm font-medium text-teal-700 hover:underline">
                        Edit client
                    </a>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <?php $__currentLoopData = ['minute' => 'Per minute', 'hour' => 'Per hour', 'day' => 'Per day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $window = $usage[$key]; ?>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500"><?php echo e($label); ?></div>
                            <div class="mt-1 text-sm text-slate-800">
                                <span class="text-xl font-semibold"><?php echo e($window['used']); ?></span>
                                <span class="text-slate-500">/ <?php echo e($window['limit']); ?> used</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                Remaining: <?php echo e($window['remaining']); ?>

                                · resets <?php echo e(\Illuminate\Support\Carbon::createFromTimestamp($window['reset_at'])->diffForHumans()); ?>

                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                <?php
                                    $pct = $window['limit'] > 0 ? min(100, (int) round(($window['used'] / $window['limit']) * 100)) : 0;
                                ?>
                                <div class="h-full <?php echo e($pct >= 100 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-teal-600')); ?>" style="width: <?php echo e($pct); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <form method="POST" action="<?php echo e(route('dashboard.rate-limits.update', $client)); ?>" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-4 sm:items-end">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Minute limit</label>
                        <input type="number" min="1" name="rate_limit_per_minute" required
                               value="<?php echo e(old('rate_limit_per_minute', $client->rate_limit_per_minute)); ?>"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Hour limit</label>
                        <input type="number" min="1" name="rate_limit_per_hour" required
                               value="<?php echo e(old('rate_limit_per_hour', $client->rate_limit_per_hour)); ?>"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Day limit</label>
                        <input type="number" min="1" name="rate_limit_per_day" required
                               value="<?php echo e(old('rate_limit_per_day', $client->rate_limit_per_day)); ?>"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                        Save limits
                    </button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                No API clients yet.
                <a href="<?php echo e(route('dashboard.clients.create')); ?>" class="text-teal-700 hover:underline">Create one</a>
                to configure rate limits.
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/rate-limits/index.blade.php ENDPATH**/ ?>