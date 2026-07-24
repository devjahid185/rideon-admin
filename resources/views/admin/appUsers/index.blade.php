@extends('layouts.admin')
@section('content')
@php
$i = 0;
$j = 0;
@endphp
<div class="content">
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8"
                id="appusersFilterForm">
                @if (request()->has('host_status'))
                <input type="hidden" name="host_status" value="{{ request()->input('host_status') }}">
                @endif
                <div class="col-md-12 d-none">
                    <input class="form-control" type="hidden" id="startDate" name="from" value="">
                    <input class="form-control" type="hidden" id="endDate" name="to" value="">
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.date_range') }}</label>
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control" id="daterange-btn">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ trans('global.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All</option>
                                <option value="1" {{ request()->input('status') == '1' ? 'selected' : '' }}>Active
                                </option>
                                <option value="0" {{ request()->input('status') == '0' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        @php
                        $label = 'Rider';
                        @endphp
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ $label }}</label>
                            <select class="form-control select2" name="customer" id="customer">
                                <option value="">{{ $searchfield }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-4 mt-4 mt-5">
                            <button type="submit" name="btn"
                                class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                            <button type="button" id="resetBtn"
                                class="btn btn-primary btn-flat">{{ trans('global.reset') }}</button>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
    <div style="margin-left: 5px; margin-bottom: 6px;" class="row">
        <div class="col-lg-12">
            {{-- Live --}}
            <a class="btn {{ request()->routeIs('admin.app-users.index') && is_null(request()->query('status')) && !request()->has('host_status') ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.app-users.index', array_merge(request()->except(['status', 'host_status']), ['status' => null])) }}">
                {{ trans('global.live') }}
                <span
                    class="badge badge-pill badge-primary">{{ $statusCounts['live'] > 0 ? $statusCounts['live'] : 0 }}</span>
            </a>

            {{-- Active --}}
            <a class="btn {{ request()->query('status') === '1' && !request()->has('host_status') ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.app-users.index', array_merge(request()->except('host_status'), ['status' => 1])) }}">
                Active
                <span
                    class="badge badge-pill badge-primary">{{ $statusCounts['active'] > 0 ? $statusCounts['active'] : 0 }}</span>
            </a>

            {{-- Inactive --}}
            <a class="btn {{ request()->query('status') === '0' && !request()->has('host_status') ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.app-users.index', array_merge(request()->except('host_status'), ['status' => 0])) }}">
                Inactive
                <span
                    class="badge badge-pill badge-primary">{{ $statusCounts['inactive'] > 0 ? $statusCounts['inactive'] : 0 }}</span>
            </a>

        </div>

    </div>

    <div id="loader" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $label }} {{ trans('global.list') }}
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table-bordered table-striped table-hover datatable datatable-AppUser table">
                    <thead>
                        <tr>
                            <th></th>

                            <th>
                                {{ trans('global.id') }}
                            </th>
                            <th>
                                {{ trans('global.name') }}
                            </th>

                            <th>
                                {{ trans('global.email') }}
                            </th>
                            <th>
                                {{ trans('global.phone') }}
                            </th>
                            <th>{{ trans('user.registered_on') }}</th>
                            <th>
                                {{ trans('global.status') }}
                            </th>

                            <th>&nbsp;

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appUsers as $key => $appUser)
                        <tr data-entry-id="{{ $appUser->id }}">
                            <td></td>
                            <td>
                                <a target="_blank" class="btn btn-xs btn-primary"
                                    href="{{ route('admin.app-users.show', $appUser->id) }}">
                                    #{{ $appUser->id ?? '' }}</a>
                            </td>
                            <td>
                                @if ($appUser->profile_image)
                                <a href="{{ $appUser->profile_image->getUrl() }}" target="_blank"
                                    style="display: inline-block">
                                    <img src="{{ $appUser->profile_image->getUrl('thumb') }}">
                                </a>
                                @else
                                <img src="{{ asset('images/icon/userdefault.jpg') }}" alt="Default Image"
                                    style="display: inline-block;">
                                @endif
                                <a target="_blank" class="btn btn-xs btn-primary"
                                    href="{{ route('admin.app-users.show', $appUser->id) }}">
                                    {{ $appUser->first_name ?? '' }} {{ $appUser->last_name ?? '' }}
                                </a>

                            </td>
                            <td>

                                @can('app_user_contact_access')
                                {{ $appUser->email }}
                                @else
                                {{ maskEmail($appUser->email) }}
                                @endcan
                            </td>
                            <td>
                                {{ $appUser->phone_country ?? '' }}
                                @can('app_user_contact_access')
                                {{ $appUser->phone ?? '' }}
                                @else
                                {{ $appUser->phone ? substr($appUser->phone, 0, -6) . str_repeat('*', 6) : '' }}
                                @endcan

                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $appUser->created_at ? $appUser->created_at->format('d M Y') : '-' }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $appUser->created_at ? $appUser->created_at->format('h:i A') : '' }}
                                </small>
                            </td>
                            <td>
                                <div class="status-toggle d-flex justify-content-between align-items-center">
                                    <input data-id="{{ $appUser->id }}" class="check statusdata" type="checkbox"
                                        data-onstyle="success" id="{{ 'user' . $i++ }}" data-offstyle="danger"
                                        data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $appUser->status ? 'checked' : '' }}>
                                    <label for="{{ 'user' . $j++ }}" class="checktoggle">checkbox</label>
                                </div>
                            </td>

                            <td>

                                @can('app_user_show')
                                <a class="btn btn-xs btn-primary"
                                    href="{{ route('admin.app-users.show', $appUser->id) }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                @endcan

                                @can('app_user_delete')
                                <button type="button" class="btn btn-xs btn-danger delete-button"
                                    data-id="{{ $appUser->id }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                                @endcan
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! admin_pagination($appUsers, 3) !!}

            </div>
        </div>
    </div>

