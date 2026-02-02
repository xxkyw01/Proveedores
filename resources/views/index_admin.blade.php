@extends('layouts.default')
@section('title', 'Administrador Dashboard')
@section('content')

@include('includes.sidebar.admin')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        @include('includes.menu.admin')

    </div>
</div>
@stop

