
<?php $__env->startSection('title', 'Usuarios del Sistema'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.Selectize', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.Datatables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


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

            <div class="container mt-4">
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($e); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <h4 class="section-title mt-4 mb-3 text-white bg-orange p-2 rounded">
                    <i class="fas fa-truck me-2"></i> Proveedores
                </h4>
                <table id="tablaProveedores" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>CardCode</th>
                            <th>Activo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($p->id); ?></td>
                                <td><?php echo e($p->username); ?></td>
                                <td><?php echo e($p->CardCode); ?></td>
                                <td><?php echo e($p->activo); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('dev.usuario.password')); ?>" class="form-password">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($p->id); ?>">
                                        <input type="hidden" name="rol" value="proveedor">
                                        <div class="input-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-sm password-input"
                                                placeholder="Nueva contraseña..." required minlength="8">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-confirmar-password">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <h4 class="section-title mt-5 mb-3 text-white bg-dark p-2 rounded">
                    <i class="fas fa-users-cog me-2"></i> Usuarios Internos
                </h4>
                <table id="tablaInternos" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Nombre</th>
                            <th>Activo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $internos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($u->IdUsuario); ?></td>
                                <td><?php echo e($u->Codigo); ?></td>
                                <td><?php echo e($u->IdRol); ?></td>
                                <td><?php echo e($u->Nombre); ?></td>
                                <td><?php echo e($u->Activo); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('dev.usuario.password')); ?>"
                                        class="form-password">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo e($u->IdUsuario); ?>">
                                        <input type="hidden" name="rol" value="interno">
                                        <div class="input-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-sm password-input"
                                                placeholder="Nueva contraseña..." required minlength="8">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-confirmar-password">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div> 
    </div> 



    <script>
        $(document).ready(function() {
            $('#tablaProveedores').DataTable({
                responsive: true,
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    emptyTable: "No hay datos disponibles"
                }
            });

            $('#tablaInternos').DataTable({
                responsive: true,
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    emptyTable: "No hay datos disponibles"
                }
            });

            const botones = document.querySelectorAll('.btn-confirmar-password');
            botones.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    const password = form.querySelector('.password-input').value;

                    if (password.length < 8) {
                        Swal.fire('Error', 'La contraseña debe tener al menos 8 caracteres.',
                            'error');
                        return;
                    }

                    Swal.fire({
                        title: '¿Estás segura?',
                        text: 'Se actualizará la contraseña de este usuario.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ee7826',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/dev/consultar_usuarios.blade.php ENDPATH**/ ?>