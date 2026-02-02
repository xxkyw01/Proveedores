<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-ligthgrey shadow-bar border-bottom-orange p-0"
    aria-label="Main navigation">

    <div class="container-fluid d-flex justify-content-between align-items-center px-2">
        
         {{-- Botón de regreso  --}}
        <a href="javascript:history.back()" class="d-flex align-items-center text-decoration-none navbar-brand">
            <i class="fas fa-arrow-left text-orange fa-2x me-3"></i>
        </a>


        {{-- Logo 
            $rutaInicio = route('proveedor.login');
            $usuario = session('Usuario');
            $proveedor = session('Proveedor');

            if ($usuario && isset($usuario['IdRol'])) {
                $rutaInicio = match ($usuario['IdRol']) {
                    1 => redirect()->route('admin.dashboard'),
                    2 => redirect()->route('almacen.dashboard'),
                    3 => redirect()->route('compras.dashboard'),
                    4 => redirect()->route('mejora.dashboard'),
                    5 => redirect()->route('dev.dashboard'),
                    default => redirect()->route('proveedor.login'),
                };
            } elseif ($proveedor) {
                $rutaInicio = redirect()->route('proveedor.dashboard');
            }
        @endphp

        @if (session('Usuario') || session('Proveedor'))
            <a class="d-flex m-0 text-decoration-none navbar-brand" href="{{ $rutaInicio }}">
                <img src="{{ asset('assets/img/logo.png') }}" alt="La Concha" height="60">
            </a>
        @else
--}}

        <button class="p-0 border-0" type="image">
            <a  class="d-flex m-0 text-decoration-none navbar-brand">
                <img src="{{asset('assets/img/logo.png')}}" alt="La Concha" height="60">
            </a>
        </button>
        
        {{-- Título centrado --}}
        <span class="fw-bold text-orange text-center d-none d-md-block flex-grow-1" style="font-size: 2.1em;">
            @yield('title')
        </span>

        {{-- Botón Hamburguesa --}}
        <button class="btn d-lg-none shadow-none bg-transparent border-0" id="toggleSidebar" style="z-index: 1100;">
            <i class="fas fa-bars text-orange fs-4"></i>
        </button>

        
    </div>
</nav>

<div id="overlay-sidebar">
    </div>
