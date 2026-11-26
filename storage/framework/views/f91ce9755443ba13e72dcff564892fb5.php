<?php $__env->startSection('title', 'Admin Login'); ?>

<?php $__env->startSection('content'); ?>
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-8">
        <div class="mb-6">
            <div class="text-xs uppercase tracking-widest text-teal-700 font-semibold">Admin Access</div>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e(config('app.name')); ?></h1>
            <p class="mt-1 text-sm text-slate-500">Sign in to manage API clients, keys, and usage.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" type="password" name="password" required
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-600">
                Remember me
            </label>

            <button type="submit"
                    class="w-full rounded-md bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                Sign in
            </button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/auth/login.blade.php ENDPATH**/ ?>