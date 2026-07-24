@extends('layouts.installer')

@section('steps')
@include('installer.steps')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Database Configuration</h2>
            
            <form action="{{ route('installer.database.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="db_connection"><i class="fas fa-database"></i> Database Connection</label>
                    <input type="text" name="db_connection" id="db_connection" class="form-control" value="mysql" required>
                </div>
                <div class="form-group">
                    <label for="db_host"><i class="fas fa-server"></i> Database Host</label>
                    <input type="text" name="db_host" id="db_host" class="form-control" value="127.0.0.1" required>
                </div>
                <div class="form-group">
                    <label for="db_port"><i class="fas fa-plug"></i> Database Port</label>
                    <input type="text" name="db_port" id="db_port" class="form-control" value="3306" required>
                </div>
                <div class="form-group">
                    <label for="db_name"><i class="fas fa-database"></i> Database Name</label>
                    <input type="text" name="db_name" id="db_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="db_username"><i class="fas fa-user"></i> Database Username</label>
                    <input type="text" name="db_username" id="db_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="db_password"><i class="fas fa-lock"></i> Database Password</label>
                    <input type="password" name="db_password" id="db_password" class="form-control">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('installer.purchaseValidation') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
            </form>
        </div>
    </div>
@endsection