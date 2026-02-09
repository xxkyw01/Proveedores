    <div class="sidebar d-flex flex-column" id="sidebar">

        <div class="logo-section text-center">
            <i class="fas fa-warehouse fa-3x text-orange mb-2"></i>
            <div class="user-info text-muted">
                <strong>Almacén</strong><br>
                Cuenta Activa
            </div>
        </div>


        <nav class="nav flex-column mt-2">
            <a class="nav-link {{ request()->is('almacen/dashboard') ? 'active' : '' }}"
                href="{{ url('almacen/dashboard') }}">
                <i class="fas fa-home nav-icon"></i><span class="nav-text">Inicio</span>
            </a>

            <a class="nav-link {{ request()->is('almacen/AgendaProveedor') ? 'active' : '' }}"
                href="{{ url('almacen/AgendaProveedor') }}">
                <i class="fas fa-calendar-day nav-icon"></i><span class="nav-text">Agenda Proveedores</span>
            </a>
            <a class="nav-link {{ request()->is('almacen/confirmarCita') ? 'active' : '' }}"
                href="{{ url('almacen/confirmarCita') }}">
                <i class="fas fa-calendar-check nav-icon"></i><span class="nav-text">Confirmar Cita</span>
            </a>

            <a class="nav-link {{ request()->is('compras/calendario') ? 'active' : '' }}"
                href="{{ route('compras.calendario.index') }}">
                <i class="fas fa-calendar-days nav-icon"></i><span class="nav-text"> Calendario
                    Proveedores</span>
            </a>

            <a class="nav-link {{ request()->is('almacen/citas-no-programadas') ? 'active' : '' }}"
                href="{{ url('almacen/citas-no-programadas') }}">
                <i class="fas fa-calendar-times nav-icon"></i><span class="nav-text">Cita No Programada</span>
            </a>

            <a class="nav-link {{ request()->is('proveedor/historial') ? 'active' : '' }}"
                href="{{ url('proveedor/historial') }}">
                <i class="fas fa-book nav-icon"></i><span class="nav-text">Gestionar Citas</span>
            </a>

            <a class="nav-link {{ request()->is('almacen/cita-apartado') ? 'active' : '' }}"
                href="{{ url('almacen/cita-apartado') }}">
                <i class="fas fa-shop-lock  nav-icon"></i><span class="nav-text">Cita Apartado</span>
            </a>

            <a class="nav-link {{ request()->is('almacen/recibo-mercancia') ? 'active' : '' }}"
                href="{{ url('almacen/recibo-mercancia') }}">
                <i class="fas fa-file-invoice  nav-icon"></i><span class="nav-text">Recibo de Mercancía</span>
            </a>

        </nav>

        <a class="nav-link text-danger" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <span class="nav-text">Cerrar Sesión</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

    </div>

    <div class="main-content" id="main-content">
        @yield('content')
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.getElementById("toggleSidebar");
            const overlay = document.getElementById("overlay-sidebar");

            function handleSidebarState() {
                const isMobile = window.innerWidth <= 991;

                if (isMobile) {
                    if (!sidebar.classList.contains("collapsed") && !sidebar.classList.contains("expanded")) {
                        sidebar.classList.add("collapsed");
                    }
                    overlay.style.display = "none";
                } else {
                    sidebar.classList.remove("collapsed", "expanded");
                    sidebar.style.transform = "none";
                    overlay.style.display = "none";
                }
            }

            handleSidebarState();
            window.addEventListener("resize", handleSidebarState);

            toggleBtn.addEventListener("click", () => {
                if (window.innerWidth <= 991) {
                    sidebar.classList.toggle("collapsed");
                    sidebar.classList.toggle("expanded");
                    overlay.classList.toggle("active");
                }
            });

            overlay.addEventListener("click", () => {
                sidebar.classList.remove("expanded");
                sidebar.classList.add("collapsed");
                overlay.classList.remove("active");
            });
        });
    </script>
