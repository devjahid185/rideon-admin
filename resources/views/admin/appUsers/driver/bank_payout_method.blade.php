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
                    <h3 class="box-title">{{ 'Bank Account' }}</h3>
                    <span class="email_status" style="display: none;">
                        (<span class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)
                    </span>
                </div>

                @php
                    $currentPayoutRoute = \Request::route()->getName();
                    $currentPayout = Str::afterLast($currentPayoutRoute, '.');
                @endphp

                <form id="bankAccountForm" method="POST"
                      action="{{ route('admin.driver.update.bank.account', $appUser->id) }}"
                      class="form-horizontal">
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

                        {{-- Account Name --}}
                        <div class="form-group">
                            <label for="account_name" class="col-sm-3 control-label">Account Name <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" id="account_name" name="account_name"
                                    class="form-control @error('account_name') is-invalid @enderror"
                                    value="{{ old('account_name', $stripeDetails['account_name'] ?? '') }}"
                                    placeholder="Enter account holder name">
                                @error('account_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Bank Name --}}
                        <div class="form-group">
                            <label for="bank_name" class="col-sm-3 control-label">Bank Name <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" id="bank_name" name="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name', $stripeDetails['bank_name'] ?? '') }}"
                                    placeholder="Enter bank name">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Branch Name --}}
                        <div class="form-group">
                            <label for="branch_name" class="col-sm-3 control-label">Branch Name</label>
                            <div class="col-sm-6">
                                <input type="text" id="branch_name" name="branch_name"
                                    class="form-control @error('branch_name') is-invalid @enderror"
                                    value="{{ old('branch_name', $stripeDetails['branch_name'] ?? '') }}"
                                    placeholder="Enter branch name (optional)">
                                @error('branch_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Account Number --}}
                        <div class="form-group">
                            <label for="account_number" class="col-sm-3 control-label">Account Number <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" id="account_number" name="account_number"
                                    class="form-control @error('account_number') is-invalid @enderror"
                                    value="{{ old('account_number', $stripeDetails['account_number'] ?? '') }}"
                                    placeholder="Enter account number">
                                @error('account_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- IBAN --}}
                        <div class="form-group">
                            <label for="iban" class="col-sm-3 control-label">IBAN</label>
                            <div class="col-sm-6">
                                <input type="text" id="iban" name="iban"
                                    class="form-control @error('iban') is-invalid @enderror"
                                    value="{{ old('iban', $stripeDetails['iban'] ?? '') }}"
                                    placeholder="Enter IBAN (optional)">
                                @error('iban')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="swift_code" class="col-sm-3 control-label">SWIFT Code</label>
                            <div class="col-sm-6">
                                <input type="text" id="swift_code" name="swift_code"
                                    class="form-control @error('swift_code') is-invalid @enderror"
                                    value="{{ old('swift_code', $stripeDetails['swift_code'] ?? '') }}"
                                    placeholder="Enter SWIFT code (optional)">
                                @error('swift_code')
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
