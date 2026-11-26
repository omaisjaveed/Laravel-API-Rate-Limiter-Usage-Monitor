<?php $__env->startSection('title', 'API Clients'); ?>
<?php $__env->startSection('heading', 'API Clients'); ?>
<?php $__env->startSection('subtitle', 'Create, edit, activate, and deactivate API clients'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="<?php echo e(route('dashboard.clients.index')); ?>" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search clients..."
                   class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
            <select name="status" class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
                <option value="">All statuses</option>
                <option value="active" <?php if($status === 'active'): echo 'selected'; endif; ?>>Active</option>
                <option value="inactive" <?php if($status === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
            </select>
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
        </form>

        <a href="<?php echo e(route('dashboard.clients.create')); ?>"
           class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
            New client
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Limits (m / h / d)</th>
                        <th class="px-4 py-3">Keys</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900"><?php echo e($client->name); ?></div>
                                <?php if($client->description): ?>
                                    <div class="mt-0.5 text-xs text-slate-500 line-clamp-1"><?php echo e($client->description); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($client->is_active): ?>
                                    <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700">Active</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?php echo e($client->rate_limit_per_minute); ?> / <?php echo e($client->rate_limit_per_hour); ?> / <?php echo e($client->rate_limit_per_day); ?>

                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?php echo e($client->active_api_keys_count); ?> active
                                <span class="text-slate-400">(<?php echo e($client->api_keys_count); ?> total)</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('dashboard.clients.edit', $client)); ?>"
                                       class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('dashboard.clients.toggle', $client)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit"
                                                class="rounded-md border px-2.5 py-1.5 text-xs font-medium <?php echo e($client->is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-teal-300 text-teal-700 hover:bg-teal-50'); ?>">
                                            <?php echo e($client->is_active ? 'Deactivate' : 'Activate'); ?>

                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No API clients yet.
                                <a href="<?php echo e(route('dashboard.clients.create')); ?>" class="text-teal-700 hover:underline">Create the first one</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($clients->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($clients->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/clients/index.blade.php ENDPATH**/ ?>