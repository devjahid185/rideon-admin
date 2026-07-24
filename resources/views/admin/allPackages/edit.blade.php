@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('global.allPackage_title') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.all-packages.update", [$allPackage->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('package_name') ? 'has-error' : '' }}">
                            <label class="required" for="package_name">{{ trans('global.package_name') }}</label>
                            <input class="form-control" type="text" name="package_name" id="package_name" value="{{ old('package_name', $allPackage->package_name) }}" required>
                            @if($errors->has('package_name'))
                                <span class="help-block" role="alert">{{ $errors->first('package_name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.package_name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('package_total_day') ? 'has-error' : '' }}">
                            <label class="required" for="package_total_day">{{ trans('global.package_total_day') }}</label>
                            <input class="form-control" type="number" name="package_total_day" id="package_total_day" value="{{ old('package_total_day', $allPackage->package_total_day) }}" step="1" required>
                            @if($errors->has('package_total_day'))
                                <span class="help-block" role="alert">{{ $errors->first('package_total_day') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.package_total_day_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('package_price') ? 'has-error' : '' }}">
                            <label class="required" for="package_price">{{ trans('global.package_price') }}</label>
                            <input class="form-control" type="number" name="package_price" id="package_price" value="{{ old('package_price', $allPackage->package_price) }}" step="0.01" required>
                            @if($errors->has('package_price'))
                                <span class="help-block" role="alert">{{ $errors->first('package_price') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.package_price_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('package_description') ? 'has-error' : '' }}">
                            <label for="package_description">{{ trans('global.package_description') }}</label>
                            <textarea class="form-control ckeditor" name="package_description" id="package_description">{!! old('package_description', $allPackage->package_description) !!}</textarea>
                            @if($errors->has('package_description'))
                                <span class="help-block" role="alert">{{ $errors->first('package_description') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.package_description_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('package_image') ? 'has-error' : '' }}">
                            <label for="package_image">{{ trans('global.package_image') }}</label>
                            <div class="needsclick dropzone" id="package_image-dropzone">
                            </div>
                            @if($errors->has('package_image'))
                                <span class="help-block" role="alert">{{ $errors->first('package_image') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.package_image_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required">{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\AllPackage::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $allPackage->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.status_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('max_item') ? 'has-error' : '' }}">
                            <label class="required" for="max_item">{{ trans('global.max_item') }}</label>
                            <input class="form-control" type="number" name="max_item" id="max_item" value="{{ old('max_item', $allPackage->max_item) }}" step="1" required>
                            @if($errors->has('max_item'))
                                <span class="help-block" role="alert">{{ $errors->first('max_item') }}</span>
                            @endif
                            <span class="help-block">{{ trans('global.max_item_helper') }}</span>
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
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.all-packages.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $allPackage->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    Dropzone.options.packageImageDropzone = {
    url: '{{ route('admin.all-packages.storeMedia') }}',
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
      $('form').find('input[name="package_image"]').remove()
      $('form').append('<input type="hidden" name="package_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="package_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($allPackage) && $allPackage->package_image)
      var file = {!! json_encode($allPackage->package_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="package_image" value="' + file.file_name + '">')
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