@extends('layouts.installer')
@section('steps')
@include('installer.steps')
@endsection
@section('content')
    <div class="container">
        <h1>Installation Complete</h1>
        <p>Congratulations! Your application has been successfully installed.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go to Application</a>
    </div>
@endsection