@extends('layouts.default')
@section('title', 'Desarrollador Dashboard')
@section('content')

@include('includes.sidebar.dev')

<div class="container-fluid" style="padding-left: 30px; padding-right: 30px;">
    <div class="row justify-content-center">
        @include('includes.menu.dev')

    </div>
</div>

@stop

