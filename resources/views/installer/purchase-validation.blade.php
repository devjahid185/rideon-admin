@extends('layouts.installer')

@section('steps')
    @include('installer.steps')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Purchase Validation</h2>
            <form action="{{ route('installer.purchaseValidation.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="coupon_code">Purchase Code</label>
                    <input type="text" name="purchase_code" id="purchase_code" class="form-control" value="" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('installer.permissions') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
            </form>

        </div>
    </div>
@endsection