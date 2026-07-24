@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.add') }} {{ trans('global.cancellationReason_title') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.cancellation.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('reason') ? 'has-error' : '' }}">
                            <label class="required" for="reason">{{ trans('global.reason') }}</label>
                            <input class="form-control" type="text" name="reason" id="reason" value="{{ old('reason', '') }}" required>
                            @if($errors->has('reason'))
                                <span class="help-block" role="alert">{{ $errors->first('reason') }}</span>
                            @endif
                        </div>
                        
                        <div class="form-group {{ $errors->has('user_type') ? 'has-error' : '' }}">
                            <label class="required" for="user_type">{{ trans('global.user_type') }}</label>
                            <!-- <input class="form-control" type="text" name="user_type" id="user_type" value="{{ old('user_type', '') }}" required> -->
                            <select class="form-control " id="user_type" name="user_type">
                            <option value="host">Host</option>
                        <option value="user">User</option>
                        </select>
                            @if($errors->has('user_type'))
                                <span class="help-block" role="alert">{{ $errors->first('user_type') }}</span>
                            @endif
                         
                        </div>

                        <div class="form-group {{ $errors->has('module_id') ? 'has-error' : '' }}">
                            <label class="required" for="module_id">{{ trans('global.module') }}</label>
                            <select class="form-control" id="module_id" name="module" required>
                                <option value="">Select Module</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('module_id'))
                                <span class="help-block" role="alert">{{ $errors->first('module_id') }}</span>
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