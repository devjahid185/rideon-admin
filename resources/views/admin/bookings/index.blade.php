@extends('layouts.admin')
@section('content')
    @php
        $i = 0;
        $j = 0;
    @endphp
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-body">
                        <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET"
                            accept-charset="UTF-8" id="bookingFilterForm">
                            <div class="col-md-12 d-none">
                                <input class="form-control" type="hidden" id="startDate" name="from" value="">
                                <input class="form-control" type="hidden" id="endDate" name="to" value="">
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.date_range') }}</label>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control" autocomplete="off" id="daterange-btn">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>Service Type</label>
                                    <select class="form-control" name="service_type" id="service_type">
                                        <option value="">All Services</option>
                                        @foreach ($serviceTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ request('service_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.rider') }}</label>
                                    <select class="form-control select2" name="customer" id="rider">
                                        <option value="">{{ $searchCustomer }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.host') }}</label>
                                    <select class="form-control select2" name="host" id="host">
                                        <option value="">{{ $searchfield }}</option>
                                    </select>
                                </div>

                                @php
                                    $statuses = [
                                        'accepted' => trans('booking.booking_accepted'),
                                        'ongoing' => trans('booking.booking_running'),
                                        'cancelled' => trans('booking.booking_cancelled'),
                                        'rejected' => trans('booking.booking_rejected'),
                                        'completed' => trans('booking.booking_completed'),
                                    ];
                                    $selectedStatus = request()->input('status');
                                @endphp

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.booking_status') }}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="">{{ trans('booking.select_status') }}</option>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $selectedStatus === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex col-sm-2 col-xs-4 mt-4 mt-5 gap-2">

                                    <button type="submit" name="btn" class="btn btn-primary btn-flat">
                                        {{ trans('booking.filter') }}
                                    </button>
                                    <button type="reset" class="btn btn-primary btn-flat"
                                        onclick="window.location='{{ url()->current() }}'">
                                        {{ trans('booking.reset') }}
                                    </button>

                                </div>

                            </div>
                    </div>
                </div>
                </form>
            </div>
            @php
                $statuses = [
                    [
                        'key' => null,
                        'label' => trans('booking.booking_all'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => 'accepted',
                        'label' => trans('booking.booking_accepted'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => 'ongoing',
                        'label' => trans('booking.booking_running'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => 'completed',
                        'label' => trans('booking.booking_completed'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => 'cancelled',
                        'label' => trans('booking.booking_cancelled'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => 'rejected',
                        'label' => trans('booking.booking_rejected'),
                        'route' => 'admin.bookings.index',
                        'isTrash' => false,
                    ],
                    [
                        'key' => null,
                        'label' => trans('booking.booking_trash'),
                        'route' => 'admin.bookings.trash',
                        'isTrash' => true,
                    ],
                ];
                $currentQuery = request()->query();
            @endphp

            <div class="row" style="margin-left: 5px; margin-bottom: 6px;">
                <div class="col-lg-12">
                    @foreach ($statuses as $status)
                        @php
                            $isActive = false;
                            if ($status['isTrash']) {
                                $isActive = request()->routeIs($status['route']);
                            } else {
                                if (is_null($status['key'])) {
                                    $isActive = request()->routeIs($status['route']) && !request()->query('status');
                                } else {
                                    $isActive = request()->query('status') === $status['key'];
                                }
                            }
                            $class = $isActive ? 'btn btn-primary' : 'btn btn-inactive';
                            $routeParams = $currentQuery;
                            if ($status['isTrash']) {
                                $url = route($status['route']);
                            } else {
                                if (is_null($status['key'])) {
                                    unset($routeParams['status']);
                                } else {
                                    $routeParams['status'] = $status['key'];
                                }
                                $url = route($status['route'], $routeParams);
                            }

                            $countKey = $status['key'] ?? ($status['isTrash'] ? 'trash' : 'all');
                            $count = $statusCounts[$countKey] ?? 0;
                        @endphp

                        <a class="{{ $class }}" href="{{ $url }}">
                            {{ $status['label'] }}
                            <span class="badge badge-pill badge-primary">{{ $count > 0 ? $count : 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('booking.all_bookings') }}
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table-bordered table-striped table-hover datatable datatable-Booking table">
                                <thead>
                                    <tr>
                                        <th width="10"></th>
                                        <th>
                                            {{ trans('booking.reservation_code') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.driver') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.rider') }}
                                        </th>
                                        <th class="icon-header">
                                            {{ trans('booking.service_type') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.vehicle_information') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.pickup_location') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.destination') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.ride_fare') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.booking_date') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.booking_status') }}
                                        </th>
                                        <th>
                                            {{ trans('booking.payment_method') }} / {{ trans('booking.payment_status') }}
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $key => $booking)
                                        <tr data-entry-id="{{ $booking->id }}">
                                            <td>

                                            </td>
                                            <td>
                                                <a target="_blank" class="badge badge-pill badge-primary live-badge"
                                                    href="{{ route('admin.bookings.show', $booking->id) }}">
                                                    <i class="fas fa-database table-icon"></i>
                                                    {{ $booking->token }}
                                                </a>
                                                <span class="badge badge-pill badge-success live-badge">
                                                    <i class="fas fa-fire table-icon"></i>
                                                    {{ optional($booking->extension)->ride_id ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($booking->host)
                                                    <a target="_blank"
                                                        href="{{ route('admin.driver.profile', ['driver_id' => $booking->host->id]) }}">
                                                        @if ($booking->host->profile_image)
                                                            <img src="{{ $booking->host->profile_image->getUrl('thumb') }}"
                                                                alt="Profile Image" class="img-circle">
                                                        @else
                                                            <img src="{{ asset('images/icon/userdefault.jpg') }}"
                                                                class="img-circle" alt="Default Image"
                                                                style="display: inline-block;">
                                                        @endif
                                                        {{ $booking->host->first_name }} {{ $booking->host->last_name }}
                                                    </a>
                                                @else
                                                    <span>--</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($booking->user)
                                                    <a target="_blank"
                                                        href="{{ route('admin.app-users.show', $booking->user->id) }}">
                                                        @if ($booking->user->profile_image)
                                                            <img src="{{ $booking->user->profile_image->getUrl('thumb') }}"
                                                                class="img-circle">
                                                        @else
                                                            <img src="{{ asset('images/icon/userdefault.jpg') }}"
                                                                alt="Default Image" style="display: inline-block;">
                                                        @endif
                                                        {{ $booking->user->first_name ?? '' }}
                                                        {{ $booking->user->last_name ?? '' }}
                                                    </a>
                                                @else
                                                    <span>--</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-secondary">
                                                    {{ $booking->extension->serviceType->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="car-type-animated">
                                                <div>
                                                    <div><strong>Type:</strong>
                                                        {{ $booking->item->item_Type->name ?? '-' }}</div>
                                                    <div><strong>Make:</strong>
                                                        {{ $booking->item->vehicleMake->name ?? '-' }}</div>
                                                    <div><strong>Model:</strong> {{ $booking->item->model ?? '-' }}</div>
                                                    <div><strong>Number:</strong>
                                                        {{ strtoupper(optional($booking->item)->registration_number ?? '-') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pickup-address">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $booking->extension && is_array($booking->extension->pickup_location)
                                                    ? $booking->extension->pickup_location['address']
                                                    : '' }}
                                            </td>

                                            <td class="dropoff-address">
                                                <i class="fas fa-map-pin"></i>
                                                {{ $booking->extension && is_array($booking->extension->dropoff_location)
                                                    ? $booking->extension->dropoff_location['address']
                                                    : '' }}
                                            </td>
                                            <td>
                                                {{ ($booking->total ?? '') . ' ' . ($general_default_currency->meta_value ?? '') }}
                                            </td>

                                            <td>
                                                {{ $booking->created_at ? $booking->created_at->format('Y-m-d') : '' }}
                                            </td>
                                            @php
                                                $statusClasses = [
                                                    'Ongoing' => [
                                                        'class' => 'badge badge-pill label-secondary live-badge',
                                                        'label' => '<span class="live-dot"></span> Live',
                                                    ],
                                                    'Cancelled' => [
                                                        'class' => 'badge badge-pill label-danger',
                                                        'label' => 'Cancelled',
                                                    ],
                                                    'Accepted' => [
                                                        'class' => 'badge badge-pill label-success',
                                                        'label' => 'Accepted',
                                                    ],
                                                    'Approved' => [
                                                        'class' => 'badge badge-pill label-success',
                                                        'label' => 'Approved',
                                                    ],
                                                    'Declined' => [
                                                        'class' => 'badge badge-pill label-warning',
                                                        'label' => 'Declined',
                                                    ],
                                                    'Completed' => [
                                                        'class' => 'badge badge-pill label-info',
                                                        'label' => 'Completed',
                                                    ],
                                                    'Refunded' => [
                                                        'class' => 'badge badge-pill label-primary',
                                                        'label' => 'Refunded',
                                                    ],
                                                    'Confirmed' => [
                                                        'class' => 'badge badge-pill label-success',
                                                        'label' => 'Confirmed',
                                                    ],
                                                ];
                                            @endphp

                                            <td>
                                                @if (array_key_exists($booking->status, $statusClasses))
                                                    <span class="{!! $statusClasses[$booking->status]['class'] !!}">
                                                        {!! $statusClasses[$booking->status]['label'] !!}
                                                    </span>
                                                @else
                                                    {{ $booking->status }}
                                                @endif
                                            </td>

                                            <td>
                                                @php
                                                    $paymentMethod = strtolower($booking->payment_method) ?? '';
                                                    $badgeClass = match (strtolower($paymentMethod)) {
                                                        'cash' => 'badge badge-pill label-secondary',
                                                        'card',
                                                        'credit card',
                                                        'debit card'
                                                            => 'badge badge-pill label-primary',
                                                        'paypal' => 'badge badge-pill badge-info',
                                                        'stripe' => 'badge badge-pill label-warning',
                                                        'wallet' => 'badge badge-pill label-success',
                                                        default => 'badge badge-pill label-light',
                                                    };
                                                @endphp

                                                <div class="{{ $badgeClass }}">{{ ucfirst($paymentMethod) }}</div>
                                                @if ($booking->payment_status === 'paid')
                                                    <span class="badge badge-pill label-success">paid</span>
                                                @elseif ($booking->payment_status === 'notpaid')
                                                    <span class="badge badge-pill label-danger">notpaid</span>
                                                @endif

                                            </td>
                                            <td>
                                                @php
                                                    $isTrashPage = request()->is('admin/bookings/trash');
                                                @endphp
                                                <!-- Restore Form -->
                                                @if ($isTrashPage)
                                                    <form id="restore-form-{{ $booking->id }}"
                                                        action="{{ route('admin.bookings.restore', $booking->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        <button type="button" class="btn btn-xs btn-success restore-btn"
                                                            data-id="{{ $booking->id }}">
                                                            <i class="fa fa-undo" aria-hidden="true"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Permanent Delete Form -->
                                                    <form id="delete-form-{{ $booking->id }}"
                                                        action="{{ route('admin.bookings.permanentDelete', $booking->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        <button type="button"
                                                            class="btn btn-xs btn-danger permanent-delete"
                                                            data-id="{{ $booking->id }}">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    @can('booking_delete')
                                                        <button type="button"
                                                            class="btn btn-xs btn-danger delete-booking-button"
                                                            data-id="{{ $booking->id }}">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    @endcan

                                                    <!-- View Button (always shown) -->
                                                    <a target="_blank" class="badge badge-pill badge-primary live-badge"
                                                        href="{{ route('admin.bookings.show', $booking->id) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! admin_pagination($bookings, 3) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('admin.common.addSteps.footer.footerJs')
@section('scripts')
    @parent

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-booking-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-id');
                    Swal.fire({
                        title: '{{ trans('global.are_you_sure') }}',
                        text: '{{ trans('global.delete_confirmation') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            deleteBooking(bookingId);
                        }
                    });
                });
            });

            function deleteBooking(bookingId) {
                const url = `{{ route('admin.bookings.destroy', ':id') }}`.replace(':id', bookingId);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    success: function(response) {
                        Swal.close();
                        toastr.success('{{ trans('global.delete_success') }}', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        toastr.error('{{ trans('global.deletion_error') }}', 'Error', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        console.error(error);
                    }
                });
            }
        });

        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            function handleDeletion(route) {
                return function(e, dt, node, config) {
                    var ids = $.map(dt.rows({
                        selected: true
                    }).nodes(), function(entry) {
                        return $(entry).data('entry-id');
                    });

                    if (ids.length === 0) {
                        Swal.fire({
                            title: '{{ trans('global.no_entries_selected') }}',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '{{ trans('global.are_you_sure') }}',
                        text: '{{ trans('global.delete_confirmation') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                method: 'POST',
                                url: route,
                                data: {
                                    ids: ids,
                                    _method: 'DELETE'
                                }
                            }).done(function(response) {
                                location.reload();
                            }).fail(function(xhr, status, error) {
                                Swal.fire(
                                    '{{ trans('global.error') }}',
                                    '{{ trans('global.delete_error') }}',
                                    'error'
                                );
                            });
                        }
                    });
                };
            }

            @php
                $deleteRoute = request()->is('admin/bookings/trash') ? route('admin.bookings.deleteTrashAll') : route('admin.bookings.deleteAll');
            @endphp

            let deleteRoute = "{{ $deleteRoute }}";

            let deleteButton = {
                text: '{{ trans('global.delete_all') }}',
                className: 'btn-danger',
                action: handleDeletion(deleteRoute)
            };

            dtButtons.push(deleteButton);

            let table = $('.datatable-Booking:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {});
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#host').select2({
                ajax: {
                    url: "{{ route('admin.searchHost') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.first_name,
                                };
                            })
                        };
                    },
                    cache: true,
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error while fetching customer data:", textStatus, errorThrown);
                    }
                }
            });
            var selectedUserId = "{{ $searchfieldId }}";
            var selectedUserName = "{{ $searchfield }}";

            if (selectedUserId) {
                var option = new Option(selectedUserName, selectedUserId, true, true);
                $('#host').append(option).trigger('change');
            }
        });
        $(document).ready(function() {
            $('#rider').select2({
                ajax: {
                    url: "{{ route('admin.searchUser') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.first_name,
                                };
                            })
                        };
                    },
                    cache: true,
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error while fetching customer data:", textStatus, errorThrown);
                    }
                }
            });
            var selectedUserId = "{{ $searchCustomerId }}";
            var selectedUserName = "{{ $searchCustomer }}";

            if (selectedUserId) {
                var option = new Option(selectedUserName, selectedUserId, true, true);
                $('#rider').append(option).trigger('change');
            }

            $(document).ready(function() {
                $('.restore-btn').on('click', function() {
                    var bookingId = $(this).data('id');
                    var form = $('#restore-form-' + bookingId);

                    Swal.fire({
                        title: "Restore Booking",
                        text: "Are you sure you want to restore this item?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, restore it!",
                        cancelButtonText: "Cancel",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Restoring',
                                text: 'Please wait while restoring...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Perform AJAX request to restore the booking
                            $.ajax({
                                url: form.attr('action'),
                                method: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    Swal.close();
                                    toastr.success(
                                        'Booking restored successfully!',
                                        'Success', {
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: "toast-bottom-right"
                                        });
                                    // Optionally, update UI as needed (e.g., remove the restored item from the list)
                                    location
                                        .reload(); // Example: Reload the page
                                },
                                error: function(response) {
                                    Swal.close();
                                    toastr.error(
                                        'An error occurred while restoring the booking.',
                                        'Error', {
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: "toast-bottom-right"
                                        });
                                }
                            });
                        }
                    });
                });

                $('.permanent-delete').on('click', function() {
                    var bookingId = $(this).data('id');
                    var form = $('#delete-form-' + bookingId);

                    Swal.fire({
                        title: "Delete Booking Permanently",
                        text: "Are you sure you want to permanently delete this item?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting',
                                text: 'Please wait while deleting...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Perform AJAX request to permanently delete the booking
                            $.ajax({
                                url: form.attr('action'),
                                method: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    Swal.close();
                                    toastr.success(
                                        'Booking permanently deleted!',
                                        'Success', {
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: "toast-bottom-right"
                                        });
                                    // Optionally, update UI as needed (e.g., remove the deleted item from the list)
                                    location
                                        .reload(); // Example: Reload the page
                                },
                                error: function(response) {
                                    Swal.close();
                                    toastr.error(
                                        'An error occurred while deleting the booking.',
                                        'Error', {
                                            closeButton: true,
                                            progressBar: true,
                                            positionClass: "toast-bottom-right"
                                        });
                                }
                            });
                        }
                    });
                });
            });
        });
        $(document).ready(function() {
            $('.restore-btn').on('click', function() {
                var bookingId = $(this).data('id');
                var form = $('#restore-form-' + bookingId);

                Swal.fire({
                    title: "Restore Booking",
                    text: "Are you sure you want to restore this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, restore it!",
                    cancelButtonText: "Cancel",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Restoring',
                            text: 'Please wait while restoring...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Perform AJAX request to restore the booking
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.close();
                                toastr.success('Booking restored successfully!',
                                    'Success', {
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-bottom-right"
                                    });
                                // Optionally, update UI as needed (e.g., remove the restored item from the list)
                                location.reload(); // Example: Reload the page
                            },
                            error: function(response) {
                                Swal.close();
                                toastr.error(
                                    'An error occurred while restoring the booking.',
                                    'Error', {
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-bottom-right"
                                    });
                            }
                        });
                    }
                });
            });

            $('.permanent-delete').on('click', function() {
                var bookingId = $(this).data('id');
                var form = $('#delete-form-' + bookingId);

                Swal.fire({
                    title: "Delete Booking Permanently",
                    text: "Are you sure you want to permanently delete this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting',
                            text: 'Please wait while deleting...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Perform AJAX request to permanently delete the booking
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.close();
                                toastr.success('Booking permanently deleted!',
                                    'Success', {
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-bottom-right"
                                    });
                                // Optionally, update UI as needed (e.g., remove the deleted item from the list)
                                location.reload(); // Example: Reload the page
                            },
                            error: function(response) {
                                Swal.close();
                                toastr.error(
                                    'An error occurred while deleting the booking.',
                                    'Error', {
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-bottom-right"
                                    });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
