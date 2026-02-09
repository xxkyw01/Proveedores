
<?php $__env->startSection('title', 'Dashboard KPIs'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.Datatables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="https://code.highcharts.com/highcharts.js"></script>

    
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

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/General/Dashboard/DashboardInterno.css')); ?>">
    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">
            
            <div class="container mb-3">
                <div class="card kpi-filters shadow-sm mx-auto">
                    <form class="row g-3 align-items-end justify-content-center text-center" method="get">
                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">Inicio</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" name="inicio" value="<?php echo e($fechaInicio); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">Fin</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                <input type="date" name="fin" value="<?php echo e($fechaFin); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">CDI</label>
                            <select name="sucursal" class="form-select form-select-sm">
                                <option value="">-- Todas --</option>
                                <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s->id); ?>" <?php echo e($sucursalId == $s->id ? 'selected' : ''); ?>>
                                        <?php echo e($s->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>


                        <div class="col-12 col-sm-auto">
                            <button class="btn btn-orange btn-sm w-100">
                                <i class="fas fa-filter me-1"></i> Aplicar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>


        
        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Agendadas</div>
                    <div class="fs-3"><?php echo e($totales['Agendadas'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Asistieron</div>
                    <div class="fs-3"><?php echo e($totales['Asistieron'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Canceladas</div>
                    <div class="fs-3"><?php echo e($totales['Canceladas'] ?? 0); ?></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">% Asistencia</div>
                    <div class="fs-3"><?php echo e($totales['PorcAsistencia'] ?? 0); ?>%</div>
                </div>
            </div>
        </div>

        
        <div class="card shadow-sm border-0 p-3 mb-3">
            <h6 class="mb-2">Eventos (participación %)</h6>
            <div id="chartEventos"></div>
        </div>

        
        <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
            <div class="col">
                <div class="card shadow-sm border-0 p-3">
                    <h6>Programada</h6>
                    <div id="panel-programada"></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3">
                    <h6>No Programada</h6>
                    <div id="panel-no-programada"></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3">
                    <h6>Apartado</h6>
                    <div id="panel-apartado"></div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3">
                    <h6>Paquetería Express</h6>
                    <div id="panel-express"></div>
                </div>
            </div>
        </div>

        
        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 p-3">
                    <div id="chartSemanas"></div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 p-3">
                    <div id="chartSucursales"></div>
                </div>
            </div>
        </div>

        
        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 p-3">
                    <h6>Top proveedores (Asistencias)</h6>
                    <div id="chartTopAsist"></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 p-3">
                    <h6>Top proveedores (Canceladas)</h6>
                    <div id="chartTopCancel"></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const donutEventos = <?php echo json_encode($donutEventos ?? [], 15, 512) ?>;
        const panels = <?php echo json_encode($panels ?? [], 15, 512) ?>;
        const tendencia = <?php echo json_encode($tendencia ?? [], 15, 512) ?>;
        const sucursalesStack = <?php echo json_encode($sucursalesStack ?? [], 15, 512) ?>;
        const topAsist = <?php echo json_encode($topAsistieron ?? [], 15, 512) ?>;
        const topCancel = <?php echo json_encode($topCancelados ?? [], 15, 512) ?>;

        Highcharts.chart('chartEventos', {
            chart: {
                type: 'pie'
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y})'
            },
            plotOptions: {
                pie: {
                    innerSize: '50%',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}<br>{point.percentage:.1f}%'
                    },
                    states: {
                        inactive: {
                            opacity: 1
                        }
                    }
                }
            },
            series: [{
                colorByPoint: true,
                data: (donutEventos || []).map(x => ({
                    name: x.evento,
                    y: Number(x.total) || 0
                }))
            }]
        });

        function dataPanel(nombre) {
            const p = (panels || []).find(x => (x.evento || '').toLowerCase() === nombre.toLowerCase());
            if (!p) return [];
            return (p.detalle || []).map(d => ({
                name: d.situacion,
                y: Number(d.total) || 0
            }));
        }

        function renderPanel(div, evento) {
            Highcharts.chart(div, {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: null
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}<br>{point.y}'
                        },
                        states: {
                            inactive: {
                                opacity: 1
                            }
                        }
                    }
                },
                series: [{
                    colorByPoint: true,
                    data: dataPanel(evento)
                }]
            });
        }
        renderPanel('panel-programada', 'Programada');
        renderPanel('panel-no-programada', 'No Programada');
        renderPanel('panel-apartado', 'Apartado');
        renderPanel('panel-express', 'Paquetería Express');

        Highcharts.chart('chartSemanas', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Tendencia semanal'
            },
            xAxis: {
                categories: (tendencia || []).map(r => r.semana)
            },
            yAxis: {
                title: {
                    text: 'Citas'
                }
            },
            tooltip: {
                shared: true
            },
            series: [{
                    name: 'Asistieron',
                    data: (tendencia || []).map(r => Number(r.asistieron) || 0)
                },
                {
                    name: 'Canceladas',
                    data: (tendencia || []).map(r => Number(r.canceladas) || 0)
                },
                {
                    name: 'No asistió',
                    data: (tendencia || []).map(r => Number(r.no_asistio) || 0)
                },
                {
                    name: 'Total',
                    type: 'spline',
                    dashStyle: 'ShortDash',
                    data: (tendencia || []).map(r => Number(r.total) || 0)
                }
            ]
        });

        Highcharts.chart('chartSucursales', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'CDI'
            },
            xAxis: {
                categories: (sucursalesStack || []).map(s => s.sucursal || 'N/D')
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Citas'
                },
                stackLabels: {
                    enabled: true
                }
            },
            tooltip: {
                pointFormatter: function() {
                    const fila = (sucursalesStack || [])[this.point.index];
                    const lbl = this.series.name;
                    const val = this.y;
                    let pct = 0;
                    if (lbl === 'Asistió') pct = fila.porc_asistio;
                    if (lbl === 'No asistió') pct = fila.porc_no_asistio;
                    if (lbl === 'Cancelada') pct = fila.porc_cancelada;
                    return `<span style="color:${this.color}">●</span> ${lbl}: <b>${val}</b> (${pct}%)<br/>`;
                },
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            series: [{
                    name: 'Asistió',
                    data: (sucursalesStack || []).map(s => Number(s.asistio) || 0)
                },
                {
                    name: 'No asistió',
                    data: (sucursalesStack || []).map(s => Number(s.no_asistio) || 0)
                },
                {
                    name: 'Cancelada',
                    data: (sucursalesStack || []).map(s => Number(s.cancelada) || 0)
                }
            ]
        });

        // ----------------- Top proveedores ----------------
        Highcharts.chart('chartTopAsist', {
            chart: {
                type: 'bar'
            },
            title: {
                text: null
            },
            xAxis: {
                categories: (topAsist || []).map(x => x.proveedor)
            },
            yAxis: {
                title: {
                    text: 'Asistencias'
                }
            },
            series: [{
                name: 'Asistencias',
                data: (topAsist || []).map(x => Number(x.asistio) || 0)
            }]
        });

        Highcharts.chart('chartTopCancel', {
            chart: {
                type: 'bar'
            },
            title: {
                text: null
            },
            xAxis: {
                categories: (topCancel || []).map(x => x.proveedor)
            },
            yAxis: {
                title: {
                    text: 'Canceladas'
                }
            },
            series: [{
                name: 'Canceladas',
                data: (topCancel || []).map(x => Number(x.cancelada) || 0)
            }]
        });

        // ----------------- Tabla semanal ------------------
        $(function() {
            $('#tablaSemanal').DataTable({
                paging: true,
                searching: false,
                info: false,
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/includes/Dashboard/Dashboardinterno.blade.php ENDPATH**/ ?>