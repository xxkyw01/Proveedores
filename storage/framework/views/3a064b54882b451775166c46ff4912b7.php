
<?php $__env->startSection('title', 'Calendario de Disponibilidad'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.flatpickr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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


    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/compras/Calendario_disponibilidad.css')); ?>">

    <div class="container-fluid mt-3 con-sidebar">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
            
            <div class="flex-shrink-0" style="width: 350px;">
                <div class="card p-3 pt-2 shadow-sm">
                    
                    <label for="sucursal_id" class="fw-bold text-orange mb-1">Sucursal</label>
                    <select name="sucursal_id" id="sucursal_id" class="form-select custom-select mb-3">
                        <option value="">-- Selecciona --</option>
                        <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    
                    <label for="calendarioFlatpickr" class="fw-bold text-orange mb-1">
                        <i class="fa fa-calendar text-orange"></i> Selecciona una Fecha
                    </label>
                    <div id="calendarioFlatpickr" class="mb-3"></div>
                </div>
            </div>

            
            <div class="flex-grow-1 w-100">
                <div id="contenedor-tabla-disponibilidad">
                    <div class="alert alert-info text-center">
                        Selecciona una fecha y una sucursal para ver la disponibilidad.
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        let fechaSeleccionada = null;

        function mostrarDisponibilidad() {
            const sucursalId = document.getElementById('sucursal_id').value;
            const fecha = fechaSeleccionada;
            if (!sucursalId || !fecha) return;

            fetch(`/compras/calendario/disponibilidad?fecha=${fecha}&sucursal_id=${sucursalId}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('contenedor-tabla-disponibilidad').innerHTML = html;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#calendarioFlatpickr", {
                inline: true,
                defaultDate: new Date(),
                dateFormat: "Y-m-d",
                locale: "es",
                onChange: function(selectedDates, dateStr) {
                    fechaSeleccionada = dateStr;
                    if (document.getElementById('sucursal_id').value) {
                        mostrarDisponibilidad();
                    }
                },
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: [ /*Do,*/ 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    longhand: [ /*Domingo,*/ 'Lunes', 'Martes', 'Miércoles', 'Jueves',
                        'Viernes', 'Sábado'
                    ],
                },
                months: {
                    shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep',
                        'Oct', 'Nov', 'Dic'
                    ],
                    longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
                        'Agosto',
                        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ],
                }
            });

            // Set today's date as default
            fechaSeleccionada = new Date().toISOString().slice(0, 10);

            document.getElementById('sucursal_id').addEventListener('change', function() {
                if (this.value && fechaSeleccionada) {
                    mostrarDisponibilidad();
                }
            });
        });
    </script>

    
    <?php if(request()->ajax() && isset($tablaDisponibilidad)): ?>
        <?php if(count($tablaDisponibilidad) > 0): ?>
            <div class="card shadow-sm">
                <div class="card-header text-center fw-bold text-orange">
                    <?php echo e($sucursal->nombre ?? ''); ?> —
                    <?php echo e(\Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY')); ?>

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="encabezado-naranja">
                            <tr>
                                <th>Hora</th>
                                <?php $__currentLoopData = array_keys((array) $tablaDisponibilidad[0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($columna !== 'hora'): ?>
                                        <th><?php echo e($columna); ?></th>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tablaDisponibilidad; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="bg-light fw-bold" data-label="Hora">
                                        <?php echo e(\Carbon\Carbon::parse($fila->hora)->format('h:i A')); ?>

                                    </td>
                                    <?php $__currentLoopData = $fila; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $valor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($col !== 'hora'): ?>
                                            <td data-label="<?php echo e($col); ?>"
                                                class="<?php echo e($valor !== 'Disponible' ? 'bg-danger text-white fw-semibold' : ''); ?>">
                                                <?php echo e($valor !== 'Disponible' ? \Illuminate\Support\Str::limit($valor, 100, '...') : ''); ?>

                                            </td>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center mt-3">
                No hay disponibilidad para la fecha seleccionada.
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/pages/compras/calendariosp.blade.php ENDPATH**/ ?>