<?php $__env->startSection('title', 'Compras Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('includes.sidebar.compras', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        <?php echo $__env->make('includes.menu.compras', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/index_compras.blade.php ENDPATH**/ ?>