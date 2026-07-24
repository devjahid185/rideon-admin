@extends('layouts.installer')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-danger">Database Connection Error</h2>
            <p class="card-text">We can't connect to the database with your settings:</p>
            <ul class="list-unstyled text-danger">
                <li>- Are you sure of your username and password?</li>
                <li>- Are you sure of your host name?</li>
                <li>- Are you sure that your database server is working?</li>
            </ul>
            <p class="card-text">If you are not very sure to understand all these terms, you should contact your hoster.</p>
            <div class="d-flex justify-content-between">
                <a href="{{ route('installer.database') }}" class="btn btn-secondary">Back to Database Settings</a>
            </div>
        </div>
    </div>
@endsection