@extends('layouts.installer')

@section('steps')
@include('installer.steps')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Permissions</h2>
            <p class="card-text">Please make sure the following directories and files have write permissions:</p>
            <ul class="list-group mb-4">
                <li class="list-group-item">
                    storage/
                    @if ($permissions['storage'])
                        <span class="text-success float-right"><i class="material-icons">check_circle</i></span>
                    @else
                        <span class="text-danger float-right"><i class="material-icons">error</i></span>
                    @endif
                </li>
                <li class="list-group-item">
                    bootstrap/cache/
                    @if ($permissions['bootstrap/cache'])
                        <span class="text-success float-right"><i class="material-icons">check_circle</i></span>
                    @else
                        <span class="text-danger float-right"><i class="material-icons">error</i></span>
                    @endif
                </li>
                <li class="list-group-item">
                    config/app.php
                    @if ($permissions['config/app.php'])
                        <span class="text-success float-right"><i class="material-icons">check_circle</i></span>
                    @else
                        <span class="text-danger float-right"><i class="material-icons">error</i></span>
                    @endif
                </li>
            </ul>
            <div class="d-flex justify-content-between">
                <a href="{{ route('installer.requirements') }}" class="btn btn-secondary">Back</a>
                @php
                    $allPermissionsGranted = $permissions['storage'] && $permissions['bootstrap/cache'] && $permissions['config/app.php'];
                @endphp
                {{--<a href="{{ route('installer.database') }}" class="btn btn-primary @if(!$allPermissionsGranted) disabled @endif">Next</a>--}}
                <a href="{{ route('installer.purchaseValidation') }}" class="btn btn-primary @if(!$allPermissionsGranted) disabled @endif">Next</a>
            </div>
        </div>
    </div>
@endsection