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

    .dataTables_length {
        display: none;
    }
</style>
@endsection
@section('content')
<div class="content">

    <div class="box">
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
            <form class="form-horizontal" id="itemFilterForm" action="" method="GET" accept-charset="UTF-8" id="orderform">

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
                            <option value="">All</option>
                            <option value="1" {{ request()->input('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request()->input('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-12 col-xs-12">
                        <label>{{ trans('global.title') }}</label>
                        <select class="form-control select2" name="item" id="item">
                            <option value="">{{ $searchfielditem }}</option>
                            <!-- Add any other options you want to display -->
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
                                        <span class="font-weight-bold total-amount">{{ trans('global.total_earnings') }} </span>
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
                {{ trans('global.Orders') }} {{ trans('global.list') }}

            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover datatable datatable-Property">
                        <thead>
                            <tr>
                                <th width="10">
                                </th>
                                <th>
                                    {{ trans('global.id') }}
                                </th>
                                <th>
                                    {{ trans('global.name') }}
                                </th>
                                <th>
                                    {{ trans('global.host') }}
                                </th>
                                <th>
                                    {{ trans('global.guestName') }}
                                </th>

                                <th>
                                    {{ trans('global.total_amount') }}
                                </th>
                                <th>
                                    {{ trans('global.start_date') }}
                                </th>
                                <th>
                                    {{ trans('global.item_status') }}
                                </th>
                                <th>
                                    {{ trans('global.payment_status') }}
                                </th>
                                <th>
                                    {{ trans('global.action') }}
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $key => $booking)
                            <tr data-entry-id="{{ $booking->id }}">
                                <td>

                                </td>
                                <td>
                                    <a class="btn btn-xs btn-primary" target="_blank" href="{{ route('admin.bookings.show', $booking->id) }}"> {{$booking->token}}
                                    </a>
                                </td>
                                <td>
                                    @switch($booking->module)
                                    @case(1)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.vehicles.base', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @case(2)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.vehicles.base', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @case(3)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.boat.base', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @case(4)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.parking.base', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @case(5)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.bookable.base', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @case(6)
                                    <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.space.description', $booking->itemid) }}"> {{ $booking->item_title ?? '' }}</a>
                                    @break
                                    @default
                                    <!-- Add your code for other cases here -->
                                    @endswitch


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
                                    {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->total ?? '') }}
                                </td>
                                <td>
                                    {{ $booking->check_in ?? '' }}
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
                                    @elseif ($booking->status === 'Confirmed')
                                    <span class="badge badge-pill label-success">Confirmed</span>
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
                                    <a class="btn btn-xs btn-primary" target="_blank" href="{{ route('admin.bookings.show', $booking->id) }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @endcan




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
@endsection
@section('scripts')


<!-- Include Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


@parent
<script>
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: false,

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
        $('.resetproduct').click(function(e) {
            e.preventDefult();
            $(this).closest('form').submit();
        })
        // Initialize the Select2 for the customer select box

        // Your other code for DateRangePicker initialization and filters
    });
</script>
<script>
    $(document).ready(function() {
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
                    
                }
            }
        });

        var searchfielditem = "{{$searchfielditem}}";
        var searchfielditemId = "{{$searchfielditemId}}";

        if(searchfielditemId)
        {
            $('#item').append(new Option(searchfielditem, searchfielditemId, true, true)).trigger('change'); 
        }
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



        // Optional: Submit the form when the "Filter" button is clicked
        $('button[name="btn"]').on('click', function() {
            $('form').submit();
        });
    });
    document.getElementById('resetBtn').addEventListener('click', function() {

        document.getElementById('itemFilterForm').reset();

        $('.select2').val('').trigger('change');

        $('#itemFilterForm').submit();
    });
</script>
<script>

</script>
@endsection