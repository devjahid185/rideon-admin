@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.create') }} {{ trans('global.cancellationPolicies_title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.cancellation.policies.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('global.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('cancellation_time') ? 'has-error' : '' }}">
                            <label class="required" for="cancellation_time">Cancellation Time</label>
                            <select class="form-control" id="cancellation_time" name="cancellation_time">
                            <option value="" disabled {{ old('cancellation_time') == '' ? 'selected' : '' }}>Select a time</option>
                                <option value="12" {{ old('cancellation_time') == '0' ? 'selected' : '' }}>Within 12 hrs</option>
                                <option value="24" {{ old('cancellation_time') == '1' ? 'selected' : '' }}>Within 12 hrs to 24 hrs</option>
                                <option value="48" {{ old('cancellation_time') == '2' ? 'selected' : '' }}>Before 24 hrs</option>
                              
                            </select>
                            @if($errors->has('cancellation_time'))
                                <span class="help-block" role="alert">{{ $errors->first('cancellation_time') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label class="required" for="description">{{ trans('global.description') }}</label>
                            <textarea class="form-control" name="description" id="description" required>{{ old('description', '') }}</textarea>
                            @if($errors->has('description'))
                                <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                        
                        <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                            <label class="required" for="type">{{ trans('global.cancellationPolicies_type') }}</label>
                            <select class="form-control " id="type" name="type">
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percent</option>
                            <option value="none">None</option>
                        </select>
                            @if($errors->has('type'))
                                <span class="help-block" role="alert">{{ $errors->first('type') }}</span>
                            @endif
                         
                        </div>
                       
                        <div class="form-group {{ $errors->has('value') ? 'has-error' : '' }}">
                            <label class="required" for="value">{{ trans('global.amount') }}</label>
                            <input class="form-control" type="text" name="value" id="value" value="{{ old('value', '') }}" required>
                            @if($errors->has('value'))
                                <span class="help-block" role="alert">{{ $errors->first('value') }}</span>
                            @endif
                        </div>

                        {{-- <div class="form-group {{ $errors->has('module') ? 'has-error' : '' }}">
                            <label class="required" for="module">{{ trans('global.module') }}</label>
                            <select class="form-control " id="module" name="module">
                            <option value="1">Select Model</option>
                            <option value="2">Vehicle</option>
                            <option value="4">Boat</option>
                        <option value="5">Parking</option>
                        <option value="6">Avialable Product</option>
                        </select>
                            @if($errors->has('module'))
                                <span class="help-block" role="alert">{{ $errors->first('module') }}</span>
                            @endif
                         
                        </div> --}}
                        <div class="form-group {{ $errors->has('module') ? 'has-error' : '' }}">
                            <label class="required" for="module">{{ trans('global.module') }}</label>
                            <select class="form-control" id="module" name="module">
                                <option value="">Select Model</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('module'))
                                <span class="help-block" role="alert">{{ $errors->first('module') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required" for="status">{{ trans('global.status') }}</label>
                            <select class="form-control " id="status" name="status">
                            <option value="1">Active</option>
                        <option value="0">Inactive</option>
                        </select>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
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