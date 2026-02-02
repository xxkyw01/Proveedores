@extends('layouts.movil')
@section('title', 'Reporte')
@section('content')
    @include('includes.scripts.Selectize')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.Cookies')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.bootstrap')

    <x-sidebar />


    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/rol/compras/reporte_maniobras.css') }}">

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">
            <div class="container" id="container">
                {{-- FORMULARIO PARA FILTRO DE FECHAS --}}
                <div class="d-flex justify-content-center">

                    <div class="bg-white rounded shadow p-4 mb-4 border-orange border-3"
                        style="width: 100%; border-color: #ee7826;">
                        <div class="row gx-3 gy-2 align-items-end justify-content-center">

                            {{-- Formulario de búsqueda --}}
                            <form id="filtroForm" class="col-md-8 row gx-2 gy-2" method="GET"
                                action="{{ route('reporte.maniobras') }}">
                                <div class="col-sm-4">
                                    <label for="fechaInicio" class="form-label fw-bold text-secondary">Fecha inicio:</label>
                                    <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                        value="{{ request('fechaInicio') }}">
                                </div>
                                <div class="col-sm-4">
                                    <label for="fechaFin" class="form-label fw-bold text-secondary">Fecha fin:</label>
                                    <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                        value="{{ request('fechaFin') }}">
                                </div>
                                <div class="col-sm-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-orange " data-bs-toggle="tooltip"
                                        title="Buscar registros por fecha">
                                        <i class="bi bi-search fs-5 me-1"></i> Buscar
                                    </button>
                                </div>
                            </form>

                            {{-- Formulario de exportación --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <form id="formExport" method="GET" action="{{ route('reporte.maniobras.export') }}"
                                    class="w-100">
                                    <input type="hidden" name="fechaInicio" id="exportFechaInicio"
                                        value="{{ request('fechaInicio') }}">
                                    <input type="hidden" name="fechaFin" id="exportFechaFin"
                                        value="{{ request('fechaFin') }}">
                                    <button type="submit" class="btn btn-success w-100" data-bs-toggle="tooltip"
                                        title="Descargar Excel con resultados">
                                        <i class="bi bi-file-earmark-excel-fill fs-5 me-1"></i> Excel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div> {{-- Cierra row --}}
        </div> {{-- Cierra container-fluid --}}



        {{-- TABLA DE RESULTADOS --}}
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
                    @foreach ($datos as $r)
                        <tr>
                            <td>{{ $r->reservacion_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->Fecha_Recepcion)->format('d-m-Y') }}</td>
                            <td>{{ $r->Sucursal }}</td>
                            <td>{{ $r->Proveedor }}</td>
                            <td>{{ $r->Tipo_Transporte }}</td>
                            <td>{{ $r->Anden }}</td>
                            <td>${{ number_format($r->Monto_Maniobra, 2) }}</td>
                            <td>{{ $r->Ordenes_Compra }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="fw-bold bg-light">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL MANIOBRA:</td>
                        <td colspan="2" class="text-start text-success">
                            ${{ number_format($datos->sum('Monto_Maniobra'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
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
@endpush
