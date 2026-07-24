@extends('layouts.admin')
@section('content')
    @php
        $i = 0;
        $j = 0;
    @endphp
    <div class="content">
        @can('add_coupon_create')
            <div style="margin-bottom: 10px;" class="row">
                <div class="col-lg-12">
                    <a class="btn btn-success" href="{{ route('admin.add-coupons.create') }}">
                        {{ trans('global.add') }} {{ trans('global.addCoupon_title_singular') }}
                    </a>
                </div>
            </div>
        @endcan
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('global.addCoupon_title_singular') }} {{ trans('global.list') }}
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table-bordered table-striped table-hover datatable datatable-AddCoupon table">
                                <thead>
                                    <tr>
                                        <th width="10"></th>
                                        <th>{{ trans('global.id') }}</th>
                                        <th>{{ trans('global.coupon') }}</th>
                                        <th>{{ trans('global.expiry_date') }}</th>
                                        <th>{{ trans('global.amount') }}</th>
                                        <th>{{ trans('global.minimum_amount') }}</th>
                                        <th>{{ trans('global.status') }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($addCoupons as $key => $addCoupon)
                                        <tr data-entry-id="{{ $addCoupon->id }}"
                                            class="{{ \Carbon\Carbon::parse($addCoupon->coupon_expiry_date)->isPast() ? 'danger' : '' }}">

                                            <td></td>
                                            <td>{{ $addCoupon->id ?? '' }}</td>

                                            <td>
                                                {{ $addCoupon->coupon_title ?? '' }}<br>
                                                <strong>{{ $addCoupon->coupon_code ?? '' }}</strong>
                                                @if ($firstBookingCoupon == $addCoupon->coupon_code)
    <span class="badge bg-success mt-2">
        First Booking Coupon
    </span>
@endif
                                            </td>

                                            <td>{{ $addCoupon->coupon_expiry_date ?? '' }}</td>
                                            <td>{{ $addCoupon->coupon_value ?? '' }}%</td>
                                            <td>{{ $addCoupon->min_order_amount ?? '' }}
                                                {{ $general_default_currency->meta_value ?? '' }}</td>

                                            <td>
                                                <div
                                                    class="status-toggle d-flex justify-content-between align-items-center">
                                                    <input data-id="{{ $addCoupon->id }}" class="check statusdata"
                                                        type="checkbox" id="{{ 'user' . $i++ }}" data-toggle="toggle"
                                                        data-on="Active" data-off="InActive"
                                                        {{ $addCoupon->status ? 'checked' : '' }}>
                                                    <label for="{{ 'user' . $j++ }}" class="checktoggle">checkbox</label>
                                                </div>
                                            </td>

                                            <td>
                                                @can('add_coupon_edit')
                                                    <a class="btn btn-xs btn-info"
                                                        href="{{ route('admin.add-coupons.edit', $addCoupon->id) }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endcan

                                                @can('add_coupon_delete')
                                                    <form action="{{ route('admin.add-coupons.destroy', $addCoupon->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="button" class="btn btn-xs btn-danger delete-button"
                                                            data-id="{{ $addCoupon->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            @can('add_coupon_delete')
                let deleteButtonTrans = '{{ trans('global.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.add-coupons.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id');
                        });

                        if (ids.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ trans('global.zero_selected') }}',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            return;
                        }

                        Swal.fire({
                            title: '{{ trans('global.are_you_sure') }}',
                            text: '{{ trans('global.adddelete_confirmation') }}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: '{{ trans('global.yes_delete') }}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var _token = $('meta[name="csrf-token"]').attr('content');
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                }).done(function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ trans('global.deleted') }}',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                }).fail(function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ trans('global.error') }}',
                                        text: '{{ trans('global.delete_error') }}'
                                    });
                                });
                            }
                        });
                    }
                };

                dtButtons.push(deleteButton);
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
            });

            let table = $('.datatable-AddCoupon:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });

        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $('.statusdata').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            var requestData = {
                'status': status,
                'pid': id
            };
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            requestData['_token'] = csrfToken;
            $.ajax({

                type: "POST",
                dataType: "json",
                url: '/admin/update-addCoupon-status',
                data: requestData,
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message, '{{ trans('global.success') }}', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    } else {
                        toastr.error(response.message, 'Error', {
                            CloseButton: true,
                            ProgressBar: true,
                            positionClass: "toast-bottom-right"
                        });
                    }
                }
            });
        })
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // JavaScript to handle the SweetAlert dialog
        document.addEventListener('DOMContentLoaded', function() {
            // Add a click event listener to the "Delete" button
            document.querySelectorAll('.delete-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    var deleteUrl = this.form.action; // Get the form's action URL

                    Swal.fire({
                        title: "{{ trans('global.are_you_sure') }}",
                        text: "{{ trans('global.you_able_revert_this') }}",
                        icon: "{{ trans('global.warning') }}",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "{{ trans('global.yes_delete_it') }}",
                        cancelButtonText: "{{ trans('global.cancel') }}",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }).then(function(response) {
                                if (response.ok) {

                                    location.reload();
                                }
                            });
                            location.reload();
                        }
                    });
                });
            });
        });
    </script>
@endsection
