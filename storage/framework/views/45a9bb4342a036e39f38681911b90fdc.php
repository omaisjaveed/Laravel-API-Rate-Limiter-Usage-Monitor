<?php $__env->startSection('title', 'Edit API Client'); ?>
<?php $__env->startSection('heading', 'Edit API Client'); ?>
<?php $__env->startSection('subtitle', 'Update “<?php echo e($client->name); ?>” settings and rate limits'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="<?php echo e(route('dashboard.clients.update', $client)); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('dashboard.clients._form', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                    Save changes
                </button>
                <a href="<?php echo e(route('dashboard.clients.index')); ?>" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/clients/edit.blade.php ENDPATH**/ ?>