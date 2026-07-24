@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}?{{ time() }}">
@endsection
@section('content')
    <div class="content">
        <div class="content container-fluid">
            @include('admin.appUsers.driver.menu')

            <div class="driver-profile-page">
                <div class="profile-container">
                    <div class="table-responsive">
                        <table id="user-documents-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('user.document_type') }}</th>
                                    <th>{{ __('user.image') }}</th>
                                    <th>{{ __('user.status') }}</th>
                                    <th>{{ __('user.date') }}</th>
                                    <th>{{ __('user.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const userId = "{{ $userId ?? '' }}";

            function fetchDocuments() {
                $.ajax({
                    url: "{{ route('admin.driver.account.documents') }}",
                    method: "POST",
                    data: {
                        user_id: userId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        let tableBody = $("#user-documents-table tbody");
                        tableBody.empty();

                        const defaultImage = "{{ asset('images/icon/userdefault.jpg') }}";

                        $.each(response.data.documents, function (key, doc) {
                            const imageUrl = doc.image || defaultImage;
                            const createdAt = doc.created_at ? new Date(doc.created_at).toLocaleString() : 'N/A';
                            const statusRaw = doc.status || 'not_uploaded';
                            const status = capitalizeFirst(statusRaw.replace(/_/g, ' '));
                            const badgeClass = getBadgeClass(statusRaw);
                            const metaKey = key;

                            const approvedBtn = statusRaw === 'approved' ? 'btn-outline-success' : 'btn-success';
                            const rejectedBtn = statusRaw === 'rejected' ? 'btn-outline-danger' : 'btn-danger';

                            const actions = `
                                <button class="btn ${approvedBtn} btn-sm update-status" data-id="${userId}" data-key="${metaKey}" data-status="approved">{{ __('user.approve') }}</button>
                                <button class="btn ${rejectedBtn} btn-sm update-status" data-id="${userId}" data-key="${metaKey}" data-status="rejected">{{ __('user.reject') }}</button>
                            `;

                            tableBody.append(`
                                <tr data-key="${metaKey}">
                                    <td>${metaKey.replace(/_/g, '_')}</td>
                                    <td>
                                        <a href="${imageUrl}" target="_blank">
                                            <img src="${imageUrl}" alt="${metaKey}" class="document-img">
                                        </a>
                                    </td>
                                    <td><span class="badge ${badgeClass} status-label">${status}</span></td>
                                    <td>${createdAt}</td>
                                    <td>${actions}</td>
                                </tr>
                            `);
                        });
                    },
                    error: function () {
                        console.error("{{ __('user.document_load_error') }}");
                    }
                });
            }

            function capitalizeFirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function getBadgeClass(status) {
                switch (status.toLowerCase()) {
                    case 'approved': return 'badge-success';
                    case 'rejected': return 'badge-danger';
                    case 'not_uploaded': return 'badge-secondary';
                    default: return 'badge-warning';
                }
            }

            $(document).on('click', '.update-status', function () {
                const userId = $(this).data('id');
                const metaKey = $(this).data('key');
                const newStatus = $(this).data('status');
                const row = $(`tr[data-key="${metaKey}"]`);
                const currentStatus = row.find('.status-label').text().trim().toLowerCase();

                if (currentStatus === newStatus.toLowerCase()) return;

                Swal.fire({
                    title: '{{ __('user.are_you_sure') }}',
                    text: `{{ __('user.about_to') }} ${newStatus} {{ __('user.this_document') }}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: '{{ __('user.yes_do_it') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.driver.account.document.status') }}",
                            method: "POST",
                            data: {
                                user_id: userId,
                                meta_key: metaKey,
                                status: newStatus,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (res) {
                                const status = capitalizeFirst(res.status);
                                const badgeClass = getBadgeClass(res.status);
                                const newBadge = `<span class="badge ${badgeClass} status-label">${status}</span>`;
                                row.find('.status-label').replaceWith(newBadge);

                                const approvedBtn = res.status === 'approved' ? 'btn-outline-success' : 'btn-success';
                                const rejectedBtn = res.status === 'rejected' ? 'btn-outline-danger' : 'btn-danger';

                                const newActions = `
                                    <button class="btn ${approvedBtn} btn-sm update-status" data-id="${userId}" data-key="${metaKey}" data-status="approved">{{ __('user.approve') }}</button>
                                    <button class="btn ${rejectedBtn} btn-sm update-status" data-id="${userId}" data-key="${metaKey}" data-status="rejected">{{ __('user.reject') }}</button>
                                `;
                                row.find('td:last').html(newActions);
                            },
                            error: function (xhr) {
                                Swal.fire("{{ __('user.error') }}", xhr.responseJSON?.message || "{{ __('user.something_went_wrong') }}", "error");
                            }
                        });
                    }
                });
            });

            fetchDocuments();
        });
    </script>
@endsection