<table class="table text-center table-bordered" style="border-radius: 10px; overflow: hidden;">
    <thead class="bg-dark text-white">
        <tr>
            <th style="width: 80px;">Hora</th>
            @foreach (array_keys((array)$tablaDisponibilidad[0]) as $columna)
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
            {{-- Mostrar hora --}}
            <td style="background-color: #f1f3f5;" class="fw-bold">
                {{ \Carbon\Carbon::parse($fila->hora)->format('H:i') }}
            </td>
            
            {{-- Mostrar disponibilidad de cada columna --}}
            @foreach ($fila as $col => $valor)
                @if ($col !== 'hora') 
                    {{-- Mostrar tipo de vehículo y estado, si están disponibles --}}
                    <td 
                        class="{{ $valor === 'Disponible' ? 'bg-success' : 'bg-danger' }} text-white fw-semibold" 
                        style="font-size: 1.00rem;"
                        title="{{ $valor }}"
                    >

                        {{-- Mostrar el estado y tipo de vehículo, limitando el texto      --}}
                        {{ \Illuminate\Support\Str::limit($valor, 100) }} 

                        
                    </td>
                @endif
            @endforeach
        </tr>
        @endif
    @endforeach
</tbody>

</table>
