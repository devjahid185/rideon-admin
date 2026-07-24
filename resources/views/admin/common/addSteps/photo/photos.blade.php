@extends('layouts.admin')
@section('styles')
<style>
/* Dropzone preview element styling */
.dz-preview .dz-image {
    width: 100%;
    height: 0;
    padding-bottom: 75%; /* Adjust based on aspect ratio */
    position: relative;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    border-radius: 10px; /* Add rounded corners */
    overflow: hidden; /* Ensure the rounded corners are visible */
}

.dz-preview .dz-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain; /* Ensures the entire image is visible without distortion */
    display: block;
    border-radius: 10px; /* Add rounded corners */
}


</style>

@endsection
@section('content')
<section class="content">
    <div class="row gap-2">
        
        @include($leftSideMenu)
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-body">

                <form id="photoUpdateform"  enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" name="id" value="{{$id}}">
                        


                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                 
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('front_image') ? 'has-error' : '' }}">
                                                <label for="front_image">{{ trans('global.front_image') }}</label>
                                                <div class="needsclick dropzone" id="front_image-dropzone">
                                                </div>
                                                @if($errors->has('front_image'))
                                                    <span class="help-block" role="alert">{{ $errors->first('front_image') }}</span>
                                                @endif
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('front_image_doc') ? 'has-error' : '' }}">
                                                <label for="front_image_doc">Document</label>
                                                <div class="needsclick dropzone" id="front_image_doc-dropzone">
                                                </div>
                                                @if($errors->has('front_image_doc'))
                                                    <span class="help-block" role="alert">{{ $errors->first('front_image_doc') }}</span>
                                                @endif
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('gallery') ? 'has-error' : '' }}">
                                                <label for="gallery">{{ trans('global.gallery') }}</label>
                                                <div class="needsclick dropzone" id="gallery-dropzone">
                                                </div>
                                                @if($errors->has('gallery'))
                                                    <span class="help-block" role="alert">{{ $errors->first('gallery') }}</span>
                                                @endif
                                
                                            </div>
                                        </div>
                                      

                                    </div>
                                </div>
                            </div>

                        
                       
                    </form>

                    <div class="row">
                        
                            <div class="col-6  col-lg-6  text-left">
                                <a data-prevent-default="" href="{{route($backButtonRoute,[$id])}}"
                                    class="btn btn-large btn-primary f-14">{{trans('global.back')}}</a>
                            </div>
                            <div class="col-6  col-lg-6 text-right">
                                <button type="button"
                                    class="btn btn-large btn-primary next-section-button next">{{trans('global.next')}}</button>
                            </div>
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
    $('.next').click(function() {
        var id = {{$id}};
        $.ajax({
            type: 'POST',
            url: '{{ route($updatePhoto) }}',
            data: $('#photoUpdateform').serialize(),
            success: function(data) {
                $('.error-message').text(''); 
                window.location.href = '{{$nextButton}}' + id;
            },
            error: function(response) {
                if (response.responseJSON && response.responseJSON.errors) {
                    var errors = response.responseJSON.errors;
                    $('.error-message').text('');

                    // Then display new error messages
                    for (var field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            var errorMessage = errors[field][
                                0
                            ]; // get the first error message
                            $('#descriptionerror-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });


});
</script>
<!-- update image  -->
<script>
    Dropzone.options.frontImageDropzone = {
    url: '{{ route($storeMedia) }}',
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
@if(isset($itemData) && $itemData->front_image)
      var file = {!! json_encode($itemData->front_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.thumbnail ?? file.thumbnail)
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
    url: '{{ route($storeMedia) }}',
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
@if(isset($itemData) && $itemData->gallery)
      var files = {!! json_encode($itemData->gallery) !!}
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

<script>
    Dropzone.options.frontImageDocDropzone = {
    url: '{{ route($storeMedia) }}',
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
      $('form').find('input[name="front_image_doc"]').remove()
      $('form').append('<input type="hidden" name="front_image_doc" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="front_image_doc"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($itemData) && $itemData->front_image_doc)
      var file = {!! json_encode($itemData->front_image_doc) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.thumbnail ?? file.thumbnail)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="front_image_doc" value="' + file.file_name + '">')
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