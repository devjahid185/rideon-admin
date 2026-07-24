@extends('layouts.admin')
@section('content')
<div class="content">
    <!--/*seaction 1 start here*/-->
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

        .dataTables_length {
            display: none;
        }
    </style>
    <div class="box">
        <div class="panel-body">
            <div class="nav-tabs-custom">
                <div class="panel-body">
                    <div class="nav-tabs-custom">
                    @include('admin.overviewcustomer.overviewtabs')
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- selecte -->
            <div class="box">
                <div class="box-body">
                    <form class="form-horizontal" id="itemFilterForm" action="" method="GET" accept-charset="UTF-8" id="bookingfilter">

                        <div>
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>


                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12">
                                <label>{{ trans('global.date_range') }}</label>
                                <div class="input-group col-xs-12">

                                    <input type="text" class="form-control" id="daterange-btn">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12 col-xs-12">
                                <label>{{ trans('global.status') }}</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="">Please Select Status </option>
                                    <option value="pending" {{ request()->input('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request()->input('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ request()->input('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="declined" {{ request()->input('status') === 'declined' ? 'selected' : '' }}>Declined</option>
                                    <option value="completed" {{ request()->input('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="refunded" {{ request()->input('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-12 col-xs-12">
                                <label>{{ trans('global.title') }}</label>
                                <select class="form-control select2" name="item" id="item">
                                    <option value="">{{ $searchfieldItem }}</option>

                                </select>
                            </div>


                            <div class="col-md-2 col-sm-2 col-xs-4 mt-5 ">
                                <br>
                                <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                <!-- <button type="button" id="resetBtn" name="reset_btn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                                           -->
                                <button type="button" name="resetBtn" id="resetBtn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
            <!-- box -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="box none">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="panel panelnone panel-primary">
                                        <div class="panel-body text-center">
                                            <span class="text-20"> {{ $totalBookings }}</span><br>
                                            <span class="font-weight-bold total-book">{{ trans('global.total_bookings') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="panel panelnone panel-primary">
                                        <div class="panel-body text-center">
                                            <span class="text-20">{{ $totalCustomers }}</span><br>
                                            <span class="font-weight-bold total-customer">{{ trans('global.total_customers') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="panel panelnone panel-primary">
                                        <div class="panel-body text-center">
                                            <span class="text-20">{{ ($general_default_currency->meta_value ?? '') . ' ' . ($totalEarnings) }}</span><br>
                                            <span class="font-weight-bold total-amount">{{ trans('global.total_amount') }} </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- select end -->
            <!--/*seaction 1  end*/-->
            <div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.item_title_singular') }} {{ trans('global.list') }}
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class=" table table-bordered table-striped table-hover datatable datatable-Property">
                                <thead>
                                    <tr>
                                        <th width="10">
                                        </th>
                                        <th>
                                            {{ trans('global.itemid') }}
                                        </th>
                                        <th>
                                            {{ trans('global.host') }}
                                        </th>
                                        <th>
                                            {{ trans('global.guestName') }}
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
                                            {{ trans('global.booking_status') }}
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
                                        <td>

                                        </td>
                                        <td>
                                            @if($itemExists)
                                            <a href="{{ route('admin.vehicles.base', $booking->itemid) }}">
                                                {{ $booking->item_title ?? '' }}
                                            </a>
                                            @else
                                            {{ $booking->item_title ?? '' }}
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.overview', $booking->host_id) }}">
                                                {{ $booking->host->first_name ?? '' }} {{ $booking->host->last_name ?? '' }}
                                            </a>

                                        </td>

                                        <td>
                                            <a href="{{ route('admin.overview', $booking->userid) }}">
                                                {{ $booking->user->first_name ?? '' }} {{ $booking->user->last_name ?? '' }}
                                            </a>
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
                                            {{ $booking->status }}
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
                                            @can('booking_show')
                                            <a class="btn btn-xs btn-primary" href="{{ route('admin.bookings.show', $booking->id) }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            @endcan

                                            <!-- @can('booking_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('admin.bookings.edit', $booking->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan -->


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
                                        <a class="page-link" href="{{ $bookings->previousPageUrl() }}" tabindex="-1">{{ trans('global.previous') }}</a>
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
                                            <a class="page-link" href="{{ $bookings->nextPageUrl() }}">{{ trans('global.next') }}</a>
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
                        [1, 'desc']
                    ],
                    // pageLength: 10,
                });
                let table = $('.datatable-Property:not(.ajaxTable)').DataTable({
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
                $('.filterproduct').click(function(e) {
                    e.preventDefult();
                    $(this).closest('form').submit();
                })

                // Initialize the Select2 for the customer select box
                $('#item').select2({
                    ajax: {
                        url: "{{ route('admin.searchItem') }}",
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


                // Optional: Submit the form when the "Filter" button is clicked
                $('button[name="btn"]').on('click', function() {
                    $('form').submit();
                });
            });

            // $('.resetproduct').on('click', function(e) {

            //     e.preventDefault(); // Prevent default action

            //     // Reset form fields
            //     resetFilters();
            //     $('form').submit();
            //     // Optionally, you can manually trigger a form submit if needed
            //     // $('form').submit(); // Comment this out if using AJAX
            // });

            // // Function to reset the filters
            // function resetFilters() {
            //         $('#daterange-btn').val('');
            //         $('#startDate').val('');
            //         $('#endDate').val('');
            //         $('#status').val('').trigger('change');
            //         $('#item').val('').trigger('change');
            //     }

            document.getElementById('resetBtn').addEventListener('click', function() {
                // Clear form fields

                document.getElementById('itemFilterForm').reset();

                // Optionally, reset the date range picker if needed

                // If you're using select2, you may need to manually trigger the reset for it
                $('.select2').val('').trigger('change');

                $('#itemFilterForm').submit();
            });
        </script>
        <script>

        </script>
        @endsection