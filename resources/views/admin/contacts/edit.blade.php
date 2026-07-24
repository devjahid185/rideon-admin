@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.edit') }} {{ trans('global.contacttitle_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.contacts.update", [$contact->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                       
                        <div class="form-group {{ $errors->has('tittle') ? 'has-error' : '' }}">
                            <label class="required" for="tittle">{{ trans('global.title') }}</label>
                            <input class="form-control" type="text" name="tittle" id="tittle" value="{{ old('tittle', $contact->tittle) }}" required>
                            @if($errors->has('tittle'))
                                <span class="help-block" role="alert">{{ $errors->first('tittle') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label class="required" for="description">{{ trans('global.description') }}</label>
                            <input class="form-control date" type="text" name="description" id="description" value="{{ old('description', $contact->description) }}" required>
                            @if($errors->has('description'))
                                <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('userid') ? 'has-error' : '' }}">
                            <label for="userid_id">{{ trans('global.user') }}</label>
                            <select class="form-control select2" name="user" id="user">
                            <option value="" >Please Select</option>
                                @foreach($userids as $userid)
                                <option value="{{ $userid->id }}" {{ $contact->user == $userid->id ? 'selected' : '' }}>{{ $userid->first_name . '' .  $userid->last_name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('userid'))
                                <span class="help-block" role="alert">{{ $errors->first('userid') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label class="required" for="description">{{ trans('global.status') }}</label>
                            <select class="form-control " id="status" name="status">
                            <option value="1" {{  $contact->status == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{  $contact->status == '0' ? 'selected' : '' }}>Inactive</option>
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