@extends('layouts.admin')

@section('content')
    @php $currentDate = date('Y-m-d'); @endphp
    <div class="content dashboard-content">
        <div class="container-fluid">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('status') }}
                </div>
            @endif
@if($installerWarning)
    <div class="alert alert-danger">
        ⚠ Installer files detected!
        <br>
        <strong>Your system is not secure.</strong>
        <br><br>
        <a href="{{ route('admin.deleteInstaller') }}"
           class="btn btn-danger btn-sm">
           🔥 Delete Installer Files Automatically
        </a>
    </div>
@endif

            @php
                $metricIcons = [
                    'total_drivers' => 'ion-model-s',
                    'total_requested_drivers' => 'ion-person-add',
                    'total_active_drivers' => 'ion-checkmark-round',
                    'total_riders' => 'ion-person-stalker',
                    'today_new_riders' => 'ion-person-add',
                    'total_income' => 'ion-cash',
                    'total_revenue' => 'ion-social-usd',
                    'today_revenue' => 'ion-social-usd-outline',
                    'today_running_rides' => 'ion-android-bicycle',
                    'today_completed_rides' => 'ion-checkmark-circled',
                    'running_rides' => 'ion-load-d',
                    'completed_rides' => 'ion-checkmark-circled',
                    'cancelled_rides' => 'ion-close-circled',
                    'rejected_rides' => 'ion-close',
                ];
            @endphp
            <div class="row">
                @foreach(['total_drivers', 'total_requested_drivers'] as $key)

                    @php

                        $queryParams = match ($key) {
                            'total_active_drivers' => ['status' => '1'],
                            'total_requested_drivers' => ['host_status' => '2'],
                            default => [],
                        };
                    @endphp
                    <div class="col-sm-6 col-md-3">
                        <div class="dashboard-card drivers-card text-center">
                            <i class="icon {{ $metricIcons[$key] ?? 'ion-ios-analytics' }} fs-30"></i>
                            <h4>{{ number_format($metrics[$key]['total_number']) }}</h4>
                            <p>{{ trans('dashboard.' . $key) }}</p>
                            <a href="{{ route('admin.drivers.index', $queryParams) }}" class="card-link">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach


                @foreach(['total_riders'] as $key)

                    @php
                        $queryParams = match ($key) {
                            'total_active_riders' => ['status' => '1'],
                            'today_new_riders' => ['from' => $currentDate, 'to' => $currentDate],
                            default => [],
                        };
                    @endphp
                    <div class="col-sm-6 col-md-3">
                        <div class="dashboard-card drivers-card">
                            <i class="icon {{ $metricIcons[$key] ?? 'ion-ios-analytics' }} fs-30"></i>
                            <h4>{{ number_format($metrics[$key]['total_number']) }}</h4>
                            <p>{{ trans('dashboard.' . $key) }} </p>
                            <a href="{{ route('admin.app-users.index', $queryParams) }}" class="card-link">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
                @foreach(['today_running_rides', 'today_completed_rides'] as $key)
                    @php
                        $queryParams = match ($key) {
                            'completed_rides' => ['status' => 'completed'],
                            'running_rides' => ['status' => 'ongoing'],
                            'cancelled_rides' => ['status' => 'cancelled'],
                            'rejected_rides' => ['status' => 'rejected'],
                            'today_running_rides' => [  'status' => 'ongoing','from' => $currentDate, 'to' => $currentDate],
                            'today_completed_rides' => [  'status' => 'completed','from' => $currentDate, 'to' => $currentDate]
                        };
                    @endphp
                    <div class="col-sm-6 col-md-3">
                        <div class="dashboard-card drivers-card">
                            <i class="icon {{ $metricIcons[$key] ?? 'ion-ios-analytics' }} fs-30"></i>
                            <h4>{{ number_format($metrics[$key]['total_number']) }}</h4>
                            <p>{{ trans('dashboard.' . $key) }}</p>

                            <a href="{{ route('admin.bookings.index', $queryParams) }}" class="card-link">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
                @foreach(['total_income', 'total_revenue', 'today_revenue'] as $key)
                    @php
                        $queryParams = match ($key) {
                            'total_revenue' => [],
                            'today_revenue' => ['from' => $currentDate, 'to' => $currentDate],
                            'total_income' => [],
                        };
                    @endphp
                    <div class="col-sm-6 col-md-3">
                        <div class="dashboard-card drivers-card">
                            <i class="icon {{ $metricIcons[$key] ?? 'ion-ios-analytics' }} fs-30"></i>
                            <h4>{{ number_format($metrics[$key]['total_number'], 2) }} {{  ($currency->meta_value ?? '') }}</h4>
                            <p>{{ trans('dashboard.' . $key) }}</p>

                            <a href="{{ route('admin.finance', $queryParams) }}" class="card-link">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach


            </div>



            <div class="row">

                <div class="col-md-12">
                    <div class="panel panel-default dashboard-panel">
                        <div class="panel-heading clearfix">
                            <h4 class="pull-left">{{ trans('dashboard.latest_rides') }}</h4>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary pull-right">
                                {{ __('See All') }}
                            </a>
                        </div>

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('dashboard.id') }}</th>
                                            <th>{{ trans('dashboard.service_type') }}</th>
                                            <th>{{ trans('dashboard.ride_date') }}</th>
                                            <th>{{ trans('dashboard.driver') }}</th>
                                            <th>{{ trans('dashboard.rider') }}</th>

                                            <th>{{ trans('dashboard.total') }}</th>
                                            <th>{{ trans('dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestBookings as $entry)
                                                                        <tr>
                                                                            <td>
                                                                                <a
                                                                                    href="{{ route('admin.bookings.show', $entry->id) }}">{{ $entry->token }}</a>
                                                                            </td>
<td>
    {{ optional(optional($entry->extension)->serviceType)->name ?? '-' }}
</td>
                                                                            <td>{{ $entry->created_at ?? 'N/A' }}</td>
                                                                            <td>


                                                                                @if ($entry->host)
                                                                                    <a target="_blank"
                                                                                        href="{{ route('admin.driver.profile', ['driver_id' => $entry->host->id]) }}">
                                                                                        @if ($entry->host->profile_image)
                                                                                            <img src="{{ $entry->host->profile_image->getUrl('thumb') }}"
                                                                                                alt="Profile Image" class="img-circle">
                                                                                        @else
                                                                                            <img src="{{ asset('images/icon/userdefault.jpg') }}" class="img-circle"
                                                                                                alt="Default Image" style="display: inline-block;">
                                                                                        @endif
                                                                                        {{ $entry->host->first_name }} {{ $entry->host->last_name }}
                                                                                    </a>
                                                                                @else
                                                                                    <span>--</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($entry->user)
                                                                                    <a target="_blank"
                                                                                        href="{{route('admin.app-users.show', $entry->user->id)   }}">
                                                                                        @if ($entry->user->profile_image)
                                                                                            <img src="{{ $entry->user->profile_image->getUrl('thumb') }}"
                                                                                                class="img-circle">
                                                                                        @else
                                                                                            <img src="{{ asset('images/icon/userdefault.jpg') }}"
                                                                                                alt="Default Image" style="display: inline-block;">
                                                                                        @endif
                                                                                        {{ $entry->user->first_name ?? '' }}
                                                                                        {{ $entry->user->last_name ?? '' }}
                                                                                    </a>
                                                                                @else
                                                                                    <span>--</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ ($currency->meta_value ?? '') . ' ' . ($entry->total ?? 'N/A') }}</td>
                                                                            <td>
                                                                                <span class="badge
                                                                                                                                                                                                                                            {{ $entry->status === 'Pending' ? 'badge-primary' :
                                            ($entry->status === 'Cancelled' ? 'badge-danger' :
                                                ($entry->status === 'Approved' ? 'badge-success' :
                                                    ($entry->status === 'Declined' ? 'badge-warning' :
                                                        ($entry->status === 'deps' ? 'admin' : 'badge-info')))) }}">
                                                                                    {{ $entry->status ?? 'N/A' }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">{{ __('No entries found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row">
                <!-- Charts -->
                <div class="col-md-6">
                    <div class="panel panel-default dashboard-panel">
                        <div class="panel-heading">
                            <h4>{{ trans('dashboard.latestUsers') }}</h4>
                        </div>
                        <div class="panel-body">
                            <canvas id="chBarUsers" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default dashboard-panel">
                        <div class="panel-heading">
                            <h4>{{ trans('dashboard.latestBookings') }}</h4>
                        </div>
                        <div class="panel-body">
                            <canvas id="chLine" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script>
   window._charts = window._charts || {};
    window._charts.chBarUsers = null;
    window._charts.chLine = null;
 function getChartPalette() {
        var isDark = document.documentElement.classList.contains('dark-mode');

        var palettes = {
            light: {
                black: '#000000',
                darkGray: '#333333',
                gridLine: '#cccccc',
                backgroundLight: 'rgba(0, 0, 0, 0.05)',
                pointBackground: '#000000'
            },
            dark: {
                black: '#ffffff',
                darkGray: '#e6e6e6',
                gridLine: 'rgba(255, 255, 255, 0.15)',
                backgroundLight: 'rgba(255, 255, 255, 0.08)',
                pointBackground: '#ffffff'
            }
        };

        return isDark ? palettes.dark : palettes.light;
    }
    function renderDashboardCharts() {
        var colors = getChartPalette();

        var latestUsersData = @json($latestUsersData);
        var latestBookingsData = @json($latestBookingsData);

        var labelsUsers = latestUsersData.map(function (r) { return r.date; });
        var dataUsers = latestUsersData.map(function (r) { return r.count; });

        var labelsBookings = latestBookingsData.map(function (r) { return r.date; });
        var dataBookings = latestBookingsData.map(function (r) { return r.count; });

        if (window._charts.chBarUsers) {
            window._charts.chBarUsers.destroy();
            window._charts.chBarUsers = null;
        }

        if (window._charts.chLine) {
            window._charts.chLine.destroy();
            window._charts.chLine = null;
        }

        var chBarUsers = document.getElementById("chBarUsers");
        if (chBarUsers) {
            window._charts.chBarUsers = new Chart(chBarUsers, {
                type: 'bar',
                data: {
                    labels: labelsUsers,
                    datasets: [{
                        label: 'Users',
                        data: dataUsers,
                        backgroundColor: document.documentElement.classList.contains('dark-mode')
                            ? 'rgba(230, 230, 230, 0.25)'
                            : colors.darkGray,
                        borderColor: colors.black,
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: { display: false },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.4,
                            categoryPercentage: 0.5,
                            gridLines: { display: false },
                            ticks: { fontColor: colors.darkGray }
                        }],
                        yAxes: [{
                            ticks: { beginAtZero: true, fontColor: colors.darkGray },
                            gridLines: { color: colors.gridLine }
                        }]
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        var chLine = document.getElementById("chLine");
        if (chLine) {
            window._charts.chLine = new Chart(chLine, {
                type: 'line',
                data: {
                    labels: labelsBookings,
                    datasets: [{
                        data: dataBookings,
                        backgroundColor: colors.backgroundLight,
                        borderColor: colors.black,
                        borderWidth: 2,
                        pointBackgroundColor: colors.pointBackground,
                        pointBorderColor: document.documentElement.classList.contains('dark-mode')
                            ? 'rgba(0,0,0,0.6)'
                            : '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    legend: { display: false },
                    scales: {
                        xAxes: [{
                            gridLines: { display: false },
                            ticks: { fontColor: colors.darkGray }
                        }],
                        yAxes: [{
                            ticks: { beginAtZero: true, fontColor: colors.darkGray },
                            gridLines: { color: colors.gridLine }
                        }]
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderDashboardCharts();
    });

        /* Status toggle AJAX */
        $('.statusdata').change(function () {
            const status = $(this).prop('checked') ? 1 : 0;
            const id = $(this).data('id');
            const $toggle = $(this);
            const requestData = {
                status: status,
                pid: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '/admin/update-item-status',
                data: requestData,
                success: function (response) {
                    if (response.status === 200) {
                        toastr.success(response.message, '{{ trans("global.success") }}', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    } else {
                        toastr.error(response.message, 'Cannot update', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        $toggle.prop('checked', !status);
                    }
                },
                error: function () {
                    toastr.error('Something went wrong. Please try again.', '{{ trans("global.error") }}', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    $toggle.prop('checked', !status);
                }
            });
        });
    </script>

      <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endsection