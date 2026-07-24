$(document).ready(function () {
    window._token = $('meta[name="csrf-token"]').attr('content')

    moment.updateLocale('en', {
        week: { dow: 1 } // Monday is the first day of the week
    })

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'en'
    })

    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'en',
        sideBySide: true
    })

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    })

    $('.select-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', 'selected')
        $select2.trigger('change')
    })
    $('.deselect-all').click(function () {
        let $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', '')
        $select2.trigger('change')
    })

    $('.select2').select2()

    $('.treeview').each(function () {
        var shouldExpand = false
        $(this).find('li').each(function () {
            if ($(this).hasClass('active')) {
                shouldExpand = true
            }
        })
        if (shouldExpand) {
            $(this).addClass('active')
        }
    })

    $('a[data-toggle^="push-menu"]').click(function () {
        setTimeout(function () {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        }, 350);
    })

    $('#daterange-btn').daterangepicker({
        opens: 'right', // Change the calendar position to the left side of the input
        autoUpdateInput: false, // Disable auto-update of the input fields
        ranges: {
            'Anytime': [moment(), moment()],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                .endOf('month')
            ]
        },
        locale: {
            format: 'YYYY-MM-DD', // Format the date as you need
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom Range'
        }
    });

    const storedStartDate = localStorage.getItem('selectedStartDate');
    const storedEndDate = localStorage.getItem('selectedEndDate');
    const urlFrom = "{{ request()->input('from') }}";
    const urlTo = "{{ request()->input('to') }}";
    if (storedStartDate && storedEndDate && urlFrom && urlTo) {
        const startDate = moment(storedStartDate);
        const endDate = moment(storedEndDate);
        $('#daterange-btn').data('daterangepicker').setStartDate(startDate);
        $('#daterange-btn').data('daterangepicker').setEndDate(endDate);
        $('#daterange-btn').val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));

        $('#startDate').val(startDate.format('YYYY-MM-DD'));
        $('#endDate').val(endDate.format('YYYY-MM-DD'));
    } else {
        $('#daterange-btn').val('');
        $('#startDate').val('');
        $('#endDate').val('');

        localStorage.removeItem('selectedStartDate');
        localStorage.removeItem('selectedEndDate');
    }

    // Update the hidden input fields and button text when the date range changes
    $('#daterange-btn').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format(
            'YYYY-MM-DD'));
        $('#startDate').val(picker.startDate.format('YYYY-MM-DD'));
        $('#endDate').val(picker.endDate.format('YYYY-MM-DD'));

        // Store the selected start and end dates in local storage
        localStorage.setItem('selectedStartDate', picker.startDate.format('YYYY-MM-DD'));
        localStorage.setItem('selectedEndDate', picker.endDate.format('YYYY-MM-DD'));
    });

    $('#daterange-btn').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#startDate').val('');
        $('#endDate').val('');
        localStorage.removeItem('selectedStartDate');
        localStorage.removeItem('selectedEndDate');
    });
    function resetFilters() {
        $('#daterange-btn').val('');
        $('#startDate').val('');
        $('#endDate').val('');
        $('#status').val('');
        $('#host').val('').trigger('change');
        $('#customer').val('').trigger('change');
    }

    // Optional: Submit the form when the "Filter" button is clicked
    $('button[name="btn"]').on('click', function () {
        $('form').submit();
    });

})


