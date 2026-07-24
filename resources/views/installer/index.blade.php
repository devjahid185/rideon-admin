@extends('layouts.installer')

@section('steps')
    <div class="step-item active">
        <div class="step-icon">1</div>
        <div class="step-title">Welcome</div>
    </div>
    <div class="step-item">
        <div class="step-icon">2</div>
        <div class="step-title">Requirements</div>
    </div>
    <div class="step-item">
        <div class="step-icon">3</div>
        <div class="step-title">Permissions</div>
    </div>
    <div class="step-item">
        <div class="step-icon">4</div>
        <div class="step-title">Purchase Validation</div>
    </div>
    <div class="step-item">
        <div class="step-icon">5</div>
        <div class="step-title">Database</div>
    </div>
    <div class="step-item">
        <div class="step-icon">6</div>
        <div class="step-title">Admin Setup</div>
    </div>
    <div class="step-item">
        <div class="step-icon">7</div>
        <div class="step-title">Finish</div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Welcome to the Installer</h2>
            <p class="card-text">This installer will guide you through the process of setting up your application.</p>
            <p class="card-text">Make sure you have the necessary server requirements and permissions before proceeding.</p>
            <p class="card-text">Click the button below to start the installation process.</p>
            <div class="text-right">
                <a href="{{ route('installer.requirements') }}" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </div>
@endsection