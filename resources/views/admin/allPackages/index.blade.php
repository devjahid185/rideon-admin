@extends('layouts.admin')
@section('content')
<div class="content">
    @can('all_package_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.all-packages.create') }}">
                    {{ trans('global.add') }} {{ trans('global.allPackage_title') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.allPackage_title') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-AllPackage">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                {{ trans('global.id') }}
                                </th>
                                <th>
                                {{ trans('global.package_name') }}
                                </th>
                                <th>
                                {{ trans('global.package_total_day') }}
                                </th>
                                <th>
                                {{ trans('global.package_price') }}
                                </th>
                                <th>
                                {{ trans('global.package_image') }}
                                </th>
                                <th>
                                {{ trans('global.status') }}
                                </th>
                                <th>
                                {{ trans('global.max_item') }}
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
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  
  let dtOverrideGlobals = {
  buttons: dtButtons,
  processing: true,
  serverSide: true,
  retrieve: true,
  aaSorting: [],
  ajax: "{{ route('admin.all-packages.index') }}",
  columns: [
    { data: 'placeholder', name: 'placeholder' },
    { data: 'id', name: 'id' },
    { data: 'package_name', name: 'package_name' },
    { data: 'package_total_day', name: 'package_total_day' },
    { data: 'package_price', name: 'package_price' },
    { data: 'package_image', name: 'package_image', sortable: false, searchable: false },
    {
      data: 'status',
      name: 'status',
      
      render: function(data, type, row) {

        console.log('Status Data:', data);
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
              ${data === 'Active' ? 'checked' : ''}
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
            url: '/admin/update-appPackage-status', // Replace with your actual URL
            data: requestData,
            success: function (response) {
              if(response.status === 200)
              {
              toastr.success(response.message, "Success!", {
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
            }
            else
            {
              toastr.error(response.message, "Error!", {
                CloseButton: true,
                ProgressBar: true,
                positionClass: "toast-bottom-right"
              });
            }
            }
          });
        });
      }
    },
    { data: 'max_item', name: 'max_item' },
    {
    data: 'actions',
    name: '{{ trans('global.actions') }}',
    orderable: false, // Disable sorting on the actions column
    searchable: false, // Disable searching on the actions column
   
}
  ],
  orderCellsTop: true,
  order: [[1, 'desc']],
  pageLength: 100
};
  let table = $('.datatable-AllPackage').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection