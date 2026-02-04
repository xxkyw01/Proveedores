@extends('layouts.movil')
@section('title', 'Calendario de Disponibilidad')
@section('content')

    @include('includes.scripts.bootstrap')
    @include('includes.scripts.flatpickr')
    <x-sidebar />


    <link rel="stylesheet" href="{{ asset('assets/css/rol/compras/Calendario_disponibilidad.css') }}">

    <div class="container-fluid mt-3 con-sidebar">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
            {{--  Panel lateral --}}
            <div class="flex-shrink-0" style="width: 350px;">
                <div class="card p-3 pt-2 shadow-sm">
                    {{-- Sucursal --}}
                    <label for="sucursal_id" class="fw-bold text-orange mb-1">Sucursal</label>
                    <select name="sucursal_id" id="sucursal_id" class="form-select custom-select mb-3">
                        <option value="">-- Selecciona --</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>

                    {{-- Fecha --}}
                    <label for="calendarioFlatpickr" class="fw-bold text-orange mb-1">
                        <i class="fa fa-calendar text-orange"></i> Selecciona una Fecha
                    </label>
                    <div id="calendarioFlatpickr" class="mb-3"></div>
                </div>
            </div>

            {{-- Tabla de Calendario --}}
            <div class="flex-grow-1 w-100">
                <div id="contenedor-tabla-disponibilidad">
                    <div class="alert alert-info text-center">
                        Selecciona una fecha y una sucursal para ver la disponibilidad.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script --}}
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

    {{-- Tabla AJAX --}}
    @if (request()->ajax() && isset($tablaDisponibilidad))
        @if (count($tablaDisponibilidad) > 0)
            <div class="card shadow-sm">
                <div class="card-header text-center fw-bold text-orange">
                    {{ $sucursal->nombre ?? '' }} —
                    {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="encabezado-naranja">
                            <tr>
                                <th>Hora</th>
                                @foreach (array_keys((array) $tablaDisponibilidad[0]) as $columna)
                                    @if ($columna !== 'hora')
                                        <th>{{ $columna }}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tablaDisponibilidad as $fila)
                                <tr>
                                    <td class="bg-light fw-bold" data-label="Hora">
                                        {{ \Carbon\Carbon::parse($fila->hora)->format('h:i A') }}
                                    </td>
                                    @foreach ($fila as $col => $valor)
                                        @if ($col !== 'hora')
                                            <td data-label="{{ $col }}"
                                                class="{{ $valor !== 'Disponible' ? 'bg-danger text-white fw-semibold' : '' }}">
                                                {{ $valor !== 'Disponible' ? \Illuminate\Support\Str::limit($valor, 100, '...') : '' }}
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center mt-3">
                No hay disponibilidad para la fecha seleccionada.
            </div>
        @endif
    @endif

@endsection
