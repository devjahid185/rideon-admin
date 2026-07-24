@extends('layouts.admin')
@section('content')
@php $i = 0; $j = 0; @endphp
<style>
/* Your CSS styles here */
</style>
<div class="content">


    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8"
                id="appusersFilterForm">
                <div class="col-md-12 d-none">
                    <input class="form-control" type="hidden" id="startDate" name="from" value="">
                    <input class="form-control" type="hidden" id="endDate" name="to" value="">
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.date_range') }}</label>
                            <div class="input-group col-xs-12">
                                <!-- Add the input element here -->
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All</option>
                                <option value="1" {{ request()->input('status') == '1' ? 'selected' : '' }}>Active
                                </option>
                                <option value="0" {{ request()->input('status') == '0' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.appUser_title_singular') }}</label>
                            <select class="form-control select2" name="customer" id="customer">
                                <option value="">{{ $searchfield }}</option>
                                <!-- Add any other options you want to display -->
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-4 mt-5 mt-4">
                            <button type="submit" name="btn"
                                class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                            <button type="button" id="resetBtn"
                                class="btn btn-primary btn-flat ">{{ trans('global.reset') }}</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <div style="margin-left: 5px; margin-bottom: 6px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn {{ request()->routeIs('admin.app-users.index') && is_null(request()->query('status')) ? 'btn-primary' : 'btn-inactive' }}" 
                            href="{{ route('admin.app-users.index') }}">
                            {{ trans('global.live') }}
                            <span class="badge badge-pill badge-primary">{{ $statusCounts['live'] > 0 ? $statusCounts['live'] : 0 }}</span>
                        </a>
                        <a class="btn {{ request()->query('status') == '1' ? 'btn-primary' : 'btn-inactive' }}" 
                            href="{{ route('admin.app-users.index', ['status' => 1]) }}">
                            Active
                            <span class="badge badge-pill badge-primary">{{ $statusCounts['active'] > 0 ? $statusCounts['active'] : 0 }}</span>
                        </a>
                        <a class="btn {{ request()->query('status') == '0' ? 'btn-primary' : 'btn-inactive' }}" 
                            href="{{ route('admin.app-users.index', ['status' => 0]) }}">
                            Inactive
                            <span class="badge badge-pill badge-primary">{{ $statusCounts['inactive'] > 0 ? $statusCounts['inactive'] : 0 }}</span>
                        </a>
                        <a class="btn {{ request()->routeIs('admin.app-users.trash') ? 'btn-primary' : 'btn-inactive' }}" 
                            href="{{ route('admin.app-users.trash') }}">
                            {{ trans('global.trash') }}
                            <span class="badge badge-pill badge-primary">{{ $statusCounts['trash'] > 0 ? $statusCounts['trash'] : 0 }}</span>
                        </a>
                    </div>
                </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ trans('global.trash') }} {{ trans('global.list') }}
            <form id="deleteAllForm" action="{{ route('admin.app-users.permanentDeleteAll') }}" method="POST" style="display: inline-block;">
    @csrf
    <button type="button" id="deleteAllButton" class="btn btn-danger">Empty All</button>
</form>



        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-AppUser">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans('global.id') }}</th>
                            <th>{{ trans('global.first_name') }}</th>
                            <th>{{ trans('global.last_name') }}</th>
                            <th>{{ trans('global.email') }}</th>
                            <th>{{ trans('global.phone') }}</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appUsers as $key => $appUser)
                        <tr data-entry-id="{{$appUser->id}}">
                         <td></td>
                            <td>#{{ $appUser->id ?? '' }}</td>
                            <td>{{ $appUser->first_name ?? '' }}</td>
                            <td>{{ $appUser->last_name ?? '' }}</td>
                            <td>{{ $appUser->email ?? '' }}</td>
                            <td>{{ $appUser->phone ?? '' }}</td>
                            <td>
                            <form id="restore-form-{{ $appUser->id }}" action="{{ route('admin.app-users.restore', $appUser->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="button" class="btn btn-xs btn-success restore-btn" data-id="{{ $appUser->id }}">
                                <i class="fa fa-undo" aria-hidden="true"></i>
                            </button>
                        </form>

                        <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $appUser->id }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <nav aria-label="...">
                    <ul class="pagination justify-content-end">
                        {{-- Pagination links here --}}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@endsection
@include('admin.common.addSteps.footer.footerJs')
@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

        function handleDeletion(route) {
            return function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id');
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
                        // Show loader
                        Swal.fire({
                            title: '{{ trans("global.deleting") }}',
                            allowOutsideClick: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            method: 'POST',
                            url: route,
                            data: { ids: ids, _method: 'DELETE' }
                        }).done(function (response) {
                            Swal.close(); // Close the loader
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

        let deleteRoute = "{{ route('admin.app-users.deleteTrashAll') }}"; 

        let deleteButton = {
            text: '{{ trans("global.delete_all") }}',
            className: 'btn-danger',
            action: handleDeletion(deleteRoute) 
        };

        dtButtons.push(deleteButton);

        // Check if the table is already initialized
        if (!$.fn.dataTable.isDataTable('.datatable-AppUser:not(.ajaxTable)')) {
            let table = $('.datatable-AppUser:not(.ajaxTable)').DataTable({ 
                buttons: dtButtons 
            });
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });
    });
