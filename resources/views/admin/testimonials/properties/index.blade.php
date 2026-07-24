@extends('layouts.admin')
@section('content')
@php $i = 0; $j = 0; @endphp
<div class="content">
    @can('property_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.properties.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.property.title_singular') }}
                </a>
            </div>
        </div>
    @endcan

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
        
.dataTables_length{
    display:none;
}
    </style>

    <div class="row">

    <div class="col-lg-12">
				<div class="box">
					<div class="box-body"> 
					<form class="form-horizontal" id="propertyFilterForm" action="" method="GET" accept-charset="UTF-8">
						
						<div >
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>
						
					
						<div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>Date Range</label>
                            <div class="input-group col-xs-12">
                              
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
								<div class="col-md-3 col-sm-12 col-xs-12">
                                        <label>Status</label>
                                        <select class="form-control select2" name="status" id="status">
                                        <option value="">All</option>
                                        <option value="active" {{ request()->input('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request()->input('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="verified" {{ request()->input('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="featured" {{ request()->input('status') === 'featured' ? 'selected' : '' }}>Featured</option>
                                    </select>

								</div>
								
                                <div class="col-md-2 col-sm-12 col-xs-12">
                            <label>Customer</label>
                            <select class="form-control select2" name="customer" id="customer">
                                <option value="">{{ $customername }}</option>
                               
                            </select>
                        </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>Title</label>
                                    <input type="text" class="form-control" name ="title" value="{{ $property_title ?: '' }}">
                                    <!-- <select class="form-control select2" name="property_title" id="customer">
                                        <option value="">{{ $searchfield }}</option>
                                     
                                    </select> -->
                                </div>


								
								<div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">
                                        <br>
                                        <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">Filter</button>
                                         <button type="button" id="resetBtn"  class="btn btn-primary btn-flat resetproduct">Reset</button>
                                        </div>
                                        
							</div>
						
						</div>
					</form>
                 </div> 
                
                    </div>

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.property.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Property">
                            <thead>
                                <tr>
                                    <th width="10">

                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.title') }}
                                    </th>
                                    <th>
                                       Host <!-- {{ trans('cruds.property.fields.userid') }} -->
                                    </th>
                                    <!-- <th>
                                        {{ trans('cruds.appUser.fields.middle') }}
                                    </th> -->
                                    <!-- <th>
                                        {{ trans('cruds.property.fields.property_type') }}
                                    </th> -->
                                    <!-- <th>
                                        {{ trans('cruds.property.fields.property_rating') }}
                                    </th> -->
                                    <th width="50">
                                        {{ trans('cruds.property.fields.price') }}
                                    </th>
                                    <th>
                                     Location   <!-- {{ trans('cruds.property.fields.place') }} -->
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.is_verified') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.is_featured') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.status') }}
                                    </th>
                                    <!-- <th>
                                        {{ trans('cruds.property.fields.weekly_discount') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.weekly_discount_type') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.monthly_discount') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.property.fields.monthly_discount_type') }}
                                    </th> -->
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($properties as $key => $property)
                                    <tr data-entry-id="{{ $property->id }}">
                                        <td>

                                        </td>
                                        <td>
                                            {{ $property->id ?? '' }}
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.properties.edit', $property->id) }}"> 
                                            {{ $property->title ?? '' }}
                                        </a>
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.overview', $property->userid_id) }}">
                                            {{ $property->userid->first_name ?? '' }} {{ $property->userid->last_name ?? '' }}
                                            </a>
                                        </td>
                                        <!-- <td>
                                            {{ $property->userid->middle ?? '' }}
                                        </td> -->
                                        <!-- <td>
                                            {{ $property->property_type->name ?? '' }}
                                        </td> -->
                                        <!-- <td>
                                            {{ $property->property_rating ?? '' }}
                                        </td> -->
                                       
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($property->price ?? '') }}

                                        </td>
                                        <td>
                                            {{ $property->place->city_name ?? '' }}
                                        </td>
                                        <td>
                                            <!-- {{ App\Models\Property::IS_VERIFIED_SELECT[$property->is_verified] ?? '' }} -->
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check isvefifieddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->is_verified ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                            <!-- {{ App\Models\Property::IS_FEATURED_SELECT[$property->is_featured] ?? '' }} -->
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check isfeatureddata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->is_featured ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                            <!-- {{ App\Models\Property::STATUS_SELECT[$property->status] ?? '' }} -->
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$property->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $property->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <!-- <td>
                                            {{ $property->weekly_discount ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Property::WEEKLY_DISCOUNT_TYPE_SELECT[$property->weekly_discount_type] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $property->monthly_discount ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Property::MONTHLY_DISCOUNT_TYPE_SELECT[$property->monthly_discount_type] ?? '' }}
                                        </td> -->
                                        <td>
                                            @can('property_edit')
                                                <a  style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="{{ route('admin.properties.edit', $property->id) }}">
                                                  <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            @endcan

                                            <!-- @can('property_delete')
                                                <form action="{{ route('admin.properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                @method('DELETE')
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                  <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                </form>
                                            @endcan -->
                                            @can('property_delete')
                                            <form action="{{ route('admin.properties.destroy', $property->id) }}" method="POST" style="display: inline-block;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $property->id }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
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
        @if ($properties->currentPage() > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $properties->previousPageUrl() }}" tabindex="-1">Previous</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
            </li>
        @endif

        {{-- Numeric Pagination Links --}}
        @for ($i = 1; $i <= $properties->lastPage(); $i++)
            <li class="page-item {{ $i == $properties->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $properties->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Next Page Link --}}
        @if ($properties->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $properties->nextPageUrl() }}">Next</a>
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

<!-- Include jQuery -->
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
@can('property_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.properties.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        alert();
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

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
        $('.resetproduct').click(function(e){
            e.preventDefult();
            $(this).closest('form').submit();
        })
        // Initialize the Select2 for the customer select box
        $('#customer').select2({
            ajax: {
                url: "{{ route('admin.searchproperty') }}",
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
				url: 'update-property-status', 
				data: requestData, 
				success: function(response){ 
					toastr.success(response.message, "Success!", {
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
				url: 'update-property-featured', 
				data: requestData, 
				success: function(response){ 
					toastr.success(response.message, "Success!", {
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
				url: 'update-property-vefified', 
				data: requestData, 
				success: function(response){ 
					toastr.success(response.message, "Success!", {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
				} 
			}); 
		})
</script>
<!-- reset button search filter -->
<script>
    $('#resetBtn').click(function(){
        $('#propertyFilterForm')[0].reset();
        var baseUrl = '{{ route('admin.properties.index') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
<!-- customer select2  -->
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
});

</script>


@endsection