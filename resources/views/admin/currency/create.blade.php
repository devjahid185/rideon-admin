@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('global.currency') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.currency.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('currency_name') ? 'has-error' : '' }}">
                            <label class="required" for="currency_name">{{ trans('global.currency') }} {{ trans('global.name') }}</label>
                            <input class="form-control" type="text" name="currency_name" id="currency_name" value="{{ old('currency_name', '') }}" required>
                          
                            
                        </div>
                        
                        <div class="form-group {{ $errors->has('currency_code') ? 'has-error' : '' }}">
                            <label class="required" for="currency_code">{{ trans('global.currency_code') }}</label>
                            <input class="form-control" type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', '') }}" required>
                           
                           
                        </div>
                        <div class="form-group {{ $errors->has('value_against_default_currency') ? 'has-error' : '' }}">
                            <label for="value_against_default_currency">{{ trans('global.value_against_default_currency') }}</label>
                            <input class="form-control" type="text" name="value_against_default_currency" id="value_against_default_currency" value="{{ old('value_against_default_currency', '') }}">
 
                        </div>

                        <div class="form-group {{ $errors->has('currency_symbol') ? 'has-error' : '' }}">
                            <label class="required" for="currency_symbol">{{ trans('global.currency_symbol') }}</label>
                            <input class="form-control" type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', '') }}">
 
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required">{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Modern\Currency::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                           
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

@section('scripts')
<script>
    Dropzone.options.frontImageDropzone = {
    url: '{{ route('admin.vehicles.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="front_image"]').remove()
      $('form').append('<input type="hidden" name="front_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="front_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($module) && $module->front_image)
      var file = {!! json_encode($module->front_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="front_image" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection