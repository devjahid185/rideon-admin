@extends('vendor.layout')
@section('content')
<section class="content">
            <div class="row gap-2">
            @include('vendor.vehicles.vehicle_left_menu')

                
                <div class="col-md-9">
                    <form id="baseUpdate">
                        @csrf
                        <input type="hidden" name="id" value="{{$id}}">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                    <!-- <p class="mb-0 f-18 mt-1">{{trans('global.what_kinds_of_car_are_you_listing')}}</p> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="label-large fw-bold">{{trans('global.what_kinds_of_car_are_you_listing')}} <span class="text-danger">*</span></label>
                                        <select name="car_type" id="car_type" data-saving="basics1" class="form-control f-14">
                                            <option value="">Please Select </option>
                                            @foreach($vehicleTypeData as $vehicleTypeData1)
                                                <option value="{{ $vehicleTypeData1->id }}"
                                                    {{ $YoulistingData ? '' : ($vehicleTypeData1->id == $vehicleData->item_type_id ? 'selected' : '') }}
                                                >{{ $vehicleTypeData1->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error-message text-danger" id="base-car_type"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="label-large fw-bold">{{trans('global.make')}} <span class="text-danger">*</span></label>
                                        <select name="make" id="vehicleMakeSelect" data-saving="basics1" class="form-control f-14">
                                            <option value="">Please Select </option>
                                          
                                        </select>
                                        <span class="error-message text-danger" id="base-make"></span>
                                    </div>
                                 
                                    <div class="col-md-4">
                                        <label class="label-large fw-bold">{{trans('global.model')}} <span class="text-danger">*</span></label>
                                        <select name="model" id="vehicleModelSelect" data-saving="basics1" class="form-control f-14">
                                        <option value="" >Please Select </option>
                                     
                                              </select>
                                              <span class="error-message text-danger" id="base-model"></span>
                                    </div>
                                    
                                </div>
                              
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="label-large fw-bold mt-4 ">{{trans('global.year')}} <span class="text-danger">*</span></label>
                                        <select name="year" id="basics-select-bathrooms" data-saving="basics1" class="form-control f-14">
                                            <option value="">Please Select </option>
                                            @php
                                                $currentYear = date('Y');
                                            @endphp

                                            @for ($i = $currentYear; $i >= $currentYear - 30; $i--)
                                                <option value="{{ $i }}"
                                                {{ (int) $i === (int) $YearData ? 'selected' : '' }}
                                                >{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <span class="error-message text-danger" id="base-year"></span>
                                    </div>
                                  
                               

                                    <div class="col-md-4">
                                        <label class="label-large fw-bold mt-4">{{trans('global.transmission')}}</label>
                                   
                                        <select name="transmission" id="basics-select-bathrooms" data-saving="basics1" class="form-control f-14">
                                            <option value="Automatic" {{ $TransmissionData === 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                            <option value="SemiAutomtic" {{ $TransmissionData === 'SemiAutomtic' ? 'selected' : '' }}>SemiAutomtic</option>
                                            <option value="Mannual" {{ $TransmissionData === 'Mannual' ? 'selected' : '' }}>Mannual</option>
                                        </select>

                                    </div>

                                    <div class="col-md-4">
                                        <label class="label-large fw-bold mt-4">{{trans('global.odometer')}}</label>
                                        <select name="odometer" id="basics-select-bathrooms" data-saving="basics1" class="form-control f-14">
                                            <option value="">Please Select </option>
                                            @foreach($vehicleOdoMeterData as $vehicleOdoMeterData1)
                                                <option value="{{ $vehicleOdoMeterData1->id }}"
                                                {{ (int) $vehicleOdoMeterData1->id === (int) $OdometerData ? 'selected' : '' }}
                                                >{{ $vehicleOdoMeterData1->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="row" style=" margin-top: 16px; ">
                                        <div><div class="col-6  col-lg-6  text-left">
                                        <a data-prevent-default="" href="{{route('vendor.vehicles.index')}}" class="btn btn-large btn-primary f-14" >{{ trans('global.back') }}</a>
                                        </div>
                                        <div class="col-6  col-lg-6 text-right">
                                            <button type="button" class="btn btn-large btn-primary next-section-button text-white">{{ trans('global.next')}}</button>
                                        </div></div>
                                    </div>
                            </div>
                        </div>
                    </form>
                </div>
                <meta name="csrf-token" content="{{ csrf_token() }}">
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
        $(document).ready(function() {
    $('.text-white').click(function() {
        var id = {{$id}};
       
        $.ajax({
            type: 'POST',
            url: '{{ route('vendor.vehicles.base-Update') }}',
            data: $('#baseUpdate').serialize(),
            success: function(data) {
                $('.error-message').text(''); 
                window.location.href = '/vendor/vehicles/description/' + id;

            },
            error: function(response) {
                if (response.responseJSON && response.responseJSON.errors) {
                    var errors = response.responseJSON.errors;
                    $('.loaderText').hide();
                    // Reset error messages first
                    $('.error-message').text('');

                    // Then display new error messages
                    for (var field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            var errorMessage = errors[field][
                                0
                            ]; // get the first error message
                            $('#base-' + field).text(errorMessage);
                        }
                    }
                }
            }
        });
    });


});



    // $(document).ready(function() {
    //     $('#vehicleMakeSelect').on('change', function() {
    //         var makeId = $(this).val();
    //         $.ajax({
    //             url: '{{ route('admin.vehicles.get-vehicletype') }}',
    //             headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //             type: 'POST',
    //             data: { make: makeId },
    //             success: function(response) {
    //                 var modelSelect = $('#vehicleModelSelect');
    //                 modelSelect.empty();
    //                 modelSelect.append('<option value="">Please Select</option>'); // Add default option
    //                     response.forEach(function(item) {
    //                         modelSelect.append('<option value="' + item.id + '">' + item.model_name + '</option>');
    //                     });
    //             },
    //             error: function(xhr, status, error) {
    //                 // Handle errors here
    //             }
    //         });
    //     });
    // });

    $(document).ready(function () {
    // Function to load vehicle models based on the selected make
    function loadVehicleMake() {
        var typeId = $('#car_type').val();
       
        if (typeId) {
            $.ajax({
                url: '{{ route('vendor.vehicles.get-vehiclemake') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: { typeId: typeId },
                success: function (response) {
                    var modelSelect = $('#vehicleMakeSelect');
                    modelSelect.empty();
                    modelSelect.append('<option value="">Please Select</option>'); // Add default option
                    response.forEach(function (item) {
                        modelSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });

                    // Check if a selected model is stored in local storage and select it
                    var selectedModel = <?php echo json_encode(intval($MakeData ?? 0)); ?>;
                    console.log(selectedModel);
                    if (selectedModel) {
                        modelSelect.val(selectedModel);
                        loadVehicleModels();
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

    // Load models when the page loads or when the make select changes
    loadVehicleMake();

    $('#car_type').on('change', function () {
        loadVehicleMake();
       
    });

    // Store the selected model in local storage when the model select changes
    $('#vehicleMakeSelect').on('change', function () {
        var selectedModel = $(this).val();
        if (selectedModel) {
            localStorage.setItem('selectedModel', selectedModel);
        } else {
            localStorage.removeItem('selectedModel');
        }
    });
});


    // Function to load vehicle models based on the selected make
    function loadVehicleModels() {
        var makeId = $('#vehicleMakeSelect').val();
        if (makeId) {
            $.ajax({
                url: '{{ route('vendor.vehicles.get-vehicletype') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: { make: makeId },
                success: function (response) {
                    var modelSelect = $('#vehicleModelSelect');
                    modelSelect.empty();
                    modelSelect.append('<option value="">Please Select</option>'); // Add default option
                    response.forEach(function (item) {
                        modelSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });

                    // Check if a selected model is stored in local storage and select it
                    var selectedModel = {{$ModelData ?? 0}};
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
            $('#vehicleModelSelect').empty();
        }
    }

    // Load models when the page loads or when the make select changes
    //loadVehicleModels();

    $('#vehicleMakeSelect').on('change', function () {
        loadVehicleModels();
    });

    // Store the selected model in local storage when the model select changes
    $('#vehicleModelSelect').on('change', function () {
        var selectedModel = $(this).val();
        if (selectedModel) {
            localStorage.setItem('selectedModel', selectedModel);
        } else {
            localStorage.removeItem('selectedModel');
        }
    });



</script>

@endsection