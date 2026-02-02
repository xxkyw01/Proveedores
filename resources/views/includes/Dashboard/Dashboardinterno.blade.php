@extends('layouts.movil')
@section('title', 'Dashboard KPIs')
@section('content')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.bootstrap')
    <script src="https://code.highcharts.com/highcharts.js"></script>

    {{-- Sidebar dinámico por rol/sesión --}}
    <x-sidebar/> 

    {{--  Enlace de Estilo --}}
    <link rel="stylesheet" href="{{ asset('assets/css/rol/General/Dashboard/DashboardInterno.css') }}">
    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">
            {{-- Filtros (centrados, fondo blanco) --}}
            <div class="container mb-3">
                <div class="card kpi-filters shadow-sm mx-auto">
                    <form class="row g-3 align-items-end justify-content-center text-center" method="get">
                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">Inicio</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" name="inicio" value="{{ $fechaInicio }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">Fin</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                <input type="date" name="fin" value="{{ $fechaFin }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-sm-auto">
                            <label class="form-label mb-1">CDI</label>
                            <select name="sucursal" class="form-select form-select-sm">
                                <option value="">-- Todas --</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}" {{ $sucursalId == $s->id ? 'selected' : '' }}>
                                        {{ $s->nombre }}
                                    </option>
                                @endforeach
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


        {{-- Tarjetas KPI --}}
        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Agendadas</div>
                    <div class="fs-3">{{ $totales['Agendadas'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Asistieron</div>
                    <div class="fs-3">{{ $totales['Asistieron'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">Canceladas</div>
                    <div class="fs-3">{{ $totales['Canceladas'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow-sm border-0 p-3 text-center">
                    <div class="fw-semibold text-muted">% Asistencia</div>
                    <div class="fs-3">{{ $totales['PorcAsistencia'] ?? 0 }}%</div>
                </div>
            </div>
        </div>

        {{-- Dona principal por evento --}}
        <div class="card shadow-sm border-0 p-3 mb-3">
            <h6 class="mb-2">Eventos (participación %)</h6>
            <div id="chartEventos"></div>
        </div>

        {{-- Cuadrantes por evento --}}
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

        {{-- Tendencia semanal + Sucursales apiladas --}}
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

        {{-- Top proveedores --}}
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

        {{-- Tabla semanal 
        <div class="card shadow-sm border-0 p-3">
            <table id="tablaSemanal" class="table table-sm table-striped mb-0 w-100">
                <thead>
                    <tr>
                        <th>Semana</th>
                        <th>Asistieron</th>
                        <th>Canceladas</th>
                        <th>No asistió</th>
                        <th>Otros</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tablaSemanal ?? [] as $r)
                        <tr>
                            <td>{{ $r['semana'] }}</td>
                            <td>{{ $r['asistieron'] }}</td>
                            <td>{{ $r['canceladas'] }}</td>
                            <td>{{ $r['no_asistio'] }}</td>
                            <td>{{ $r['otros'] }}</td>
                            <td>{{ $r['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        --}}

    </div>

    <script>
        // Datos desde el backend
        const donutEventos = @json($donutEventos ?? []);
        const panels = @json($panels ?? []);
        const tendencia = @json($tendencia ?? []);
        const sucursalesStack = @json($sucursalesStack ?? []);
        const topAsist = @json($topAsistieron ?? []);
        const topCancel = @json($topCancelados ?? []);

        // ---------------- Dona principal ----------------
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

        // ------------- Cuadrantes por evento -------------
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

        // ------------- Tendencia por semana --------------
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

        // --------- Apiladas por sucursal (agrupadas) -----
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
@endsection
