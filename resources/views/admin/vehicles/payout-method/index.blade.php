@extends('layouts.admin')


@section('content')
<div class="content">
    <div style="margin-bottom: 10px;" class="row mb-2">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.payout-method.create') }}">
                {{ trans('vehicle.add') }} {{ trans('vehicle.payout_method') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('vehicle.payout_method') }} {{ trans('vehicle.list') }}
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-vehicle-fuel-type">
                        <thead>
                            <tr>
                                <th width="10"></th>
                                <th>{{ trans('global.id') }}</th>
                                <th>{{ trans('global.name') }}</th>
                                <th>{{ trans('global.status') }}</th>
                                <th width="120">&nbsp;</th>
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
const texts = {
    deleteAllText: '{{ trans("global.delete_all") }}',
    noEntriesText: '{{ trans("global.no_entries_selected") }}',
    areYouSureText: '{{ trans("global.are_you_sure") }}',
    deleteConfirmText: '{{ trans("global.delete_confirmation") }}',
    yesContinueText: '{{ trans("global.yes_continue") }}',
    deletedText: '{{ trans("global.deleted") }}',
    entriesDeletedText: '{{ trans("global.entries_deleted") }}',
    errorText: '{{ trans("global.error") }}',
    deleteErrorText: '{{ trans("global.delete_error") }}',
    successText: '{{ trans("global.success") }}',
    genericErrorText: 'Something went wrong. Please try again.'
};

const fuelTypeColumns = [
    { data: 'placeholder', name: 'placeholder', searchable: false, orderable: false },
    { data: 'id', name: 'id' },
    { data: 'name', name: 'name' },
    {
        data: 'status',
        name: 'status',
        render: (data, type, row) => `
            <div class="status-toggle d-flex justify-content-between align-items-center">
                <input
                    data-id="${row.id}"
                    class="check statusdata"
                    type="checkbox"
                    id="user${row.id}"
                    data-toggle="toggle"
                    data-on="Active"
                    data-off="InActive"
                    ${data ? 'checked' : ''}
                >
                <label for="user${row.id}" class="checktoggle">checkbox</label>
            </div>
        `,
        createdCell: function (td, cellData, rowData) {
            if (typeof handleStatusToggle === 'function') {
                handleStatusToggle(td, cellData, rowData, {
                    ajaxUpdateRoute: "{{ url('admin/update-payout-method') }}",
                    texts: texts
                });
            }
        }
    },
    { data: 'actions', name: 'actions', orderable: false, searchable: false }
];

// ✅ Define initializeFeatureDataTable directly here
function initializeFeatureDataTable({ tableSelector, ajaxUrl, deleteUrl, columns, texts }) {
    const table = $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: columns,
        order: [[1, 'desc']],
        language: {
            emptyTable: texts.noEntriesText
        }
    });

    // Optional: bulk delete button
    if (deleteUrl) {
        const deleteButton = $('<button>')
            .addClass('btn btn-danger btn-sm delete-selected')
            .html(`<i class="fa fa-trash"></i> ${texts.deleteAllText}`)
            .css({ marginLeft: '10px' });

        $('.dataTables_length').after(deleteButton);

        $(document).on('click', '.delete-selected', function () {
            const ids = $.map(table.rows('.selected').data(), function (row) {
                return row.id;
            });

            if (!ids.length) { alert(texts.noEntriesText); return; }

            if (!confirm(`${texts.areYouSureText}\n${texts.deleteConfirmText}`)) { return; }

            $.ajax({
                headers: {'x-csrf-token': $('meta[name="csrf-token"]').attr('content')},
                method: 'POST',
                url: deleteUrl,
                data: { ids: ids },
                success: function () { 
                    alert(texts.entriesDeletedText); 
                    table.ajax.reload(); 
                },
                error: function () { alert(texts.deleteErrorText); }
            });
        });

        // Row selection toggle
        $(tableSelector + ' tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
        });
    }
}

// ✅ Now call it as before
$(function () {
    initializeFeatureDataTable({
        tableSelector: '.datatable-vehicle-fuel-type',
        ajaxUrl: "{{ route('admin.payout-method.index') }}",
        deleteUrl: "{{ route('admin.payout-method.deleteAll') }}",
        columns: fuelTypeColumns,
        texts: texts
    });
});
</script>
@endsection