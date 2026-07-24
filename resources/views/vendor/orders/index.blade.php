@extends('vendor.layout')
@section('content')
<div class="content">

    {{-- <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('vendor.bookings.create') }}">
    {{ trans('global.add') }} {{ trans('global.booking_title_singular') }}
    </a>
</div>
</div>--}}
@section('styles')
<style>
        .dataTables_info{
            display: none;
        }
        .paging_simple_numbers{
            display: none;
        }
        .pagination.justify-content-end{
            float: right;
        }
        .main-footer {
    overflow: hidden;
    margin-left: 0;
}
.dataTables_length{
    display:none;
}
    </style>
@endsection

<div class="box">
    <div class="box-body">
        <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8" id="bookingFilterForm">

            <div class="col-md-12 d-none">
                <input class="form-control" type="hidden" id="startDate" name="from" value="">
                <input class="form-control" type="hidden" id="endDate" name="to" value="">
            </div>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 col-sm-12 col-xs-12">
                        <label>{{ trans('global.date_range') }} </label>
                        <div class="input-group col-xs-12">
                            <!-- Add the input element here -->
                            <input type="text" class="form-control" id="daterange-btn">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 col-xs-12">
                        <label>{{ trans('global.itemid') }}</label>
                        <select class="form-control select2" name="item" id="item">
                            <!-- Add any other options you want to display -->
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <label>{{ trans('global.customer') }}</label>
                        <select class="form-control select2" name="customer" id="customer">
                            <!-- Add any other options you want to display -->
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <label>{{ trans('global.status') }}</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">Please Select Status </option>
                            <option value="pending" {{ request()->input('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request()->input('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request()->input('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="declined" {{ request()->input('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                            <option value="completed" {{ request()->input('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="refunded" {{ request()->input('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">

                        <button type="submit" name="btn" class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                        <button type="button" name="reset_btn" id="resetBtn" class="btn btn-primary btn-flat">{{ trans('global.reset') }}</button>
                    </div>
                    <div class="col-md-1 col-sm-2 col-xs-4 mt-5">
                        <br>

                    </div>
                </div>
            </div>
    </div>
    </form>
    <!-- all booking layout -->
    
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="panel panelnone panel-primary">
                                <div class="panel-body text-center">
                                    <span class="text-20"> {{ $totalBookings }}</span><br>
                                    <span class="font-weight-bold total-book"> {{ trans('global.total_bookings') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="panel panelnone panel-primary">
                                <div class="panel-body text-center">
                                    <span class="text-20">{{ $totalCustomers }}</span><br>
                                    <span class="font-weight-bold total-customer">{{ trans('global.total_customers') }} </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="panel panelnone panel-primary">
                                <div class="panel-body text-center">
                                    <span class="text-20">{{ ($general_default_currency->meta_value ?? '') . ' ' . ($totalEarnings) }}</span><br>
                                    <span class="font-weight-bold total-amount">{{ trans('global.totalEarning') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="panel panelnone panel-primary">
                                <div class="panel-body text-center">
                                    <span class="text-20">{{ ($general_default_currency->meta_value ?? '') . ' ' . ($totalRefunded) }}</span><br>
                                    <span class="font-weight-bold total-amount">{{ trans('global.totalRefund') }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    <div class="row" style="margin-left: 5px; margin-bottom: 6px;">
    <div class="col-lg-12">
        <a class="{{ request()->routeIs('vendor.orders.index') && !request()->query('status') ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index') }}">
            {{ trans('global.live') }}
            <span class="badge badge-pill badge-primary">{{ $statusCounts['live'] > 0 ? $statusCounts['live'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'pending' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'pending']) }}">
            Pending
            <span class="badge badge-pill badge-primary">{{ $statusCounts['pending'] > 0 ? $statusCounts['pending'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'confirmed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'confirmed']) }}">
            Confirmed
            <span class="badge badge-pill badge-primary">{{ $statusCounts['confirmed'] > 0 ? $statusCounts['confirmed'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'cancelled' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'cancelled']) }}">
            Cancelled
            <span class="badge badge-pill badge-primary">{{ $statusCounts['cancelled'] > 0 ? $statusCounts['cancelled'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'declined' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'declined']) }}">
            Declined
            <span class="badge badge-pill badge-primary">{{ $statusCounts['declined'] > 0 ? $statusCounts['declined'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'completed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'completed']) }}">
            Completed
            <span class="badge badge-pill badge-primary">{{ $statusCounts['completed'] > 0 ? $statusCounts['completed'] : 0 }}</span>
        </a>

        <a class="{{ request()->query('status') === 'refunded' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('vendor.orders.index', ['status' => 'refunded']) }}">
            Refunded
            <span class="badge badge-pill badge-primary">{{ $statusCounts['refunded'] > 0 ? $statusCounts['refunded'] : 0 }}</span>
        </a>
    </div>
</div>

    <div class="row">
        <div class="col-lg-12">
            <div class=" panel-default">
                <div class="panel-heading">
                    {{ trans('global.orders') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Booking">
                            <thead>
                                <tr>
                                <th width="10">
                                  
                                  </th>
                                    <th>
                                        {{ trans('global.token') }}
                                    </th>
                                    <th>
                                        {{ trans('global.itemid') }}
                                    </th>
                                    <th>
                                        {{ trans('global.customer') }}
                                    </th>
                                    <th>
                                        {{ trans('global.payment_method') }}
                                    </th>
                                    <th>
                                        {{ trans('global.total') }}
                                    </th>
                                    <th>
                                        {{ trans('global.booking_date') }}
                                    </th>
                                    <th>
                                        {{ trans('global.check_in') }}
                                    </th>
                                    <th>
                                        {{ trans('global.check_out') }}
                                    </th>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <th>
                                        {{ trans('global.payment_status') }}
                                    </th>
                                    <th>
                                        {{ trans('global.action') }}
                                    </th>
                                </tr>
                            </thead>
                            @php
                                // Fetch all item IDs related to the bookings in a single query
                                $itemIds = \App\Models\Modern\Item::whereIn('id', $bookings->pluck('itemid'))->pluck('id')->toArray();
                            @endphp
                            <tbody>
                                @foreach($bookings as $key => $booking)
                                @php
                                $itemExists = in_array($booking->itemid, $itemIds);
                                @endphp
                                    <tr data-entry-id="{{ $booking->id }}">
                                    <td></td>
                                        <td>
                                        @if($itemExists)
                                        <a target="_blank" class="btn btn-xs btn-primary" href="{{ route('vendor.orders.show', $booking->id) }}">  #{{ $booking->token }}</a>
                                        @else
                                        {{ $booking->token }}
                                        @endif
                                        </td>
                                        <td>
                                        @if($itemExists)  
                                                <a href="{{ route('vendor.vehicles.base', $booking->itemid) }}">{{ $booking->item_title ?? '' }}</a>
                                           
                                        @else
                                        {{ $booking->item_title ?? '' }}
                                        @endif
                                    </td>

                                    <td>

                                        {{ $booking->user->first_name ?? '' }} {{ $booking->user->last_name ?? '' }}

                                    </td>
                                    <td>
                                        {{ $booking->payment_method ?? '' }}
                                    </td>
                                    <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->total ?? '') }}
                                    </td>
                                    <td>
                                        {{ $booking->created_at ? $booking->created_at->format('Y-m-d') : '' }}
                                    </td>

                                    <td>
                                        {{ $booking->check_in ? $booking->check_in : '' }}<br> 
                                        {{ $booking->start_time ? $booking->start_time : '' }}
                                    </td>
                                    <td>
                                        {{ $booking->check_out ? $booking->check_out : '' }}<br>
                                        {{ $booking->end_time ? $booking->end_time : '' }}
                                    </td>


                                    <td>
                                        @if ($booking->status === 'Pending')
                                        <span class="badge badge-pill label-secondary">Pending</span>
                                        @elseif ($booking->status === 'Cancelled')
                                        <span class="badge badge-pill label-danger">Cancelled</span>
                                        @elseif ($booking->status === 'Approved')
                                        <span class="badge badge-pill label-success">Approved</span>
                                        @elseif ($booking->status === 'Declined')
                                        <span class="badge badge-pill label-warning">Declined</span>
                                        @elseif ($booking->status === 'Completed')
                                        <span class="badge badge-pill label-info">Completed</span>
                                        @elseif ($booking->status === 'Refunded')
                                        <span class="badge badge-pill label-primary">Refunded</span>
                                        @else
                                        <span class="badge badge-pill label-success"> {{ $booking->status }} </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->payment_status === 'paid')
                                        <span class="badge badge-pill label-success">paid</span>
                                        @elseif ($booking->payment_status === 'notpaid')
                                        <span class="badge badge-pill label-danger">notpaid</span>
                                        @endif
                                    </td>

                                    <td>
                                    @if ($booking->status === 'Pending')
                                    <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info confirm-order" data-id="{{ $booking->id }}" href="javascript:void(0);" title="Accept">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </a>

                                    <!-- Decline button with cross icon and tooltip -->
                                    <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-danger decline-order" data-id="{{ $booking->id }}" href="javascript:void(0);" title="Decline">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>

                                    <a class="btn btn-xs btn-info" href="{{ route('vendor.orders.show', $booking->id) }}" target="_blank" title="View">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>

                                        <input type="hidden" id="hiddenBookingId" value="{{ $booking->id }}">
                                        <!-- Modal for selecting cancellation reason -->
                                        <div class="modal" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="declineModalLabel">Select Cancellation Reason</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="cancellation-reasons">
                                                            <!-- Cancellation reasons will be loaded here as radio buttons -->
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger" id="submit-cancellation">Submit Cancellation</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <a class="btn btn-xs btn-info" href="{{ route('vendor.orders.show', $booking->id) }}" target="_blank" title="View">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>

                                        @endif
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
                                    <a class="page-link" href="{{ $bookings->previousPageUrl() }}" tabindex="-1">Previous</a>
                                </li>
                                @else
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
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
                                        <a class="page-link" href="{{ $bookings->nextPageUrl() }}">Next</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
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
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Include Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

@endsection
@section('scripts')
@parent
<script>
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [
                [0, 'desc']
            ],

        });
        let table = $('.datatable-Booking:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

    })
</script>

<script>
    $(document).ready(function() {
        // Initialize the Select2 for the customer select box
        $('#customer').select2({
            ajax: {
                url: "{{ route('vendor.searchBookingUser') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    // Transform the response data into Select2 format
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
                    // Optionally display an error message to the user
                    alert("An error occurred while loading customer data. Please try again later.");
                }
            }
        });

        var searchfieldCustomerId = "{{ $searchfieldCustomerId }}"; 
        var searchfieldCustomerName = "{{ $searchfieldCustomerName }}"; 
        var option = new Option(searchfieldCustomerName, searchfieldCustomerId, true, true);
        $('#customer').append(option).trigger('change'); 

    });
</script>
<script>
    $(document).ready(function() {
        // Initialize the Select2 for the customer select box
        $('#item').select2({
            ajax: {
                url: "{{ route('vendor.searchVendorItem') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    // Transform the response data into Select2 format
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
                    // Optionally display an error message to the user
                    
                }
            }
        });

        var searchfieldItemId = "{{ $searchfieldItemId }}"; 
        var searchfieldItemName = "{{ $searchfieldItemName }}"; 
        var option = new Option(searchfieldItemName, searchfieldItemId, true, true);
        $('#item').append(option).trigger('change'); // Append and trigger change to display the value


        // Your other code for DateRangePicker initialization and filters
    });
</script>

<!-- Include DateRangePicker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize the DateRangePicker
        $('#daterange-btn').daterangepicker({
            opens: 'right', // Change the calendar position to the left side of the input
            autoUpdateInput: false, // Disable auto-update of the input fields
            ranges: {
                'Anytime': [moment(), moment()],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'YYYY-MM-DD', // Format the date as you need
                separator: ' - ',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range'
            }
        });

        // Check if the start and end dates are stored in local storage
        const storedStartDate = localStorage.getItem('selectedStartDate');
        const storedEndDate = localStorage.getItem('selectedEndDate');

        // Get the URL parameters for 'from' and 'to'
        const urlFrom = "{{ request()->input('from') }}";
        const urlTo = "{{ request()->input('to') }}";

        // If both start and end dates are available in local storage, and the URL parameters 'from' and 'to' are not empty, set the initial date range
        if (storedStartDate && storedEndDate && urlFrom && urlTo) {
            const startDate = moment(storedStartDate);
            const endDate = moment(storedEndDate);
            $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
            $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
            $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
        } else {
            // Otherwise, clear the date range in DateRangePicker
            $('#daterange-btn').val('');
            $('#startDate').val('');
            $('#endDate').val('');

            // Clear the stored start and end dates from local storage
            localStorage.removeItem('selectedStartDate');
            localStorage.removeItem('selectedEndDate');
        }

        // Update the hidden input fields and button text when the date range changes
        $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
            $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));

            // Store the selected start and end dates in local storage
            localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
            localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
        });

        // Clear the date range selection and input fields when the 'Cancel' button is clicked
        $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#startDate').val('');
            $('#endDate').val('');

            // Clear the stored start and end dates from local storage
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
        var baseUrl = '{{ route('vendor.orders.index') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>

<script>
    $(document).on('click', '.confirm-order', function(e) {
        e.preventDefault(); // Prevent default action of the button

        var bookingId = $(this).data('id'); // Get booking ID from data-id attribute
        var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token

        $.ajax({
            type: "POST",
            url: "{{ route('vendor.confirm.order') }}", // Replace with your route
            data: {
                _token: csrfToken,
                id: bookingId // Pass booking ID
            },
            success: function(response) {
                if (response.status === 200) {
                    // Success notification
                    toastr.success(response.message, '{{ trans("global.success") }}', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    window.location.reload();
                } else {
                    // Error notification
                    toastr.error(response.message, '{{ trans("global.error") }}', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                }
               
            },
            error: function(xhr, status, error) {
                // AJAX error handling
                toastr.error('Something went wrong. Please try again.', '{{ trans("global.error") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
            }
        });
    });
</script>

<script>
// $(document).on('click', '.decline-order', function(e) {
   
//     e.preventDefault(); // Prevent default action of the button

//     var bookingId = $(this).data('id'); // Get the booking ID
//     var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token
//     $('#hiddenBookingId').val(bookingId);
//     // Show the modal to select a cancellation reason
//     $('#declineModal').modal('show');

//     // Fetch cancellation reasons from the server
//     $.ajax({
//         type: "GET",
//         url: "{{ route('vendor.cancellationReasons') }}", // Replace with your route to fetch cancellation reasons
//         data: {
//             _token: csrfToken
//         },
//         success: function(response) {
//             if(response.status === 200) {
//                 var reasonsHtml = '';
//                 response.data.forEach(function(reason) {
//                     reasonsHtml += `<div class="form-check">
//                                         <input class="form-check-input" type="radio" name="cancellation_reason" value="${reason.order_cancellation_id }" id="reason_${reason.order_cancellation_id }">
//                                         <label class="form-check-label" for="reason_${reason.order_cancellation_id }">
//                                             ${reason.reason}
//                                         </label>
//                                       </div>`;
//                 });

//                 $('#cancellation-reasons').html(reasonsHtml);
//             } else {
//                 alert('Failed to load cancellation reasons.');
//             }
//         }
//     });
// });

$(document).on('click', '.decline-order', function(e) {
    e.preventDefault();

    var bookingId = $(this).data('id'); // Get booking ID
    var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token

    // Fetch cancellation reasons from the server
    $.ajax({
        type: "GET",
        url: "{{ route('vendor.cancellationReasons') }}", // Replace with your cancellation reasons URL
        data: {
            _token: csrfToken
        },
        success: function(response) {
            if (response.status === 200) {
                var reasonsHtml = '';
                response.data.forEach(function(reason) {
                    reasonsHtml += `<div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason" value="${reason.order_cancellation_id}" id="reason_${reason.order_cancellation_id}">
                                        <label class="form-check-label" for="reason_${reason.order_cancellation_id}">
                                            ${reason.reason}
                                        </label>
                                      </div>`;
                });

                // Show SweetAlert modal
                Swal.fire({
                    title: 'Select Cancellation Reason',
                    html: `
                        <div id="cancellation-reasons">
                            ${reasonsHtml}
                        </div>
                    `,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Submit Cancellation',
                    preConfirm: function() {
                        // Get selected cancellation reason
                        var cancellationReasonId = $('input[name="cancellation_reason"]:checked').val();
                        if (!cancellationReasonId) {
                            Swal.showValidationMessage('Please select a cancellation reason.');
                            return false; // Prevent form submission if no reason is selected
                        }
                        return cancellationReasonId;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send the cancellation request to the server
                        $.ajax({
                            type: "POST",
                            url: "{{ route('vendor.cancel.order') }}", // Replace with your cancellation route
                            data: {
                                _token: csrfToken,
                                booking_id: bookingId,
                                cancellation_reason_id: result.value
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Success', 'Order cancelled successfully!', 'success');
                                    window.location.reload(); // Reload the page to reflect changes
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Error', 'Failed to load cancellation reasons.', 'error');
            }
        }
    });
});

// Submit the cancellation reason
$(document).on('click', '#submit-cancellation', function() {
    var bookingId = document.getElementById('hiddenBookingId').value;
    console.log("booking idddd ", bookingId);
    var cancellationReasonId = $('input[name="cancellation_reason"]:checked').val(); // Get the selected cancellation reason

    if (!cancellationReasonId) {
        toastr.warning('Please select a cancellation reason.', '{{ trans("global.warning") }}', {
            CloseButton: true,
            ProgressBar: true,
            positionClass: "toast-bottom-right"
        });
        return;
    }

    var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token

    // Send the cancellation request to the server
    $.ajax({
        type: "POST",
        url: "{{ route('vendor.cancel.order') }}", // Replace with your cancellation route
        data: {
            _token: csrfToken,
            booking_id: bookingId,
            cancellation_reason_id: cancellationReasonId
        },
        success: function(response) {
            if (response.status === 200) {
                // Success notification
                toastr.success(response.message, '{{ trans("global.success") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });

                // Optional: Reload the page or update the UI
                $('#declineModal').modal('hide');
                window.location.reload(); // Reloads the page to reflect changes
            } else {
                // Error notification
                toastr.error(response.message, '{{ trans("global.error") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
            }
            $('#declineModal').modal('hide');
        },
        error: function(xhr, status, error) {
            // AJAX error handling
            toastr.error('Something went wrong. Please try again.', '{{ trans("global.error") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    });
});

</script>
@endsection