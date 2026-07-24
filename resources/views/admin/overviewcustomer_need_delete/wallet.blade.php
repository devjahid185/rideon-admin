@extends('layouts.admin')
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
                    {{ trans('global.wallet') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Property">
                        <thead>
                            <tr> 
                            <th>
                            {{ trans('global.id') }}
                                </th>
                                <th>
                                {{ trans('global.user') }}
                                </th>
                             
                                <th>
                                {{ trans('global.amount') }}
                                </th>
                                <th>
                                {{ trans('global.wallet_type') }}
                                </th>
                                <th>
                                {{ trans('global.description') }} 
                                </th>
                                <th>
                                {{ trans('global.status') }}
                                </th>
                               
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($wallets as $key => $payout)
                             
                                    <tr data-entry-id="{{ $payout->id }}">
                                        
                                        <td>
                                            {{ $payout->id ?? '' }}
                                        </td>
                                      
                                        <td>
                                        <a href="{{ route('admin.overview', $payout->user_id) }}">
                                            {{ $payout->appUser->first_name ?? '' }} {{ $payout->appUser->last_name}}
                                        </a> 
                                        </td>
                                        <td>
                                            {{ $payout->amount ?? '' }}
                                        </td>
                                        
                                        <td>
                                            {{ $payout->type ?? '' }}
                                            
                                        </td>
                                        <td>
                                            {{ $payout->description ?? '' }}
                                        </td>
                                        <td>
                                        <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$payout->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $payout->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                       
    
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="...">
    <ul class="pagination justify-content-end">
        {{-- Previous Page Link --}}
        @if ($wallets->currentPage() > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $wallets->previousPageUrl() }}" tabindex="-1">{{ trans('global.previous') }}</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">{{ trans('global.previous') }}</span>
            </li>
        @endif

        {{-- Numeric Pagination Links --}}
        @for ($i = 1; $i <= $wallets->lastPage(); $i++)
            <li class="page-item {{ $i == $wallets->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $wallets->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        {{-- Next Page Link --}}
        @if ($wallets->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $wallets->nextPageUrl() }}">{{ trans('global.next') }}</a>
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
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
</script>

<!-- Include DateRangePicker -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
    // Fetch the selected status from local storage
    const selectedStatus = localStorage.getItem('selectedStatus');

    // Check if the selected status is not null or empty
    if (selectedStatus) {
        // Set the selected status in the dropdown
        $('#status').val(selectedStatus);
    }

    // Event handler when the status value changes
    $('#status').on('change', function () {
        // Store the selected status in local storage
        const selectedValue = $(this).val();
        localStorage.setItem('selectedStatus', selectedValue);
    });

    // Check if the URL parameters for status, from, and to are empty
    const urlStatus = "{{ request()->input('status') }}";
    const urlFrom = "{{ request()->input('from') }}";
    const urlTo = "{{ request()->input('to') }}";

    if (!urlStatus) {
        // If all URL parameters are empty, remove the stored status value from local storage
        localStorage.removeItem('selectedStatus');
    }
});
</script>


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

document.getElementById('resetBtn').addEventListener('click', function() {

        document.getElementById('itemFilterForm').reset();

        $('.select2').val('').trigger('change');

        $('#itemFilterForm').submit();
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
				url: '/admin/update-wallet-status', 
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
@endsection