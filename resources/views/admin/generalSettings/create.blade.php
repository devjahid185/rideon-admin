@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.generalSetting.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.general-settings.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('meta_key') ? 'has-error' : '' }}">
                            <label class="required" for="meta_key">{{ trans('cruds.generalSetting.fields.meta_key') }}</label>
                            <input class="form-control" type="text" name="meta_key" id="meta_key" value="{{ old('meta_key', '') }}" required>
                            @if($errors->has('meta_key'))
                                <span class="help-block" role="alert">{{ $errors->first('meta_key') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.generalSetting.fields.meta_key_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('meta_value') ? 'has-error' : '' }}">
                            <label class="required" for="meta_value">{{ trans('cruds.generalSetting.fields.meta_value') }}</label>
                            <input class="form-control" type="text" name="meta_value" id="meta_value" value="{{ old('meta_value', '') }}" required>
                            @if($errors->has('meta_value'))
                                <span class="help-block" role="alert">{{ $errors->first('meta_value') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.generalSetting.fields.meta_value_helper') }}</span>
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