function showSwal({
    title = 'Are you sure?',
    text = '',
    html = '',
    footer = '',
    icon = 'warning',

    showCancelButton = true,
    confirmButtonText = 'Yes, continue',
    cancelButtonText = 'Cancel',
    confirmButtonColor = '#3085d6',
    cancelButtonColor = '#d33',

    allowOutsideClick = false,
    allowEscapeKey = false,

    timer = null,
    timerProgressBar = false,

    input = null, // 'text', 'email', 'password', 'select', etc.
    inputPlaceholder = '',
    inputValue = '',
    inputOptions = {},

    width = null, // '600px', etc.
    position = 'center',
    backdrop = true,
    animation = true,

    showLoader = false,
    preConfirm = null, // async callback

    didOpen = null,
    willClose = null,

    showUpdatingOnConfirm = true // ✅ NEW: automatically show "Updating..." loading on confirm
} = {}) {
    return Swal.fire({
        title,
        text,
        html,
        footer,
        icon,

        showCancelButton,
        confirmButtonText,
        cancelButtonText,
        confirmButtonColor,
        cancelButtonColor,

        allowOutsideClick,
        allowEscapeKey,

        timer,
        timerProgressBar,

        input,
        inputPlaceholder,
        inputValue,
        inputOptions,

        width,
        position,
        backdrop,
        animation,

        showLoaderOnConfirm: showLoader,

        preConfirm: preConfirm ? preConfirm : undefined,

        didOpen: () => {
            if (showLoader && !preConfirm) Swal.showLoading();
            if (typeof didOpen === 'function') didOpen();
        },
        willClose: () => {
            if (typeof willClose === 'function') willClose();
        }
    }).then((result) => {
        if (result.isConfirmed && showUpdatingOnConfirm) {
            Swal.fire({
                title: 'Updating...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        return result;
    });
}


/**
 * Generic reusable toggle update handler using showSwal for confirmation.
 * 
 * @param {string} selector - jQuery selector for checkboxes.
 * @param {string} url - URL to post the update to.
 * @param {string} dataKey - The data key to send (e.g., 'status', 'isverified', 'featured').
 * @param {object} swalOptions - { title: '...', text: '...' } passed from Blade for dynamic wording.
 * @param {object} toastOptions - (Optional) Toastr config overrides.
 */
function handleToggleUpdate(selector, url, dataKey, swalOptions = {}, toastOptions = {}) {
    const defaultToastOptions = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-bottom-right"
    };

    $(document).on('change', selector, function () {
        const $toggle = $(this);
        const previousValue = $toggle.prop('checked');
        const revertedValue = !previousValue;
        const valueToSend = previousValue ? 1 : 0;
        const id = $toggle.data('id');

        showSwal({
            title: swalOptions.title || 'Are you sure?',
            text: swalOptions.text || 'Do you want to update this item?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: url,
                    data: {
                        [dataKey]: valueToSend,
                        pid: id,
                        _token: window._token
                    },
                    success: function (response) {
                        Swal.close();
                        if (response.status === 200) {
                            toastr.success(response.message || 'Updated successfully', 'Success', { ...defaultToastOptions, ...toastOptions });
                        } else {
                            toastr.error(response.message || 'Could not update', 'Error', { ...defaultToastOptions, ...toastOptions });
                            $toggle.prop('checked', revertedValue);
                        }
                    },
                    error: function (xhr) {
                        Swal.close();
                        const message = xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Something went wrong. Please try again.';
                        toastr.error(message, 'Error', { ...defaultToastOptions, ...toastOptions });
                        $toggle.prop('checked', revertedValue);
                    }
                });
            } else {
                // User cancelled, revert toggle
                $toggle.prop('checked', revertedValue);
            }
        });
    });
}

/**
 * Attach incomplete steps tooltip on hover for elements with `.progress-circle`.
 * @param {string} fetchUrl - Route URL for fetching incomplete steps.
 */
function attachIncompleteStepTooltips() {
    const progressCircles = document.querySelectorAll('.progress-circle');
    let activePopoverElement = null;

    progressCircles.forEach(progressCircle => {

        function showTooltip() {
            // Close any active popover before opening a new one
            if (activePopoverElement) {
                $(activePopoverElement).popover('destroy');
                activePopoverElement = null;
            }

            const incompleteSteps = JSON.parse(progressCircle.getAttribute('data-incomplete-steps') || '[]');
            let content;

            if (incompleteSteps.length > 0) {
                content = `<strong>Incomplete steps:</strong><br>${incompleteSteps.join(', ')}`;
            } else {
                content = `<span class="text-success">All steps are completed.</span>`;
            }

            $(progressCircle).popover({
                content: content,
                html: true,
                placement: 'top',
                trigger: 'manual',
                container: 'body',
                animation: true
            }).popover('show');

            activePopoverElement = progressCircle;
        }

        function hideTooltip() {
            if (activePopoverElement) {
                $(activePopoverElement).popover('destroy');
                activePopoverElement = null;
            }
        }

        progressCircle.addEventListener('mouseenter', showTooltip);
        progressCircle.addEventListener('mouseleave', hideTooltip);
    });

    // Optional: close popover if clicked outside
    document.addEventListener('click', (e) => {
        if (activePopoverElement && !activePopoverElement.contains(e.target)) {
            $(activePopoverElement).popover('destroy');
            activePopoverElement = null;
        }
    });
}
/**
 * Attaches delete confirmation with Swal + AJAX to buttons having .delete-new-button
 * 
 * @param {string} realRoute - The route segment for the delete URL (e.g., 'bookings')
 */
