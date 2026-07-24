@extends('vendor.layout')
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

 <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('vendor.vehicles.create') }}">
    {{ trans('global.add') }} {{ trans('global.vehicle') }}
    </a>
</div>
</div>



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
                            <div class="col-md-2 col-sm-12 col-xs-12">
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
                                <label>Progress</label>
                                <select class="form-control select2" id="step_progress_range" name="step_progress_range">
                                    <option value="">Select a range</option>
                                    <option value="0-25" {{ request()->input('step_progress_range') == '0-25' ? 'selected' : '' }}>0% - 25%</option>
                                    <option value="26-50" {{ request()->input('step_progress_range') == '26-50' ? 'selected' : '' }}>26% - 50%</option>
                                    <option value="51-75" {{ request()->input('step_progress_range') == '51-75' ? 'selected' : '' }}>51% - 75%</option>
                                    <option value="76-100" {{ request()->input('step_progress_range') == '76-100' ? 'selected' : '' }}>76% - 100%</option>
                                </select>
                            </div>
                        

                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <label> {{ trans('global.title') }}</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ request()->input('title', '') }}">

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
        @include('vendor.common.liveTrashSwitcher')

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
                                        {{ trans('global.title') }}
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
                                    <th width="50">
                                        {{ trans('global.price') }}
                                    </th>
                                    <th>
                                        {{ trans('global.place') }}
                                    </th>
                                    <th>
                                        {{ trans('global.verified') }}
                                    </th>
                                    <th>
                                        {{ trans('global.is_featured') }}
                                    </th>
                                    <th>
                                        Step Progress
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
                                    <td>
                                        <a href="{{ route('vendor.vehicles.base', ['id' => $item->id]) }}">
                                            {{ $item->title ?? '' }}
                                        </a>
                                    </td>
                                    <td>{{ $item->item_type ? $item->item_type->name : 'N/A' }}</td>
                                    <td>
                                    {{ $item->userid->first_name ?? '' }} {{ $items->item->last_name ?? '' }}
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
                                        <div class="status-label d-flex justify-content-between align-items-center">
                                            <i class="fa {{ $item->is_verified ? 'fa-check text-success' : 'fa-times text-danger' }} fa-2x"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-label d-flex justify-content-between align-items-center">
                                            <i class="fa {{ $item->is_featured ? 'fa-check text-success' : 'fa-times text-danger' }} fa-2x"></i>
                                        </div>
                                    </td>

                                    <td>
                                        @php
                                        $completionPercentage = $item->step_progress ?? 0;
                                        @endphp

                                        <div class="progress-circle" data-item-id="{{ $item->id }}" style="background: conic-gradient(#28c76f {{ $completionPercentage }}%, #dd4b39 {{ $completionPercentage }}% 100%);">
                                            <span>{{ round($completionPercentage) }}%</span>
                                        </div>
                                    </td>
                                    <td>

                                    <div class="status-label d-flex justify-content-between align-items-center" title = "Contact Admin">
                                            <i class="fa {{ $item->status ? 'fa-check text-success' : 'fa-times text-danger' }} fa-2x"></i>
                                        </div>
                                        <div class="status-toggle d-flex justify-content-between align-items-center" style = "display: none;">
                                            <input data-id="{{$item->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $item->status ? 'checked' : '' }}>
                                            <label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
                                        </div>
                                    </td>

                                    <td>
                                    
                                      

                                        <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="{{ route('vendor.vehicles.base', ['id' => $item->id]) }}">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                              
                                        <button type="button" class="btn btn-xs btn-danger delete-new-button" data-id="{{ $item->id }}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    

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


@endsection

@include('admin.common.addSteps.footer.footerJs')
@section('scripts')
@parent

<script>
    $('.statusdata').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var id = $(this).data('id');
    var $toggle = $(this);

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var userToken = '{{ auth()->user()->token }}';

    var requestData = {
        'token': userToken,
        'status': 1,
        'pid': id
    };
    requestData['_token'] = csrfToken;
    $.ajax({
        type: "POST",
        dataType: "json",
        url: '{{ route('vendor.vehicles.itemUnpublishedByVendor') }}',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: requestData,
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.message, '{{ trans("global.success") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
            } else if (response.status === 400) { // Handle the Contact Admin message
                    toastr.error(response.message, 'Cannot update', {
                        CloseButton: true,
                        ProgressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    $toggle.prop('checked', !status);
              } else {
                toastr.error(response.message, 'Cannot update', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
                $toggle.prop('checked', !status);
            }
        },
        error: function(xhr, status, error) {
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
            const url = `{{ url('vendor') }}/${realRoute}/${itemId}`;
           
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
                url: "{{ route('vendor.itemtypeSearch') }}",
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
    const incompleteStepUrl = "{{ route('vendor.item-incomplete-steps') }}";
    const progressCircles = document.querySelectorAll('.progress-circle');

    progressCircles.forEach(progressCircle => {
        // Function to show tooltip
        function showTooltip() {
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

        // Add event listeners
        progressCircle.addEventListener('mouseenter', showTooltip);
        progressCircle.addEventListener('mouseleave', hideTooltip);
    });
});

</script>

@endsection