@extends('layouts.admin')
@section('content')
<div class="content">

@can($permissionrealRoute.'_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route($createRoute) }}">
                    {{ trans('global.add') }} {{$title}}
                </a>
            </div>
        </div>
        @endcan
        <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body">
                    <form class="form-horizontal" id="propertyFilterForm" action="{{ route($indexRoute) }}" method="GET" accept-charset="UTF-8">
                        <div class="row">
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <label>Type</label>
                                <select class="form-control select2" name="typeId" id="typeId">
                                    <option value="">{{ trans('global.pleaseSelect') }}</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ request('typeId') == $type->id ? 'selected' : '' }} >{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2 mt-4 col-sm-2 col-xs-4 mt-5">
                                <br>
                                <button type="submit" name="btn" class="btn btn-primary btn-flat filterproduct">{{ trans('global.filter') }}</button>
                                <button type="button" id="resetBtn" class="btn btn-primary btn-flat resetproduct">{{ trans('global.reset') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ $title }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-vehicleMake">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
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
                                    {{ trans('global.type') }}
                                </th>
                                <th>
                                    {{ trans('global.image') }}
                                </th>
                                <th>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                    </table>
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
  let deleteButton = {
    text: '{{ trans("global.delete_all") }}',
    url: "{{ route('admin.vehicle-makes.deleteAll') }}", // Replace with your delete route
    className: 'btn-danger',
    action: function (e, dt, node, config) {
        var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
            return entry.id;
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

        // Use SweetAlert for confirmation
        Swal.fire({
            title: '{{ trans("global.are_you_sure") }}',
            text: '{{ trans("global.delete_confirmation") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    headers: { 'x-csrf-token': csrfToken },
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function () {
                    Swal.fire(
                        '{{ trans("global.deleted") }}',
                        '{{ trans("global.entries_deleted") }}',
                        'success'
                    );
                    dt.ajax.reload();
                }).fail(function (xhr, status, error) {
                    Swal.fire(
                        '{{ trans("global.error") }}',
                        '{{ trans("global.delete_error") }}',
                        'error'
                    );
                });
            }
        });
    }
};

        dtButtons.push(deleteButton)
  
  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: {
        url: "{{ route($indexRoute) }}",
        type: 'GET',
        data: function(d) {
                d.typeId = $('#typeId').val();
            }
    },
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'name', name: 'name' },
{ data: 'description', name: 'description' },

{ 
    data: 'status',
      name: 'status',
      render: function (data, type, row) {
        return `
          <div class="status-toggle d-flex justify-content-between align-items-center">
            <input
              data-id="${row.id}"
              class="check statusdata"
              type="checkbox"
              data-onstyle="success"
              id="${'user' + row.id}"
              data-offstyle="danger"
              data-toggle="toggle"
              data-on="Active"
              data-off="InActive"
              ${data ? 'checked' : ''}
            >
            <label for="${'user' + row.id}" class="checktoggle">checkbox</label>
          </div>
        `;
      },
      createdCell: function (td, cellData, rowData, row, col) {
        // Add an event listener for the toggle change event
        $(td).on('change', '.statusdata', function () {
          var status = $(this).prop('checked') ? 1 : 0;
          var id = rowData.id;

          var requestData = {
            'status': status,
            'pid': id
          };

          var csrfToken = $('meta[name="csrf-token"]').attr('content');
          requestData['_token'] = csrfToken;

          $.ajax({
            type: "POST",
            dataType: "json",
            url: '{{$ajaxUpdate}}', // Replace with your actual URL
            data: requestData,
            success: function (response) {
              toastr.success(response.message, '{{ trans("global.success") }}', {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
              });
              // Update the label's 'active' class based on the status
              var label = $(td).find('label.checktoggle');
              if (status === 1) {
                label.addClass('active');
              } else {
                label.removeClass('active');
              }
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
        });
      }
    },
    { data: 'typeName', name: 'typeName', orderable: false, searchable: false},   
{ data: 'image', name: 'image', sortable: false, searchable: false },

{ 


data: 'actions',
        name: '{{ trans('global.actions') }}',
        orderable: false,
        searchable: false
      },
    ],
    orderCellsTop: true,
    order: [[1, 'desc']],
    pageLength: 100,
  };

  let table = $('.datatable-vehicleMake').DataTable(dtOverrideGlobals);

  $('#typeId').on('change', function() {
        $('#propertyFilterForm').submit();
    });
    $('#resetBtn').on('click', function() {
        
        $('#typeId').val('').trigger('change');
        
        $('#propertyFilterForm').submit();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();
    });
    


  // Enable row selection
   table.on('click', 'tr', function () {
            $(this).toggleClass('selected');
        });
  
});

</script>
@endsection