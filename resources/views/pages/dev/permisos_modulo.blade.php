<!---permisos_modulo.blade.php-->
<!--Controlador :  PermisosModuloController.php-->
@extends('layouts.movil')
@section('title', 'Crear Usuario Dev')
@section('content')

    <!-- Session Rol solamente para ROl de DEV -->
    @if (!session()->has('Proveedor') && !(session('Usuario') && in_array(session('Usuario.IdRol'), [5])))
        <script>
            window.location.href = "/";
        </script>
    @endif

<div class="container">

</div>
