@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.booking.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.bookings.update", [$booking->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('host') ? 'has-error' : '' }}">
                            <label for="host_id">{{ trans('cruds.booking.fields.host') }}</label>
                            <select class="form-control select2" name="host_id" id="host_id">
                                @foreach($hosts as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('host_id') ? old('host_id') : $booking->host->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('host'))
                                <span class="help-block" role="alert">{{ $errors->first('host') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.host_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('userid') ? 'has-error' : '' }}">
                            <label class="required" for="userid">{{ trans('cruds.booking.fields.userid') }}</label>
                            <input class="form-control" type="text" name="userid" id="userid" value="{{ old('userid', $booking->userid) }}" required>
                            @if($errors->has('userid'))
                                <span class="help-block" role="alert">{{ $errors->first('userid') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.userid_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('check_in') ? 'has-error' : '' }}">
                            <label class="required" for="check_in">{{ trans('cruds.booking.fields.check_in') }}</label>
                            <input class="form-control date" type="text" name="check_in" id="check_in" value="{{ old('check_in', $booking->check_in) }}" required>
                            @if($errors->has('check_in'))
                                <span class="help-block" role="alert">{{ $errors->first('check_in') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.check_in_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('check_out') ? 'has-error' : '' }}">
                            <label for="check_out">{{ trans('cruds.booking.fields.check_out') }}</label>
                            <input class="form-control date" type="text" name="check_out" id="check_out" value="{{ old('check_out', $booking->check_out) }}">
                            @if($errors->has('check_out'))
                                <span class="help-block" role="alert">{{ $errors->first('check_out') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.check_out_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required" for="status">{{ trans('cruds.booking.fields.status') }}</label>
                            <input class="form-control" type="text" name="status" id="status" value="{{ old('status', $booking->status) }}" required>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('total_night') ? 'has-error' : '' }}">
                            <label class="required" for="total_night">{{ trans('cruds.booking.fields.total_night') }}</label>
                            <input class="form-control" type="number" name="total_night" id="total_night" value="{{ old('total_night', $booking->total_night) }}" step="1" required>
                            @if($errors->has('total_night'))
                                <span class="help-block" role="alert">{{ $errors->first('total_night') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.total_night_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('per_night') ? 'has-error' : '' }}">
                            <label class="required" for="per_night">{{ trans('cruds.booking.fields.per_night') }}</label>
                            <input class="form-control" type="number" name="per_night" id="per_night" value="{{ old('per_night', $booking->per_night) }}" step="0.01" required>
                            @if($errors->has('per_night'))
                                <span class="help-block" role="alert">{{ $errors->first('per_night') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.per_night_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('book_for') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.booking.fields.book_for') }}</label>
                            <select class="form-control" name="book_for" id="book_for">
                                <option value disabled {{ old('book_for', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Booking::BOOK_FOR_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('book_for', $booking->book_for) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('book_for'))
                                <span class="help-block" role="alert">{{ $errors->first('book_for') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.book_for_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('base_price') ? 'has-error' : '' }}">
                            <label class="required" for="base_price">{{ trans('cruds.booking.fields.base_price') }}</label>
                            <input class="form-control" type="number" name="base_price" id="base_price" value="{{ old('base_price', $booking->base_price) }}" step="0.01" required>
                            @if($errors->has('base_price'))
                                <span class="help-block" role="alert">{{ $errors->first('base_price') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.base_price_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('cleaning_charge') ? 'has-error' : '' }}">
                            <label for="cleaning_charge">{{ trans('cruds.booking.fields.cleaning_charge') }}</label>
                            <input class="form-control" type="number" name="cleaning_charge" id="cleaning_charge" value="{{ old('cleaning_charge', $booking->cleaning_charge) }}" step="0.01">
                            @if($errors->has('cleaning_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('cleaning_charge') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.cleaning_charge_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('guest_charge') ? 'has-error' : '' }}">
                            <label for="guest_charge">{{ trans('cruds.booking.fields.guest_charge') }}</label>
                            <input class="form-control" type="number" name="guest_charge" id="guest_charge" value="{{ old('guest_charge', $booking->guest_charge) }}" step="0.01">
                            @if($errors->has('guest_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('guest_charge') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.guest_charge_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('service_charge') ? 'has-error' : '' }}">
                            <label for="service_charge">{{ trans('cruds.booking.fields.service_charge') }}</label>
                            <input class="form-control" type="number" name="service_charge" id="service_charge" value="{{ old('service_charge', $booking->service_charge) }}" step="0.01">
                            @if($errors->has('service_charge'))
                                <span class="help-block" role="alert">{{ $errors->first('service_charge') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.service_charge_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('security_money') ? 'has-error' : '' }}">
                            <label for="security_money">{{ trans('cruds.booking.fields.security_money') }}</label>
                            <input class="form-control" type="number" name="security_money" id="security_money" value="{{ old('security_money', $booking->security_money) }}" step="0.01">
                            @if($errors->has('security_money'))
                                <span class="help-block" role="alert">{{ $errors->first('security_money') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.security_money_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                            <label for="total">{{ trans('cruds.booking.fields.total') }}</label>
                            <input class="form-control" type="number" name="total" id="total" value="{{ old('total', $booking->total) }}" step="0.01">
                            @if($errors->has('total'))
                                <span class="help-block" role="alert">{{ $errors->first('total') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.total_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('currency_code') ? 'has-error' : '' }}">
                            <label for="currency_code">{{ trans('cruds.booking.fields.currency_code') }}</label>
                            <input class="form-control" type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', $booking->currency_code) }}">
                            @if($errors->has('currency_code'))
                                <span class="help-block" role="alert">{{ $errors->first('currency_code') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.currency_code_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('cancellation_reasion') ? 'has-error' : '' }}">
                            <label for="cancellation_reasion">{{ trans('cruds.booking.fields.cancellation_reasion') }}</label>
                            <input class="form-control" type="text" name="cancellation_reasion" id="cancellation_reasion" value="{{ old('cancellation_reasion', $booking->cancellation_reasion) }}">
                            @if($errors->has('cancellation_reasion'))
                                <span class="help-block" role="alert">{{ $errors->first('cancellation_reasion') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.cancellation_reasion_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('wall_amt') ? 'has-error' : '' }}">
                            <label for="wall_amt">{{ trans('cruds.booking.fields.wall_amt') }}</label>
                            <input class="form-control" type="number" name="wall_amt" id="wall_amt" value="{{ old('wall_amt', $booking->wall_amt) }}" step="0.01">
                            @if($errors->has('wall_amt'))
                                <span class="help-block" role="alert">{{ $errors->first('wall_amt') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.wall_amt_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('rating') ? 'has-error' : '' }}">
                            <label for="rating">{{ trans('cruds.booking.fields.rating') }}</label>
                            <input class="form-control" type="number" name="rating" id="rating" value="{{ old('rating', $booking->rating) }}" step="0.01">
                            @if($errors->has('rating'))
                                <span class="help-block" role="alert">{{ $errors->first('rating') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.rating_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('cancelled_by') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.booking.fields.cancelled_by') }}</label>
                            <select class="form-control" name="cancelled_by" id="cancelled_by">
                                <option value disabled {{ old('cancelled_by', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Booking::CANCELLED_BY_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('cancelled_by', $booking->cancelled_by) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('cancelled_by'))
                                <span class="help-block" role="alert">{{ $errors->first('cancelled_by') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.booking.fields.cancelled_by_helper') }}</span>
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