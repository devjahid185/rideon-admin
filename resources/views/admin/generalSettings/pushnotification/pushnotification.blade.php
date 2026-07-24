@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="row gap-2">
            <div class="col-md-3 settings_bar_gap">
                <div class="box box-info box_info">
                    <div class="">
                        <h4 class="all_settings f-18 mt-1" style="margin-left:15px;"> {{ trans('global.manage_settings') }}
                        </h4>
                        @include('admin.generalSettings.general-setting-links.links')
                    </div>
                </div>
            </div>


            <div class="col-md-9">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="display: inline-block; margin-right: 600px;">
                            {{ trans('global.Firebase_key') }}</h3><span class="email_status" style="display: none;">(<span
                                class="text-green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>)</span>
                        <div class="checkbox-container" style="display: inline-block; vertical-align: middle;">

                            <input class="check statusdata" type="checkbox" data-onstyle="success" id="firebase_status"
                                data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="Inactive" data-url=""
                                {{ $pushnotification_status != "" && $pushnotification_status == 'firebase' ? 'checked' : '' }}>
                            <label for="firebase_status" style="margin-left: 91%; margin-top: 8px;"
                                class="checktoggle">checkbox</label>

                        </div>


                    </div>

                    <form id="general_form" method="POST" action="{{ route('admin.pushnotificationupdate') }}"
                        class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="form-group mt-3">
                            <label class="col-sm-3 control-label" for="firebase_project_id">{{ __('Firebase Project ID') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="firebase_project_id" id="firebase_project_id"
                                    placeholder="your-firebase-project-id" value="{{ $firebase_project_id ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="col-sm-3 control-label" for="firebase_service_account_json_path">{{ __('Service Account JSON Path') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="firebase_service_account_json_path"
                                    id="firebase_service_account_json_path" placeholder="firebase/service_account.json"
                                    value="{{ $firebase_service_account_json_path ?? 'firebase/service_account.json' }}">
                                <small class="text-muted d-block" style="margin-top: 6px;">
                                    {{ __('JSON file should exist in storage or public storage firebase folder.') }}
                                </small>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info btn-space">{{ trans('global.save') }}</button>
                        </div>
                    </form>
                </div>


                <!-- Box for sending user messages -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.push_notification') }}</h3>
                    </div>

                    <form  method="POST" action="{{ route('admin.sendusermessage') }}"
                        class="form-horizontal user_message_form" enctype="multipart/form-data" novalidate="novalidate">
                        {{ csrf_field() }}
                           
                        <div class="form-group mt-3"> <input type="hidden" name='user_type' value="user" />
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.user') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control select2" name="userid_id" id="userid_id" required>
                                    <option value="All">All</option>
                                    @foreach($userids as $id => $namePhoneEmail)
                                        <option value="{{ $id }}" {{ old('userid_id') == $id ? 'selected' : '' }}>
                                            {{ $namePhoneEmail }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.subject') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="subject" id="subjectwizard_subject"
                                    placeholder="subject" value="{{ old('subject') }}">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.message') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="message" id="messagewizard_message"
                                    placeholder="message" rows="5">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info btn-space">{{ trans('global.send_message') }}</button>
                        </div>
                    </form>
                </div>


                     <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.push_notification_drivers') }}</h3>
                    </div>

                    <form  method="POST" action="{{ route('admin.sendusermessage') }}"
                        class="form-horizontal user_message_form" enctype="multipart/form-data" novalidate="novalidate">
                        {{ csrf_field() }}
                        <input type="hidden" name='user_type' value="driver" />
                        <div class="form-group mt-3">
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.user') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control select2" name="userid_id" id="drivers" required>
                                    <option value="All">All</option>
                                    @foreach($drivers as $id => $namePhoneEmail)
                                        <option value="{{ $id }}" {{ old('drivers') == $id ? 'selected' : '' }}>
                                            {{ $namePhoneEmail }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.subject') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input class="form-control" type="text" name="subject" id="subjectwizard_subject"
                                    placeholder="subject" value="{{ old('subject') }}">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="inputEmail3">{{ trans('global.message') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="message" id="messagewizard_message"
                                    placeholder="message" rows="5">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info btn-space">{{ trans('global.send_message') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('admin.generalSettings.toastermsgDemo')
@section('scripts')

    <script>

        $(document).ready(function () {
            $('#general_form').on('submit', function (event) {
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
                        if (response.status === 403) {
                            var response = JSON.parse(response.responseText);
                            toastr.error(response.error, 'Error', {
                                CloseButton: true,
                                ProgressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        } else {
                            // General error handling
                            toastr.error(response.error, 'Error', {
                                CloseButton: true,
                                ProgressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        }
                    }
                });
            });

            $('.user_message_form').on('submit', function (event) {
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
                        if (response.status === 403) {
                            var response = JSON.parse(response.responseText);
                            toastr.error(response.error, 'Error', {
                                CloseButton: true,
                                ProgressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        } else {
                            // General error handling
                            toastr.error(response.error, 'Error', {
                                CloseButton: true,
                                ProgressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        }
                    }
                });
            });

            $('#firebase_status').change(function () {
                updateCheckboxStatus('firebase', $(this).prop('checked'));
            });

            // Function to update checkbox status via AJAX
            function updateCheckboxStatus(type, isChecked) {

                var url = "{{ route('admin.updatePushNotificationStatus') }}";

                // Make AJAX call to update backend
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { type: type },
                    dataType: 'json', // Ensure expected response type
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token for Laravel security
                    },
                    success: function (response) {


                        if (response.success) {
                            toastr.success(response.success, 'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        } else {
                            toastr.error(response.error, 'Error', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        }
                    },
                    error: function (xhr, error) {
                        // console.error('Error updating ' + type +' url ' +url+ ' status:', error);

                        toastr.error(response.error, 'Error', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });

                        // Handle error response if needed
                    }
                });
            }

        }); // onload end
    </script>

@endsection
