@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.create') }} {{ trans('global.item_rule') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.item-rule.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('rule_name') ? 'has-error' : '' }}">
                            <label class="required" for="rule_name">{{ trans('global.rules') }} {{ trans('global.name') }}</label>
                            <input class="form-control" type="text" name="rule_name" id="rule_name" value="{{ old('rule_name', '') }}" required>
                            @if($errors->has('rule_name'))
                                <span class="help-block" role="alert">{{ $errors->first('rule_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('module') ? 'has-error' : '' }}">
                            <label class="required" for="module">{{ trans('global.module') }}</label>
                            <select class="form-control" id="module" name="module">
                                <option value="">Select Module</option>
                                @foreach($moduleData as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('module'))
                                <span class="help-block" role="alert">{{ $errors->first('module') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label class="required" for="description">{{ trans('global.status') }}</label>
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