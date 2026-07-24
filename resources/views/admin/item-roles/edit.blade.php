@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.edit') }} {{ trans('global.item_rule') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.item-rule.update", [$itemRoledata->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                       
                        <div class="form-group {{ $errors->has('rule_name') ? 'has-error' : '' }}">
                            <label class="required" for="rule_name">{{ trans('global.name') }}</label>
                            <input class="form-control" type="text" name="rule_name" id="rule_name" value="{{ old('rule_name', $itemRoledata->rule_name) }}" required>
                            @if($errors->has('rule_name'))
                                <span class="help-block" role="alert">{{ $errors->first('rule_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('module') ? 'has-error' : '' }}">
                            <label class="required" for="module">{{ trans('global.module') }}</label>
                            <select class="form-control" id="module" name="module">
                                <option value="">Select Module</option>
                                @foreach($moduleData as $module)
                                    <option value="{{ $module->id }}"{{ $itemRoledata->moduleGet->id == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('module'))
                                <span class="help-block" role="alert">{{ $errors->first('module') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required" for="status">{{ trans('global.status') }}</label>
                            <select class="form-control " id="status" name="status">
                            <option value="1" {{  $itemRoledata->status == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{  $itemRoledata->status == '0' ? 'selected' : '' }}>Inactive</option>
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