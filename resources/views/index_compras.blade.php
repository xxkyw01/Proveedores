@extends('layouts.default')
@section('title', 'Compras Dashboard')
@section('content')
@include('includes.sidebar.compras')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        @include('includes.menu.compras')

    </div>
</div>

@stop
