@extends('layouts.admin')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: black;
}

</style>
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('global.vehicles') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.vehicles.update", [$property->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label class="required" for="title">{{ trans('global.title') }}</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $property->title) }}" required>
                            @if($errors->has('title'))
                                <span class="help-block" role="alert">{{ $errors->first('title') }}</span>
                            @endif
                     
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label for="description">{{ trans('global.description') }}</label>
                            <textarea class="form-control" name="description" id="description">{{ old('description', $property->description) }}</textarea>
                            @if($errors->has('description'))
                                <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                            @endif
                            
                        </div>
                        <div class="form-group {{ $errors->has('userid') ? 'has-error' : '' }}">
                            <label for="userid_id">{{ trans('global.userid') }}</label>
                            <select class="form-control select2" name="userid_id" id="userid_id">
                                @foreach($userids as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('userid_id') ? old('userid_id') : $property->userid->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('userid'))
                                <span class="help-block" role="alert">{{ $errors->first('userid') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('property_type') ? 'has-error' : '' }}">
                            <label for="property_type_id">{{ trans('global.property_type') }}</label>
                            <select class="form-control select2" name="property_type_id" id="property_type_id">
                                @foreach($property_types as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('property_type_id') ? old('property_type_id') : $property->property_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('property_type'))
                                <span class="help-block" role="alert">{{ $errors->first('property_type') }}</span>
                            @endif
                
                        </div>
                        <div style="display:none" class="form-group {{ $errors->has('bedrooms') ? 'has-error' : '' }}">
                            <label for="bedrooms">{{ trans('global.bedrooms') }}</label>
                            <input class="form-control" type="number" name="bedrooms" id="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" step="1">
                            @if($errors->has('bedrooms'))
                                <span class="help-block" role="alert">{{ $errors->first('bedrooms') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('beds') ? 'has-error' : '' }}">
                            <label for="beds">{{ trans('global.beds') }}</label>
                            <input class="form-control" type="number" name="beds" id="beds" value="{{ old('beds', $property->beds) }}" step="1">
                            @if($errors->has('beds'))
                                <span class="help-block" role="alert">{{ $errors->first('beds') }}</span>
                            @endif
                  
                        </div>
                        <div class="form-group {{ $errors->has('bathroom') ? 'has-error' : '' }}">
                            <label for="bathroom">{{ trans('global.bathroom') }}</label>
                            <input class="form-control" type="number" name="bathroom" id="bathroom" value="{{ old('bathroom', $property->bathroom) }}" step="1">
                            @if($errors->has('bathroom'))
                                <span class="help-block" role="alert">{{ $errors->first('bathroom') }}</span>
                            @endif
                            
                        </div>
                        <div style="display:none" class="form-group {{ $errors->has('bed_type') ? 'has-error' : '' }}">
                            <label for="bed_type_id">{{ trans('global.bed_type') }}</label>
                            <select class="form-control select2" name="bed_type_id" id="bed_type_id">
                                @foreach($bed_types as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('bed_type_id') ? old('bed_type_id') : $property->bed_type->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('bed_type'))
                                <span class="help-block" role="alert">{{ $errors->first('bed_type') }}</span>
                            @endif
                     
                        </div>
                        <div class="form-group {{ $errors->has('property_sqft') ? 'has-error' : '' }}">
                            <label for="property_sqft">{{ trans('global.property_sqft') }} 2</label>
                            <input class="form-control" type="text" name="property_sqft" id="property_sqft" value="{{ old('property_sqft', $property->property_sqft) }}">
                            @if($errors->has('property_sqft'))
                                <span class="help-block" role="alert">{{ $errors->first('property_sqft') }}</span>
                            @endif
                          
                        </div>
                        <div class="form-group {{ $errors->has('features') ? 'has-error' : '' }}">
                        <label for="features_id">{{ trans('global.features') }}</label>
                        <select class="form-control multipleaddselect select2" name="features_id[]" id="features" multiple>
                            @foreach($features as $id => $entry)
                                <option value="{{ $id }}" {{ in_array($id, $features_id) ? 'selected' : '' }}>{{ $entry }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('features'))
                            <span class="help-block" role="alert">{{ $errors->first('features') }}</span>
                        @endif
                      
                    </div>


                        <div style="display:none" class="form-group {{ $errors->has('property_rating') ? 'has-error' : '' }}">
                            <label for="property_rating">{{ trans('global.property_rating') }}</label>
                            <input class="form-control" type="number" name="property_rating" id="property_rating" value="{{ old('property_rating', $property->property_rating) }}" step="0.01">
                            @if($errors->has('property_rating'))
                                <span class="help-block" role="alert">{{ $errors->first('property_rating') }}</span>
                            @endif
                         
                        </div>
                        <div style="display:none" class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                            <label for="mobile">{{ trans('global.mobile') }}</label>
                            <input class="form-control" type="text" name="mobile" id="mobile" value="{{ old('mobile', $property->mobile) }}">
                            @if($errors->has('mobile'))
                                <span class="help-block" role="alert">{{ $errors->first('mobile') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('front_image') ? 'has-error' : '' }}">
                            <label for="front_image">{{ trans('global.front_image') }}</label>
                            <div class="needsclick dropzone" id="front_image-dropzone">
                            </div>
                            @if($errors->has('front_image'))
                                <span class="help-block" role="alert">{{ $errors->first('front_image') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('gallery') ? 'has-error' : '' }}">
                            <label for="gallery">{{ trans('global.gallery') }}</label>
                            <div class="needsclick dropzone" id="gallery-dropzone">
                            </div>
                            @if($errors->has('gallery'))
                                <span class="help-block" role="alert">{{ $errors->first('gallery') }}</span>
                            @endif
              
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Property::STATUS_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $property->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('person_allowed') ? 'has-error' : '' }}">
                            <label for="person_allowed">{{ trans('global.person_allowed') }}</label>
                            <input class="form-control" type="number" name="person_allowed" id="person_allowed" value="{{ old('person_allowed', $property->person_allowed) }}" step="1">
                            @if($errors->has('person_allowed'))
                                <span class="help-block" role="alert">{{ $errors->first('person_allowed') }}</span>
                            @endif
                    
                        </div>
                        <div style="display:none" class="form-group {{ $errors->has('accommodates') ? 'has-error' : '' }}">
                            <label for="accommodates">{{ trans('global.accommodates') }}</label>
                            <input class="form-control" type="number" name="accommodates" id="accommodates" value="{{ old('accommodates', $property->accommodates) }}" step="1">
                            @if($errors->has('accommodates'))
                                <span class="help-block" role="alert">{{ $errors->first('accommodates') }}</span>
                            @endif
                         
                        </div>
                        <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                            <label for="price">{{ trans('global.price') }}</label>
                            <input class="form-control" type="number" name="price" id="price" value="{{ old('price', $property->price) }}" step="0.01">
                            @if($errors->has('price'))
                                <span class="help-block" role="alert">{{ $errors->first('price') }}</span>
                            @endif
        
                        </div>
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address">{{ trans('global.address') }}</label>
                            <textarea class="form-control" name="address" id="address">{{ old('address', $property->address) }}</textarea>
                            @if($errors->has('address'))
                                <span class="help-block" role="alert">{{ $errors->first('address') }}</span>
                            @endif
            
                        </div>
                        <div class="form-group {{ $errors->has('place') ? 'has-error' : '' }}">
                            <label class="required" for="place_id">{{ trans('global.place') }}</label>
                            <select class="form-control select2" name="place_id" id="place_id" required>
                                @foreach($places as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('place_id') ? old('place_id') : $property->place->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('place'))
                                <span class="help-block" role="alert">{{ $errors->first('place') }}</span>
                            @endif
                           
                        </div>
                        <div class="form-group {{ $errors->has('state_region') ? 'has-error' : '' }}">
                            <label for="state_region">{{ trans('global.state_region') }}</label>
                            <input class="form-control" type="text" name="state_region" id="state_region" value="{{ old('state_region', $property->state_region) }}">
                            @if($errors->has('state_region'))
                                <span class="help-block" role="alert">{{ $errors->first('state_region') }}</span>
                            @endif
                   
                        </div>
                        <div class="form-group {{ $errors->has('zip_postal_code') ? 'has-error' : '' }}">
                            <label for="zip_postal_code">{{ trans('global.zip_postal_code') }}</label>
                            <input class="form-control" type="text" name="zip_postal_code" id="zip_postal_code" value="{{ old('zip_postal_code', $property->zip_postal_code) }}">
                            @if($errors->has('zip_postal_code'))
                                <span class="help-block" role="alert">{{ $errors->first('zip_postal_code') }}</span>
                            @endif
                       
                        </div>
                        <div class="form-group {{ $errors->has('latitude') ? 'has-error' : '' }}">
                            <label for="latitude">{{ trans('global.latitude') }}</label>
                            <input class="form-control" type="text" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
                            @if($errors->has('latitude'))
                                <span class="help-block" role="alert">{{ $errors->first('latitude') }}</span>
                            @endif
                  
                        </div>
                        <div class="form-group {{ $errors->has('longtitude') ? 'has-error' : '' }}">
                            <label for="longtitude">{{ trans('global.longtitude') }}</label>
                            <input class="form-control" type="text" name="longtitude" id="longtitude" value="{{ old('longtitude', $property->longtitude) }}">
                            @if($errors->has('longtitude'))
                                <span class="help-block" role="alert">{{ $errors->first('longtitude') }}</span>
                            @endif
                         
                        </div>
                        <div class="form-group {{ $errors->has('is_verified') ? 'has-error' : '' }}">
                            <label>{{ trans('global.is_verified') }}</label>
                            <select class="form-control" name="is_verified" id="is_verified">
                                <option value disabled {{ old('is_verified', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Property::IS_VERIFIED_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('is_verified', $property->is_verified) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('is_verified'))
                                <span class="help-block" role="alert">{{ $errors->first('is_verified') }}</span>
                            @endif
                   
                        </div>
                        <div class="form-group {{ $errors->has('is_featured') ? 'has-error' : '' }}">
                            <label>{{ trans('global.is_featured') }}</label>
                            <select class="form-control" name="is_featured" id="is_featured">
                                <option value disabled {{ old('is_featured', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Property::IS_FEATURED_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('is_featured', $property->is_featured) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('is_featured'))
                                <span class="help-block" role="alert">{{ $errors->first('is_featured') }}</span>
                            @endif
                    
                        </div>
                        <div class="form-group {{ $errors->has('weekly_discount') ? 'has-error' : '' }}">
                            <label for="weekly_discount">{{ trans('global.weekly_discount') }}</label>
                            <input class="form-control" type="text" name="weekly_discount" id="weekly_discount" value="{{ old('weekly_discount', $property->weekly_discount) }}">
                            @if($errors->has('weekly_discount'))
                                <span class="help-block" role="alert">{{ $errors->first('weekly_discount') }}</span>
                            @endif
                        
                        </div>
                        <div class="form-group {{ $errors->has('weekly_discount_type') ? 'has-error' : '' }}">
                            <label>{{ trans('global.weekly_discount_type') }}</label>
                            <select class="form-control" name="weekly_discount_type" id="weekly_discount_type">
                                <option value disabled {{ old('weekly_discount_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Property::WEEKLY_DISCOUNT_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('weekly_discount_type', $property->weekly_discount_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('weekly_discount_type'))
                                <span class="help-block" role="alert">{{ $errors->first('weekly_discount_type') }}</span>
                            @endif
                          
                        </div>
                        <div class="form-group {{ $errors->has('monthly_discount') ? 'has-error' : '' }}">
                            <label for="monthly_discount">{{ trans('global.monthly_discount') }}</label>
                            <input class="form-control" type="number" name="monthly_discount" id="monthly_discount" value="{{ old('monthly_discount', $property->monthly_discount) }}" step="1">
                            @if($errors->has('monthly_discount'))
                                <span class="help-block" role="alert">{{ $errors->first('monthly_discount') }}</span>
                            @endif
                   
                        </div>
                        <div class="form-group {{ $errors->has('monthly_discount_type') ? 'has-error' : '' }}">
                            <label>{{ trans('global.monthly_discount_type') }}</label>
                            <select class="form-control" name="monthly_discount_type" id="monthly_discount_type">
                                <option value disabled {{ old('monthly_discount_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Property::MONTHLY_DISCOUNT_TYPE_SELECT as $key => $label)
                                    <option value="{{ $key }}" {{ old('monthly_discount_type', $property->monthly_discount_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('monthly_discount_type'))
                                <span class="help-block" role="alert">{{ $errors->first('monthly_discount_type') }}</span>
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
    Dropzone.options.frontImageDropzone = {
    url: '{{ route('admin.properties.storeMedia') }}',
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
@if(isset($property) && $property->front_image)
      var file = {!! json_encode($property->front_image) !!}
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
<script>
    var uploadedGalleryMap = {}
Dropzone.options.galleryDropzone = {
    url: '{{ route('admin.properties.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
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
      $('form').append('<input type="hidden" name="gallery[]" value="' + response.name + '">')
      uploadedGalleryMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedGalleryMap[file.name]
      }
      $('form').find('input[name="gallery[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($property) && $property->gallery)
      var files = {!! json_encode($property->gallery) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="gallery[]" value="' + file.file_name + '">')
        }
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
<!-- Include Select2 JS at the end of the page -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    // Initialize Select2 on the features_id select element
    $(document).ready(function() {
        $('.multipleaddselect').select2({
            tags: true // Allow custom tags to be added
        });
    });
</script>
@endsection