
<?php $__env->startSection('title', 'Crear Usuario Dev'); ?>
<?php $__env->startSection('content'); ?>

    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="container mt-5">
                <h3>Crear Usuario con Contraseña Segura</h3>
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('dev.crear')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label">Usuario:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña:</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol:</label>
                        <select name="rol" class="form-select" required>
                            <option value="proveedor">Proveedor</option>
                            <option value="admin">Administrador</option>
                            <option value="almacen">Almacén</option>
                            <option value="compras">Compras</option>
                            <option value="mejora">Mejora Continua</option>
                            <option value="dev">Desarrollador</option>
                        </select>
                    </div>

                    <div class="mb-3" id="campoCardCode" style="display: none;">
                        <label class="form-label">CardCode (para proveedor):</label>
                        <input type="text" name="cardcode" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Crear Usuario</button>
                </form>
            </div>

        </div> 
    </div> 


    <script>
        document.querySelector('select[name="rol"]').addEventListener('change', function() {
            document.getElementById('campoCardCode').style.display =
                this.value === 'proveedor' ? 'block' : 'none';
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/dev/crear_usuario.blade.php ENDPATH**/ ?>