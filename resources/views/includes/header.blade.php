<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-ligthgrey shadow-bar border-bottom-orange p-0"
    aria-label="Main navigation">

    <div class="container-fluid d-flex justify-content-between align-items-center px-2">
   
        <a href="javascript:history.back()" class="d-flex align-items-center text-decoration-none navbar-brand">
            <i class="fas fa-arrow-left text-orange fa-2x me-3"></i>
        </a>


        <button class="p-0 border-0" type="image">
            <a  class="d-flex m-0 text-decoration-none navbar-brand">
                <img src="{{asset('assets/img/logo.png')}}" alt="La Concha" class="brand-img logo-header">
            </a>
        </button>
        
        <span class="fw-bold text-orange text-center d-none d-md-block flex-grow-1 header-title">
            @yield('title')
        </span>

        <button class="btn d-lg-none shadow-none bg-transparent border-0" id="toggleSidebar" style="z-index: 1100;">
            <i class="fas fa-bars text-orange fs-4"></i>
        </button>

        
    </div>
</nav>

<div id="overlay-sidebar">
    </div>
