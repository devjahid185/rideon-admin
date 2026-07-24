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

    .progress-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        border: 2px solid #ddd;
        color: white;
        pointer-events: auto;
        z-index: 10;
    }

    .tooltip1 {
        position: absolute;
        background: #333;
        color: #fff;
        padding: 5px;
        border-radius: 3px;
        font-size: 12px;
        top: 100%;
        /* Adjust as needed */
        left: 50%;
        /* Adjust as needed */
        transform: translateX(-50%);
        white-space: nowrap;
        z-index: 20;
    }
</style>
@endsection
@section('content')
@php $i = 0; $j = 0;
if($title=='vehicles')
$title='vehicle';
else
$title=$title;

@endphp
<div class="content">

    @can($title.'_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.'.$realRoute.'.create') }}">
                {{ trans('global.add') }} {{ $title}}
            </a>
        </div>
    </div>
    @endcan



    <div class="row">

        <div class="col-lg-12">
            <div class="box">
                <div class="box-body">
                    <form class="form-horizontal" id="itemFilterForm" action="" method="GET" accept-charset="UTF-8">

                        <div>
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>


                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12">
                                <label>Type</label>
                                <select class="form-control select2" name="type" id="type">
                                    <option value=""> {{$typeName}} </option>

                                </select>
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <label>{{ trans('global.date_range') }}</label>
                                <div class="input-group col-xs-12">

                                    <input type="text" class="form-control" id="daterange-btn">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <label> {{ trans('global.status') }}</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="">All</option>
                                    <option value="active" {{ request()->input('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request()->input('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="verified" {{ request()->input('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="featured" {{ request()->input('status') === 'featured' ? 'selected' : '' }}>Featured</option>
                                </select>

                            </div>
                            

                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <label>{{ trans('global.host') }}</label>
                                <select class="form-control select2" name="vendor" id="vendor">
                                    <option value="">{{ $vendorname }}</option>

                                </select>
                            </div>
                        
                            <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">
                                <br>
                                <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                <button type="button" id="resetBtn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                            </div>

                        </div>

                </div>
                </form>
            </div>

        </div>
        @include('admin.common.liveTrashSwitcher')

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.' . strtolower($title)) }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable  datatable-Item">
                            <thead>
                                <tr>
                                    <th>

                                    </th>
                                    <th>
                                        {{ trans('global.id') }}#
                                    </th>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        {{ trans('global.host') }}
                                    </th>
                                    <th>
                                        {{ trans('global.image') }}
                                    </th>
                                    <th>
                                        Document
                                    </th>
                                    <th>
                                        Insurance
                                    </th>
                                    <th width="50">
                                        {{ trans('global.price') }}
                                    </th>
                                    <th>
                                        {{ trans('global.place') }}
                                    </th>
                                   
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>

                                    <th> {{ trans('global.actions') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key => $item)
                                <tr data-entry-id="{{$item->id}}">
                                    <td>
                                    </td>
                                    <td>
                                        {{ $item->id ?? '' }}
                                    </td>
                                    <td>{{ $item->item_type ? $item->item_type->name : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.overview', $item->userid_id ?? '#') }}">
                                            {{ $item->userid->first_name ?? '' }} {{ $items->item->last_name ?? '' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($item->front_image)
                                        <a href="{{ $item->front_image->url}}">
                                            <img src="{{ $item->front_image->thumbnail }}" alt="{{ $item->title }}" class="item-image-size">
                                        </a>

                                        @endif
                                    </td>

                                    <td>
                                        @if($item->front_image_doc)
                                        <a href="{{ $item->front_image_doc->url}}">
                                            <img src="{{ $item->front_image_doc->thumbnail }}" alt="{{ $item->title }}" class="item-image-size">
                                        </a>

                                        @endif
                                    </td>
                                    <td>
                                        @if($item->item_insurance_doc)
                                        <a href="{{ $item->item_insurance_doc->url}}">
                                            <img src="{{ $item->item_insurance_doc->thumbnail }}" alt="{{ $item->title }}" class="item-image-size">
                                        </a>

                                        @endif
                                    </td>
                                    <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($item->price ?? '') }}

                                    </td>
                                    <td>
                                        @php
                                        $parts = [];
                                        if (!empty($item->city_name)) {
                                        $parts[] = $item->city_name;
                                        }
                                        if (!empty($item->state_region)) {
                                        $parts[] = $item->state_region;
                                        }
                                        if (!empty($item->country)) {
                                        $parts[] = $item->country;
                                        }
                                        @endphp

                                        {{ implode(' , ', $parts) }}
                                    </td>
                                    
                                    <td>
                                    @php
                                        $statuses = $item->itemMeta->whereIn('meta_key', [
                                            'item_driving_licence_status',
                                            'item_inspection_certificate_status',
                                            'item_registration_status'
                                        ])->pluck('meta_value');

                                        if ($statuses->contains('pending')) {
                                            $iconColor = 'text-warning'; // Yellow if any pending
                                        } elseif ($statuses->contains('rejected')) {
                                            $iconColor = 'text-danger'; // Red if any rejected
                                        } else {
                                            $iconColor = 'text-success'; // Green if all approved
                                        }
                                    @endphp



                                    <i class="fa fa-file-alt fa-lg view-item-documents {{ $iconColor }}" data-id="{{ $item->id }}" 
                                    style="cursor: pointer;" title="View Item Documents"></i>


                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input data-id="{{$item->id}}" class="check statusdata" type="checkbox" data-onstyle="success" 
                                            id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" 
                                            data-on="Active" data-off="InActive" {{ $item->status ? 'checked' : '' }}>
                                        <label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
                                    </div>
                                </td>


                                    <td>
                                        @can($title.'_edit')
                                        @php
                                        $base = $realRoute;
                                        @endphp

                                        <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="{{ route('admin.'.$base.'.base', ['id' => $item->id]) }}">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                        @endcan

                                        @can('title_delete')
                                        <button type="button" class="btn btn-xs btn-danger delete-new-button" data-id="{{ $item->id }}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                        @endcan

                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end">
                                {{-- Previous Page Link --}}
                                @if ($items->currentPage() > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $items->previousPageUrl() }}" tabindex="-1"> {{ trans('global.previous') }}</a>
                                </li>
                                @else
                                <li class="page-item disabled">
                                    <span class="page-link"> {{ trans('global.previous') }}</span>
                                </li>
                                @endif

                                {{-- Numeric Pagination Links --}}
                                @for ($i = 1; $i <= $items->lastPage(); $i++)
                                    <li class="page-item {{ $i == $items->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($items->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $items->nextPageUrl() }}"> {{ trans('global.next') }}</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled">
                                        <span class="page-link"> {{ trans('global.next') }}</span>
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

<div class="modal fade" id="itemDocumentsModal" tabindex="-1" role="dialog" aria-labelledby="itemDocumentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itemDocumentsModalLabel">Item Documents</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="item-documents-modal-table">
          <thead>
            <tr>
              <th>Document Type</th>
              <th>Image</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Documents will be loaded dynamically -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@include('vendor.vehicles.addSteps.footerJs')
@section('scripts')
@parent
<script>
    $('.isfeatureddata').change(function() {
        var isfeatured = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');
        var requestData = {
            'featured': isfeatured,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;
        $.ajax({

            type: "POST",
            dataType: "json",
            url: 'update-item-featured',
            data: requestData,
            success: function(response) {
                toastr.success(response.message, '{{ trans("global.success") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
            },
            error: function(response) {
                if(response.status === 403) {
                    toastr.error(response.responseJSON.message, 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                } else {
                    toastr.error('Something went wrong. Please try again.', 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                }
            }
        });
    })
</script>
<script>
    $('.statusdata').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');
        var $toggle = $(this);
        var requestData = {
            'status': status,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;

        $.ajax({
            type: "POST",
            dataType: "json",
            url: 'update-item-status',
            data: requestData,
            success: function(response) {
                if (response.status === 200) { // Success response
                    toastr.success(response.message, '{{ trans("global.success") }}', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                } else { // Handle error response
                    toastr.error(response.message, 'Can not update', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    $toggle.prop('checked', !status);
                }
            },
            error: function(xhr, status, error) { // AJAX error handling
                toastr.error('Something went wrong. Please try again.', '{{ trans("global.error") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
                $toggle.prop('checked', !status);
            }
        });
    })
</script>

<script>
    $('.isvefifieddata').change(function() {
        var isvefified = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');
        var requestData = {
            'isverified': isvefified,
            'pid': id
        };
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;
        $.ajax({

            type: "POST",
            dataType: "json",
            url: 'update-item-verified',
            data: requestData,
            success: function(response) {
                if (response.status === 200) { // Success response
                    toastr.success(response.message, '{{ trans("global.success") }}', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                } else { // Handle error response
                    toastr.error(response.message, 'Can not update', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    $toggle.prop('checked', !status);
                }
            },
            error: function(xhr, status, error) { // AJAX error handling
                toastr.error('Something went wrong. Please try again.', '{{ trans("global.error") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
                $toggle.prop('checked', !status);
            }
        });
    })
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-new-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const realRoute = '{{ $realRoute }}'; // Assuming $realRoute is passed to the view

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
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
                        deleteBooking(realRoute, itemId);
                    }
                });
            });
        });

        function deleteBooking(realRoute, itemId) {
            const url = `{{ url('admin') }}/${realRoute}/${itemId}`;

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    toastr.success('Item deleted successfully', 'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    // Optionally, refresh the page or update UI as needed
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    toastr.error('Error deleting item', 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    console.error(error);
                }
            });
        }
    });
</script>
<script>
    $(document).ready(function() {
        // Initialize the Select2 for the customer select box
        $('#vendor').select2({
            ajax: {
                url: "{{ route('admin.searchHost') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    //console.log(data); // Debug the entire data response
                    // Transform the response data into Select2 format
                    return {
                        results: $.map(data, function(item) {
                            //console.log(item); // Debug each item
                            return {
                                id: item.id,
                                text: item.first_name,
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

        var vendorId = "{{ $vendorId }}"; 
        var vendorname = "{{ $vendorname }}"; 

        if (vendorId) {
            var option = new Option(vendorname, vendorId, true, true);
            $('#vendor').append(option).trigger('change');
        }

        $('#type').select2({
            ajax: {
                url: "{{ route('admin.typeSearch') }}",
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
                    console.error("Error while fetching vehicle types:", textStatus, errorThrown);
                    // Optionally display an error message to the user
                    
                }
            }
        });
        var selectedUserId = "{{ $typeId }}"; // Get user ID from the controller
        var selectedUserName = "{{ $typeName }}"; // Get user name from the controller

        if (selectedUserId) {
            var option = new Option(selectedUserName, selectedUserId, true, true);
            $('#type').append(option).trigger('change');
        }

    });

    document.getElementById('resetBtn').addEventListener('click', function() {
        // Clear form fields
       
       
        document.getElementById('itemFilterForm').reset();
        document.getElementById('title').value = '';
        $('.select2').val('').trigger('change');


        $('#itemFilterForm').submit();
    });
</script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    const incompleteStepUrl = "{{ route('admin.incomplete-steps') }}";
    const progressCircles = document.querySelectorAll('.progress-circle');

    progressCircles.forEach(progressCircle => {
        // Function to show tooltip
        function showTooltip() {

            const allTooltips = document.querySelectorAll('.tooltip1');
            allTooltips.forEach(tooltip => tooltip.remove());

            const itemId = progressCircle.getAttribute('data-item-id');

            fetch(`${incompleteStepUrl}?pid=${itemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        const incompleteSteps = data.incomplete_steps;
                      
                        let tooltip = progressCircle.querySelector('.tooltip1');

                        // Remove existing tooltip if it exists
                        if (tooltip) {
                            tooltip.remove();
                        }

                        let tooltipStep = document.createElement('div');
                        tooltipStep.className = 'tooltip1';

                        if (incompleteSteps.length > 0) {
                           
                            tooltipStep.innerHTML = 'Incomplete steps: ' + incompleteSteps.join(', ');
                        } else {
                            tooltipStep.innerHTML = 'All steps are completed.';
                        }

                        progressCircle.appendChild(tooltipStep);
                        tooltipTimeout = setTimeout(() => {
                            tooltipStep.remove();
                        }, 1000);
                    } else if (data.status === 204) {
                       
                    } else if (data.status === 400) {
                        console.log("400: Invalid data.");
                    } else {
                        console.log("Unexpected status: " + data.status);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to hide tooltip
        function hideTooltip() {
            let tooltip = progressCircle.querySelector('.tooltip1');
            if (tooltip) {
                tooltip.remove();
            }
        }
        function hideTooltip() {
            // Clear the timeout if it's still active
            clearTimeout(tooltipTimeout);

            // Remove all tooltips
            const allTooltips = document.querySelectorAll('.tooltip1');
            allTooltips.forEach(tooltip => tooltip.remove());
        }

        // Add event listeners for mouse hover actions
        progressCircle.addEventListener('mouseenter', showTooltip);
        progressCircle.addEventListener('mouseleave', hideTooltip);
    });
  
});

</script>

<!-- Include DateRangePicker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize the DateRangePicker
        $('#daterange-btn').daterangepicker({
            opens:'right', // Change the calendar position to the left side of the input
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
            $('#host').val('').trigger('change');
            $('#customer').val('').trigger('change');
        }

        // Optional: Submit the form when the "Filter" button is clicked
        $('button[name="btn"]').on('click', function() {
            $('form').submit();
        });

        

        document.getElementById('resetBtn').addEventListener('click', function() {
        // Clear form fields
        document.getElementById('itemFilterForm').reset();

        // Optionally, reset the date range picker if needed
        // For example, if you're using a date picker library, you might need to reset it separately

        // If you're using select2, you may need to manually trigger the reset for it
        $('.select2').val('').trigger('change');
        
        
        $('#itemFilterForm').submit();
    });
    });
</script>
<script>


$(document).ready(function () {
    $('.view-item-documents').on('click', function () {
        var itemId = $(this).data('id');
        $('#loader').show();

        $.ajax({
            url: "{{ route('admin.get-item-documents') }}",
            method: "POST",
            data: { item_id: itemId, _token: "{{ csrf_token() }}" },
            success: function (response) {
                $('#item-documents-modal-table tbody').empty();
                var defaultImagePath = "{{ asset('public/images/icon/userdefault.jpg') }}";

                $.each(response.data.documents, function (key, value) {
                    var imageUrl = value.image || defaultImagePath;
                    var status = capitalizeFirstLetter(value.status || "Pending");
                    var statusColor = getStatusColor(value.status);

                    $('#item-documents-modal-table tbody').append(
                        `<tr data-key="${key}">
                            <th>${capitalizeFirstLetter(key.replace(/_/g, " "))}</th>
                            <td><a href="${imageUrl}" target="_blank">
                                <img src="${imageUrl}" alt="${key}" style="max-width: 200px; height: auto;">
                            </a></td>
                            <td><span class="status-label" style="${statusColor}">${status}</span></td>
                            <td>
                                <button class="btn btn-success update-status" data-id="${itemId}" data-key="${key}" data-status="approved">Approve</button>
                                <button class="btn btn-danger update-status" data-id="${itemId}" data-key="${key}" data-status="rejected">Reject</button>
                            </td>
                        </tr>`
                    );
                });

                $('#itemDocumentsModal').modal('show');
            },
            error: function () {
                console.error("Failed to load item documents.");
            },
            complete: function () {
                $('#loader').hide();
            },
        });
    });

    $(document).on("click", ".update-status", function () {
    var itemId = $(this).data("id");
    var metaKey = $(this).data("key");
    var status = $(this).data("status");

    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to change the status?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#dc3545",
        confirmButtonText: "Yes, change it!",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.update-item-document-status') }}",
                method: "POST",
                data: { item_id: itemId, meta_key: metaKey, status: status, _token: "{{ csrf_token() }}" },
                success: function (response) {
                    var statusText = capitalizeFirstLetter(response.status);
                    var statusColor = getStatusColor(response.status);

                    var statusElement = $(`tr[data-key="${metaKey}"] .status-label`);
                    if (statusElement.length) {
                        statusElement.text(statusText).attr("style", statusColor);
                    } else {
                        console.warn("Status element not found for metaKey:", metaKey);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON ? xhr.responseJSON.message : "Something went wrong!",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
});

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function getStatusColor(status) {
        return status === "approved"
            ? "background-color: #28a745; color: white; padding: 5px; border-radius: 5px;"
            : status === "rejected"
            ? "background-color: #dc3545; color: white; padding: 5px; border-radius: 5px;"
            : "background-color: #ffc107; color: black; padding: 5px; border-radius: 5px;";
    }
});

</script>
@endsection