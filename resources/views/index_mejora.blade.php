@extends('layouts.default')
@section('title', 'Mejora Dashboard')
@section('content')
@include('includes.sidebar.mejora')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        @include('includes.menu.mejora')

    </div>
</div>

@stop

