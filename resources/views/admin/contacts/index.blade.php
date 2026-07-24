@extends('layouts.admin')
@section('content')
@php $i = 0; $j = 0; @endphp

<div class="content">
    @can('contact_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.contacts.create') }}">
            {{ trans('global.add') }} {{ trans('global.contacttitle_singular') }}
            </a>
        </div>
    </div>
    @endcan
    <!-- filter -->
    <div class="box">
					<div class="box-body"> 
					<form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8" id="contactFilterForm">
						
					
						
						<div class="col-md-12">
						<div class="row">
                    
                        
                        <div class="col-md-3 col-sm-12 col-xs-12">
                        <label>{{ trans('global.status') }}</label>
                        <select class="form-control" name="status" id="status">
                          <option value="">All</option>
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.user') }}</label>
                            <select class="form-control select2" name="user" id="customer">
                                <option value="">{{ $userName }}</option>
                               
                            </select>
                        </div>

								<div class="col-md-2 col-sm-2 col-xs-4 mt-5">
								<br>
								<button type="submit" name="btn" class="btn btn-primary btn-flat">  {{ trans('global.filter') }}</button>
                                <button type="button"id='resetBtn' class="btn btn-primary btn-flat">  {{ trans('global.reset') }}</button>
								</div>
								
							</div>
						</div></form>
						</div> 
					
<!--  -->
    
</div>
    <!-- end filter -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
              {{ trans('global.contacttitle_singular') }}    {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table
                            class=" table table-bordered table-striped table-hover datatable datatable-Booking randum">
                            <thead>
                                <tr>
                                <th> </th>
                                    <th> {{ trans('global.id') }}</th>
                                    <th>{{ trans('global.title') }}</th>
                                    <th>{{ trans('global.description') }}</th>
                                    <th>{{ trans('global.user') }} </th>
                                    <th>{{ trans('global.status') }}</th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contactdata as $data)
                                <tr data-entry-id="{{ $data->id }}">
                                <td>  </td>
                                    <td> {{ $data->id ?? '' }} </td>

                                    <td>{{ $data->tittle ?? '' }} </td>
                                    <td>{{ $data->description}} </td>
                                    <td>
                                    <a href="{{ route('admin.overview', $data->user) }}">
                                        {{ $data->appUser->first_name ?? '' }} {{ $data->appUser->last_name ?? '' }}
                                        </a>
                                    </td>
                                    <td>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$data->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $data->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                    </td>
                                    <td>
                                        @can('contact_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.contacts.show', $data->id) }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>

                                        @endcan

                                        @can('contact_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.contacts.edit', $data->id) }}">
                                            {{-- {{ trans('global.edit') }} --}}
                                            <i class="fa fa-pencil" aria-hidden="true"></i>

                                        </a>
                                        @endcan
                                        <!-- <a style="margin-bottom: 5px; margin-top: 5px" class="btn btn-xs btn-danger delete-button" href="{{ route('admin.contact.delete', $data->id) }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a> -->
                                                <a  class="btn btn-xs btn-danger delete-button deleteclass"  data-id="{{$data->id}}"  href="{{ route('admin.contact.delete', $data->id) }}">
                                         <i class="fa fa-trash" aria-hidden="true"></i>
												</a>

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
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->

@endsection
@section('scripts')
@parent
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

    
    let deleteRoute = "{{ route('admin.contact.deleteAll') }}"; 
   

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
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });

})
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
				url: 'update-contact-status', 
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
<script>
    $('#resetBtn').click(function(){
        $('#contactFilterForm')[0].reset();
        var baseUrl = '{{ route('admin.contacts.index') }}';
        window.history.replaceState({}, document.title, baseUrl);
        window.location.reload();
    });
</script>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('.delete-button').on("click", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var deleteUrl = $(this).attr('href');

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
                    $.ajax({
                        url: deleteUrl, 
                        type: 'GET',
                        data: {},
                        success: function(response) {
                            Swal.fire({
                                title: "{{ trans('global.Deleted') }}",
                                text: "{{ trans('global.the_record_has_been_deleted') }}",
                                icon: "{{ trans('global.success') }}",
                            }).then(function() {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while deleting the record.",
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });
    });
</script>
@endsection