@extends('layouts.default')
@section('title', 'Proveedor Dashboard')
@section('content')

    @include('includes.sidebar.proveedor')

    <div class="container-fluid" style="padding-left:30px; padding-right:30px;">
        <div class="row justify-content-center">
            @include('includes.menu.proveedores')

        </div>
    </div>


@stop
