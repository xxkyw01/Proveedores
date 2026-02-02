@php
    
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
@endphp

@includeIf($view)

{{-- Debugging info  pd: no produccion
<pre>
    {{ json_encode(['rolId'=> $rolId, 'esProveedor'=>$esProveedor, 'vista'=>$view], JSON_PRETTY_PRINT) }}
</pre
--}}