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
					<form class="form-horizontal" id="propertyFilterForm" action="" method="GET" accept-charset="UTF-8">
						
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
                            <select class="form-control select2" name="customer" id="customer">
                                <option value="">{{ $customername }}</option>
                               
                            </select>
                        </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <label>  {{ trans('global.title') }}</label>
                                    <input type="text" class="form-control" name ="title" value="{{ $items_title ?: '' }}">
                                    
                                </div>


								
								<div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">
                                        <br>
                                        <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                         <button type="button" id="resetBtn"  class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
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
    {{ trans('global.trash') }} {{ trans('global.list') }}
    <form id="deleteAllForm" action="{{ route('admin.common.trash.permanentDeleteAll') }}" method="POST" style="display: inline-block;">
    @csrf
    <input type="hidden" name="module" value="{{ $module }}"> <!-- Add module_id here -->
    <button type="button" id="deleteAllButton" class="btn btn-danger">(Empty All)</button>
</form>
</div>


                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable  datatable-Property">
                            <thead>
                                <tr>
                                <th></th>
                                    <th>{{ trans('global.id') }}#</th>
                                    <th>{{ trans('global.title') }}</th>
                                    <th>{{ trans('global.host') }}</th>
                                    <th>{{ trans('global.image') }}</th>
                                    <th>{{ trans('global.place') }}</th>
                                    <th>{{ trans('global.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as  $item)
                                    <tr data-entry-id="{{$item->id}}">
                                    <td></td>
                                        <td>#{{ $item->id ?? '' }}</td>
                                        <td>{{ $item->title ?? '' }}</td>
                                        <td>{{ $item->userid->first_name ?? '' }} {{ $item->userid->last_name ?? '' }}</td>
                                        <td>
                                        @if($item->front_image)
                                            <a href="{{ $item->front_image->url}}">
                                                <img src="{{ $item->front_image->thumbnail }}" alt="{{ $item->title }}" class="item-image-size">
                                                </a>
                                           
                                            @endif
                                        </td>
                                        <td>{{ $item->place->city_name ?? '' }}</td>
                                        <td>
                                            <form id="restore-form-{{ $item->id }}" action="{{ route('admin.common.trash.restore', $item->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-success restore-btn" data-id="{{ $item->id }}">
                                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                                </button>
                                            </form>

                                        

                                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.common.trash.permanentDelete', $item->id) }}" method="POST" style="display: inline-block;">
                                            @method('POST')
                                            @csrf
                                            <!-- Pass module name as a data attribute -->
                                            <button type="button" class="btn btn-xs btn-danger permanent-delete" data-id="{{ $item->id }}" >
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>

                                           
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end">
                                @if ($items->currentPage() > 1)
                                    <li class="page-items">
                                        <a class="page-link" href="{{ $items->previousPageUrl() }}" tabindex="-1">{{ trans('global.previous') }}</a>
                                    </li>
                                @else
                                    <li class="page-items disabled">
                                        <span class="page-link">{{ trans('global.previous') }}</span>
                                    </li>
                                @endif

                                @for ($i = 1; $i <= $items->lastPage(); $i++)
                                    <li class="page-items {{ $i == $items->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                @if ($items->hasMorePages())
                                    <li class="page-items">
                                        <a class="page-link" href="{{ $items->nextPageUrl() }}">{{ trans('global.next') }}</a>
                                    </li>
                                @else
                                    <li class="page-items disabled">
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


@endsection

@section('scripts')

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
                        title: 'No entries selected',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            method: 'DELETE',
                            url: route,
                            data: { ids: ids }
                        }).done(function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Messages have been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });

                            // Remove the rows from DataTables
                            location.reload();
                        }).fail(function (xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the messages.',
                                'error'
                            );
                        });
                    }
                });
            };
        }

        let deleteRoute = "{{ route('admin.trash-delete.rows') }}";

        let deleteButton = {
            text: 'Delete all',
            className: 'btn-danger',
            action: handleDeletion(deleteRoute)
        };

        dtButtons.push(deleteButton);

    let table = $('.datatable-Property:not(.ajaxTable)').DataTable({
        buttons: dtButtons,
        orderCellsTop: true,
        select: {
            style: 'multi'
        }
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
                    // Show loading state
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
                            // Optionally, refresh the page or update UI as needed
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
        $('.restore-btn').on('click', function() {
            var propertyId = $(this).data('id');
            var form = $('#restore-form-' + propertyId);

            // Display confirmation dialog using SweetAlert
            Swal.fire({
                

                title: "Restore Property",
                text: "Are you sure you want to restore this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, restore it!",
                cancelButtonText: "Cancel",
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: '{{ trans('global.restoring') }}',
                        text: '{{ trans('global.please_wait_restoring') }}',
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
                            toastr.success('item restored successfully!', 'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                            // Optionally, refresh the page or update the UI as needed
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

       // Permanent Delete Item
    $('.permanent-delete').on('click', function() {
        var itemId = $(this).data('id');
        var moduleName = $(this).data('module-name'); // Fetch module name from data attribute
        var form = $('#delete-form-' + itemId);

        Swal.fire({
            title: "{{ trans('global.are_you_sure') }}",
            text: "{{ trans('global.delete_trash_item') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{ trans('global.yes_delete_it') }}",
            cancelButtonText: "{{ trans('global.cancel') }}",
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

                // Append module_name to form data
                var formData = form.serializeArray();
                formData.push({ name: 'module_name', value: moduleName });

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        toastr.success('{{ trans('global.permanently_delete_success') }}', '{{ trans('global.success') }}', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function(response) {
                        Swal.close();
                        toastr.error('{{ trans('global.delete_error') }}', '{{ trans('global.error') }}', {
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

@endsection