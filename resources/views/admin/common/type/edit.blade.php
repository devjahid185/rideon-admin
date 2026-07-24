@extends('layouts.admin')
@section('content')
    <div class="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.edit') }} {{ $title }}
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route($updateRoute, [$ItemType->id]) }}" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label class="required" for="name">{{ trans('global.name') }}</label>
                                <input class="form-control" type="text" name="name" id="name"
                                    value="{{ old('name', $ItemType->name) }}" required>
                                @if ($errors->has('name'))
                                    <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                                <label for="description">{{ trans('global.description') }}</label>
                                <input class="form-control" type="text" name="description" id="description"
                                    value="{{ old('description', $ItemType->description) }}">
                                @if ($errors->has('description'))
                                    <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                                <label class="required">{{ trans('global.status') }}</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                                        {{ trans('global.pleaseSelect') }}</option>
                                    @foreach (App\Models\Modern\ItemType::STATUS_SELECT as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('status', $ItemType->status) === (string) $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('status'))
                                    <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('service_types') ? 'has-error' : '' }}">
                                <label class="required">Service Types</label>
                                <select class="form-control select2" name="service_types[]" multiple required>
                                    @foreach ($serviceTypes as $serviceType)
                                        <option value="{{ $serviceType->id }}"
                                            {{ in_array($serviceType->id, old('service_types', $ItemType->serviceTypes->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                            {{ $serviceType->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('service_types'))
                                    <span class="help-block">{{ $errors->first('service_types') }}</span>
                                @endif
                            </div>

                            <div id="max-weight-wrapper" class="form-group {{ $errors->has('max_weight') ? 'has-error' : '' }}" style="display:none;">
    <label class="required">Maximum Weight to Carry (KG)</label>
    <input type="number" step="0.01" class="form-control"
           name="max_weight"
           value="{{ old('max_weight', $ItemType->max_weight ?? '') }}">
    @if ($errors->has('max_weight'))
        <span class="help-block">{{ $errors->first('max_weight') }}</span>
    @endif
</div>


                            <!-- Line Break for better visual separation -->
                            <hr>

                            <!-- ItemCityFare fields -->
                            <div class="form-group row">

                                <div class="{{ $errors->has('recommended_fare') ? 'has-error' : '' }} col-lg-4">
                                    <label class="required" for="recommended_fare">Recommended Fare/KM</label>
                                    <input class="form-control" type="number" step="0.01" name="recommended_fare"
                                        id="recommended_fare"
                                        value="{{ old('recommended_fare', $ItemType->cityFare->recommended_fare ?? '') }}"
                                        required>
                                    @if ($errors->has('recommended_fare'))
                                        <span class="help-block"
                                            role="alert">{{ $errors->first('recommended_fare') }}</span>
                                    @endif
                                </div>

                                <div class="{{ $errors->has('admin_commission') ? 'has-error' : '' }} col-lg-4">
                                    <label class="required" for="admin_commission">Admin Commission (%)</label>
                                    <input class="form-control" type="number" step="0.01" name="admin_commission"
                                        id="admin_commission"
                                        value="{{ old('admin_commission', $ItemType->cityFare->admin_commission ?? '') }}"
                                        required>
                                    @if ($errors->has('admin_commission'))
                                        <span class="help-block"
                                            role="alert">{{ $errors->first('admin_commission') }}</span>
                                    @endif
                                </div>
                                <div class="form-group {{ $errors->has('min_fare') ? 'has-error' : '' }} col-lg-4">
                                    <label class="required" for="min_fare">Minimum Fare</label>
                                    <input class="form-control" type="number" step="0.01" name="min_fare" id="min_fare"
                                        value="{{ old('min_fare', $ItemType->cityFare->min_fare ?? '') }}" required>
                                    @if ($errors->has('min_fare'))
                                        <span class="help-block" role="alert">{{ $errors->first('min_fare') }}</span>
                                    @endif
                                </div>



                            </div>

                            {{--
                            <div class="form-group {{ $errors->has('max_fare') ? 'has-error' : '' }} col-lg-3">
                                <label class="required" for="max_fare">Maximum Fare</label>
                                <input class="form-control" type="number" step="0.01" name="max_fare" id="max_fare"
                                    value="{{ old('max_fare', $ItemType->cityFare->max_fare ?? '') }}" required>
                                @if ($errors->has('max_fare'))
                                <span class="help-block" role="alert">{{ $errors->first('max_fare') }}</span>
                                @endif
                            </div> --}}

                            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                                <label for="image">{{ trans('global.image') }}</label>
                                <div class="needsclick dropzone" id="image-dropzone"></div>
                                @if ($errors->has('image'))
                                    <span class="help-block" role="alert">{{ $errors->first('image') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
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
        Dropzone.options.imageDropzone = {
            url: '{{ route($storeMediaRoute) }}',
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
            success: function(file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($ItemType) && $ItemType->image)
                    var file = {!! json_encode($ItemType->image) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
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

        function toggleMaxWeight() {
        let selectedServices = $('select[name="service_types[]"]').val() || [];
        let deliveryServiceId = '2';

        if (selectedServices.includes(deliveryServiceId)) {
            $('#max-weight-wrapper').show();
        } else {
            $('#max-weight-wrapper').hide();
            $('#max-weight-wrapper input').val('');
        }
    }

    $(document).ready(function () {
        toggleMaxWeight();

        $('select[name="service_types[]"]').on('change', function () {
            toggleMaxWeight();
        });
    });
    </script>
@endsection