</script>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#deleteAllButton').on('click', function() {
            Swal.fire({
                title: '{{ trans('global.are_you_sure') }}',
                text: '{{ trans('global.delete_all_trash_items') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ trans('global.yes_delete_it') }}',
                cancelButtonText: '{{ trans('global.cancel') }}',
            }).then(function(result) {
                if (result.isConfirmed) {
                  
                    Swal.fire({
                        title: '{{ trans('global.deleting') }}',
                        text: '{{ trans('global.please_wait') }}',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    var form = $('#deleteAllForm')[0];
                    var formData = new FormData(form);

                    $.ajax({
                        type: 'POST',
                        url: $(form).attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.close();
                            toastr.success('{{ trans('global.all_permanently_deleted_success') }}', 'Success', {
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
        });
    });
</script>
<script>
$(document).ready(function() {
    // Restore button click handler
    $('.restore-btn').on('click', function() {
        var appUserId = $(this).data('id');
        var form = $('#restore-form-' + appUserId);

        Swal.fire({
            title: "Restore User",
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

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.close();
                        toastr.success('Item restored successfully!', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function(response) {
                        Swal.close();
                        toastr.error('An error occurred while restoring the item.', 'Error', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    }
                });
            }
        });
    });

    // Permanent delete button click handler
    $('.delete-button').on('click', function() {
        var appUserId = $(this).data('id');

        Swal.fire({
            title: "Permanently Delete User",
            text: "Are you sure you want to permanently delete this item?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
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

                $.ajax({
                    url: '{{ route('admin.app-users.permanentDelete', '') }}/' + appUserId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: appUserId
                    },
                    success: function(response) {
                        Swal.close();
                        toastr.success('Item permanently deleted successfully!', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function(response) {
                        Swal.close();
                        toastr.error('An error occurred while deleting the item.', 'Error', {
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>





<script>
$(document).ready(function() {
    // Initialize the Select2 for the customer select box
    $(document).ready(function() {
        // Initialize the Select2 for the customer select box
        $('#customer').select2({
            ajax: {
                url: "{{ route('admin.searchcustomer') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    // Transform the response data into Select2 format
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.first_name,
                            };
                        })
                    };
                },
                cache: true, // Cache the AJAX results to avoid multiple requests for the same data
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error while fetching customer data:", textStatus,
                        errorThrown);
                    // Optionally display an error message to the user
                    alert(
                        "An error occurred while loading customer data. Please try again later.");
                }
            }
        });

        // Preselect the customer option if search term is provided
        // const selectedCustomerId = "{{ request()->input('customer') }}";
        // if (selectedCustomerId) {
        //     const customerSelect = $('#customer');
        //     const option = new Option(selectedCustomerId, selectedCustomerId, true, true);
        //     customerSelect.append(option).trigger('change');
        // }

        // Your other code for DateRangePicker initialization and filters
    });
});
</script>


<!-- Include DateRangePicker -->

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
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                .endOf('month')
            ]
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
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
        'YYYY-MM-DD'));
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
$('.statusdata').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).data('id');
    var requestData = {
        'status': status,
        'pid': id
    };
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    requestData['_token'] = csrfToken;
    $.ajax({

        type: "POST",
        dataType: "json",
        url: '/admin/update-appuser-status',
        data: requestData,
        success: function(response) {
            toastr.success(response.message, '{{ trans("global.success") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    });
})
</script>
<script>
$('.identify').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).data('id');
    var requestData = {
        'verified': status,
        'pid': id
    };
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    requestData['_token'] = csrfToken;
    $.ajax({

        type: "POST",
        dataType: "json",
        url: '/admin/update-appuser-identify',
        data: requestData,
        success: function(response) {
            toastr.success(response.message, '{{ trans("global.success") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    });
})
</script>
<script>
$('.phone_verify').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).data('id');
    var requestData = {
        'phone_verify': status,
        'pid': id
    };
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    requestData['_token'] = csrfToken;
    $.ajax({

        type: "POST",
        dataType: "json",
        url: '/admin/update-appuser-phoneverify',
        data: requestData,
        success: function(response) {
            toastr.success(response.message, '{{ trans("global.success") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    });
})
</script>
<script>
$('.email_verify').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).data('id');
    var requestData = {
        'email_verify': status,
        'pid': id
    };
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    requestData['_token'] = csrfToken;
    $.ajax({

        type: "POST",
        dataType: "json",
        url: '/admin/update-appuser-emailverify',
        data: requestData,
        success: function(response) {
            toastr.success(response.message, '{{ trans("global.success") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
            });
        }
    });
})
</script>
<script>
$('#resetBtn').click(function() {
    $('#appusersFilterForm')[0].reset();
    var baseUrl = '{{ route('admin.app-users.index') }}';
    window.history.replaceState({}, document.title, baseUrl);
    window.location.reload();
});
</script>
@endsection