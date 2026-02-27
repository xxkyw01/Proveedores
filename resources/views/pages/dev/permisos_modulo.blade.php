@extends('layouts.movil')
@section('title', 'Crear Usuario Dev')
@section('content')

    @if (!session()->has('Proveedor') && !(session('Usuario') && in_array(session('Usuario.IdRol'), [5])))
        <script>
            window.location.href = "/";
        </script>
    @endif

<div class="container">

</div>
