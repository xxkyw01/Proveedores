<!DOCTYPE html>
<html lang="es" class="h-100" lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <title>Intranet | <?php echo $__env->yieldContent('title'); ?></title>
    <?php echo $__env->make('includes.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body class="bg-cream">
    <div class="container-fluid">
        <?php echo $__env->make('includes.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="m-0 p-0 mt-5 mb-5">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</body>
<!---
<footer>
    <?php echo $__env->make('includes.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</footer>
---->

</html>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/layouts/movil.blade.php ENDPATH**/ ?>