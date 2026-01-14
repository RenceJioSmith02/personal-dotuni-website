@extends('adminlte::page')

@section('title', config('app.name'))

@section('meta_tags')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('css')
    {{-- Global CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/global.css') }}">
    @stack('css')
@stop

@section('js')
    {{-- Global JS --}}
    
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
    </script>

    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script src="{{ asset('assets/js/global.js') }}"></script>
    @stack('js')
@stop
