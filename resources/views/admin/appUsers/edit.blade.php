@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('global.appUser_title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.app-users.update", [$appUser->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="host_status" value="{{ request()->input('host_status') }}">

                        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            <label class="required" for="first_name">{{ trans('global.first_name') }}</label>
                            <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name', $appUser->first_name) }}" required>
                            @if($errors->has('first_name'))
                                <span class="help-block" role="alert">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('middle') ? 'has-error' : '' }}">
                            <label for="middle">{{ trans('global.middle') }}</label>
                            <input class="form-control" type="text" name="middle" id="middle" value="{{ old('middle', $appUser->middle) }}">
                            @if($errors->has('middle'))
                                <span class="help-block" role="alert">{{ $errors->first('middle') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                            <label for="last_name">{{ trans('global.last_name') }}</label>
                            <input class="form-control" type="text" name="last_name" id="last_name" value="{{ old('last_name', $appUser->last_name) }}">
                            @if($errors->has('last_name'))
                                <span class="help-block" role="alert">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="required" for="email">{{ trans('global.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $appUser->email) }}" required>
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('phone_country') ? 'has-error' : '' }} row">
                                <!-- Phone Country Dropdown -->
                                <div class="col-md-2">
                                    <label for="phone_country">{{ trans('global.phone_country') }}</label>
                                    <select class="form-control" name="phone_country" id="phone_country" onchange="updateDefaultCountry()">
                                        @foreach (config('countries') as $country)
                                            <option value="{{ $country['dial_code'] }}"
                                                @if(old('phone_country', $appUser->phone_country) == $country['dial_code']) selected @endif>
                                                {{ $country['name'] }} ({{ $country['dial_code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('phone_country'))
                                        <span class="help-block" role="alert">{{ $errors->first('phone_country') }}</span>
                                    @endif
                                </div>

                                <!-- Default Country (hidden but still part of the form) -->
                                <div class="col-md-4" style="display:none;">
                                    <label for="default_country">{{ trans('global.default_country') }}</label>
                                    <input class="form-control" type="text" name="default_country" id="default_country" 
                                        value="{{ old('default_country', $appUser->default_country) }}">
                                    @if($errors->has('default_country'))
                                        <span class="help-block" role="alert">{{ $errors->first('default_country') }}</span>
                                    @endif
                                </div>

                                <!-- Phone Input Field -->
                                <div class="col-md-10">
                                    <label class="required" for="phone">{{ trans('global.phone') }}</label>
                                    <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $appUser->phone) }}" required>
                                    @if($errors->has('phone'))
                                        <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="required" for="password">{{ trans('global.password') }}</label>
                            <input class="form-control" type="password" name="password" id="password">
                            @if($errors->has('password'))
                                <span class="help-block" role="alert">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('profile_image') ? 'has-error' : '' }}">
                            <label for="profile_image">{{ trans('global.profile_image') }}</label>
                            <div class="needsclick dropzone" id="profile_image-dropzone">
                            </div>
                            @if($errors->has('profile_image'))
                                <span class="help-block" role="alert">{{ $errors->first('profile_image') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\AppUser::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $appUser->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('package') ? 'has-error' : '' }}">
                            <label for="package_id">{{ trans('global.package') }}</label>
                            <select class="form-control select2" name="package_id" id="package_id">
                                @foreach($packages as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('package_id') ? old('package_id') : $appUser->package->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('package'))
                                <span class="help-block" role="alert">{{ $errors->first('package') }}</span>
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
<script>
    Dropzone.options.profileImageDropzone = {
    url: '{{ route('admin.app-users.storeMedia') }}',
    maxFilesize: 1, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 1,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="profile_image"]').remove()
      $('form').append('<input type="hidden" name="profile_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="profile_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($appUser) && $appUser->profile_image)
      var file = {!! json_encode($appUser->profile_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="profile_image" value="' + file.file_name + '">')
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
    // Update the default_country field based on the phone_country selection
    function updateDefaultCountry() {
        var phoneCountryDialCode = document.getElementById("phone_country").value;
        var countries = @json(config('countries')); // Get the countries data as JSON
        var selectedCountry = countries.find(function(country) {
            return country.dial_code === phoneCountryDialCode;
        });

        // Update the default country input with the selected country code
        if (selectedCountry) {
            document.getElementById("default_country").value = selectedCountry.code;
        }
    }

    // Call the function on page load to set the default country based on the current selection
    document.addEventListener("DOMContentLoaded", function() {
        updateDefaultCountry();
    });
</script>
@endsection