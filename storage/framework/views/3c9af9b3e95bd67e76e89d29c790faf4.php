<?php $__env->startSection('title', 'Violations'); ?>
<?php $__env->startSection('heading', 'Rate Limit Violations'); ?>
<?php $__env->startSection('subtitle', 'Blocked requests (HTTP 429) and violation statistics'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total violations</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900"><?php echo e($totalViolations); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Today</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900"><?php echo e($violationsToday); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Clients affected</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900"><?php echo e($affectedClients); ?></div>
        </div>
    </div>

    <?php if($topOffenders->isNotEmpty()): ?>
        <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Top clients by violations</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <?php $__currentLoopData = $topOffenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center justify-between">
                        <span class="text-slate-700"><?php echo e($row->client?->name ?? 'Unknown'); ?></span>
                        <span class="font-medium text-red-700"><?php echo e($row->violations_count); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('dashboard.violations.index')); ?>" class="mb-4 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
        <select name="client" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All clients</option>
            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($client->id); ?>" <?php if((string) $clientId === (string) $client->id): echo 'selected'; endif; ?>><?php echo e($client->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="date" name="from" value="<?php echo e($from); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" value="<?php echo e($to); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
        <a href="<?php echo e(route('dashboard.violations.index')); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Request</th>
                        <th class="px-4 py-3">Rate limit</th>
                        <th class="px-4 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $violations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600"><?php echo e($log->requested_at?->format('Y-m-d H:i:s')); ?></td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900"><?php echo e($log->client?->name ?? '—'); ?></div>
                                <div class="font-mono text-xs text-slate-500"><?php echo e($log->apiKey?->key_prefix ? $log->apiKey->key_prefix.'…' : '—'); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold"><?php echo e($log->method); ?></span>
                                <span class="text-slate-600"><?php echo e($log->endpoint); ?></span>
                                <div class="text-xs text-red-600">HTTP <?php echo e($log->status_code); ?> blocked</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?php if($log->rate_limit_limit !== null): ?>
                                    <?php echo e($log->rate_limit_remaining ?? 0); ?> / <?php echo e($log->rate_limit_limit); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($log->ip_address ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No rate-limit violations recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($violations->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($violations->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/violations/index.blade.php ENDPATH**/ ?>