@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}?{{ time() }}">
@endsection
@section('content')
    <div class="content container-fluid">
        @include('admin.appUsers.rider.menu')
        <div class="driver-profile-page">
            <div class="profile-container">
                <div class="row g-3 text-capitalize align-items-center">
                    @php
                        $toggles = [
                            ['label' => __('user.profile_verify'), 'field' => 'status', 'value' => $appUser->status, 'class' => 'profileVerify'],
                            ['label' => __('user.email_verify'), 'field' => 'email_verify', 'value' => $appUser->email_verify, 'class' => 'emailVerify'],
                            ['label' => __('user.phone_verify'), 'field' => 'phone_verify', 'value' => $appUser->phone_verify, 'class' => 'phoneVerify']
                        ];
                    @endphp

                    @foreach($toggles as $toggle)
                        <div class="col-md-4 form-group">
                            <label for="{{ $toggle['field'] }}">{{ $toggle['label'] }}</label>
                            <div class="custom-toggle inline-block">
                                <label class="switch">
                                    <input type="checkbox" data-id="{{ $appUser->id }}" class="{{ $toggle['class'] }}"
                                        data-toggle="toggle" data-on="Active" data-off="InActive" {{ $toggle['value'] == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="sections-container">
                    <div class="section">
                        <div class="avatar-section">








                            @if ($appUser->profile_image)
                                <a href="{{ $appUser->profile_image->getUrl() }}" target="_blank">
                                    <img src="{{ $appUser->profile_image->getUrl('preview') }}" alt="Profile Image">
                                </a>
                            @else
                                <div class="avatar"></div>
                            @endif
                            <h1 class="profile-name">{{ $appUser->first_name }} {{ $appUser->last_name }}</h1>
                            <div class="profile-username">#{{ $appUser->id }}</div>


                        </div>
                    </div>

                    <div class="section">
                        <h3 class="section-title">{{ trans('user.ride_information') }}</h3>
                        <div class="vehicle-card grid-layout">
                            @php
                                $baseBookingUrl = url('admin/bookings');
                                $rideStats = [
                                    ['label' => trans('user.live_rides'), 'key' => 'live_rides', 'status' => 'ongoing'],
                                    ['label' => trans('user.cancelled_rides'), 'key' => 'cancelled_rides', 'status' => 'cancelled'],
                                    ['label' => trans('user.rejected_rides'), 'key' => 'rejected_rides', 'status' => 'rejected'],
                                    ['label' => trans('user.completed_rides'), 'key' => 'completed_rides', 'status' => 'completed'],
                                    ['label' => trans('user.total_rides'), 'key' => 'total_rides', 'status' => null],
                                ];
                            @endphp

                            @foreach ($rideStats as $stat)
                                @php
                                    $queryParams = [
                                        'from' => '',
                                        'to' => '',
                                        'customer' => $userId,
                                        'host' => '',
                                        'status' => $stat['status'],
                                        'btn' => ''
                                    ];
                                    $statUrl = $baseBookingUrl . '?' . http_build_query($queryParams);
                                @endphp

                                <div class="info-item">
                                    <span class="info-label">
                                        <a href="{{ $statUrl }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $stat['label'] }}:
                                        </a>
                                    </span>
                                    <span class="info-value">{{ $data[$stat['key']] }}</span>
                                </div>
                            @endforeach

                        </div>
                    </div>



                    <div class="section">
                        <h3 class="section-title">{{ trans('user.personal_information') }}</h3>
                        <div class="vehicle-card grid-layout">
                            @php
                                $personalInfo = [
                                    ['label' => trans('user.name'), 'value' => $appUser->first_name . ' ' . $appUser->last_name],
                                    ['label' => trans('user.email'), 'value' => $appUser->email],
                                    ['label' => trans('user.mobile_number'), 'value' => $appUser->phone_country . $appUser->phone],
                                    ['label' => trans('user.gender'), 'value' => $appUser->gender ?? trans('user.unknown')],
                                    ['label' => trans('user.regiter_date'), 'value' => $appUser->created_at->format('Y-m-d')],
                                ];
                            @endphp
                            @foreach ($personalInfo as $info)
                                <div class="info-item">
                                    <span class="info-label">{{ $info['label'] }}:</span>
                                    <span class="info-value">{{ $info['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>


                </div>
            </div>
        </div>
@endsection
    @section('scripts')
        <script>
            @parent

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

            setupToggle('.profileVerify', 'status', '/admin/driver/account/profileVerify/{{$appUser->id}}');
            setupToggle('.emailVerify', 'email_verify', '/admin/driver/account/emailVerify/{{$appUser->id}}');
            setupToggle('.documentVerify', 'document_verify', '/admin/driver/account/documentVerify/{{$appUser->id}}');
            setupToggle('.phoneVerify', 'phone_verify', '/admin/driver/account/phoneVerify/{{$appUser->id}}');
        </script>

    @endsection