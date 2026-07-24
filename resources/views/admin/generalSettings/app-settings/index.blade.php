@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="row gap-2">
        <div class="col-md-3 settings_bar_gap">
            <div class="box box-info box_info">
                <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ trans('global.app_settings') }}</h4>
                @include('admin.generalSettings.general-setting-links.links')
            </div>
        </div>

        <div class="col-md-9">
            <div class="box box-info">
                <form id="app_settings_form" method="POST" action="{{ route('admin.settings.app.update') }}"
                    class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.general_app_settings') }}</h3>
                    </div>

                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="firebase_update_interval">
                            {{ __('Minimum Firebase Location Update Time (seconds)') }} <span
                                class="text-danger">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="number" min="1" name="firebase_update_interval"
                                id="firebase_update_interval" value="{{ $firebase_update_interval ?? '' }}" required>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="location_accuracy_threshold">
                            {{ __('Location Accuracy Threshold (km)') }}
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="number" step="0.1" name="location_accuracy_threshold"
                                id="location_accuracy_threshold" value="{{ $location_accuracy_threshold ?? '' }}">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="background_location_interval">
                            {{ __('Background Location Update Interval (seconds)') }}
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="number" min="1" name="background_location_interval"
                                id="background_location_interval" value="{{ $background_location_interval ?? '' }}">
                        </div>
                    </div>

                    {{-- NEW FIELD: Driver Search Interval --}}
                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="driver_search_interval">
                            {{ __('How often to search for nearby drivers (seconds)') }}
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="number" min="1" name="driver_search_interval"
                                id="driver_search_interval" value="{{ $driver_search_interval ?? '' }}">
                        </div>
                    </div>

                    {{-- NEW FIELD: Show distance/time after pickup using Google --}}
                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="use_google_after_pickup">
                            {{ __('Use Google to show distance/time after pickup?') }}
                        </label>
                        <div class="col-sm-6">
                            <select class="form-control" name="use_google_after_pickup" id="use_google_after_pickup">
                                <option value="1" {{ (isset($use_google_after_pickup) && $use_google_after_pickup==1)
                                    ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ (isset($use_google_after_pickup) && $use_google_after_pickup==0)
                                    ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    {{-- NEW FIELD: Show distance/time before pickup using Google --}}
                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="use_google_before_pickup">
                            {{ __('Use Google to show distance/time before pickup?') }}
                        </label>
                        <div class="col-sm-6">
                            <select class="form-control" name="use_google_before_pickup" id="use_google_before_pickup">
                                <option value="1" {{ (isset($use_google_before_pickup) && $use_google_before_pickup==1)
                                    ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ (isset($use_google_before_pickup) && $use_google_before_pickup==0)
                                    ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="minimum_hits_time">
                            {{ __('Minimum Hits Time After Pickup the Driver (seconds)') }}
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control" type="number" min="1" name="minimum_hits_time"
                                id="minimum_hits_time" value="{{ $minimum_hits_time ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="col-sm-4 control-label" for="use_google_source_destination">
                            {{ __('Use Google to show distance from source to destination?') }}
                        </label>
                        <div class="col-sm-6">
                            <select class="form-control" name="use_google_source_destination"
                                id="use_google_source_destination">
                                <option value="1" {{ (isset($use_google_source_destination) &&
                                    $use_google_source_destination==1) ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ (isset($use_google_source_destination) &&
                                    $use_google_source_destination==0) ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-info btn-space">{{ trans('global.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#app_settings_form').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function (response) {
                    toastr.success(response.success, 'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                },
                error: function (response) {
                    toastr.error('Form submission is disabled in demo mode.', 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                }
            });
        });
    });
</script>
@endsection