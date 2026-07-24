@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}">
@endsection

@section('content')
    <div class="content container-fluid">
        @include('admin.appUsers.driver.menu')
        <div class="driver-profile-page">
            <div class="profile-container">
                <div class="row  mt-3 text-capitalize align-items-center">
                    <div class="col-md-6">
                        <h3 class="section-title mb-0">{{ trans('vehicle.vehicle_information') }}</h3>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <div class="custom-toggle inline-block">
                                <label class="switch">
                                    <input type="checkbox" data-id="{{ $vehicle->id }}" class="statusUpdate"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $vehicle->status == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <span class="toggle-label">{{ trans('vehicle.vehicle_verified') }}</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row  mt-3 coenr-capitalize">
                    <form id="itemUpdateForm">
                        @csrf
                        <input type="hidden" name="id" value="{{ $vehicle->id }}">
                        <div class="col-md-4">
                            <label class="fw-bold">{{ trans('vehicle.type') }} <span class="text-danger">*</span></label>
                            <select name="car_type" id="car_type" class="form-control">
                                <option value="">{{ trans('global.pleaseSelect') }}</option>
                                @foreach ($vehicleType as $type)
                                    <option value="{{ $type->id }}" {{ $vehicle->item_type_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error-message text-danger" id="base-car_type"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">{{ trans('vehicle.make') }} <span class="text-danger">*</span></label>
                            <select name="make" id="vehicleMakeSelect" class="form-control">
                                {{-- Populated via JS --}}
                            </select>
                            <span class="error-message text-danger" id="base-make"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">{{ trans('vehicle.model') }} <span class="text-danger">*</span></label>
                            <input type="text" name="model" id="vehicleModelSelect" value="{{ $vehicleModel }}"
                                class="form-control" />
                            <span class="error-message text-danger" id="base-model"></span>
                        </div>
                </div>
                <div class="row mt-3 coenr-capitalize">
                    <div class="col-md-6">
                        <label class="fw-bold">{{ trans('vehicle.vehicle_year') }} <span
                                class="text-danger">*</span></label>
                        <select name="year" class="form-control">
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            @php $currentYear = date('Y'); @endphp
                            @for ($i = $currentYear; $i >= $currentYear - 30; $i--)
                                <option value="{{ $i }}" {{ $vehicleYear == $i ? 'selected' : '' }}> {{ $i }}</option>
                            @endfor
                        </select>
                        <span class="error-message text-danger" id="base-year"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Vehicle Number</label>
                        <input type="text" name="registration_number" class="form-control" value="{{ $vehicleNumber }}">
                        <span class="error-message text-danger" id="base-registration_number"></span>
                    </div>
                </div>
                <div class="row g-3 coenr-capitalize">
                    <div class="col-md-4">
                        @php $frontImageMedia = $vehicle->getFirstMedia('front_image'); @endphp
                        <div class="form-group {{ $errors->has('front_image') ? 'has-error' : '' }}">
                            <label for="front_image" class="d-flex align-items-center">
                                {{ trans('vehicle.vehicle_image') }}
                                @if($frontImageMedia)
                                    <a href="{{ $frontImageMedia->getUrl() }}" target="_blank"
                                        title="{{ trans('vehicle.view_uploaded_image') }}" class="ml-2 text-decoration-none">
                                        <i class="fas fa-paperclip text-primary"></i>
                                    </a>
                                @endif
                            </label>
                            <div class="needsclick dropzone" id="front_image-dropzone"></div>
                            @error('front_image')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @php
                        $frontImageDocMedia = $vehicle->getFirstMedia('vehicle_registration_doc');
                        $insuranceDocMedia = $vehicle->getFirstMedia('vehicle_insurance_doc');
                    @endphp

                    <div class="col-md-4 ">
                        <div class="form-group {{ $errors->has('vehicle_registration_doc') ? 'has-error' : '' }}">
                            <label for="vehicle_registration_doc" class="d-flex align-items-center">
                                {{ trans('vehicle.vehicle_document') }}
                                @if($frontImageDocMedia)
                                    <a href="{{ $frontImageDocMedia->getUrl() }}" target="_blank"
                                        title="{{ trans('vehicle.view_uploaded_document') }}" class="ml-2 text-decoration-none">
                                        <i class="fas fa-paperclip text-primary"></i>
                                    </a>
                                @endif
                            </label>
                            <div class="needsclick dropzone" id="vehicle_registration_doc-dropzone"></div>
                            @error('vehicle_registration_doc')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('vehicle_insurance_doc') ? 'has-error' : '' }}">
                            <label for="vehicle_insurance_doc" class="d-flex align-items-center">
                                {{ trans('vehicle.vehicle_insurance') }}
                                @if($insuranceDocMedia)
                                    <a href="{{ $insuranceDocMedia->getUrl() }}" target="_blank"
                                        title="{{ trans('vehicle.view_uploaded_insurance') }}"
                                        class="ml-2 text-decoration-none">
                                        <i class="fas fa-paperclip text-primary"></i>
                                    </a>
                                @endif
                            </label>
                            <div class="needsclick dropzone" id="vehicle_insurance_doc-dropzone"></div>
                            @error('vehicle_insurance_doc')
                                <span class="help-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row  g-3 coenr-capitalize">
                    <div class="col-md-12 text-left">
                        <button class="btn btn-primary btn-lg w-100 py-3 saveVehicle" type="submit">
                            {{ __('global.Update') }}
                        </button>
                    </div>
                </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>   Dropzone.autoDiscover = false;
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[1, 'desc']],
            });
            let table = $('.datatable-Property:not(.ajaxTable)').DataTable({ buttons: dtButtons });

            $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });

            // Toggle Handlers (status, featured, verified)
            function setupToggle(selector, field, url) {
                $(selector).change(function () {
                    const value = $(this).prop('checked') ? 1 : 0;
                    const id = $(this).data('id');
                    const data = { _token: $('meta[name="csrf-token"]').attr('content'), pid: id };
                    data[field] = value;

                    $.post(url, data, function (response) {
                        toastr.success(response.message, '{{ trans('global.success') }}', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    });
                });
            }

            setupToggle('.statusUpdate', 'status', '/admin/update-item-status');
            setupToggle('.isfeatureddata', 'featured', '/admin/update-item-featured');
            setupToggle('.isvefifieddata', 'isverified', '/admin/update-item-verified');

            // Save Vehicle
            $('.saveVehicle').click(function () {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.vehicles.base-Update') }}',
                    data: $('#itemUpdateForm').serialize(),
                    success: function (response) {
                        $('.error-message').text('');
                        toastr.success(response.message, 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function (response) {
                        if (response.responseJSON?.errors) {
                            $('.error-message').text('');
                            $.each(response.responseJSON.errors, function (field, message) {
                                $('#base-' + field).text(message[0]);
                            });
                        }
                    }
                });
            });
            function loadVehicleMake() {
                var typeId = $('#car_type').val();

                if (typeId) {
                    $.ajax({
                        url: '{{ route('admin.vehicles.get-vehiclemake') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            typeId: typeId
                        },
                        success: function (response) {
                            var modelSelect = $('#vehicleMakeSelect');
                            modelSelect.empty();
                            modelSelect.append(
                                '<option value="">{{ trans('global.pleaseSelect') }}</option>'); // Add default option
                            response.forEach(function (item) {
                                modelSelect.append('<option value="' + item.id + '">' + item
                                    .name + '</option>');
                            });

                            // Check if a selected model is stored in local storage and select it
                            var selectedModel = <?php echo json_encode(intval($vehicleMake ?? 0)); ?>;
                            if (selectedModel) {
                                modelSelect.val(selectedModel);

                            }
                        },
                        error: function (xhr, status, error) {
                            // Handle errors here
                        }
                    });
                } else {
                    // Clear the model select if no make is selected
                    $('#vehicleMakeSelect').empty();
                }
            }
            // Vehicle Make Loader
            $('#car_type').change(loadVehicleMake);
            loadVehicleMake();

            // Dropzone Initializer Function
            function initDropzone(selector, inputName, existingFile = null) {
                new Dropzone(selector, {
                    url: '{{ route($storeMedia) }}',
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
                    success(file, response) {
                        $('form').find(`input[name="${inputName}"]`).remove();
                        $('form').append(`<input type="hidden" name="${inputName}" value="${response.name}">`);
                    },
                    removedfile(file) {
                        file.previewElement.remove();
                        if (file.status !== 'error') {
                            $('form').find(`input[name="${inputName}"]`).remove();
                            this.options.maxFiles++;
                        }
                    },
                    init() {
                        if (existingFile) {
                            const file = existingFile;
                            this.options.addedfile.call(this, file);
                            this.options.thumbnail.call(this, file, file.thumbnail ?? file.thumbnail);
                            file.previewElement.classList.add('dz-complete');
                            $('form').append(`<input type="hidden" name="${inputName}" value="${file.file_name}">`);
                            this.options.maxFiles--;
                        }
                    },
                    error(file, response) {
                        const message = $.type(response) === 'string' ? response : (response.errors?.file || 'Upload failed');
                        file.previewElement.classList.add('dz-error');
                        $(file.previewElement).find('[data-dz-errormessage]').text(message);
                    }
                });
            }

            Dropzone.autoDiscover = false;

            @if (isset($vehicle))
                initDropzone("#front_image-dropzone", 'front_image', {!! json_encode($vehicle->front_image) !!});
                initDropzone("#vehicle_registration_doc-dropzone", 'vehicle_registration_doc', {!! json_encode($vehicle->vehicle_registration_doc) !!});
                initDropzone("#vehicle_insurance_doc-dropzone", 'vehicle_insurance_doc', {!! json_encode($vehicle->vehicle_insurance_doc) !!});
            @else
                initDropzone("#front_image-dropzone", 'front_image');
                initDropzone("#vehicle_registration_doc-dropzone", 'vehicle_registration_doc');
                initDropzone("#vehicle_insurance_doc-dropzone", 'vehicle_insurance_doc');
            @endif
                                            });
    </script>
@endsection