@extends('layouts.movil')
@section('title', 'Auditoria')
@section('content')
@include('includes.sidebar.auditoria')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        @include('includes.menu.auditoria')

    </div>
</div>

@stop
