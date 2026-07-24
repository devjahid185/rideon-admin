@extends('layouts.admin')
@section('content')
<div class="content">
    @can('availability_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.availabilities.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.availability.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.availability.title_singular') }} {{ trans('global.list') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Availability">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('cruds.availability.fields.id') }}
                                </th>
                                <th>
                                    {{ trans('cruds.availability.fields.quantity') }}
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
    ajax: "{{ route('admin.availabilities.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'quantity', name: 'quantity' },
{
    data: null,
        name: 'actions',
        orderable: false,
        searchable: false,
        render: function(data) {
          return `
            <a href="{{ route('admin.availabilities.show', ':id') }}" class="btn btn-xs btn-primary">
              <i class="fa fa-eye" aria-hidden="true"></i>
            </a>
            <a href="{{ route('admin.availabilities.edit', ':id') }}" class="btn btn-xs btn-warning">
              <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
          `.replace(/:id/g, data.id);
        },
      },
    ],
    orderCellsTop: true,
    order: [[1, 'desc']],
    pageLength: 100,
  };
  let table = $('.datatable-Availability').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection