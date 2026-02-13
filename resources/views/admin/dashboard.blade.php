@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to AdminLTE!</p>

    {{-- Now anywhere in AdminLTE: --}}
    <h2>{{ auth()->user()->name }}</h2>
    <h2>{{ auth()->user()->role_name }}</h2>

    {{-- Check role: --}}
    <br><br>
    @if(auth()->user()->hasRole('admin'))
        <h3>admin menu</h3>
    @endif

    {{-- Multiple: --}}
    <br><br>
    @if(auth()->user()->hasRole('publisher') || auth()->user()->hasRole('admin'))
        <h4>admin and publisher menu</h4>
    @endif

    {{-- Loop: --}}
    <br><br>
    @foreach(auth()->user()->roles as $role)
        <span>{{ $role->name }}</span>
    @endforeach


@stop



@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop