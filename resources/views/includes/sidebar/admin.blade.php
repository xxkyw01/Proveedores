    <!--Para Escritorio-->
    <div class="sidebar d-flex flex-column" id="sidebar">

        <div class="logo-section text-center">
            <i class="fas fa-user-shield fa-3x text-orange mb-2"></i>
            <div class="user-info text-muted">
                <strong>Administrador</strong><br>
                Cuenta Activa
            </div>
        </div>

        <nav class="nav flex-column mt-2">
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                href="{{ url('admin/dashboard') }}">
                <i class="fas fa-home nav-icon"></i><span class="nav-text">Inicio</span>
            </a>

            <div class="accordion" id="accordionSidebar">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingMejora">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseMejora" aria-expanded="false" aria-controls="collapseProveedor">
                            Mejora Continua
                        </button>
                    </h2>
                    <div id="collapseMejora" class="accordion-collapse collapse" aria-labelledby="headingMejora"
                        data-bs-parent="#accordionSidebar">
                        <div class="accordion-body">
                            <a class="nav-link {{ request()->is('almacen/KPIDashboard') ? 'active' : '' }}"
                                href="{{ url('almacen/KPIDashboard') }}">
                                <i class="fas fa-chart-pie nav-icon"></i><span class="nav-text"> Dashboard </span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingProveedor">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseProveedor" aria-expanded="false" aria-controls="collapseProveedor">
                            Proveedor
                        </button>
                    </h2>

                    <div id="collapseProveedor" class="accordion-collapse collapse" aria-labelledby="headingProveedor"
                        data-bs-parent="#accordionSidebar">
                        <div class="accordion-body">
            
                            <a class="nav-link {{ request()->is('proveedor/historial') ? 'active' : '' }}"
                                href="{{ url('proveedor/historial') }}">
                                <i class="fas fa-book nav-icon"></i><span class="nav-text">Historial de Proveedores</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ALMACÉN -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAlmacen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseAlmacen" aria-expanded="false" aria-controls="collapseAlmacen">
                            Almacén
                        </button>
                    </h2>
                    <div id="collapseAlmacen" class="accordion-collapse collapse" aria-labelledby="headingAlmacen"
                        data-bs-parent="#accordionSidebar">
                        <div class="accordion-body">
                            <a class="nav-link {{ request()->is('almacen/AgendaProveedor') ? 'active' : '' }}"
                                href="{{ url('almacen/AgendaProveedor') }}">
                                <i class="fas fa-calendar-day nav-icon"></i><span class="nav-text">Agenda
                                    Proveedores</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- COMPRAS -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCompras">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseCompras" aria-expanded="false" aria-controls="collapseCompras">
                            Compras
                        </button>
                    </h2>
                    <div id="collapseCompras" class="accordion-collapse collapse" aria-labelledby="headingCompras"
                        data-bs-parent="#accordionSidebar">
                        <div class="accordion-body">
                            <a class="nav-link {{ request()->is('reporte/maniobras') ? 'active' : '' }}"
                                href="{{ route('reporte.maniobras') }}">
                                <i class="fas fa-clipboard-list nav-icon"></i><span class="nav-text">Reporte de
                                    Maniobras</span>
                            </a>
                        </div>
                    </div>

                    <div id="collapseCompras" class="accordion-collapse collapse" aria-labelledby="headingCompras"
                        data-bs-parent="#accordionSidebar">
                        <div class="accordion-body">
                            <a class="nav-link {{ request()->is('compras/calendario') ? 'active' : '' }}"
                                href="{{ route('compras.calendario.index') }}">
                                <i class="fas fa-clipboard-list nav-icon"></i><span class="nav-text"> Calendario
                                    Proveedores</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>


        </nav>

        <a class="nav-link text-danger" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <span class="nav-text">Cerrar Sesión</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <div class="main-content" id="main-content">
            @yield('content')
        </div>

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
