@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('global.location') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="module" value="{{$module}}">
                        <div class="form-group {{ $errors->has('city_name') ? 'has-error' : '' }}">
                            <label class="required" for="city_name">{{ trans('global.location_name') }}</label>
                            <input class="form-control" type="text" name="city_name" id="city_name" value="{{ old('city_name', '') }}" onchange="fetchLatLong()" required>
                            @if($errors->has('city_name'))
                                <span class="help-block" role="alert">{{ $errors->first('city_name') }}</span>
                            @endif
                          
                        </div>
                        <div class="form-group {{ $errors->has('country_code') ? 'has-error' : '' }}">
                    <label class="label-large fw-bold mt-4">{{ trans('global.country')}} <span class="text-danger">*</span></label>
                    <select id="country_code" name="country_code" class="form-control f-14" onchange="fetchLatLong()">
                        <option value="">Select Country</option>
                        @foreach ($countries as $code => $name)
                            <option value="{{ $code }}" {{ old('country_code') === $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('country_code'))
                        <span class="help-block" role="alert">{{ $errors->first('country_code') }}</span>
                    @endif
                </div>

                        <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                            <label for="image">{{ trans('global.image') }}</label>
                            <div class="needsclick dropzone" id="image-dropzone">
                            </div>
                            @if($errors->has('image'))
                                <span class="help-block" role="alert">{{ $errors->first('image') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label for="description">{{ trans('global.description') }}</label>
                            <textarea class="form-control ckeditor" name="description" id="description">{!! old('description') !!}</textarea>
                            @if($errors->has('description'))
                                <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('latitude') ? 'has-error' : '' }}">
                    <label class="required" for="latitude">{{ trans('global.latitude') }}</label>
                    <input class="form-control" type="text" name="latitude" id="latitude" value="{{ old('latitude', '') }}" required>
                    @if($errors->has('latitude'))
                        <span class="help-block" role="alert">{{ $errors->first('latitude') }}</span>
                    @endif
                </div>

                  <div class="form-group {{ $errors->has('longtitude') ? 'has-error' : '' }}">
                      <label class="required" for="longtitude">{{ trans('global.longtitude') }}</label>
                      <input class="form-control" type="text" name="longtitude" id="longtitude" value="{{ old('longtitude', '') }}" required>
                      @if($errors->has('longtitude'))
                          <span class="help-block" role="alert">{{ $errors->first('longtitude') }}</span>
                      @endif
                  </div>

                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label class="required">{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status" required>
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\City::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', '1') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
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

@section('scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $api_google_map_key->meta_value ?? '' }}&libraries=places&callback=initMap"></script>
<script>
    function fetchLatLong() {
        var city = document.getElementById('city_name').value;
        var country = document.getElementById('country_code').value;
        var address = city + ', ' + country; // Concatenate city and country
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longtitude = results[0].geometry.location.lng();
                document.getElementById('latitude').value = latitude;
                document.getElementById('longtitude').value = longtitude;
            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        });
    }
</script>


<script>
    Dropzone.options.imageDropzone = {
    url: '{{ route($storeMediaRoute) }}',
    maxFilesize: 5, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="image"]').remove()
      $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($city) && $city->image)
      var file = {!! json_encode($city->image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
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
                xhr.open('POST', '{{ route($storeCKEditorImageRoute) }}', true);
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
                data.append('crud_id', '{{ $city->id ?? 0 }}');
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


@endsection