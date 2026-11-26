<?php $__env->startSection('title', 'Generate API Key'); ?>
<?php $__env->startSection('heading', 'Generate API Key'); ?>
<?php $__env->startSection('subtitle', 'The raw key is shown only once after creation'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <?php if($clients->isEmpty()): ?>
            <p class="text-sm text-slate-600">
                No active API clients found.
                <a href="<?php echo e(route('dashboard.clients.create')); ?>" class="font-medium text-teal-700 hover:underline">Create a client</a> first.
            </p>
        <?php else: ?>
            <form method="POST" action="<?php echo e(route('dashboard.keys.store')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="api_client_id" class="block text-sm font-medium text-slate-700">API client</label>
                    <select id="api_client_id" name="api_client_id" required
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 <?php $__errorArgs = ['api_client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Select client</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php if((string) old('api_client_id', $selectedClientId) === (string) $client->id): echo 'selected'; endif; ?>>
                                <?php echo e($client->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['api_client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Key label (optional)</label>
                    <input id="name" name="name" type="text" maxlength="255" value="<?php echo e(old('name')); ?>"
                           placeholder="e.g. Production mobile app"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    Only a hashed key is stored. The full secret appears once after generate/regenerate.
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                        Generate key
                    </button>
                    <a href="<?php echo e(route('dashboard.keys.index')); ?>" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/keys/create.blade.php ENDPATH**/ ?>