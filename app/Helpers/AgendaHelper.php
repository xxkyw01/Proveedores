<?php

function getStatusClass($status)
{
    $status = strtolower(trim($status ?? ''));
    
    return match ($status) {
        'pendiente' => 'bg-secondary',
        'confirmada' => 'badge-confirmado',
        'asistió', 'asistio' => 'bg-success',
        'no asistió', 'no asistio' => 'bg-dark',
        'cancelada', 'cancelado' => 'bg-danger',
        'en proceso' => 'bg-warning',
        'recepción tardía', 'recepcion tardia' => 'bg-dark',
        'cancelada por proveedor' => 'bg-danger',
        default => 'bg-light text-dark'
    };
}

function formatOrdenCompra($data)
{
    if (empty($data)) return '<span class="badge-orden">Sin orden</span>';

    try {
        $ordenes = is_array($data) ? $data : json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $ordenes = explode(',', $data);
        }

        return implode('', array_map(fn($o) => 
            '<span class="badge-orden">'.trim($o).'</span>', 
            (array)$ordenes
        ));
    } catch (Exception $e) {
        return '<span class="badge-orden">Error en formato</span>';
    }
}