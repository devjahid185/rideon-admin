@extends('layouts.installer')

@section('steps')
@include('installer.steps')
@endsection

@section('content')
<div class="container">
    <h2>Complete Installation</h2>
    <form action="{{ route('installer.admin.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Admin Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Admin Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                required>
        </div>

        <div class="form-group" style="display:none">
            <h4>Choose Modules</h4>
            @foreach ($modules as $module)
            <div class="form-check">
                <input type="checkbox" checked="checked" name="modules[]" id="module_{{ $module->id }}"
                    value="{{ $module->id }}" class="form-check-input">
                <label for="module_{{ $module->id }}" class="form-check-label">{{ $module->name }}</label>
            </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary"> Complete Installation</button>
    </form>
</div>
@endsection