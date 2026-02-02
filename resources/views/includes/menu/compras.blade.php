<div class="container-fluid con-sidebar">
    <div class="row justify-content-center">

        <!-- Dashboard KPI -->
        <div class="col-lg-4 col-md-6 col-6 mb-3">

            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('almacen/KPIDashboard') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-chart-pie font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Dashboard KPI's</p>
                    </div>
                </a>
            </div>
        </div>


        <div class="col-lg-4 col-md-6 col-6 mb-3">

            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('/proveedor/citas') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-calendar-plus font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Solicitar Cita</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Gestionar cita  -->
        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('/proveedor/historial') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-book font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Historial Cita</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Agenda Proveedor -->
        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('/almacen/AgendaProveedor') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-calendar-day font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Agenda Proveedores</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Calendario Proveedores  -->
        <div class="col-lg-4 col-md-6 col-6 mb-3">
            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('/compras/calendario') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-calendar-days font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Calendario Proveedores</p>
                    </div>
                </a>
            </div>
        </div>


        <!-- Cita Express -->
        <div class="col-lg-4 col-md-6 col-6 mb-3">

            <div class="wrimagecard wrimagecard-topimage">
                <a href="{{ url('/almacen/cita-express') }}">
                    <div class="wrimagecard-topimage_header bg-tr-orange text-center">
                        <i class="fas fa-truck-fast font-large-2 text-orange"></i>
                    </div>
                    <div class="wrimagecard-topimage_title">
                        <p class="fs-1 fw-bold m-0">Cita Express</p>
                    </div>
                </a>
            </div>
        </div>


    </div> {{-- Cierra row --}}
</div> {{-- Cierra container-fluid --}}
