<table class="table text-center table-bordered" style="border-radius: 10px; overflow: hidden;">
    <thead class="bg-dark text-white">
        <tr>
            <th style="width: 80px;">Hora</th>
            <?php $__currentLoopData = array_keys((array)$tablaDisponibilidad[0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($columna !== 'hora'): ?>
                    <th><?php echo e($columna); ?></th>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>

            <tbody>
    <?php
        $horaActual = \Carbon\Carbon::now()->format('H:i');
    ?>
    <?php $__currentLoopData = $tablaDisponibilidad; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $horaFila = \Carbon\Carbon::parse($fila->hora)->format('H:i');
        ?>
        <?php if($horaFila >= $horaActual): ?>
        <tr>
            
            <td style="background-color: #f1f3f5;" class="fw-bold">
                <?php echo e(\Carbon\Carbon::parse($fila->hora)->format('H:i')); ?>

            </td>
            
            
            <?php $__currentLoopData = $fila; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $valor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($col !== 'hora'): ?> 
                    
                    <td 
                        class="<?php echo e($valor === 'Disponible' ? 'bg-success' : 'bg-danger'); ?> text-white fw-semibold" 
                        style="font-size: 1.00rem;"
                        title="<?php echo e($valor); ?>"
                    >

                        
                        <?php echo e(\Illuminate\Support\Str::limit($valor, 100)); ?> 

                        
                    </td>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>

</table>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/almacen/partials/tablaDisponibilidad.blade.php ENDPATH**/ ?>