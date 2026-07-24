@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}">

    <style>
    .alert.alert-danger.m-3 {
    display: none !important;
    }


    .autoFillOtp-toggle {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    }

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 22px;
    margin-right: 10px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 15px;
    width: 15px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.toggle-label {
    font-size: 14px;
}

</style>
@endsection

@section('content')
<section class="content">
    @include('admin.appUsers.driver.menu')
    <div class="row" style="margin-top:20px">
        {{-- Sidebar --}}
        <div class="col-md-3 settings_bar_gap">
            <div class="box box-info box_info">
                <div>
                    <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ 'Payout Methods' }}</h4>
                    @include('admin.appUsers.driver.payout_method_link')
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ 'Paypal' }}</h3>
                    <span class="email_status" style="display: none;">
                        (<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)
                    </span>
                </div>

                @php
                    // Get current route name, e.g., 'admin.vendor.stripe'
                    $currentPayoutRoute = \Request::route()->getName(); 
                    $currentPayout = Str::afterLast($currentPayoutRoute, '.'); 

                   // dd($currentPayout);
                @endphp

                <form id="stripePayoutForm" method="POST" action="{{ route('admin.driver.update.bank.account', $appUser->id) }}" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="payout_method_name" value="{{ $currentPayout }}">
                    <div class="box-body">

                    <div class="form-group">
                        <div class="col-sm-6 autoFillOtp-toggle" style="margin-left: 80%; margin-top: 1px;">
                            <label class="switch">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                    {{ (!empty($stripeDetails['is_active']) && $stripeDetails['is_active']) ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                            <span class="toggle-label">
                                {{ (!empty($stripeDetails['is_active']) && $stripeDetails['is_active']) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">{{ 'Email' }} <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                                  value="{{ old('email', $stripeDetails['email'] ?? '') }}" placeholder="Enter Stripe Email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note" class="col-sm-3 control-label">{{ 'Note' }} <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" name="note" class="form-control @error('note') is-invalid @enderror"
                                   value="{{ old('note', $stripeDetails['note'] ?? '') }}" placeholder="Enter Note">
                                @error('note')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-info btn-space">{{ trans('global.save') }}</button>
                        <a class="btn btn-danger" href="{{ route('admin.settings') }}">{{ trans('global.cancel') }}</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
