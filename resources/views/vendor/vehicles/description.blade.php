@extends('vendor.layout')
@section('content')

<section class="content">
    <div class="row gap-2">
    @include('vendor.vehicles.vehicle_left_menu')

        <div class="col-md-9">
            <form id="descriptionFormupdate">
                @csrf
                <input type="hidden" name="id" value="{{ $id }}">

                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8 col-sm-12 col-xs-12 mb-15">
                                <label class="label-large fw-bold">{{ trans('global.listing_name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control f-14" value="{{ $itemData->title ?? '' }}" placeholder="" maxlength="100" required>
                                <span class="text-danger"></span>
                                <span class="error-message" id="descriptionerror-name"></span>
                            </div>

                            <div class="col-md-8 col-sm-12 col-xs-12 mb-15">
                                <label class="label-large fw-bold">{{ trans('global.summary')}} <span class="text-danger">*</span></label>
                                <textarea class="form-control f-14" name="summary" rows="6" placeholder="" required>{{ $itemData->description ?? '' }}</textarea>
                                <span class="text-danger"></span>
                                <span class="error-message" id="descriptionerror-summary"></span>
                            </div>
                            
                            <div class="col-md-8 col-sm-12 col-xs-12 mb-15">
                                <div class="form-group {{ $errors->has('userid') ? 'has-error' : '' }}">
                                    <label for="userid_id">{{ trans('global.userid') }}</label>
                                    <input type="hidden" name="userid_id" value="{{ old('userid_id', auth()->user()->id) }}">
                                    <select class="form-control select2" name="userid_id" id="userid_id" disabled required selected>
                                        @foreach($userids as $userid => $entry)
                                            <option value="{{ $userid }}" {{ (isset($itemData->userid_id) && $itemData->userid_id == $userid) ? 'selected' : '' }}>
                                                {{ $entry }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('userid'))
                                        <span class="help-block" role="alert">{{ $errors->first('userid') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php
                            $currentPath = request()->path();
                        @endphp

                        @if (str_contains($currentPath, 'admin/bookable/description'))
                            <div class="row mt-3">
                                <div class="col-md-8 col-sm-12 col-xs-12 mb20">
                                    <label class="label-large fw-bold">{{ trans('global.style_note')}} <span class="text-danger">*</span></label>
                                    <textarea class="form-control f-14" name="style_note" rows="6" placeholder="" required>{{ $styleNote->meta_value ?? '' }}</textarea>
                                    <span class="text-danger"></span>
                                    <span class="error-message" id="descriptionerror-style_note"></span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-8 col-sm-12 col-xs-12 mb20">
                                    <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                                        <label for="chrt-image">{{ trans('global.Other_image_to_upload') }}</label>
                                        <div class="needsclick dropzone" id="chart-dropzone"></div>
                                        @if($errors->has('image'))
                                            <span class="help-block" role="alert">{{ $errors->first('image') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <br>
                        <div class="row">
                            <div class="col-6 col-lg-6 text-left">
                                <a data-prevent-default href="{{ route('admin.vehicles.base', [$id]) }}" class="btn btn-large btn-primary f-14 backStep">{{ trans('global.back') }}</a>
                            </div>
                            <div class="col-6 col-lg-6 text-right">
                                <button type="button" class="btn btn-large btn-primary next-section-button nextStep">{{ trans('global.next') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.nextStep').click(function() {
        var id = {{ $id }};
        $.ajax({
            type: 'POST',
            url: '{{ route('vendor.vehicles.description-Update') }}',
            data: $('#descriptionFormupdate').serialize(),
            success: function(data) {
                $('.error-message').text('');
                window.location.href = '{{ $nextButton }}' + id;
            },
            error: function(response) {
                if (response.responseJSON && response.responseJSON.errors) {
                    var errors = response.responseJSON.errors;
                    $('.error-message').text('');

                    for (var field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            var errorMessage = errors[field][0];
                            $('#descriptionerror-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });
});
</script>

<script>
Dropzone.options.chartDropzone = {
    url: '{{ route('admin.vehicles.storeMedia') }}',
    maxFilesize: 2,
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
        $('form').find('input[name="chart_image"]').remove();
        $('form').append('<input type="hidden" name="chart_image" value="' + response.name + '">');
    },
    removedfile: function (file) {
        file.previewElement.remove();
        if (file.status !== 'error') {
            $('form').find('input[name="chart_image"]').remove();
            this.options.maxFiles = this.options.maxFiles + 1;
        }
    },
    init: function () {
        @if(isset($itemData) && $itemData->chart_image)
            var file = {!! json_encode($itemData->chart_image) !!}
            this.options.addedfile.call(this, file);
            this.options.thumbnail.call(this, file, file.preview ?? file.preview_url);
            file.previewElement.classList.add('dz-complete');
            $('form').append('<input type="hidden" name="chart_image" value="' + file.file_name + '">');
            this.options.maxFiles = this.options.maxFiles - 1;
        @endif
    },
    error: function (file, response) {
        var message = $.type(response) === 'string' ? response : response.errors.file;
        file.previewElement.classList.add('dz-error');
        var nodes = file.previewElement.querySelectorAll('[data-dz-errormessage]');
        nodes.forEach(node => node.textContent = message);
    }
}
</script>
@endsection