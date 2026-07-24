@extends('layouts.installer')

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-danger">Purchase Code Validation Failed</h2>
            <p class="card-text">There was an issue with the Purchase code you entered:</p>
            <ul class="list-unstyled text-danger">
                <li>- The Purchase code may have already been used for this website.</li>
                <li>- The Purchase code may not be valid for the current installation.</li>
                <li>- Please double-check the code or contact support for assistance.</li>
            </ul>
            <p class="card-text">If you're unsure about the validity of the Purchase, you can contact support for more details.</p>
            <div class="d-flex justify-content-between">
                <a href="{{ route('installer.purchaseValidation') }}" class="btn btn-secondary">Back to Purchase Code Page</a>
                
            </div>
        </div>
    </div>
@endsection
