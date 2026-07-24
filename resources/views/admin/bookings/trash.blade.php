@extends('layouts.admin')
@section('styles')
    <style>
        .dataTables_info {
            display: none;
        }

        .paging_simple_numbers {
            display: none;
        }

        .pagination.justify-content-end {
            float: right;
        }

        .main-footer {
            overflow: hidden;
            margin-left: 0;
        }

        .img-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .live-badge {
            position: relative;
            padding-left: 30px;
        }

        .live-dot {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(62, 11, 163, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
            }
        }

        .car-type-animated {

            /* Light blue background */
            padding: 8px;

            animation: subtlePulse 2s ease-in-out infinite;
            color: #0369a1;

        }

        .car-type-animated div {
            margin-bottom: 2px;
        }



        .pickup-address {
            background-color: #e6f7ff;
            color: #0050b3;
            padding: 8px;
            align-items: center;

        }

        .pickup-address i {
            color: #1890ff;
        }

        .dropoff-address {
            background-color: #fff1f0;
            color: #07a812;
            padding: 8px;
            align-items: center;
            gap: 6px;
        }

        .dropoff-address i {
            color: #0c473d;
        }
    </style>
@endsection
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
                                        <input type="text" class="form-control" id="daterange-btn">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.rider') }}</label>
                                    <select class="form-control select2" name="customer" id="customer1">
                                        <option value="">{{ $searchCustomer }}</option>
                                        <!-- Add any other options you want to display -->
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.host') }}</label>
                                    <select class="form-control select2" name="host" id="host">
                                        <option value="">{{ $searchfield }}</option>
                                        <!-- Add any other options you want to display -->
                                    </select>
                                </div>


                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('booking.booking_status') }}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="">
                                            {{ trans('booking.select_status') ?? 'Please Select Status' }}</option>
                                        <option value="accepted"
                                            {{ request()->input('status') === 'accepted' ? 'selected' : '' }}>
                                            {{ trans('booking.booking_accepted') ?? 'Accepted' }}
                                        </option>
                                        <option value="ongoing"
                                            {{ request()->input('status') === 'ongoing' ? 'selected' : '' }}>
                                            {{ trans('booking.booking_running') ?? 'Running' }}
                                        </option>
                                        <option value="cancelled"
                                            {{ request()->input('status') === 'cancelled' ? 'selected' : '' }}>
                                            {{ trans('booking.booking_cancelled') ?? 'Cancelled' }}
                                        </option>
                                        <option value="rejected"
                                            {{ request()->input('status') === 'rejected' ? 'selected' : '' }}>
                                            {{ trans('booking.booking_rejected') ?? 'Rejected' }}
                                        </option>
                                        <option value="completed"
                                            {{ request()->input('status') === 'completed' ? 'selected' : '' }}>
                                            {{ trans('booking.booking_completed') ?? 'Completed' }}
                                        </option>
                                    </select>

                                </div>

                                <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">

                                    <button type="submit" name="btn" class="btn btn-primary btn-flat">
                                        {{ trans('booking.filter') }}
                                    </button>
                                    <button type="button" name="reset_btn" id="resetBtn" class="btn btn-primary btn-flat">
                                        {{ trans('booking.reset') }}
                                    </button>

                                </div>
                                {{-- -<div class="col-md-1 col-sm-2 col-xs-4 mt-5">
								<br>
								
								</div>- --}}
                            </div>
                    </div>
                </div>
                </form>
            </div>

            <!-- all booking layout -->




            <div class="row" style="margin-left: 5px; margin-bottom: 6px;">
                <div class="col-lg-12">
                    <a class="{{ request()->routeIs('admin.bookings.index') && !request()->query('status') ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index') }}">
                        {{ trans('booking.booking_all') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['all'] > 0 ? $statusCounts['all'] : 0 }}</span>
                    </a>


                    <a class="{{ request()->query('status') === 'accepted' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index', ['status' => 'accepted']) }}">
                        {{ trans('booking.booking_accepted') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['accepted'] > 0 ? $statusCounts['accepted'] : 0 }}</span>
                    </a>

                    {{-- <a class="{{ request()->query('status') === 'confirmed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                    href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}">
                    Confirmed
                    <span class="badge badge-pill badge-primary">{{ $statusCounts['confirmed'] > 0 ? $statusCounts['confirmed'] : 0 }}</span>
                </a> --}}

                    <a class="{{ request()->query('status') === 'ongoing' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index', ['status' => 'ongoing']) }}">
                        {{ trans('booking.booking_running') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['ongoing'] > 0 ? $statusCounts['ongoing'] : 0 }}</span>
                    </a>



                    <a class="{{ request()->query('status') === 'completed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index', ['status' => 'completed']) }}">
                        {{ trans('booking.booking_completed') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['completed'] > 0 ? $statusCounts['completed'] : 0 }}</span>
                    </a>
                    <a class="{{ request()->query('status') === 'cancelled' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}">
                        {{ trans('booking.booking_cancelled') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['cancelled'] > 0 ? $statusCounts['cancelled'] : 0 }}</span>
                    </a>

                    <a class="{{ request()->query('status') === 'rejected' ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.index', ['status' => 'rejected']) }}">
                        {{ trans('booking.booking_rejected') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['rejected'] > 0 ? $statusCounts['rejected'] : 0 }}</span>
                    </a>

                    <a class="{{ request()->routeIs('admin.bookings.trash') ? 'btn btn-primary' : 'btn btn-inactive' }}"
                        href="{{ route('admin.bookings.trash') }}">

                        {{ trans('booking.booking_trash') }}
                        <span
                            class="badge badge-pill badge-primary">{{ $statusCounts['trash'] > 0 ? $statusCounts['trash'] : 0 }}</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('booking.all_bookings') }}
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class=" table table-bordered table-striped table-hover datatable datatable-Booking">
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

                                                <a target="_blank" class="btn btn-xs btn-primary"
                                                    href="{{ route('admin.bookings.show', $booking->id) }}">
                                                    {{ $booking->token }}</a>

                                            </td>


                                            <td>
                                                @if ($booking->host)
                                                    <a target="_blank"
                                                        href="{{ route('admin.overview', ['booking' => $booking->host->id, 'user_type' => 'driver']) }}">
                                                        @if ($booking->host->profile_image)
                                                            <img src="{{ $booking->host->profile_image->getUrl('thumb') }}"
                                                                alt="Profile Image" class="img-circle">
                                                        @else
                                                            <img src="{{ asset('public/images/icon/userdefault.jpg') }}"
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
                                                        href="{{ route('admin.wallet', ['booking' => $booking->user->id, 'user_type' => 'user']) }}">
                                                        @if ($booking->user->profile_image)
                                                            <img src="{{ $booking->user->profile_image->getUrl('thumb') }}"
                                                                class="img-circle">
                                                        @else
                                                            <img src="{{ asset('public/images/icon/userdefault.jpg') }}"
                                                                alt="Default Image" style="display: inline-block;">
                                                        @endif
                                                        {{ $booking->user->first_name ?? '' }}
                                                        {{ $booking->user->last_name ?? '' }}
                                                    </a>
                                                @else
                                                    <span>--</span>
                                                @endif
                                            </td>

                                            <td class="car-type-animated">
                                                <div>
                                                    <div><strong>Type:</strong>
                                                        {{ $booking->item->item_Type->name ?? '-' }}</div>
                                                    <div><strong>Make:</strong> {{ $booking->item->make ?? '-' }}</div>
                                                    <div><strong>Model:</strong> {{ $booking->item->model ?? '-' }}</div>
                                                    <div><strong>Number:</strong>
                                                      </div>
                                                </div>
                                            </td>
                                            <td class="pickup-address">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $booking->extension && is_array($booking->extension->pickup_location) ? $booking->extension->pickup_location['address'] : '' }}
                                            </td>

                                            <td class="dropoff-address">
                                                <i class="fas fa-map-pin"></i>
                                                {{ $booking->extension && is_array($booking->extension->dropoff_location) ? $booking->extension->dropoff_location['address'] : '' }}
                                            </td>

                                            <td>
                                                {{ ($booking->total ?? '') . ' ' . ($general_default_currency->meta_value ?? '') }}
                                            </td>
                                            <td>
                                                {{ $booking->created_at ? $booking->created_at->format('Y-m-d') : '' }}
                                            </td>

                                            <td>
                                                @if ($booking->status === 'Ongoing')
                                                    <span class="badge badge-pill label-secondary live-badge">
                                                        <span class="live-dot"></span> Live
                                                    </span>
                                                @elseif ($booking->status === 'Cancelled')
                                                    <span class="badge badge-pill label-danger">Cancelled</span>
                                                @elseif ($booking->status === 'Accepted')
                                                    <span class="badge badge-pill label-success">Accepted</span>
                                                @elseif ($booking->status === 'Approved')
                                                    <span class="badge badge-pill label-success">Approved</span>
                                                @elseif ($booking->status === 'Declined')
                                                    <span class="badge badge-pill label-warning">Declined</span>
                                                @elseif ($booking->status === 'Completed')
                                                    <span class="badge badge-pill label-info">Completed</span>
                                                @elseif ($booking->status === 'Refunded')
                                                    <span class="badge badge-pill label-primary">Refunded</span>
                                                @elseif ($booking->status === 'Confirmed')
                                                    <span class="badge badge-pill label-success">Confirmed</span>
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
                                                <!-- Action buttons for trashed bookings -->
                                                <form id="restore-form-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.restore', $booking->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    <button type="button" class="btn btn-xs btn-success restore-btn"
                                                        data-id="{{ $booking->id }}">
                                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                                    </button>
                                                </form>



                                                <form id="delete-form-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.permanentDelete', $booking->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @method('POST')
                                                    @csrf

                                                    <button type="button" class="btn btn-xs btn-danger permanent-delete"
                                                        data-id="{{ $booking->id }}">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end">
                                    {{-- Previous Page Link --}}
                                    @if ($bookings->currentPage() > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $bookings->previousPageUrl() }}"
                                                tabindex="-1">{{ trans('global.previous') }}</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">{{ trans('global.previous') }}</span>
                                        </li>
                                    @endif

                                    {{-- Numeric Pagination Links --}}
                                    @for ($i = 1; $i <= $bookings->lastPage(); $i++)
                                        <li class="page-item {{ $i == $bookings->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($bookings->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="{{ $bookings->nextPageUrl() }}">{{ trans('global.next') }}</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">{{ trans('global.next') }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
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

    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            function handleDeletion(route) {
                return function(e, dt, node, config) {
                    var ids = $.map(dt.rows({
                        selected: true
                    }).nodes(), function(entry) {
                        return $(entry).data('entry-id')
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
                                location.reload(); // Reload the page after deletion
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


            let deleteRoute = "{{ route('admin.bookings.deleteTrashAll') }}";


            let deleteButton = {
                text: '{{ trans('global.delete_all') }}',
                className: 'btn-danger',
                action: handleDeletion(deleteRoute)
            };

            dtButtons.push(deleteButton);

            let table = $('.datatable-Booking:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                //   $($.fn.dataTable.tables(true)).DataTable()
                //       .columns.adjust();
            });

        })
    </script>



    <script>
        $(document).ready(function() {

            $('#customer').select2({
                ajax: {
                    url: "{{ route('admin.searchcustomer') }}",
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

                        alert("An error occurred while loading customer data. Please try again later.");
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            $('#item').select2({
                ajax: {
                    url: "{{ route('admin.searchItem') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {

                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name,
                                };
                            })
                        };
                    },
                    cache: true, // Cache the AJAX results to avoid multiple requests for the same data
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error while fetching customer data:", textStatus, errorThrown);

                        alert("An error occurred while loading customer data. Please try again later.");
                    }
                }
            });


        });
    </script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#daterange-btn').daterangepicker({
                opens: 'right',
                autoUpdateInput: false,
                ranges: {
                    'Anytime': [moment(), moment()],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month')
                        .endOf('month')
                    ]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range'
                }
            });


            const storedStartDate = localStorage.getItem('selectedStartDate');
            const storedEndDate = localStorage.getItem('selectedEndDate');


            const urlFrom = "{{ request()->input('from') }}";
            const urlTo = "{{ request()->input('to') }}";


            if (storedStartDate && storedEndDate && urlFrom && urlTo) {
                const startDate = moment(storedStartDate);
                const endDate = moment(storedEndDate);
                $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
                $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
                $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
            } else {

                $('#daterange-btn').val('');
                $('#startDate').val('');
                $('#endDate').val('');


                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            }


            $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
                $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));


                localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
                localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
            });


            $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#startDate').val('');
                $('#endDate').val('');


                localStorage.removeItem('selectedStartDate');
                localStorage.removeItem('selectedEndDate');
            });

            // Function to reset the filters
            function resetFilters() {
                $('#daterange-btn').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                $('#status').val('');
                $('#customer').val('').trigger('change');
            }

            // Optional: Submit the form when the "Filter" button is clicked
            $('button[name="btn"]').on('click', function() {
                $('form').submit();
            });
        });
    </script>
    <script>
        $('#resetBtn').click(function() {
            $('#bookingFilterForm')[0].reset();
            var baseUrl = '{{ route('admin.bookings.index') }}';
            window.history.replaceState({}, document.title, baseUrl);
            window.location.reload();
        });
    </script>
@endsection
