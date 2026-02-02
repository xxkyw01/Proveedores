@if (isset($tablaDisponibilidad))
    <div class="card ">
        <div class="card-header text-center fw-bold text-orange">
            {{ $sucursal->nombre ?? ' ' }} → {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="encabezado-naranja">
    <tr>
        <th>Hora</th>
        @foreach (array_keys((array)$tablaDisponibilidad[0]) as $columna)
            @if ($columna !== 'hora')
                <th>{{ $columna }}</th>
            @endif
        @endforeach
    </tr>
</thead>

                <tbody>
                    @foreach ($tablaDisponibilidad as $fila)
                        <tr>
                            <td class="bg-light fw-bold">
                            {{ \Carbon\Carbon::parse($fila->hora)->format('h:i A') }}
                            </td>
                            @foreach ($fila as $col => $valor)
                                @if ($col !== 'hora')
                                    <td class="{{ $valor !== 'Disponible' ? 'bg-danger text-white fw-semibold' : '' }}">
                                        {{ $valor !== 'Disponible' ? \Illuminate\Support\Str::limit($valor,100, '...') : '' }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
