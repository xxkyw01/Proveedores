
<?php $__env->startSection('title', 'Disponibilidad de Andenes'); ?>
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

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/almacen/tablero.css')); ?>">
    <?php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.id_sucursal');
    ?>

    <div class="container-fluid con-sidebar">

        <div class="row justify-content-center">
            
            <?php if(!in_array($rolId, [2])): ?>
                
                <div class="card-cabecera mx-auto text-center mb-4">
                    <h4 class="text-orange fw-bold mb-1"><?php echo e($sucursal->nombre ?? ''); ?></h4>
                    <h3>
                        <?php echo e(\Carbon\Carbon::parse($fecha)->translatedFormat('l d \d\e F \d\e Y')); ?>

                    </h3>
                    <?php if(in_array($rolId, [1, 3, 4, 5, 6])): ?>
                        <form method="GET" action="<?php echo e(route('almacen.tablero.mostrar')); ?>" class="mt-3">
                            <div class="selector-sucursal">
                                <select name="sucursal_id" id="sucursal_id" class="form-select custom-select"
                                    onchange="this.form.submit()">
                                    <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>"
                                            <?php echo e(request('sucursal_id') == $s->id ? 'selected' : ''); ?>>
                                            <?php echo e($s->nombre); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
        </div>
        <?php endif; ?>
    </div> 


    
    
    <div class="container-fluid con-sidebar">

        <div class="row justify-content-center">

    <div id="contenedor-tabla" class="table-responsive" style="overflow-x: auto; min-width: 100%;">
        <table class="table text-center table-bordered" style="border-radius: 10px; overflow: hidden;">
            <thead class="bg-dark text-white">
                <tr>
                    <th style="width: 80px;">Hora</th>
                    <?php $__currentLoopData = array_keys((array) $tablaDisponibilidad[0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                    
                                    <td class="<?php echo e($valor === 'Disponible' ? 'bg-success' : 'bg-danger'); ?> text-white fw-semibold"
                                        style="font-size: 1.00rem;" title="<?php echo e($valor); ?>">

                                        
                                        <?php echo e(\Illuminate\Support\Str::limit($valor, 100)); ?>



                                    </td>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>

        </table>

    </div>

        </div>
    </div>  

    <script>
        function MultiQuery() {
            const sucursal_id = document.getElementById('sucursal_id')?.value ?? '<?php echo e(request('sucursal_id')); ?>';
            fetch(`<?php echo e(route('almacen.tablero.parcial')); ?>?sucursal_id=${sucursal_id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('contenedor-tabla').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error al actualizar tabla:', error);
                });
        }
        setInterval(MultiQuery, 10000);
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/almacen/tablero.blade.php ENDPATH**/ ?>