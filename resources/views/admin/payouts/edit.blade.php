@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('global.payout_title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.payouts.update", [$payout->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('vendorid') ? 'has-error' : '' }}">
                            <label class="required" for="vendorid">{{ trans('global.vendorid') }}</label>
                            <input class="form-control" type="number" name="vendorid" id="vendorid" value="{{ old('vendorid', $payout->vendorid) }}" step="1" required>
                            @if($errors->has('vendorid'))
                                <span class="help-block" role="alert">{{ $errors->first('vendorid') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                            <label class="required" for="amount">{{ trans('global.amount') }}</label>
                            <input class="form-control" type="number" name="amount" id="amount" value="{{ old('amount', $payout->amount) }}" step="0.01" required>
                            @if($errors->has('amount'))
                                <span class="help-block" role="alert">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('currency') ? 'has-error' : '' }}">
                            <label for="currency">{{ trans('global.currency') }}</label>
                            <input class="form-control" type="text" name="currency" id="currency" value="{{ old('currency', $payout->currency) }}">
                            @if($errors->has('currency'))
                                <span class="help-block" role="alert">{{ $errors->first('currency') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('vendor_name') ? 'has-error' : '' }}">
                            <label for="vendor_name">{{ trans('global.vendor_name') }}</label>
                            <input class="form-control" type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name', $payout->vendor_name) }}">
                            @if($errors->has('vendor_name'))
                                <span class="help-block" role="alert">{{ $errors->first('vendor_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
                            <label for="payment_method">{{ trans('global.payment_method') }}</label>
                            <input class="form-control" type="text" name="payment_method" id="payment_method" value="{{ old('payment_method', $payout->payment_method) }}">
                            @if($errors->has('payment_method'))
                                <span class="help-block" role="alert">{{ $errors->first('payment_method') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('account_number') ? 'has-error' : '' }}">
                            <label for="account_number">{{ trans('global.account_number') }}</label>
                            <input class="form-control" type="text" name="account_number" id="account_number" value="{{ old('account_number', $payout->account_number) }}">
                            @if($errors->has('account_number'))
                                <span class="help-block" role="alert">{{ $errors->first('account_number') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('payout_status') ? 'has-error' : '' }}">
                            <label>{{ trans('global.payout_status') }}</label>
                            <select class="form-control" name="payout_status" id="payout_status">
                                <option value disabled {{ old('payout_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Payout::PAYOUT_STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('payout_status', $payout->payout_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('payout_status'))
                                <span class="help-block" role="alert">{{ $errors->first('payout_status') }}</span>
                            @endif
                        </div>
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