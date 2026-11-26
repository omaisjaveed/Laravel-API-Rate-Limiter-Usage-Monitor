<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf8',
                            100: '#d1faf0',
                            500: '#0d9488',
                            600: '#0f766e',
                            700: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="w-full lg:w-64 bg-slate-900 text-slate-100 lg:min-h-screen">
            <div class="px-5 py-5 border-b border-slate-700">
                <a href="<?php echo e(route('dashboard.overview')); ?>" class="block">
                    <div class="text-xs uppercase tracking-widest text-teal-300">API Monitor</div>
                    <div class="mt-1 text-lg font-semibold leading-tight"><?php echo e(config('app.name')); ?></div>
                </a>
            </div>

            <nav class="px-3 py-4 space-y-1 text-sm">
                <?php
                    $links = [
                        ['route' => 'dashboard.overview', 'label' => 'Overview'],
                        ['route' => 'dashboard.clients.index', 'label' => 'API Clients', 'active' => 'dashboard.clients.*'],
                        ['route' => 'dashboard.keys.index', 'label' => 'API Keys', 'active' => 'dashboard.keys.*'],
                        ['route' => 'dashboard.rate-limits.index', 'label' => 'Rate Limits', 'active' => 'dashboard.rate-limits.*'],
                        ['route' => 'dashboard.logs.index', 'label' => 'Request Logs', 'active' => 'dashboard.logs.*'],
                        ['route' => 'dashboard.violations.index', 'label' => 'Violations', 'active' => 'dashboard.violations.*'],
                    ];
                ?>

                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActive = request()->routeIs($link['active'] ?? $link['route']);
                    ?>
                    <a href="<?php echo e(route($link['route'])); ?>"
                       class="block rounded-md px-3 py-2 transition <?php echo e($isActive ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                        <?php echo e($link['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900"><?php echo $__env->yieldContent('heading', 'Dashboard'); ?></h1>
                    <?php if (! empty(trim($__env->yieldContent('subtitle')))): ?>
                        <p class="text-sm text-slate-500 mt-0.5"><?php echo $__env->yieldContent('subtitle'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-medium text-slate-800"><?php echo e(auth()->user()->name); ?></div>
                        <div class="text-xs text-slate-500"><?php echo e(auth()->user()->email); ?></div>
                    </div>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                <?php if(session('status')): ?>
                    <div class="mb-4 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Laravel API Rate Limiter & Usage Monitor project in my current local folder\resources\views/layouts/app.blade.php ENDPATH**/ ?>