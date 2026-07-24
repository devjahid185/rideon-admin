@extends('layouts.admin')
@section('content')
<div class="content">
    @can($permissionrealRoute.'_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route($createRoute) }}">
                    {{ trans('global.add') }} {{ $title }}
                </a>
            </div>
        </div>
    @endcan
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
            <div class="panel-heading d-flex justify-content-between align-items-center">
                    <span>{{ $title }} {{ trans('global.list') }}</span>
                    <!-- <button class="btn btn-danger" id="deleteAll">
                       All Delete
                    </button> -->
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover  datatable datatable-City">
                        <thead>
                            <tr>
                                <th width="10">

                                </th>
                                <th>
                                    {{ trans('global.id') }}
                                </th>
                              
                                <th>
                                    {{ trans('global.city_name') }}
                                </th>
                                <th>
                                    {{ trans('global.country') }}
                                </th>
                                <th>
                                    {{ trans('global.image') }}
                                </th>
                                <th>
                                {{ trans('global.latitude') }}
                                </th>
                                <th>
                                {{ trans('global.longtitude') }}
                                </th>
                               
                                <th>
                                    {{ trans('global.status') }}
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
    url: "{{ route('admin.item-location.deleteAll') }}", // Replace with your delete route
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
            ajax: "{{ route($indexRoute) }}",
            columns: [
                { data: 'placeholder', name: 'placeholder', orderable: false, searchable: false, className: 'select-checkbox' },
                { data: 'id', name: 'id' },
                { data: 'city_name', name: 'city_name' },
                { data: 'country_code', name: 'country_code' },
                { data: 'image', name: 'image', sortable: false, searchable: false },
                { data: 'latitude', name: '{{ trans('global.latitude') }}' },
                { data: 'longtitude', name: '{{ trans('global.longtitude') }}' },
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
                                url: '{{$ajaxUpdate}}', 
                                data: requestData,
                                success: function (response) {
                                    toastr.success(response.message, '{{ trans("global.success") }}', {
                                        CloseButton: true,
                                        ProgressBar: true,
                                        positionClass: "toast-bottom-right"
                                    });

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

        let table = $('.datatable-City').DataTable(dtOverrideGlobals);
        $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

        // Enable row selection
        table.on('click', 'tr', function () {
            $(this).toggleClass('selected');
        });
    });
</script>


@endsection