<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-100">
<head>
    <title>Intranet | <?php echo $__env->yieldContent('title'); ?></title>
    <?php echo $__env->make('includes.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-cream d-flex flex-column">
    <header>
        <?php echo $__env->make('includes.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </header>
    
    <div class="wrapper mt-5 mb-5">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/layouts/default.blade.php ENDPATH**/ ?>