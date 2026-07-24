@extends('layouts.admin')
@section('content')
@php $i = 0; $j = 0; @endphp

<div class="content">
    @can('item_rule_create')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.item-rule.create') }}">
            {{ trans('global.add') }} {{ trans('global.item_rule') }}
            </a>
        </div>
    </div>
    @endcan
 
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
              {{ trans('global.item_rule') }}    {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table
                            class=" table table-bordered table-striped table-hover datatable datatable-Booking randum">
                            <thead>
                                <tr>
                                <th> </th>
                                    <th> {{ trans('global.id') }}</th>
                                    <th>{{ trans('global.name') }}</th>
                                    <th>{{ trans('global.module') }}</th>
                                    <th>{{ trans('global.status') }}</th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemRolesData as $data)
                                <tr data-entry-id="{{ $data->id }}">
                                <td> </td>
                                    <td> {{ $data->id ?? '' }} </td>

                                    <td>{{ $data->rule_name ?? '' }} </td>
                                    <td>{{  $data->moduleGet->name ?? '' }} </td>
                                    <td>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$data->id}}" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $data->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.item-rule.show', $data->id) }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                        @can('item_rule_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.item-rule.edit', $data->id) }}">
                                            {{-- {{ trans('global.edit') }} --}}
                                            <i class="fa fa-pencil" aria-hidden="true"></i>

                                        </a>
                                        @endcan

                                        @can('item_rule_delete')
                                                <a  class="btn btn-xs btn-danger delete-button deleteclass"  data-id="{{$data->id}}"  href="{{ route('admin.item-rule.delete', $data->id) }}">
                                         <i class="fa fa-trash" aria-hidden="true"></i>
												</a>
                                        @endcan
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

    
    let deleteRoute = "{{ route('admin.item-rule.deleteAll') }}"; 
   

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
				url: 'update-item-rule-status', 
				data: requestData, 
				success: function(response){ 
                    if(response.status === 200){
                    toastr.success(response.message, '{{ trans("global.success") }}', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});
                }
                else{
                    toastr.error(response.message, 'Error', {
						CloseButton: true,
						ProgressBar: true,
						positionClass: "toast-bottom-right"
					});

                }
				} 
			}); 
		})
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