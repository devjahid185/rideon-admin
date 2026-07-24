@extends('layouts.admin')

@section('content')
<div class="content">
@can('static_page_create')
<div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.currency.create') }}">
                    {{ trans('global.add') }} {{ trans('global.currency') }}
                </a>
            </div>
        </div>
@endcan
    <div class="row">
        <div class="col-lg-12">
        <div class="panel panel-default">
                <div class="panel-heading">
                {{ trans('global.currency') }}
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-Currency">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('global.currency') }} {{ trans('global.name') }} </th>
                                    <th>{{ trans('global.currency_code') }}</th>
                                    <th>{{ trans('global.value_against_default_currency') }}</th>
                                    <th>{{ trans('global.status') }}</th>
                                    <th>{{ trans('global.currency_symbol') }}</th>
                                    <th>{{ trans('global.actions') }}</th>
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
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
    let deleteButton = {
        text: '{{ trans("global.delete_all") }}',
        url: "{{ route('admin.currency.massDestroy') }}",
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
    dtButtons.push(deleteButton);

    let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: "{{ route('admin.currency') }}",
        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'currency_name', name: 'currency_name' },
            { data: 'currency_code', name: 'currency_code' },
            { data: 'value_against_default_currency', name: 'value_against_default_currency' },
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
                        ${data === 1 ? 'checked' : ''}
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
                        url: '/admin/update-currency-status', // Replace with your actual URL
                        data: requestData,
                        success: function (response) {
                            if(response.status === 200)
                         {
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
                       }
                       else
                       {
                        toastr.error(response.message, 'Error', {
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
            { data: 'currency_symbol', name: 'currency_symbol' },
            {
                data: 'actions',
                name: '{{ trans('global.actions') }}',
                orderable: false,
                searchable: false
            }
        ],
        orderCellsTop: true,
        order: [[1, 'desc']],
        pageLength: 100
    };

    let table = $('.datatable-Currency').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
});
</script>
@endsection
