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
</style>
@endsection

@section('content')

<!-- Trash   -->
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
                                    <label> {{ trans('global.host') }} </label>
                                    <select class="form-control select2" name="customer" id="customer">
                                        <option value="">{{ $searchfield }}</option>
                                        <!-- Add any other options you want to display -->
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>{{ trans('global.status') }}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="">Please Select Status </option>
                                        <option value="pending"
                                            {{ request()->input('status') === 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="confirmed"
                                            {{ request()->input('status') === 'confirmed' ? 'selected' : '' }}>Confirmed
                                        </option>
                                        <option value="cancelled"
                                            {{ request()->input('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                                        </option>
                                        <option value="declined"
                                            {{ request()->input('status') === 'declined' ? 'selected' : '' }}>Declined
                                        </option>
                                        <option value="completed"
                                            {{ request()->input('status') === 'completed' ? 'selected' : '' }}>Completed
                                        </option>
                                        <option value="refunded"
                                            {{ request()->input('status') === 'refunded' ? 'selected' : '' }}>Refunded
                                        </option>
                                    </select>
                                </div>
                                <!-- <div class="col-md-2 col-sm-12 col-xs-12">
                        <label>{{ trans('global.module') }}</label>
                        <select class="form-control" name="module" id="status">
                            <option value="">Please Select Module </option>
                            <option value="1" {{ request()->input('module') === '1' ? 'selected' : '' }}>Property Module</option>
                            <option value="2" {{ request()->input('module') === '2' ? 'selected' : '' }}>Vehicle</option>
                            <option value="4" {{ request()->input('module') === '4' ? 'selected' : '' }}>Boats</option>
                            <option value="5" {{ request()->input('module') === '5' ? 'selected' : '' }}>Parking</option>
                            <option value="6" {{ request()->input('module') === '6' ? 'selected' : '' }}>Bookable Product</option>
                           
                        </select>
                        </div> -->

                                <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">

                                    <button type="submit" name="btn"
                                        class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                                    <button type="button" name="reset_btn" id="resetBtn"
                                        class="btn btn-primary btn-flat">{{ trans('global.reset') }}</button>
                                </div>
                                {{---<div class="col-md-1 col-sm-2 col-xs-4 mt-5">
								<br>
								
								</div>---}}
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
        
        <div class="row" style="margin-left: 5px; margin-bottom: 6px;">
    <div class="col-lg-12">
        <a class="{{ request()->routeIs('admin.bookings.index') && !request()->query('status') ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index') }}">
            {{ trans('global.live') }}
            <span class="badge badge-pill badge-primary">{{ $statusCounts['live'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'pending' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'pending']) }}">
            Pending
            <span class="badge badge-pill badge-primary">{{ $statusCounts['pending'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'confirmed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}">
            Confirmed
            <span class="badge badge-pill badge-primary">{{ $statusCounts['confirmed'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'cancelled' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}">
            Cancelled
            <span class="badge badge-pill badge-primary">{{ $statusCounts['cancelled'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'declined' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'declined']) }}">
            Declined
            <span class="badge badge-pill badge-primary">{{ $statusCounts['declined'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'completed' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'completed']) }}">
            Completed
            <span class="badge badge-pill badge-primary">{{ $statusCounts['completed'] }}</span>
        </a>

        <a class="{{ request()->query('status') === 'refunded' ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.index', ['status' => 'refunded']) }}">
            Refunded
            <span class="badge badge-pill badge-primary">{{ $statusCounts['refunded'] }}</span>
        </a>

        <!-- Display static 0 for trash if there's no trashed bookings -->
        <a class="{{ request()->routeIs('admin.bookings.trash') ? 'btn btn-primary' : 'btn btn-inactive' }}"
            href="{{ route('admin.bookings.trash') }}">
            {{ trans('global.trash') }} 
            <span class="badge badge-pill badge-primary">{{ $statusCounts['trash'] > 0 ? $statusCounts['trash'] : '0' }}</span>
        </a>
    </div>
</div>

        <!-- Table for bookings -->
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                  Bookings  {{ trans('global.trash') }} {{ trans('global.list') }}

                </div>


                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable datatable-Booking">
                            <thead>
                                <tr>
                                <th width="10">

                                    </th>
                                    <th width="10">{{ trans('global.id') }}</th>
                                    <th>{{$currentModule->name}} {{ trans('global.name') }}</th>
                                    <th>{{ trans('global.host') }}</th>
                                    <th>{{ trans('global.user_title_singular') }}</th>
                                    <th>{{ trans('global.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr data-entry-id="{{ $booking->id }}">
                                <td>

                                </td>
                                <td>
                                    <a target="_blank" class="btn btn-xs btn-primary"
                                        href="{{ route('admin.bookings.show', $booking->id) }}">
                                        {{ $booking->token }}</a>
                                </td>
                                    <td>
                                        <!-- Display the name based on the module -->
                                        {{ $booking->item_title ?? '' }}
                                    </td>
                                    <td>
                                        <!-- Display the host's name -->
                                        {{ $booking->host->first_name ?? '' }} {{ $booking->host->last_name ?? '' }}
                                    </td>
                                    <td>
                                        <!-- Display the user's name -->
                                        {{ $booking->user->first_name ?? '' }} {{ $booking->user->last_name ?? '' }}
                                    </td>
                                    <td>
                                        <!-- Action buttons for trashed bookings -->
                                        <form id="restore-form-{{ $booking->id }}"
                                            action="{{ route('admin.bookings.restore', $booking->id) }}" method="POST"
                                            style="display: inline-block;">
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
                            toastr.success('Booking restored successfully!', 'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                            // Optionally, update UI as needed (e.g., remove the restored item from the list)
                            location.reload(); // Example: Reload the page
                        },
                        error: function(response) {
                            Swal.close();
                            toastr.error('An error occurred while restoring the booking.', 'Error', {
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
                            toastr.success('Booking permanently deleted!', 'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                            // Optionally, update UI as needed (e.g., remove the deleted item from the list)
                            location.reload(); // Example: Reload the page
                        },
                        error: function(response) {
                            Swal.close();
                            toastr.error('An error occurred while deleting the booking.', 'Error', {
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
        return function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
                Swal.fire({
                    title: '{{ trans("global.no_entries_selected") }}',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: '{{ trans("global.are_you_sure") }}',
                text: '{{ trans("global.delete_confirmation") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        method: 'POST',
                        url: route,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(function (response) {
                        location.reload(); // Reload the page after deletion
                    }).fail(function (xhr, status, error) {
                        Swal.fire(
                            '{{ trans("global.error") }}',
                            '{{ trans("global.delete_error") }}',
                            'error'
                        );
                    });
                }
            });
        };
    }

    
    let deleteRoute = "{{ route('admin.bookings.deleteTrashAll') }}"; 
   

    let deleteButton = {
        text: '{{ trans("global.delete_all") }}',
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
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
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