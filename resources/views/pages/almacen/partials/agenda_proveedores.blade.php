<div class="row">
    <!-- Ayer -->
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 columna-dia" data-index="0" id="col-ayer">
        <div class="container mt-4">
            <div class="date-header text-center">
                <div class="date-day" id="day-0">{{ \Carbon\Carbon::yesterday()->translatedFormat('l') }}</div>
                <div class="date-number" id="date-0">{{ \Carbon\Carbon::yesterday()->translatedFormat('j F') }}</div>
            </div>
            <div class="agenda-scroll">
                <div class="timeline" id="timeline-0">
                    @foreach($reservaciones as $reserva)
                        @if(\Carbon\Carbon::parse($reserva->fecha)->isYesterday())
                            @include('pages.almacen.partials.agenda_item', ['reserva' => $reserva])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Hoy -->
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 columna-dia" data-index="1" id="col-hoy">
        <div class="container mt-4">
            <div class="date-header text-center">
                <div class="date-day" id="day-1">{{ \Carbon\Carbon::today()->translatedFormat('l') }}</div>
                <div class="date-number" id="date-1">{{ \Carbon\Carbon::today()->translatedFormat('j F') }}</div>
            </div>
            <div class="agenda-scroll">
                <div class="timeline" id="timeline-1">
                    @foreach($reservaciones as $reserva)
                        @if(\Carbon\Carbon::parse($reserva->fecha)->isToday())
                            @include('pages.almacen.partials.agenda_item', ['reserva' => $reserva])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Mañana -->
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 columna-dia" data-index="2" id="col-manana">
        <div class="container mt-4">
            <div class="date-header text-center">
                <div class="date-day" id="day-2">{{ \Carbon\Carbon::tomorrow()->translatedFormat('l') }}</div>
                <div class="date-number" id="date-2">{{ \Carbon\Carbon::tomorrow()->translatedFormat('j F') }}</div>
            </div>
            <div class="agenda-scroll">
                <div class="timeline" id="timeline-2">
                    @foreach($reservaciones as $reserva)
                        @if(\Carbon\Carbon::parse($reserva->fecha)->isTomorrow())
                            @include('pages.almacen.partials.agenda_item', ['reserva' => $reserva])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>