function attachDeleteButtons(realRoute, swalOptions = {}) {
    const deleteButtons = document.querySelectorAll('.delete-new-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');

            showSwal({
                title: swalOptions.title || 'Are you sure?',
                text: swalOptions.text || 'You won\'t be able to revert this!',
                icon: swalOptions.icon || 'warning',
                confirmButtonText: swalOptions.confirmButtonText || 'Yes, delete it!',
                cancelButtonText: swalOptions.cancelButtonText || 'Cancel',
                showUpdatingOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteBooking(realRoute, itemId);
                }
            });
        });
    });

    function deleteBooking(realRoute, itemId) {
        const url = `${window.baseAdminUrl}/${realRoute}/${itemId}`;
        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.close();
                toastr.success(response.message || 'Item deleted successfully', 'Success', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
                window.location.reload();
            },
            error: function (xhr, status, error) {
                Swal.close();
                toastr.error(xhr.responseJSON?.message || 'Error deleting item', 'Error', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right"
                });
                console.error(error);
            }
        });
    }
}




/**
 * Initialise a Select2 AJAX dropdown with optional pagination, dynamic and static parameters, and pre‑selection.
 *
 * @param {string} selector               - jQuery selector for your <select>
 * @param {string} url                    - AJAX endpoint
 * @param {function} processItemFunction  - maps each returned item to { id, text }
 * @param {object|null} preselectedItem   - { id: ..., text: ... } to pre‑select, or null
 * @param {object} extraParams            - optional static params to send on every request
 */
function attachAjaxSelect(
    selector,
    url,
    processItemFunction,
    preselectedItem = null,
    extraParams = {}
) {
    const $select = $(selector);

    $select.select2({
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                const payload = {
                    q: params.term || '',
                    page: params.page || 1
                };
                const dt = $select.data('type');
                if (dt !== undefined) {
                    payload.data_type = dt;
                }
                return Object.assign(payload, extraParams);
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        allowClear: true,
        placeholder: 'Select an option',
        minimumInputLength: 0,
    });


    if (preselectedItem && preselectedItem.id) {
        const option = new Option(
            preselectedItem.text,
            preselectedItem.id,
            true,
            true
        );
        $select.append(option).trigger('change');
    }
}



/**
 * Resets the filter form and clears Select2 values.
 *
 * @param {string} formSelector - The selector for the filter form.
 * @param {Array} selectSelectors - Array of Select2 selectors to clear.
 */
function attachFilterResetButton(formSelector, selectSelectors) {
    document.getElementById('resetBtn')?.addEventListener('click', function () {
        const form = document.querySelector(formSelector);
        if (form) {
            form.reset();
        }
        selectSelectors.forEach(selector => {
            $(selector).val('').trigger('change');
        });
        form?.submit();
    });
}

/**
 * Attaches restore or permanent delete functionality to buttons with SweetAlert + AJAX + Toastr.
 *
 * @param {string} selector - e.g., '.restore-btn' or '.permanent-delete'
 * @param {object} options - {
 *     title: 'Restore Booking',
 *     text: 'Are you sure...',
 *     confirmButtonText: 'Yes, restore it!',
 *     successMessage: 'Booking restored successfully!',
 *     errorMessage: 'An error occurred...',
 * }
 */
function attachRestoreOrDeleteButtons(selector, options) {
    $(document).on('click', selector, function () {
        const bookingId = $(this).data('id');
        const form = $(this).closest('form');

        showSwal({
            title: options.title || 'Are you sure?',
            text: options.text || 'Do you want to proceed?',
            icon: options.icon || 'warning',
            confirmButtonText: options.confirmButtonText || 'Yes, continue!',
            cancelButtonText: options.cancelButtonText || 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: options.processingTitle || 'Processing',
                    text: options.processingText || 'Please wait...',
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
                    success: function (response) {
                        Swal.close();
                        toastr.success(options.successMessage || 'Operation completed successfully.', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function (xhr) {
                        Swal.close();
                        toastr.error(options.errorMessage || 'An error occurred. Please try again.', 'Error', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    }
                });
            }
        });
    });
}
/**
 * Attaches a bulk delete button to DataTables with SweetAlert + AJAX.
 *
 * @param {object} options {
 *     datatableSelector: '.datatable-Booking',
 *     deleteRoute: 'admin.bookings.deleteAll',
 *     buttonText: 'Delete Selected',
 *     buttonClass: 'btn-danger',
 *     swalOptions: { title, text, confirmButtonText, cancelButtonText },
 * }
 */
