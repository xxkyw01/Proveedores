<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="logo-section text-center">
        <i class="fas fa-user-shield fa-3x text-orange mb-2"></i>
        <div class="user-info text-muted">
            <strong>Auditoria</strong><br>
            Cuenta Activa
        </div>
    </div>


    <nav class="nav flex-column mt-2">
        <a class="nav-link {{ request()->is('auditoria/dashboard') ? 'active' : '' }}"
            href="{{ url('auditoria/dashboard') }}">
            <i class="fas fa-home nav-icon"></i><span class="nav-text">Inicio</span>
        </a>

        <!-- Agenda Proveedores -->
        <a class="nav-link {{ request()->is('almacen/AgendaProveedor') ? 'active' : '' }}"
            href="{{ url('almacen/AgendaProveedor') }}">
            <i class="fas fa-calendar-day nav-icon"></i><span class="nav-text">Agenda
                Proveedores</span>
        </a>
        <!---MENSAJE
        <a class="nav-link {{ request()->is('mejora/mensajeria') ? 'active' : '' }}" href="{{ url('mejora/mensajeria') }}">
            <i class="fas fa-commenting nav-icon"></i><span class="nav-text">Mensaje</span>
        </a>
        -->
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
