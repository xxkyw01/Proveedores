<?php if(isset($tablaDisponibilidad)): ?>
    <div class="card ">
        <div class="card-header text-center fw-bold text-orange">
            <?php echo e($sucursal->nombre ?? ' '); ?> → <?php echo e(\Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY')); ?>

        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="encabezado-naranja">
    <tr>
        <th>Hora</th>
        <?php $__currentLoopData = array_keys((array)$tablaDisponibilidad[0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($columna !== 'hora'): ?>
                <th><?php echo e($columna); ?></th>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
</thead>

                <tbody>
                    <?php $__currentLoopData = $tablaDisponibilidad; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="bg-light fw-bold">
                            <?php echo e(\Carbon\Carbon::parse($fila->hora)->format('h:i A')); ?>

                            </td>
                            <?php $__currentLoopData = $fila; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $valor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($col !== 'hora'): ?>
                                    <td class="<?php echo e($valor !== 'Disponible' ? 'bg-danger text-white fw-semibold' : ''); ?>">
                                        <?php echo e($valor !== 'Disponible' ? \Illuminate\Support\Str::limit($valor,100, '...') : ''); ?>

                                    </td>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/compras/partials/tabla_disponibilidad.blade.php ENDPATH**/ ?>