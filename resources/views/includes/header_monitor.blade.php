<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-ligthgrey shadow-bar border-bottom-orange p-0"
    aria-label="Main navigation">

    <div class="container-fluid d-flex justify-content-between align-items-center px-2">
        

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