</div>

@endsection

@include('admin.common.addSteps.footer.footerJs')

@section('scripts')
@parent

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const appUserId = this.dataset.id;

                Swal.fire({
                    title: "{{ trans('global.are_you_sure') }}",
                    text: "{{ trans('global.delete_confirmation') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => Swal.showLoading()
                        });
                        deleteAppUser(appUserId);
                    }
                });
            });
        });

        function deleteAppUser(id) {
            const url = "{{ route('admin.app-users.destroy', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success() {
                    Swal.close();
                    toastr.success("{{ trans('global.delete_app_user') }}", 'Success', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                    location.reload();
                },
                error() {
                    Swal.close();
                    toastr.error("{{ trans('global.deletion_error') }}", 'Error', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-bottom-right"
                    });
                }
            });
        }
    });
</script>

<script>
    $(function() {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

        function handleDeletion(route) {
            return function(e, dt) {
                let ids = $.map(dt.rows({
                    selected: true
                }).nodes(), entry => $(entry).data('entry-id'));

                if (!ids.length) {
                    Swal.fire({
                        title: "{{ trans('global.no_entries_selected') }}",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                Swal.fire({
                    title: "{{ trans('global.are_you_sure') }}",
                    text: "{{ trans('global.delete_confirmation') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: 'POST',
                            url: route,
                            data: {
                                ids: ids,
                                _method: 'DELETE'
                            }
                        }).done(() => location.reload());
                    }
                });
            };
        }

        dtButtons.push({
            text: "{{ trans('global.delete_all') }}",
            className: 'btn-danger',
            action: handleDeletion("{{ route('admin.app-users.deleteAll') }}")
        });

        $('.datatable-AppUser:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        });
    });
</script>

<script>
    $('#resetBtn').on('click', function() {
        $('#appusersFilterForm')[0].reset();
        window.location.href = "{{ route('admin.app-users.index') }}";
    });
</script>

@endsection