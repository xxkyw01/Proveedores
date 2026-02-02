
    @php
        $isToday = \Carbon\Carbon::parse($reserva->fecha)->isToday();
        $isPast = $isToday && \Carbon\Carbon::parse($reserva->hora)->lt(\Carbon\Carbon::now());
    @endphp

    @if (!$isPast)
        <div class="timeline-item {{ $isToday ? 'today-item' : '' }}">
            <div class="timeline-marker">
                <div class="linea-con-circulo">
                    <div class="circulo"></div>
                    <div class="linea"></div>
                </div>
            </div>

            <div class="timeline-card">
                <div class="card-header">
                    <span class="badge-time">{{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}</span>
                    <span class="badge-status {{ getStatusClass($reserva->estado) }}">{{ $reserva->estado }}</span>
                </div>
                <div class="card-body">
                    <h5 class="proveedor-nombre">{{ $reserva->proveedor_nombre ?? 'Otro' }}</h5>
                    <div class="orden-compra">
                        {!! formatOrdenCompra($reserva->orden_compra) !!}
                    </div>
                    <div class="card-details">
                        <span><i class="material-icons">local_shipping</i>
                            {{ $reserva->transporte_nombre ?? 'No especificado' }}</span>
                        <span><i class="material-icons">place</i> {{ $reserva->Lugar ?? 'No especificado' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
