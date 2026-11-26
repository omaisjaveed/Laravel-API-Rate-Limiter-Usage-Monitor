<?php $__env->startSection('title', 'API Keys'); ?>
<?php $__env->startSection('heading', 'API Key Management'); ?>
<?php $__env->startSection('subtitle', 'Generate, copy, revoke, and regenerate client API keys'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('plain_api_key')): ?>
        <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 p-4">
            <div class="text-sm font-semibold text-amber-900">Copy your API key now</div>
            <p class="mt-1 text-sm text-amber-800">This is the only time the full key is shown. Store it securely.</p>
            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                <code id="plain-api-key" class="block w-full overflow-x-auto rounded-md border border-amber-200 bg-white px-3 py-2 text-sm text-slate-800"><?php echo e(session('plain_api_key')); ?></code>
                <button type="button" id="copy-api-key"
                        class="rounded-md bg-amber-700 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-800">
                    Copy
                </button>
            </div>
            <p id="copy-feedback" class="mt-2 hidden text-xs font-medium text-teal-700">Copied to clipboard.</p>
        </div>
        <script>
            document.getElementById('copy-api-key')?.addEventListener('click', async () => {
                const value = document.getElementById('plain-api-key')?.textContent?.trim() || '';
                try {
                    await navigator.clipboard.writeText(value);
                    const feedback = document.getElementById('copy-feedback');
                    if (feedback) feedback.classList.remove('hidden');
                } catch (e) {
                    alert('Copy failed. Select the key and copy manually.');
                }
            });
        </script>
    <?php endif; ?>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="<?php echo e(route('dashboard.keys.index')); ?>" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <select name="client" class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
                <option value="">All clients</option>
                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($client->id); ?>" <?php if((string) $clientId === (string) $client->id): echo 'selected'; endif; ?>>
                        <?php echo e($client->name); ?><?php echo e($client->is_active ? '' : ' (inactive)'); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="status" class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
                <option value="">All statuses</option>
                <option value="active" <?php if($status === 'active'): echo 'selected'; endif; ?>>Active</option>
                <option value="revoked" <?php if($status === 'revoked'): echo 'selected'; endif; ?>>Revoked</option>
            </select>
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
        </form>

        <a href="<?php echo e(route('dashboard.keys.create', array_filter(['client' => $clientId]))); ?>"
           class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
            Generate key
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Key</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Last used</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $keys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900"><?php echo e($key->name ?: 'Untitled key'); ?></div>
                                <div class="mt-0.5 font-mono text-xs text-slate-500"><?php echo e($key->key_prefix); ?>…</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <?php echo e($key->client?->name ?? '—'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php if($key->isRevoked()): ?>
                                    <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Revoked</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?php echo e($key->last_used_at?->diffForHumans() ?? 'Never'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="<?php echo e(route('dashboard.keys.regenerate', $key)); ?>"
                                          onsubmit="return confirm('Regenerate this key? The old secret will stop working immediately.')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                            Regenerate
                                        </button>
                                    </form>

                                    <?php if (! ($key->isRevoked())): ?>
                                        <form method="POST" action="<?php echo e(route('dashboard.keys.revoke', $key)); ?>"
                                              onsubmit="return confirm('Revoke this API key?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="rounded-md border border-red-300 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                                                Revoke
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No API keys yet.
                                <a href="<?php echo e(route('dashboard.keys.create')); ?>" class="text-teal-700 hover:underline">Generate one</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($keys->hasPages()): ?>
            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($keys->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/dashboard/keys/index.blade.php ENDPATH**/ ?>