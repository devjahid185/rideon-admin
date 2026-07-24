@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('global.review_title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.reviews.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('bookingid') ? 'has-error' : '' }}">
                            <label class="required" for="bookingid">{{ trans('global.bookingid') }}</label>
                            <input class="form-control" type="number" name="bookingid" id="bookingid" value="{{ old('bookingid', '') }}" step="1" required>
                            @if($errors->has('bookingid'))
                                <span class="help-block" role="alert">{{ $errors->first('bookingid') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('property_name') ? 'has-error' : '' }}">
                            <label class="required" for="property_name">{{ trans('global.property_name') }}</label>
                            <textarea class="form-control" name="property_name" id="property_name" required>{{ old('property_name') }}</textarea>
                            @if($errors->has('property_name'))
                                <span class="help-block" role="alert">{{ $errors->first('property_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('property_id') ? 'has-error' : '' }}">
                            <label class="required" for="property_id">Property Id</label>
                            <input  type="number" class="form-control" name="property_id" id="property_id" required>{{ old('property_id') }}</input>
                            @if($errors->has('property_id'))
                                <span class="help-block" role="alert">{{ $errors->first('property_id') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('guestid') ? 'has-error' : '' }}">
                            <label class="required" for="guestid">{{ trans('global.guestid') }}</label>
                            <input class="form-control" type="text" name="guestid" id="guestid" value="{{ old('guestid', '') }}" required>
                            @if($errors->has('guestid'))
                                <span class="help-block" role="alert">{{ $errors->first('guestid') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('guest_name') ? 'has-error' : '' }}">
                            <label for="guest_name">{{ trans('global.guest_name') }}</label>
                            <input class="form-control" type="text" name="guest_name" id="guest_name" value="{{ old('guest_name', '') }}">
                            @if($errors->has('guest_name'))
                                <span class="help-block" role="alert">{{ $errors->first('guest_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('hostid') ? 'has-error' : '' }}">
                            <label for="hostid">{{ trans('global.hostid') }}</label>
                            <input class="form-control" type="number" name="hostid" id="hostid" value="{{ old('hostid', '') }}" step="1">
                            @if($errors->has('hostid'))
                                <span class="help-block" role="alert">{{ $errors->first('hostid') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('host_name') ? 'has-error' : '' }}">
                            <label for="host_name">{{ trans('global.host_name') }}</label>
                            <input class="form-control" type="text" name="host_name" id="host_name" value="{{ old('host_name', '') }}">
                            @if($errors->has('host_name'))
                                <span class="help-block" role="alert">{{ $errors->first('host_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('rating') ? 'has-error' : '' }}">
                            <label class="required" for="rating">{{ trans('global.rating') }}</label>
                            <input class="form-control" type="number" name="rating" id="rating" value="{{ old('rating', '') }}" step="1" required>
                            @if($errors->has('rating'))
                                <span class="help-block" role="alert">{{ $errors->first('rating') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                            <label for="message">{{ trans('global.message') }}</label>
                            <textarea class="form-control" name="message" id="message">{{ old('message') }}</textarea>
                            @if($errors->has('message'))
                                <span class="help-block" role="alert">{{ $errors->first('message') }}</span>
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