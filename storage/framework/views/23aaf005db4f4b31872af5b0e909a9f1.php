<?php $__env->startSection('title', 'Request Logs'); ?>
<?php $__env->startSection('heading', 'API Request Logs'); ?>
<?php $__env->startSection('subtitle', 'Usage history without secrets — no raw keys or auth headers stored'); ?>

<?php $__env->startSection('content'); ?>
    <form method="GET" action="<?php echo e(route('dashboard.logs.index')); ?>" class="mb-4 grid gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <select name="client" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All clients</option>
            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($client->id); ?>" <?php if((string) $filters['client'] === (string) $client->id): echo 'selected'; endif; ?>><?php echo e($client->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <input type="text" name="endpoint" value="<?php echo e($filters['endpoint']); ?>" placeholder="Endpoint contains..."
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <select name="method" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All methods</option>
            <?php $__currentLoopData = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($method); ?>" <?php if($filters['method'] === $method): echo 'selected'; endif; ?>><?php echo e($method); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <input type="number" name="status" value="<?php echo e($filters['status']); ?>" placeholder="Status code"
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <select name="blocked" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">Blocked: any</option>
            <option value="1" <?php if($filters['blocked'] === '1'): echo 'selected'; endif; ?>>Blocked only</option>
            <option value="0" <?php if($filters['blocked'] === '0'): echo 'selected'; endif; ?>>Not blocked</option>
        </select>

        <input type="date" name="from" value="<?php echo e($filters['from']); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" value="<?php echo e($filters['to']); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <div class="flex gap-2">
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
            <a href="<?php echo e(route('dashboard.logs.index')); ?>" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Request</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Rate limit</th>
                        <th class="px-4 py-3">Time (ms)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                <?php echo e($log->requested_at?->format('Y-m-d H:i:s')); ?>

                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900"><?php echo e($log->client?->name ?? '—'); ?></div>
                                <div class="text-xs text-slate-500 font-mono"><?php echo e($log->apiKey?->key_prefix ? $log->apiKey->key_prefix.'…' : '—'); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800"><?php echo e($log->method); ?></span>
                                <span class="text-slate-600"><?php echo e($log->endpoint); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                    <?php echo e($log->was_blocked ? 'bg-red-50 text-red-700' : ($log->status_code >= 400 ? 'bg-amber-50 text-amber-700' : 'bg-teal-50 text-teal-700')); ?>">
                                    <?php echo e($log->status_code); ?><?php echo e($log->was_blocked ? ' blocked' : ''); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?php if($log->rate_limit_limit !== null): ?>
                                    <?php echo e($log->rate_limit_remaining ?? 0); ?> / <?php echo e($log->rate_limit_limit); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($log->response_time_ms); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                No request logs yet. Call an API endpoint with your key first.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/logs/index.blade.php ENDPATH**/ ?>