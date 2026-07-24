
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const appUserId = this.getAttribute('data-id');
                    Swal.fire({
                        title: '{{ trans("global.are_you_sure") }}',
                        text: '{{ trans("global.delete_confirmation") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            deleteAppUser(appUserId);
                        }
                    });
                });
            });

            function deleteAppUser(appUserId) {
                const url = `{{ route('admin.app-users.destroy', ':id') }}`.replace(':id', appUserId);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    success: function (response) {
                        Swal.close();
                        toastr.success('{{ trans('global.delete_app_user') }}', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        // Optionally, refresh the page or update UI as needed
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        toastr.error('{{ trans('global.deletion_error') }}', 'Error', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        console.error(error);
                    }
                });
            }
        });

        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            let table = $('.datatable-AppUser:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        })
        $(document).ready(function () {
           
            function resetFilters() {
                $('#daterange-btn').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                $('#status').val('');
                $('#driver').val('').trigger('change');
            }
            $('button[name="btn"]').on('click', function () {
                $('form').submit();
            });

            $('#driver').select2({
                minimumInputLength: 4,
                ajax: {
                    url: "{{ route('admin.driver.search') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name,
                                };
                            })
                        };
                    },
                    cache: true,
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("Error while fetching driver data:", textStatus, errorThrown);
                    }
                }
            });

            var searchfieldId = "{{ $searchfieldId }}";
            var searchfield = "{{ $searchfield }}";
            if (searchfieldId) {
                var option = new Option(searchfield, searchfieldId, true, true);
                $('#driver').append(option).trigger('change');
            }
            $('#resetBtn').click(function () {
                $('#appusersFilterForm')[0].reset();
                var baseUrl = '{{ route('admin.drivers.index') }}';
                window.history.replaceState({}, document.title, baseUrl);
                window.location.reload();
            });
            function maskPhoneNumber(phone) {
                if (phone.length > 6) {
                    return phone.slice(0, -6) + '******';
                } else {
                    return phone;
                }
            }
            function maskEmail(email) {
                var emailParts = email.split('@');
                var localPart = emailParts[0];
                var domainPart = emailParts[1];
                var maskedLocalPart = localPart.length > 3 ? localPart.slice(0, 3) + localPart.slice(3).replace(/./g, '*') : localPart;
                return maskedLocalPart + '@' + domainPart;
            }
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
            const $csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const $body = document.body;
            const $driverSelect = document.getElementById('driver');
            const $dateRangeBtn = document.getElementById('daterange-btn');
            const $appUsersFilterForm = document.getElementById('appusersFilterForm');
            const showToast = (message, type = 'success', options = {}) => {
                toastr[type](message, type.charAt(0).toUpperCase() + type.slice(1), {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-bottom-right',
                    ...options
                });
            };

            const showSwal = (title, text, icon, confirmText, cancelText, onConfirm) => {
                Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText || 'Cancel'
                }).then(result => result.isConfirmed && onConfirm());
            };

            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function getStatusColor(status) {
                return status === "approved"
                    ? "background-color: #28a745; color: white; padding: 5px; border-radius: 5px;"
                    : status === "rejected"
                        ? "background-color: #dc3545; color: white; padding: 5px; border-radius: 5px;"
                        : "background-color: #ffc107; color: black; padding: 5px; border-radius: 5px;";
            }


            const handleStatusToggle = (selector, url, key, onSuccess) => {
                $body.addEventListener('change', async e => {
                    if (!e.target.matches(selector)) return;
                    const checkbox = e.target;
                    const status = checkbox.checked ? 1 : 0;
                    const id = checkbox.dataset.id;
                    showSwal(
                        'Are you sure?',
                        `Do you want to update the ${key}?`,
                        'warning',
                        'Yes, update it',
                        null,
                        async () => {
                            $('#loader').show();
                            try {
                                await $.ajax({
                                    type: 'POST',
                                    dataType: 'json',
                                    url,
                                    data: { [key]: status, pid: id, _token: $csrfToken }
                                });
                                showToast('{{ trans("global.success") }}');
                                onSuccess && onSuccess();
                            } catch (error) {
                                showToast('Something went wrong. Please try again.', 'error');
                                checkbox.checked = !status;
                            } finally {
                                $('#loader').hide();
                            }
                        }
                    );
                });
            };

            handleStatusToggle('.statusdata', '/admin/update-appuser-status', 'status');
            handleStatusToggle('.identify', '/admin/update-appuser-identify', 'verified', () => { });
            handleStatusToggle('.phone_verify', '/admin/update-appuser-phoneverify', 'phone_verify');
            handleStatusToggle('.email_verify', '/admin/update-appuser-emailverify', 'email_verify');
            handleStatusToggle('.hoststatusdata', '/admin/update-appuser-document-status', 'status', () => {
                $('.hoststatusdata[data-id="' + id + '"]').closest('.status-toggle').find('.requested-label').remove();
            });

        });
    </script>