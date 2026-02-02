<!-- HEADER -->
<nav class="navbar navbar-expand-lg fixed-top bg-ligthgrey shadow-bar border-bottom-orange p-0"
    aria-label="Main navigation">

    <div class="container-fluid d-flex justify-content-between align-items-center px-2">
        
         
        <a href="javascript:history.back()" class="d-flex align-items-center text-decoration-none navbar-brand">
            <i class="fas fa-arrow-left text-orange fa-2x me-3"></i>
        </a>


        

        <button class="p-0 border-0" type="image">
            <a  class="d-flex m-0 text-decoration-none navbar-brand">
                <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="La Concha" height="60">
            </a>
        </button>
        
        
        <span class="fw-bold text-orange text-center d-none d-md-block flex-grow-1" style="font-size: 2.1em;">
            <?php echo $__env->yieldContent('title'); ?>
        </span>

        
        <button class="btn d-lg-none shadow-none bg-transparent border-0" id="toggleSidebar" style="z-index: 1100;">
            <i class="fas fa-bars text-orange fs-4"></i>
        </button>

        
    </div>
</nav>

<div id="overlay-sidebar">
    </div>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/includes/header.blade.php ENDPATH**/ ?>