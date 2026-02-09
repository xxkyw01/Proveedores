
<?php $__env->startSection('title', 'Reporte'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.Selectize', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.Cookies', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.Datatables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/compras/reporte_maniobras.css')); ?>">

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">
            <div class="container" id="container">
                
                <div class="d-flex justify-content-center">

                    <div class="bg-white rounded shadow p-4 mb-4 border-orange border-3"
                        style="width: 100%; border-color: #ee7826;">
                        <div class="row gx-3 gy-2 align-items-end justify-content-center">

                            
                            <form id="filtroForm" class="col-md-8 row gx-2 gy-2" method="GET"
                                action="<?php echo e(route('reporte.maniobras')); ?>">
                                <div class="col-sm-4">
                                    <label for="fechaInicio" class="form-label fw-bold text-secondary">Fecha inicio:</label>
                                    <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                        value="<?php echo e(request('fechaInicio')); ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="fechaFin" class="form-label fw-bold text-secondary">Fecha fin:</label>
                                    <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                        value="<?php echo e(request('fechaFin')); ?>">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-orange " data-bs-toggle="tooltip"
                                        title="Buscar registros por fecha">
                                        <i class="bi bi-search fs-5 me-1"></i> Buscar
                                    </button>
                                </div>
                            </form>

                            
                            <div class="col-md-2 d-flex align-items-end">
                                <form id="formExport" method="GET" action="<?php echo e(route('reporte.maniobras.export')); ?>"
                                    class="w-100">
                                    <input type="hidden" name="fechaInicio" id="exportFechaInicio"
                                        value="<?php echo e(request('fechaInicio')); ?>">
                                    <input type="hidden" name="fechaFin" id="exportFechaFin"
                                        value="<?php echo e(request('fechaFin')); ?>">
                                    <button type="submit" class="btn btn-success w-100" data-bs-toggle="tooltip"
                                        title="Descargar Excel con resultados">
                                        <i class="bi bi-file-earmark-excel-fill fs-5 me-1"></i> Excel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div> 
        </div> 



        
        <div class="table-responsive border-orange border-3 rounded shadow-sm">
            <table id="tablaReporte" class="table table-bordered table-striped align-middle mb-0">
                <thead class="text-center align-middle text-white" style="background-color: #ff9800;">
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Sucursal</th>
                        <th>Proveedor</th>
                        <th>Transporte</th>
                        <th>Andén</th>
                        <th>Monto Maniobra</th>
                        <th>Órdenes de Compra</th>
                    </tr>
                </thead>
                <tbody class="text-center align-middle">
                    <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($r->reservacion_id); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($r->Fecha_Recepcion)->format('d-m-Y')); ?></td>
                            <td><?php echo e($r->Sucursal); ?></td>
                            <td><?php echo e($r->Proveedor); ?></td>
                            <td><?php echo e($r->Tipo_Transporte); ?></td>
                            <td><?php echo e($r->Anden); ?></td>
                            <td>$<?php echo e(number_format($r->Monto_Maniobra, 2)); ?></td>
                            <td><?php echo e($r->Ordenes_Compra); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="fw-bold bg-light">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL MANIOBRA:</td>
                        <td colspan="2" class="text-start text-success">
                            $<?php echo e(number_format($datos->sum('Monto_Maniobra'), 2)); ?>

                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaReporte').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                pageLength: 10,
                lengthChange: false,
                info: false,
                ordering: true,
                responsive: true
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const formExport = document.getElementById('formExport');
            const inputInicio = document.getElementById('exportFechaInicio');
            const inputFin = document.getElementById('exportFechaFin');

            formExport.addEventListener('submit', function(e) {
                if (!inputInicio.value || !inputFin.value) {
                    e.preventDefault(); // Cancela la descarga
                    Swal.fire({
                        icon: 'info',
                        title: 'Fechas requeridas',
                        text: 'Debes seleccionar una fecha de inicio y una fecha fin antes de exportar.',
                        confirmButtonColor: '#ee7826'
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/pages/compras/reporte_maniobras.blade.php ENDPATH**/ ?>