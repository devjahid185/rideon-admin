@extends('vendor.layout')
@section('content')

@section('styles')
<style>
    .loader {
        border: 8px solid #f3f3f3;
        /* Light gray background */
        border-top: 8px solid #3498db;
        /* Blue color for the spinner */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1.5s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.bank') }} {{ trans('global.detail') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('vendor.bankaccount.storeOrUpdate', $bankAccount ? $bankAccount->id : '') }}" enctype="multipart/form-data">
                        @csrf
                        @if($bankAccount)
                        @method('PUT')
                        @else
                        @method('POST')
                        @endif

                        <!-- First Row: Account Name, Bank Name, Branch Name -->
                        <div class="row">
                            <div class="form-group col-md-4 {{ $errors->has('account_name') ? 'has-error' : '' }}">
                                <label class="required" for="account_name">{{ trans('global.account') }} {{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="account_name" id="account_name" value="{{ old('account_name', $bankAccount ? $bankAccount->account_name : '') }}" required>
                                @if($errors->has('account_name'))
                                <span class="help-block" role="alert">{{ $errors->first('account_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 {{ $errors->has('bank_name') ? 'has-error' : '' }}">
                                <label class="required" for="bank_name">{{ trans('global.bank') }} {{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $bankAccount ? $bankAccount->bank_name : '') }}" required>
                                @if($errors->has('bank_name'))
                                <span class="help-block" role="alert">{{ $errors->first('bank_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 {{ $errors->has('branch_name') ? 'has-error' : '' }}">
                                <label class="required" for="branch_name">{{ trans('global.branch') }} {{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $bankAccount ? $bankAccount->branch_name : '') }}">
                                @if($errors->has('branch_name'))
                                <span class="help-block" role="alert">{{ $errors->first('branch_name') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Second Row: Account Number, IBAN, Swift Code -->
                        <div class="row">
                            <div class="form-group col-md-4 {{ $errors->has('account_number') ? 'has-error' : '' }}">
                                <label class="required" for="account_number">{{ trans('global.account_number') }}</label>
                                <input class="form-control" type="text" name="account_number" id="account_number" value="{{ old('account_number', $bankAccount ? $bankAccount->account_number : '') }}" required>
                                @if($errors->has('account_number'))
                                <span class="help-block" role="alert">{{ $errors->first('account_number') }}</span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 {{ $errors->has('iban') ? 'has-error' : '' }}">
                                <label class="required" for="iban">{{ trans('global.iban') }} {{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="iban" id="iban" value="{{ old('iban', $bankAccount ? $bankAccount->iban : '') }}">
                                @if($errors->has('iban'))
                                <span class="help-block" role="alert">{{ $errors->first('iban') }}</span>
                                @endif
                            </div>

                            <div class="form-group col-md-4 {{ $errors->has('swift_code') ? 'has-error' : '' }}">
                                <label class="required" for="swift_code">{{ trans('global.swift_code') }}</label>
                                <input class="form-control" type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $bankAccount ? $bankAccount->swift_code : '') }}">
                                @if($errors->has('swift_code'))
                                <span class="help-block" role="alert">{{ $errors->first('swift_code') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection