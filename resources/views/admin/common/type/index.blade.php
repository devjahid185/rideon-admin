@extends('layouts.admin')
@section('styles')
    <style>
        .dataTables_info {
            display: none;
        }

        .paging_simple_numbers {
            display: none;
        }

        .pagination.justify-content-end {
            float: right;
        }

        .main-footer {
            overflow: hidden;
            margin-left: 0;
        }
    </style>
@endsection
@section('content')
    @php
        $i = 0;
        $j = 0;
        if ($title == 'vehicles') {
            $title = 'vehicle';
        } else {
            $title = $title;
        }

    @endphp
    <div class="content">
        @can($permissionrealRoute . '_create')
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
                    <div class="panel-heading">
                        {{ $title }} {{ trans('global.list') }}
                    </div>
                    <div class="panel-body">
                        <table class="table-bordered table-striped table-hover datatable datatable-ItemType table">
                            <thead>
                                <tr>
                                    <th width="10"></th>
                                    <th>{{ trans('global.id') }}</th>
                                    <th>{{ trans('global.name') }}</th>
                                    <th>{{ trans('global.description') }}</th>
                                    <th>{{ trans('global.image') }}</th>
                                    <th>City Fare</th>
                                    <th>Admin Commission (%)</th>
                                    <th>Service Types</th>
                                    <th>Max Weight</th>

                                    <th>{{ trans('global.status') }}</th>

                                    <th>{{ trans('global.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($itemTypes as $key => $itemType)
                                    <tr data-entry-id="{{ $itemType->id }}">
                                        <td></td>
                                        <td>{{ $itemType->id ?? '' }}</td>
                                        <td>{{ $itemType->name ?? '' }}</td>
                                        <td>{{ $itemType->description ?? '' }}</td>

                                        <td>
                                            @if ($itemType->image)
                                                <a href="{{ $itemType->image->url }}">
                                                    <img src="{{ $itemType->image->thumbnail }}"
                                                        alt="{{ $itemType->name }}" class="item-image-size">
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($itemType->cityFare)
                                                Recommended: {{ number_format($itemType->cityFare->recommended_fare, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($itemType->cityFare)
                                                {{ $itemType->cityFare->admin_commission }}%
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td>
                                            @if ($itemType->serviceTypes && $itemType->serviceTypes->count())
                                                @foreach ($itemType->serviceTypes as $serviceType)
                                                    <span class="label btn-primary">{{ $serviceType->name }}</span>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $isDelivery = $itemType->serviceTypes
                                                    ->pluck('name')
                                                    ->map(fn($n) => strtolower($n))
                                                    ->contains('delivery');
                                            @endphp

                                            @if ($isDelivery && $itemType->max_weight)
                                                {{ $itemType->max_weight }} KG
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td>
                                            <div class="status-toggle d-flex justify-content-between align-items-center">
                                                <input data-id="{{ $itemType->id }}" class="check statusdata"
                                                    type="checkbox" data-onstyle="success" id="{{ 'type' . $i++ }}"
                                                    data-offstyle="danger" data-toggle="toggle" data-on="Active"
                                                    data-off="Inactive" {{ $itemType->status ? 'checked' : '' }}>
                                                <label for="{{ 'type' . $j++ }}" class="checktoggle">checkbox</label>
                                            </div>

                                        </td>

                                        <td>
                                            @can($permissionrealRoute . '_edit')
                                                <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info"
                                                    href="{{ url('admin/vehicle-type/' . $itemType->id . '/edit') }}">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            @endcan

                                            @can($permissionrealRoute . '_delete')
                                                <button type="button" class="btn btn-xs btn-danger delete-type-button"
                                                    data-id="{{ $itemType->id }}">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-end">
                                {{-- Previous Page Link --}}
                                @if ($itemTypes->currentPage() > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $itemTypes->previousPageUrl() }}"
                                            tabindex="-1">{{ trans('global.previous') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ trans('global.previous') }}</span>
                                    </li>
                                @endif

                                {{-- Numeric Pagination Links --}}
                                @for ($i = 1; $i <= $itemTypes->lastPage(); $i++)
                                    <li class="page-item {{ $i == $itemTypes->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $itemTypes->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($itemTypes->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $itemTypes->nextPageUrl() }}">{{ trans('global.next') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ trans('global.next') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
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
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-type-button');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemTypeId = this.getAttribute('data-id');

                    Swal.fire({
                        title: '{{ trans('global.are_you_sure') }}',
                        text: '{{ trans('global.delete_confirmation') }}',
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
                            deleteItemType(itemTypeId);
                        }
                    });
                });
            });

            function deleteItemType(itemTypeId) {
                //const url = `{{ url('admin/vehicle-type') }}/${itemTypeId}`;
                const url = `/admin/vehicle-type/${itemTypeId}`;
                // const url = `{{ route('admin.vehicle-type.destroy', ':id') }}`.replace(':id', itemTypeId);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    success: function(response) {
                        Swal.close();
                        toastr.success('{{ trans('global.delete_success') }}', 'Success', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    },
                    error: function(xhr, status, error) {
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
    </script>

    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            function handleDeletion(route) {
                return function(e, dt, node, config) {
                    var ids = $.map(dt.rows({
                        selected: true
                    }).nodes(), function(entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        Swal.fire({
                            title: '{{ trans('global.no_entries_selected') }}',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '{{ trans('global.are_you_sure') }}',
                        text: '{{ trans('global.delete_confirmation') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var csrfToken = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                method: 'POST',
                                url: route,
                                data: {
                                    ids: ids,
                                    _method: 'DELETE'
                                }
                            }).done(function(response) {
                                location.reload(); // Reload the page after deletion
                            }).fail(function(xhr, status, error) {
                                Swal.fire(
                                    '{{ trans('global.error') }}',
                                    '{{ trans('global.delete_error') }}',
                                    'error'
                                );
                            });
                        }
                    });
                };
            }


            let deleteRoute = "{{ route('admin.bookings.deleteAll') }}";


            let deleteButton = {
                text: '{{ trans('global.delete_all') }}',
                className: 'btn-danger',
                action: handleDeletion(deleteRoute)
            };

            dtButtons.push(deleteButton);

            let table = $('.datatable-ItemType:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                //   $($.fn.dataTable.tables(true)).DataTable()
                //       .columns.adjust();
            });

        })
    </script>
    <script>
        $(document).ready(function() {
            $('.statusdata').change(function() {
                var status = $(this).prop('checked') == true ? 1 : 0;
                var id = $(this).data('id');
                var $toggle = $(this);

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.update-vehicle-type-status') }}",
                    data: {
                        status: status,
                        pid: id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response.status);
                        if (response.status === 200) {
                            toastr.success(response.message, '{{ trans('global.success') }}', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        } else {
                            toastr.error(response.message, 'Error', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                            $toggle.prop('checked', !status);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Something went wrong. Please try again.',
                            '{{ trans('global.error') }}', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-bottom-right"
                            });
                        $toggle.prop('checked', !status);
                    }
                });
            });
        });
    </script>
@endsection
