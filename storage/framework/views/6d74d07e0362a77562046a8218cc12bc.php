<?php
    
    $rolId       = (int) (session('Usuario.IdRol') ?? 0);
    $esProveedor = session()->has('Proveedor');

    $prefix = 'includes.sidebar.';

    if ($esProveedor) {
        $view = $prefix.'proveedor';
    } else {
        $view = match ($rolId) {
            1 => $prefix.'admin',
            2 => $prefix.'almacen',
            3 => $prefix.'compras',
            4 => $prefix.'mejora',
            5 => $prefix.'dev',
            7 => $prefix.'auditoria',
            default => $prefix.'mejora',
        };
    }
?>

<?php if ($__env->exists($view)) echo $__env->make($view, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/components/sidebar.blade.php ENDPATH**/ ?>