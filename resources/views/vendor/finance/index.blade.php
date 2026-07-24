@extends('vendor.layout')
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
@section('content')
<div class="content">

    
				<div class="box">
					<div class="box-body">
					<form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8" id="bookingFilterForm">
						
						<div class="col-md-12 d-none">
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>
						
						<div class="col-md-12">
						<div class="row">
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>{{ trans('global.date_range') }}</label>
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>{{$currentModule->name}} Name</label>
                            <select class="form-control select2" name="item" id="item">
                                <option value="">{{ $searchfieldItem }}</option>
                                <!-- Add any other options you want to display -->
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-12 col-xs-12">
                            <label> {{ trans('global.user_title_singular') }} </label>
                            <select class="form-control select2" name="customer" id="customersearch">
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
                            <option value="completed" {{ request()->input('status') === 'completed' ? 'selected' : '' }}>Completed</option>
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
                    <div class="box">
    <div class="panel-body">
        <div class="row g-3 coenr-capitalize">
<div class="col-md-12">
<div class="row g-3">
<div class="col-sm-6 mt-3 mt-3">
<div class="caredoane cardbg-4">
<h4 class="title">   {{ ($general_default_currency->meta_value ?? '') . ' ' . (number_format($admin_commission, 2, '.', '') ?? '') }}</h4>
<div class="subtitle">{{ trans('global.admin_commission') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/earning.png') }}" alt="transaction">
</div>
</div>
<div class="col-sm-6 mt-3">
<div class="caredoane cardbg-8 " >
<h4 class="title">  {{ ($general_default_currency->meta_value ?? '') . ' ' . ( number_format($vendor_commission, 2, '.', '') ?? '') }}</h4>
<div class="subtitle">{{ trans('global.vendor_commission') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/earning.png') }}" alt="transaction">
</div>
</div>
</div>
</div>
</div>
</div>

                 <!--  -->

    <div class="row">
        <div class="col-lg-12">
            <div class=" panel-default">
                <div class="panel-heading">
                    {{ trans('global.finance') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover datatable datatable-Booking">
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
                                    {{ trans('global.user_title_singular') }} 
                                    </th>
                                   
                                    <th>
                                        {{ trans('global.admin_commission') }}
                                    </th>
                                    <th>
                                        {{ trans('global.vendor_commission') }}
                                    </th>
                                    <th>
                                        {{ trans('global.iva_tax') }}
                                    </th>
                                    <th>
                                        {{ trans('global.deducted') }}
                                    </th>
                                    <th>
                                        {{ trans('global.refundable') }}
                                    </th>
                                
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                    <th>
                                        {{ trans('global.payment_status') }}
                                    </th>
                                    <!-- <th>
                                    Action
                                    </th> -->
                                  
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
                                    @if ($booking->host)
                                        <a href="{{ route('admin.overview', $booking->host->id) }}">
                                            {{ $booking->host->first_name }} {{ $booking->host->last_name }}
                                        </a>
                                    @else
                                        <span>--</span>
                                    @endif
                                    </td>

                                    <td>
                                    @if ($booking->user)
                                    <a href="{{ route('admin.overview', $booking->user->id) }}">
                                        {{ $booking->user->first_name ?? '' }} {{ $booking->user->last_name ?? '' }}
                                    </a>
                                    @else
                                        <span>--</span>
                                    @endif
                                    </td>
                                     
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->admin_commission ?? '') }}
                                        </td>
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->vendor_commission ?? '') }}
                                        </td>
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->iva_tax ?? '') }}
                                        </td>
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->deductedAmount ?? '') }}
                                        </td>
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($booking->refundableAmount ?? '') }}
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  
  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: false,
   
   
  });
  let table = $('.datatable-Booking:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
    //   $($.fn.dataTable.tables(true)).DataTable()
    //       .columns.adjust();
  });
  
})

</script>

<script>
 $(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $('#customer').select2({
        ajax: {
            url: "{{ route('admin.searchcustomer') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                // Transform the response data into Select2 format
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.first_name,
                        };
                    })
                };
            },
            cache: true, // Cache the AJAX results to avoid multiple requests for the same data
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error while fetching customer data:", textStatus, errorThrown);
                // Optionally display an error message to the user
                alert("An error occurred while loading customer data. Please try again later.");
            }
        }
    });

    var adminsearchId = "{{ $adminsearchId }}"; // Get user ID from the controller adminsearch
    var adminsearchName = "{{ $adminsearch }}"; // Get user name from the controller

        if (adminsearchId) {
            var option = new Option(adminsearchName, adminsearchId, true, true);
            $('#customer').append(option).trigger('change');
    } 
});

</script>
<script>
 $(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $('#customersearch').select2({
        ajax: {
            url: "{{ route('vendor.searchBookingUser') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                // Transform the response data into Select2 format
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name,
                        };
                    })
                };
            },
            cache: true, // Cache the AJAX results to avoid multiple requests for the same data
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error while fetching customersearch data:", textStatus, errorThrown);
                // Optionally display an error message to the user
                alert("An error occurred while loading customersearch data. Please try again later.");
            }
        }
    });

        var customersearchId = "{{ $customersearchId }}"; 
        var customersearch = "{{ $customersearch }}"; 
        var option = new Option(customersearch, customersearchId, true, true);
        $('#customersearch').append(option).trigger('change'); 
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
                processResults: function (data) {
                    // Transform the response data into Select2 format
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.name,
                            };
                        })
                    };
                },
                cache: true, // Cache the AJAX results to avoid multiple requests for the same data
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error while fetching customer data:", textStatus, errorThrown);
                    // Optionally display an error message to the user
                    alert("An error occurred while loading customer data. Please try again later.");
                }
            }
        });

        var searchfieldItemId = "{{ $searchfieldItemId }}"; 
        var searchfieldItem = "{{ $searchfieldItem }}"; 
        var option = new Option(searchfieldItem, searchfieldItemId, true, true);
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
    $('#resetBtn').click(function(){
        $('#bookingFilterForm')[0].reset();
        var baseUrl = '{{ route('vendor.finance') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
@endsection