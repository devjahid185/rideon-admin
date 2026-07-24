@extends('layouts.admin')

@section('content')
    <div class="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.edit') }} {{ trans('global.sos') }}
                    </div>

                    <div class="panel-body">
                        <form method="POST" action="{{ route('admin.sos.update', [$sos->id]) }}"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf

                            {{-- SOS Name --}}
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label class="required" for="name">{{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="name" id="name"
                                    value="{{ old('name', $sos->name) }}" required>
                                @if ($errors->has('name'))
                                    <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            {{-- SOS Phone Number --}}
                            <div class="form-group {{ $errors->has('sos_number') ? 'has-error' : '' }}">
                                <label class="required" for="sos_number">{{ trans('global.mobile') }}</label>
                                <input class="form-control" type="text" name="sos_number" id="sos_number"
                                    value="{{ old('sos_number', $sos->sos_number) }}" required>
                                @if ($errors->has('sos_number'))
                                    <span class="help-block" role="alert">{{ $errors->first('sos_number') }}</span>
                                @endif
                            </div>

                            {{-- SOS Description --}}
                            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                                <label for="description">{{ trans('global.description') }}</label>
                                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $sos->description) }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                                @endif
                            </div>

                            {{-- Status --}}
                            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                                <label class="required" for="status">{{ trans('global.status') }}</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" {{ $sos->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $sos->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @if ($errors->has('status'))
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
