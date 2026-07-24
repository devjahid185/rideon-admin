@extends('layouts.admin')
@section('content')
@php $i = 0; $j = 0; @endphp
<div class="content">
    @can('property_type_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.module.create') }}">
                    {{ trans('global.add') }} {{ trans('global.module') }}
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
        .main-footer {
    overflow: hidden;
    margin-left: 0;
}
table.dataTable tbody td.select-checkbox:before{
    display:none;
}
.dataTables_length{
    display:none;
}
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.module') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable  datatable-Property">
                        <thead>
                            <tr>
                                <th>
                                    {{ trans('global.id') }}
                                </th>
                                <th>
                                    {{ trans('global.name') }}
                                </th>
                                
                                <th>
                                    {{ trans('global.description') }}
                                </th>
                                <th>
                                    {{ trans('global.status') }}
                                </th>
                                <th>
                                    {{ trans('global.default') }}
                                </th>
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($module as $key => $modules)
                                    <tr >
                                        <td>
                                            #{{ $modules->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $modules->name ?? '' }}
                                        </td>
                                      
                                        <td>
                                           {{ $modules->description ?? '' }}
                                        </td>
                                        <td>
                                         <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$modules->id}}" data-type="status" class="check statusdata" type="checkbox" data-onstyle="success" id="{{'user'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $modules->status ? 'checked' : '' }}	>
												<label for="{{'user'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                         <div class="status-toggle d-flex justify-content-between align-items-center">
												<input data-id="{{$modules->id}}" data-type="default_module" class="check statusdata default_module" type="checkbox" data-onstyle="success" id="{{'default_module'. $i++}}" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $modules->default_module ? 'checked' : '' }}	>
												<label for="{{'default_module'. $j++}}" class="checktoggle">checkbox</label>
											</div>
                                        </td>
                                        <td>
                                           <a  style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-primary" href="{{ route('admin.module.show', $modules->id) }}">
                                           <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                           <a  style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="{{ route('admin.module.edit', $modules->id) }}">
                                          <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>

                                          
                                            <form action="{{ route('admin.module.destroy', $modules->id) }}" method="POST" style="display: inline-block;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-danger delete-button" data-id="{{ $modules->id }}">
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
@section('scripts')
@parent
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    // order: [[ 1, 'desc' ]],
    // pageLength: 10,
  });
  let table = $('.datatable-Property:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function() {
                var deleteUrl = this.form.action; 

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $('.statusdata').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');
        var type = $(this).data('type');
         if(type=='default_module')
        $('.default_module').not(this).prop('checked', false);

        var requestData = {
            'status': status,
            'pid': id,
            'type': type
        };

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        requestData['_token'] = csrfToken;

        $.ajax({
            type: "POST",
            dataType: "json",
            url: 'update-module-status',
            data: requestData,
            success: function(response) {
                toastr.success(response.message, '{{ trans("global.success") }}', {
                    CloseButton: true,
                    ProgressBar: true,
                    positionClass: "toast-bottom-right"
                });
            }
        });
    });
</script>

@endsection