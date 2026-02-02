@extends('layouts.default')
@section('title', 'Lider/ Supervisor Dashboard')
@section('content')
@include('includes.scripts.Selectize')
@include('includes.scripts.SweetAlert2')
@include('includes.scripts.Cookies')

    @include('includes.sidebar.almacen')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
        <div class="row justify-content-center">
            @include('includes.menu.lider')

        </div>
    </div>

@stop