function attachBulkDeleteButton(options) {
    const {
        datatableSelector = '.datatable-Booking:not(.ajaxTable)',
        deleteRoute,
        buttonText = 'Delete Selected',
        buttonClass = 'btn-danger',
        swalOptions = {}
    } = options;

    const dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

    const deleteButton = {
        text: buttonText,
        className: buttonClass,
        action: function (e, dt, node, config) {
            const ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id');
            });

            if (ids.length === 0) {
                Swal.fire({
                    title: swalOptions.noSelectionTitle || 'No entries selected',
                    icon: 'warning',
                    confirmButtonText: swalOptions.okButtonText || 'OK'
                });
                return;
            }

            showSwal({
                title: swalOptions.title || 'Are you sure?',
                text: swalOptions.text || 'You will not be able to recover these!',
                icon: 'warning',
                confirmButtonText: swalOptions.confirmButtonText || 'Yes, delete!',
                cancelButtonText: swalOptions.cancelButtonText || 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'POST',
                        url: deleteRoute,
                        data: { ids: ids, _method: 'DELETE' }
                    })
                        .done(function () {
                            toastr.success('Deleted successfully.', 'Success', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                            location.reload();
                        })
                        .fail(function (xhr) {
                            toastr.error('Could not delete selected items.', 'Error', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        });
                }
            });
        }
    };

    dtButtons.push(deleteButton);

    $(datatableSelector).DataTable({
        buttons: dtButtons,
        orderCellsTop: true,
        select: {
            style: 'multi'
        },
        paging: false,
        searching: false,
        info: false
    });
}


$(document).ready(function () {
    $('.view-details').on('click', function () {
        var userId = $(this).data('id');
        $('#loader').show();
        $.ajax({
            url: "{{ route('admin.get-appuser-host-status-detail') }}",
            method: 'POST',
            data: {
                user_id: userId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log(response.data);
                var defaultImagePath = "{{ asset('public/images/icon/userdefault.jpg') }}";
                $('#modal-table tbody').empty();
                var data = response.data;
                if (data.phone && data.country_code) {
                    data.phone = data.country_code + data.phone;
                    delete data.country_code;
                }
                if (data.phone) {
                    data.phone = data.phone;
                }
                if (data.email) {
                    data.email = data.email;
                }
                if (data.company_name === null) {
                    data.company_name = '';
                }
                $.each(data, function (key, value) {
                    if (key === 'image') {
                        var imageUrl = value ? value : defaultImagePath;
                        $('#modal-table tbody').append(
                            '<tr>' +
                            '<th>' + capitalizeFirstLetter(key.replace('_', ' ')) + '</th>' +
                            '<td><a href="' + imageUrl + '" target="_blank"><img src="' + imageUrl + '" alt="' + key + '" style="max-width: 200px; height: auto;"></a></td>' +
                            '</tr>'
                        );
                    } else {
                        $('#modal-table tbody').append(
                            '<tr>' +
                            '<th>' + capitalizeFirstLetter(key.replace('_', ' ')) + '</th>' +
                            '<td>' + value + '</td>' +
                            '</tr>'
                        );
                    }
                });

                $('#detailsModal').modal('show');
            },
            error: function () {
                console.log('Failed to load user details.');
            },
            complete: function () {
                $('#loader').hide();
            }
        });
    });



    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});

// Initializes a reusable Dropzone for single image uploads with preview and hidden input handling.
// Supports existing file initialization, size/type validation, and clean error display.
// Usage: initDropzone({ selector, url, inputName, existingFile });
function initDropzone({
    selector,
    url,
    inputName,
    existingFile = null,
    maxFilesize = 1,
    acceptedFiles = '.jpeg,.jpg,.png,.gif',
    maxFiles = 1,
    additionalParams = { size: 1, width: 4096, height: 4096 },
    csrfToken = $('meta[name="csrf-token"]').attr('content')
}) {
    const dropzoneOptions = {
        url: url,
        maxFilesize: maxFilesize,
        acceptedFiles: acceptedFiles,
        maxFiles: maxFiles,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        params: additionalParams,
        success: function (file, response) {
            $('form').find(`input[name="${inputName}"]`).remove();
            $('form').append(`<input type="hidden" name="${inputName}" value="${response.name}">`);
        },
        removedfile: function (file) {
            file.previewElement.remove();
            if (file.status !== 'error') {
                $('form').find(`input[name="${inputName}"]`).remove();
                this.options.maxFiles += 1;
            }
        },
        init: function () {
            if (existingFile) {
                this.options.addedfile.call(this, existingFile);
                this.options.thumbnail.call(this, existingFile, existingFile.preview ?? existingFile.preview_url);
                existingFile.previewElement.classList.add('dz-complete');
                $('form').append(`<input type="hidden" name="${inputName}" value="${existingFile.file_name}">`);
                this.options.maxFiles -= 1;
            }
        },
        error: function (file, response) {
            let message = $.type(response) === 'string' ? response : response.errors.file;
            file.previewElement.classList.add('dz-error');
            const errorElements = file.previewElement.querySelectorAll('[data-dz-errormessage]');
            errorElements.forEach(node => {
                node.textContent = message;
            });
        }
    };

    new Dropzone(selector, dropzoneOptions);
}
