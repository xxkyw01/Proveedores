@extends('layouts.movil')
@section('title', 'Disponibilidad de Andenes')
@section('content')
 <x-sidebar/> 

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/tablero.css') }}">
    @php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.id_sucursal');
    @endphp

    <div class="container-fluid con-sidebar">

        <div class="row justify-content-center">

            @if (!in_array($rolId, [2]))
                <div class="card-cabecera mx-auto text-center mb-4">
                    <h4 class="text-orange fw-bold mb-1">{{ $sucursal->nombre ?? '' }}</h4>
                    <h3>
                        {{ \Carbon\Carbon::parse($fecha)->translatedFormat('l d \d\e F \d\e Y') }}
                    </h3>
                    @if (in_array($rolId, [1, 3, 4, 5, 6]))
                        <form method="GET" action="{{ route('almacen.tablero.mostrar') }}" class="mt-3">
                            <div class="selector-sucursal">
                                <select name="sucursal_id" id="sucursal_id" class="form-select custom-select"
                                    onchange="this.form.submit()">
                                    @foreach ($sucursales as $s)
                                        <option value="{{ $s->id }}"
                                            {{ request('sucursal_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    @endif
                </div>
        </div>
        @endif
    </div>

    
    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">
    <div id="contenedor-tabla" class="table-responsive" style="overflow-x: auto; min-width: 100%;">
        <table class="table text-center table-bordered" style="border-radius: 10px; overflow: hidden;">
            <thead class="bg-dark text-white">
                <tr>
                    <th style="width: 80px;">Hora</th>
                    @foreach (array_keys((array) $tablaDisponibilidad[0]) as $columna)
                        @if ($columna !== 'hora')
                            <th>{{ $columna }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @php
                    $horaActual = \Carbon\Carbon::now()->format('H:i');
                @endphp
                @foreach ($tablaDisponibilidad as $fila)
                    @php
                        $horaFila = \Carbon\Carbon::parse($fila->hora)->format('H:i');
                    @endphp
                    @if ($horaFila >= $horaActual)
                        <tr>
                            <td style="background-color: #f1f3f5;" class="fw-bold">
                                {{ \Carbon\Carbon::parse($fila->hora)->format('H:i') }}
                            </td>

                            @foreach ($fila as $col => $valor)
                                @if ($col !== 'hora')
                                    <td class="{{ $valor === 'Disponible' ? 'bg-success' : 'bg-danger' }} text-white fw-semibold"
                                        style="font-size: 1.00rem;" title="{{ $valor }}">
                                        {{ \Illuminate\Support\Str::limit($valor, 100) }}


                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>

        </table>

    </div>

        </div>
    </div> 

    <script>
        function MultiQuery() {
            const sucursal_id = document.getElementById('sucursal_id')?.value ?? '{{ request('sucursal_id') }}';
            fetch(`{{ route('almacen.tablero.parcial') }}?sucursal_id=${sucursal_id}`)
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

@endsection
