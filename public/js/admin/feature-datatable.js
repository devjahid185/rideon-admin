function initializeFeatureDataTable({
    tableSelector = '.datatable-feature',
    ajaxUrl,
    deleteUrl,
    columns,
    texts
}) {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
    dtButtons.push({
        text: texts.deleteAllText,
        url: deleteUrl,
        className: 'btn-danger',
        action: function (e, dt) {
            const ids = $.map(dt.rows({ selected: true }).data(), entry => entry.id);
            if (ids.length === 0) {
                showSwal({
                    title: texts.noEntriesText,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            showSwal({
                title: texts.areYouSureText,
                text: texts.deleteConfirmText,
                confirmButtonText: texts.yesContinueText,
                icon: 'warning'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        headers: { 'x-csrf-token': csrfToken },
                        method: 'POST',
                        url: this.url,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(() => {
                        Swal.fire(texts.deletedText, texts.entriesDeletedText, 'success');
                        dt.ajax.reload();
                         Swal.close();
                    }).fail(() => {
                        Swal.fire(texts.errorText, texts.deleteErrorText, 'error');
                         Swal.close();
                    });
                }
            });
        }
    });

    let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: ajaxUrl,
        columns: columns,
        orderCellsTop: true,
        order: [[1, 'desc']],
        pageLength: 50
    };

    const table = $(tableSelector).DataTable(dtOverrideGlobals);

    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    table.on('click', 'tr', function () {
        $(this).toggleClass('selected');
    });
}

function handleStatusToggle(td, cellData, rowData, options) {
    const { ajaxUpdateRoute, texts } = options;
    const checkbox = $(td).find('.statusdata');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    checkbox.off('change').on('change', function () {
        const statusCheckbox = $(this);
        const status = statusCheckbox.prop('checked') ? 1 : 0;
        const id = statusCheckbox.data('id');

        showSwal({
            title: texts.areYouSureText,
            text: texts.changeStatusConfirmText,
            confirmButtonText: texts.yesContinueText,
            icon: 'warning'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajaxUpdateRoute,
                    data: {
                        _token: csrfToken,
                        status: status,
                        pid: id
                    },
                    success: function (response) {
                        toastr.success(response.message, texts.successText, {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        $(td).find('label.checktoggle').toggleClass('active', status === 1);
                         Swal.close();
                    },
                    error: function (response) {
                        toastr.error(response.responseJSON?.message || texts.genericErrorText, texts.errorText);
                        statusCheckbox.prop('checked', !status);
                         Swal.close();
                    }
                });
            } else {
                statusCheckbox.prop('checked', !status);
            }
        });
    });
}

