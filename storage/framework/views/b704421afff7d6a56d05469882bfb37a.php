<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="logo-section text-center">
        <i class="fas fa-user-shield fa-3x text-orange mb-2"></i>
        <div class="user-info text-muted">
            <strong>Auditoria</strong><br>
            Cuenta Activa
        </div>
    </div>


    <nav class="nav flex-column mt-2">
        <a class="nav-link <?php echo e(request()->is('auditoria/dashboard') ? 'active' : ''); ?>"
            href="<?php echo e(url('auditoria/dashboard')); ?>">
            <i class="fas fa-home nav-icon"></i><span class="nav-text">Inicio</span>
        </a>
        <a class="nav-link <?php echo e(request()->is('almacen/AgendaProveedor') ? 'active' : ''); ?>"
            href="<?php echo e(url('almacen/AgendaProveedor')); ?>">
            <i class="fas fa-calendar-day nav-icon"></i><span class="nav-text">Agenda
                Proveedores</span>
        </a>

    </nav>

    <a class="nav-link text-danger" href="<?php echo e(route('logout')); ?>"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt nav-icon"></i>
        <span class="nav-text">Cerrar Sesión</span>
    </a>
    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
</div>

<div class="main-content" id="main-content">
    <?php echo $__env->yieldContent('content'); ?>
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
<?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/includes/sidebar/auditoria.blade.php ENDPATH**/ ?>