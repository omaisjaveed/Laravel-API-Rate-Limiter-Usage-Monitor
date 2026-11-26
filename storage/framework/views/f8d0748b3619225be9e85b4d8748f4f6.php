<?php $__env->startSection('title', 'Overview'); ?>
<?php $__env->startSection('heading', 'Dashboard Overview'); ?>
<?php $__env->startSection('subtitle', 'High-level API usage and rate-limit health'); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total requests</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900"><?php echo e(number_format($total_requests)); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Successful</div>
            <div class="mt-1 text-2xl font-semibold text-teal-700"><?php echo e(number_format($successful_requests)); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Failed</div>
            <div class="mt-1 text-2xl font-semibold text-amber-700"><?php echo e(number_format($failed_requests)); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Violations</div>
            <div class="mt-1 text-2xl font-semibold text-red-700"><?php echo e(number_format($rate_limit_violations)); ?></div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Active clients</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900"><?php echo e(number_format($active_clients)); ?></div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Request activity (last 7 days)</h2>
                <a href="<?php echo e(route('dashboard.logs.index')); ?>" class="text-xs font-medium text-teal-700 hover:underline">View logs</a>
            </div>
            <div class="mt-4 h-64">
                <canvas id="activityChart" aria-label="Request activity chart"></canvas>
            </div>
            <?php if(array_sum($activity['values']) === 0): ?>
                <p class="mt-2 text-sm text-slate-500">No requests in the last 7 days yet.</p>
            <?php endif; ?>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Top API clients</h2>
                <a href="<?php echo e(route('dashboard.clients.index')); ?>" class="text-xs font-medium text-teal-700 hover:underline">Manage</a>
            </div>
            <ul class="mt-4 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $top_clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="flex items-center justify-between gap-3 text-sm">
                        <div>
                            <div class="font-medium text-slate-800"><?php echo e($row->client?->name ?? 'Unknown'); ?></div>
                            <div class="text-xs text-slate-500">
                                <?php echo e($row->client?->is_active ? 'Active' : 'Inactive'); ?>

                            </div>
                        </div>
                        <div class="font-semibold text-slate-900"><?php echo e(number_format($row->requests_count)); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-sm text-slate-500">No client traffic yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mt-4 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-900">Recent activity</h2>
            <a href="<?php echo e(route('dashboard.violations.index')); ?>" class="text-xs font-medium text-teal-700 hover:underline">View violations</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-5 py-3">Client</th>
                        <th class="px-5 py-3">Request</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">ms</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recent_activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600"><?php echo e($log->requested_at?->format('Y-m-d H:i:s')); ?></td>
                            <td class="px-5 py-3 text-slate-800"><?php echo e($log->client?->name ?? '—'); ?></td>
                            <td class="px-5 py-3">
                                <span class="font-semibold"><?php echo e($log->method); ?></span>
                                <span class="text-slate-600"><?php echo e($log->endpoint); ?></span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                    <?php echo e($log->was_blocked ? 'bg-red-50 text-red-700' : ($log->status_code >= 400 ? 'bg-amber-50 text-amber-700' : 'bg-teal-50 text-teal-700')); ?>">
                                    <?php echo e($log->status_code); ?><?php echo e($log->was_blocked ? ' blocked' : ''); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600"><?php echo e($log->response_time_ms); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                No recent API activity. Call <code class="text-xs">/api/v1/products</code> with your API key.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const ctx = document.getElementById('activityChart');
            if (!ctx || typeof Chart === 'undefined') return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($activity['labels'], 15, 512) ?>,
                    datasets: [{
                        label: 'Requests',
                        data: <?php echo json_encode($activity['values'], 15, 512) ?>,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(13, 148, 136, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#0f766e',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/overview.blade.php ENDPATH**/ ?>