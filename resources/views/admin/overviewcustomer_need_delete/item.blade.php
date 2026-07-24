@extends('layouts.admin')
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
@php $i = 0; $j = 0; @endphp
<div class="content">
<!--/*seaction 1 start here*/-->
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
					<form class="form-horizontal" id="itemFilterForm" action="" method="GET" accept-charset="UTF-8">
                    @csrf
						<div >
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
                                    <select class="form-control select2" name="item_title" id="itemTitle">
                                        <option value="">{{ $searchfield }}</option>
                                     
                                    </select>
                                </div>


								<div class="col-md-2 col-sm-2 col-xs-4 mt-5 ">
                                        <br>
                                        <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                         <!-- <button type="submit" name="reset_btn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button> -->
                                         <button type="button" name="resetBtn" id="resetBtn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                                        </div>
                                        
							</div>
						</form>
						</div> 
					
                 </div> 
                
                  
  <!-- select end -->
    <!--/*seaction 1  end*/-->
    <div >
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
                                        {{ trans('global.id') }}
                                    </th>
                                    <th>
                                        {{ trans('global.title') }}
                                    </th>
                                    <th>
                                        {{ trans('global.module') }}
                                    </th>
                                  
                                   
                                    <th width="50">
                                        {{ trans('global.price') }}
                                    </th>
                                    <th>
                                        {{ trans('global.place') }}
                                    </th>
                                    <th>
                                        {{ trans('global.is_verified') }}
                                    </th>
                                    <th>
                                        {{ trans('global.is_featured') }}
                                    </th>
                                    <th>
                                        {{ trans('global.status') }}
                                    </th>
                                   
                                    <th>&nbsp;
                                        
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key => $item)
                                    <tr data-entry-id="{{ $item->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $item->id ?? '' }}
                                        </td>
                                        <td>
                                        @switch($item->module)
                                        @case(1)
                                            <a target="_blank" href="{{ route('admin.vehicles.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @case(2)
                                            <a target="_blank" href="{{ route('admin.vehicles.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @case(3)
                                            <a target="_blank" href="{{ route('admin.boat.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @case(4)
                                            <a target="_blank" href="{{ route('admin.parking.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @case(5)
                                            <a target="_blank" href="{{ route('admin.bookable.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @case(6)
                                            <a target="_blank" href="{{ route('admin.space.base', $item->id) }}">{{ $item->title ?? '' }}</a>
                                            @break
                                        @default
                                            <!-- Add your code for other cases here -->
                                    @endswitch

                                        
                                        </td>
                                        <td>
                                            {{ $item->moduleName->name ?? '' }}
                                        </td>
                                    
                                       
                                       
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($item->price ?? '') }}

                                        </td>
                                        <td>
                                            {{ $item->place->city_name ?? '' }}
                                        </td>
                                        <td>
                                      
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$item->id}}" class="check isvefifieddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $item->is_verified ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$item->id}}" class="check isfeatureddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $item->is_featured ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$item->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $item->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                       
                                        <td>
                                          

                                            @can('property_edit')

                                           
                                            @switch($item->module)
                                        @case(1)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.vehicles.base', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @case(2)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.vehicles.base', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @case(3)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.boat.base', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @case(4)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.parking.base', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @case(5)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.bookable.base', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @case(6)
                                            <a target="_blank" class="btn btn-xs btn-info" href="{{ route('admin.space.description', $item->id) }}"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
                                            @break
                                        @default
                                            <!-- Add your code for other cases here -->
                                    @endswitch

                                               
                                            @endcan

                                            @can('property_delete')
                                                <form action="{{ route('admin.vehicles.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $item->id }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                                </form>
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
                <a class="page-link" href="{{ $items->previousPageUrl() }}" tabindex="-1">Previous</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
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
                <a class="page-link" href="{{ $items->nextPageUrl() }}">Next</a>
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
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    // pageLength: 10,
  });
  let table = $('.datatable-Property:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>


<script>
    $(document).ready(function() {
        $('.filterproduct').click(function(e){
            e.preventDefult();
            $(this).closest('form').submit();
        })
        $('.resetproduct').on('click', function(e) {
        e.preventDefault();
        
        // Call the reset function
        resetFilters();
        
        // Submit the form after resetting
        $(this).closest('form').submit();
    });

    // Reset function to clear filters
    function resetFilters() {
        $('#daterange-btn').val(''); // Reset the date range input
        $('#startDate').val('');     // Reset the hidden startDate field
        $('#endDate').val('');       // Reset the hidden endDate field
        $('#status').val('');        // Reset the status dropdown
        $('#status').trigger('change'); // Trigger change event for Select2 if used

        $('#itemTitle').val(''); // Reset item title select
        $('#itemTitle').trigger('change'); // Trigger change event for Select2
    }
        // Initialize the Select2 for the customer select box
        $('#itemTitle').select2({
            ajax: {
                url: "{{ route('admin.searchItem') }}",
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

document.getElementById('resetBtn').addEventListener('click', function() {

document.getElementById('itemFilterForm').reset();

$('.select2').val('').trigger('change');

$('#itemFilterForm').submit();
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
				url: '/admin/update-item-status', 
				data: requestData, 
				success: function(response){ 
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
				url: '/admin/update-item-featured', 
				data: requestData, 
				success: function(response){ 
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
				url: '/admin/update-item-verified', 
				data: requestData, 
				success: function(response){ 
					toastr.success(response.message, '{{ trans("global.success") }}', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				} 
			}); 
		})
</script>
<!-- reset button search filter -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // JavaScript to handle the SweetAlert dialog
    document.addEventListener('DOMContentLoaded', function() {
        // Add a click event listener to the "Delete" button
        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function() {
                var deleteUrl = this.form.action; // Get the form's action URL

                Swal.fire({
                    title: "{{ trans('global.are_you_sure') }}",
                    text: "{{ trans('global.you_able_revert_this') }}",
                    icon: "{{ trans('global.warning') }}",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ trans('global.yes_delete_it') }}",
                    cancelButtonText: "{{ trans('global.cancel') }}",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(function(response) {
                            if (response.ok) {
                              
                                location.reload();
                            } 
                        });
                        location.reload();
                    }
                });
            });
        });
    });
</script>
@endsection