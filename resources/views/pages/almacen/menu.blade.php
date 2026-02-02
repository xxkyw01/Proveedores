@extends('layouts.default')
@section('title', 'Menú Almacen')
@section('content')
@include('includes.scripts.Selectize')
@include('includes.scripts.SweetAlert2')
@include('includes.scripts.Cookies')


<div class="row row-cols-lg-7 row-cols-md-5 row-cols-sm-3 justify-content-center align-items-center">
    @include('includes.menu.almacen')
</div>

@stop