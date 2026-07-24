@extends('layouts.admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}?{{ time() }}">
@endsection
@section('content')
    <div class="content container-fluid">
        @include('admin.appUsers.driver.menu')

        <div class="driver-profile-page">
            <div class="profile-container">

                <div class="sections-container">
                    <div class="section">
                        <div class="avatar-section">


                            <div class="custom-toggle mb-5">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-red-600 flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle text-danger"></i> Ride Disabled
                                    </span>

                                    <label class="switch">
                                        <input type="checkbox" class="documentVerify" name="document_verify" {{
        $appUser->document_verify ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>

                                    <span class="text-sm font-bold text-green-600 flex items-center gap-1">
                                        <i class="fas fa-check-circle text-success"></i>Ride Enabled
                                    </span>
                                </div>
                            </div>




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
                                        'customer' => '',
                                        'host' => $appUser->id,
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
                        <h3 class="section-title">{{ trans('user.earnings') }}</h3>
                        <div class="vehicle-card grid-layout">
                            @php
                                $currency = $general_default_currency->meta_value ?? '';
                                $earnings = [
                                    ['label' => trans('user.today_earnings'), 'key' => 'today_earnings'],
                                    ['label' => trans('user.admin_commission'), 'key' => 'admin_commission'],
                                    ['label' => trans('user.driver_earnings'), 'key' => 'driver_earnings'],
                                    ['label' => trans('user.by_cash'), 'key' => 'cash_earnings'],
                                    ['label' => trans('user.by_card_online'), 'key' => 'online_earnings'],
                                ];
                            @endphp
                            @foreach ($earnings as $earning)
                                <div class="info-item">
                                    <span class="info-label">{{ $earning['label'] }}:</span>
                                    <span class="info-value">{{ number_format($data[$earning['key']] ?? 0, 2, '.', '') }}
                                        {{ $currency }}</span>
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
                                    [
                                        'label' => trans('user.email'),
                                        'value' => auth()->user()->can('app_user_contact_access')
                                            ? $appUser->email
                                            : maskEmail($appUser->email)
                                    ],
                                    [
                                        'label' => trans('user.mobile_number'),
                                        'value' => ($appUser->phone_country ?? '') . ' ' .
                                            (auth()->user()->can('app_user_contact_access')
                                                ? $appUser->phone
                                                : ($appUser->phone ? substr($appUser->phone, 0, -6) . str_repeat('*', 6) : '')
                                            )
                                    ],
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

                    <div class="section">
                        <h3 class="section-title">{{ trans('user.verification_status') }}</h3>
                        <div class="vehicle-card grid-layout">
                            @php
                                $verifications = [
                                    // ['label' => trans('user.email_verify'), 'status' => $appUser->email_verify, 'key' =>
                                    // 'email_verify'],
                                    // ['label' => trans('user.mobile_verification'), 'status' => $appUser->phone_verify, 'key' =>
                                    // 'phone_verify'],
                                    [
                                        'label' => trans('user.document_verification'),
                                        'status' => $appUser->document_verify,
                                        'key' =>
                                            'verified'
                                    ],
                                    [
                                        'label' => trans('user.vehicle_document_verification'),
                                        'status' => $data['vehicle_verified'] !==
                                            'N/A',
                                        'key' => 'vehicle_verified'
                                    ],
                                    [
                                        'label' => trans('user.is_driver_active'),
                                        'status' => $appUser->status != 0,
                                        'key' =>
                                            'is_driver_active'
                                    ],
                                ];
                            @endphp
                            @foreach ($verifications as $verification)
                                <div class="verification-item">
                                    <div class="verification-label">{{ $verification['label'] }}</div>
                                    <div class="{{ $verification['status'] ? 'verified-badge' : 'unverified-badge' }}">
                                        <i
                                            class="glyphicon {{ $verification['status'] ? 'glyphicon-ok' : 'glyphicon-remove' }}"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="section">
                        <h3 class="section-title">{{ trans('user.vehicle_information') }}</h3>
                        <div class="vehicle-card">
                            @php
                                $vehicleInfo = [
                                    ['label' => trans('vehicle.make'), 'key' => 'vehicle_make'],
                                    ['label' => trans('vehicle.model'), 'key' => 'vehicle_model'],
                                    ['label' => trans('vehicle.vehicle_number'), 'key' => 'vehicle_registration_number'],
                                    ['label' => trans('vehicle.vehicle_year'), 'key' => 'vehicle_year'],
                                ];
                            @endphp
                            @foreach ($vehicleInfo as $info)
                                <div class="info-item">
                                    <span class="info-label">{{ $info['label'] }}:</span>
                                    <span class="info-value">{{ $data[$info['key']] ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
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
                const isChecked = $(this).prop('checked');
                const value = isChecked ? 1 : 0;
                const id = $(this).data('id');
                const checkbox = $(this);

                Swal.fire({
                    title: 'Are You Sure?',
                    text: isChecked ? 'Are you sure you want to ENABLE this?' : 'Are you sure you want to DISABLE this?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, do it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const data = {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            pid: id
                        };
                        data[field] = value;

                        $.post(url, data, function (response) {
                            Swal.close(); // close loading
                            toastr.success(response.message, '{{ trans('global.success') }}', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        }).fail(function () {
                            Swal.close(); // close loading
                            toastr.error('Something went wrong. Please try again.');
                            checkbox.prop('checked', !isChecked); // revert toggle
                        });
                    } else {
                        checkbox.prop('checked', !isChecked); // revert if cancelled
                    }
                });
            });
        }

        setupToggle('.documentVerify', 'document_verify', '/admin/driver/account/documentVerify/{{ $appUser->id }}');
    </script>

@endsection