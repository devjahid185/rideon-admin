@extends('layouts.admin')
@section('content')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/driver.css') }}?{{ time() }}">
@endsection
<div class="content">
    <div class="box">
        <div class="box-body">
            <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8"
                id="appusersFilterForm">
                @if(request()->has('host_status'))
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
                                <!-- Add the input element here -->
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
                                <option value="0" {{ request()->input('status') == '0' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        @php
                        $label = trans('user.drivers'); // Default

                        @endphp
                        <div class="col-md-3 col-sm-12 col-xs-12">
                            <label>{{ $label }}</label>
                            <select class="form-control select2" name="driver" id="driver">
                                <option value="">{{ $searchfield }}</option>
                                <!-- Add any other options you want to display -->
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-4 mt-5 mt-4">
                            <button type="submit" name="btn" class="btn btn-primary btn-flat">{{ trans('global.filter')
                                                    }}</button>
                            <button type="button" id="resetBtn" class="btn btn-primary btn-flat ">{{
        trans('global.reset') }}</button>
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
                href="{{ route('admin.drivers.index', array_merge(request()->except(['status', 'host_status']), ['status' => null])) }}">
                {{ trans('global.live') }}
                <span class="badge badge-pill badge-primary">{{ $statusCounts['live'] > 0 ? $statusCounts['live'] : 0
                                        }}</span>
            </a>

            {{-- Active --}}
            <a class="btn {{ request()->query('status') === '1' && !request()->has('host_status') ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.drivers.index', array_merge(request()->except('host_status'), ['status' => 1])) }}">
                Active
                <span class="badge badge-pill badge-primary">{{ $statusCounts['active'] > 0 ? $statusCounts['active'] :
        0 }}</span>
            </a>

            {{-- Inactive --}}
            <a class="btn {{ request()->query('status') === '0' && !request()->has('host_status') ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.drivers.index', array_merge(request()->except('host_status'), ['status' => 0])) }}">
                Inactive
                <span class="badge badge-pill badge-primary">{{ $statusCounts['inactive'] > 0 ?
        $statusCounts['inactive'] : 0 }}</span>
            </a>


            <a class="btn {{ request()->query('host_status') === '2' ? 'btn-primary' : 'btn-inactive' }}"
                href="{{ route('admin.drivers.index', array_merge(request()->except('status'), ['host_status' => 2])) }}">
                Requested
                <span class="badge badge-pill badge-primary">{{ $statusCounts['requested'] > 0 ?
        $statusCounts['requested'] : 0 }}</span>
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
            {{ $label }} {{ trans('user.list') }}
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-AppUser">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans('user.id') }}</th>
                            <th>{{ trans('user.driver') }}</th>
                            <th>{{ trans('user.vehicle') }}</th>
                            <th>{{ trans('user.ride_information') }}</th>
                            <th>{{ trans('user.banned') }}</th>
                            <th>{{ trans('user.approve_status') }}</th>
                            <th>{{ trans('user.registered_on') }}</th>
                            <th>{{ trans('user.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $userType = request()->input('user_type');
                        $routeName = 'admin.driver.profile';
                        $documentKeys = [
                        'driving_licence_front_status',
                        'driving_licence_back_status',
                        'driver_id_front_status',
                        'driver_id_back_status'
                        ];
                        @endphp
                        @foreach($appUsers as $key => $appUser)
                        @php
                        $statuses = $appUser->metadata->whereIn('meta_key', $documentKeys)->pluck('meta_value');
                        $iconColor = 'text-muted';
                        if ($statuses->contains('pending'))
                        $iconColor = 'text-warning';
                        elseif ($statuses->contains('rejected'))
                        $iconColor = 'text-danger';
                        elseif ($statuses->count() > 0)
                        $iconColor = 'text-success';
                        $maskedEmail = '';

                        @endphp

                        <tr data-entry-id="{{ $appUser->id }}">
                            <td></td>

                            {{-- ID --}}
                            <td>
                                <a target="_blank" class="btn btn-xs btn-primary"
                                    href="{{ route($routeName, $appUser->id) }}">
                                    #{{ $appUser->id }}
                                </a>
                            </td>

                            {{-- User Info: Avatar + Name + Email + Phone --}}
                            <td>
                                <div class="row" style="margin: 0;">
                                    {{-- Avatar --}}
                                    <div class="col-xs-2" style="padding-right: 5px;">
                                        @if($appUser->profile_image)
                                        <a href="{{ $appUser->profile_image->getUrl() }}" target="_blank">
                                            <img src="{{ $appUser->profile_image->getUrl('preview') }}"
                                                class="img-circle img-responsive"
                                                style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                        </a>
                                        @else
                                        <img src="{{ asset('images/icon/userdefault.jpg') }}" alt="Default"
                                            class="img-circle img-responsive"
                                            style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-xs-10" style="padding-left:30px;"> {{-- increased padding here --}}
                                        <a target="_blank" href="{{ route($routeName, $appUser->id) }}">
                                            <strong>{{ $appUser->first_name }} {{ $appUser->last_name }}</strong>
                                        </a><br>
                                        <small class="text-muted">
                                            {{ auth()->user()->can('app_user_contact_access') ? $appUser->email :
                                maskEmail($appUser->email) }}<br>
                                            {{ $appUser->phone_country ?? '' }}
                                            {{ auth()->user()->can('app_user_contact_access')
                                ? $appUser->phone
                                : ($appUser->phone ? substr($appUser->phone, 0, -6) . str_repeat('*', 6) :
                                    '')  }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($appUser->item)
                                <div class="text-muted small">
                                    <strong>{{ trans('vehicle.make') }}:</strong>
                                    {{ $appUser->item->vehicleMake->name ?? 'N/A' }}<br>
                                    <strong>{{ trans('vehicle.model') }}::</strong>
                                    {{ $appUser->item->model ?? 'N/A'
                                   }}<br>
                                    <strong>{{ trans('vehicle.vehicle_number') }}::</strong> {{   $appUser->item->registration_number ?? 'N/A' }}<br>
                                    <strong>{{ trans('vehicle.vehicle_year') }}::</strong> {{ $appUser->item->itemVehicle->year ?? 'N/A' }}
                                </div>
                                @else
                                <span class="text-danger">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($appUser->item)
                                <div class="text-muted small">
                                    <strong>{{ trans('user.live_rides') }}:</strong> {{
                                                            $appUser->hostBookings->where(
                                                                'status',
                                                                'Ongoing'
                                                            )->count() }}<br>
                                    <strong>{{ trans('user.completed_rides') }}:</strong> {{
                                                            $appUser->hostBookings->where(
                                                                'status',
                                                                'Completed'
                                                            )->count() }}<br>
                                    <strong>{{ trans('user.cancelled_rides') }}:</strong> {{
                                                            $appUser->hostBookings->where(
                                                                'status',
                                                                'Cancelled'
                                                            )->count() }}<br>
                                    <strong>{{ trans('user.rejected_rides') }}:</strong> {{
                                                            $appUser->hostBookings->where(
                                                                'status',
                                                                'Rejected'
                                                            )->count() }}
                                </div>
                                @else
                                <span class="text-danger">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="status-toggle d-flex justify-content-between align-items-center">
                                    <input data-id="{{ $appUser->id }}" class="check statusdata" type="checkbox"
                                        id="status_{{ $appUser->id }}" data-toggle="toggle" data-on="Active"
                                        data-off="Inactive" data-onstyle="success" data-offstyle="danger" {{
                                $appUser->status ? 'checked' : '' }}>
                                    <label for="status_{{ $appUser->id }}" class="checktoggle">checkbox</label>
                                </div>
                            </td>
                            {{-- Documents --}}


                            <td>

                                @php
                                $hasAnyDocument = $appUser->metadata->contains(fn($meta) =>
                                in_array($meta->meta_key, $documentKeys));
                                @endphp

                                <div class="d-flex justify-content-between align-items-center gap-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($hasAnyDocument)
                                        <a href="{{ route('admin.driver.document', $appUser->id) }}" target="_blank"
                                            class="d-flex align-items-center gap-2 text-decoration-none">
                                            <i class="fa fa-file fa-lg view-verification-documents {{ $iconColor }}"
                                                title="View Documents" style="cursor: pointer;"></i>
                                            <span class="text-muted small">{{ trans('user.documents') }}</span>
                                        </a>
                                        @else
                                        <span class="text-muted small">{{ trans('user.no_documents') }}</span>
                                        @endif
                                    </div>


                                    <div class="d-flex align-items-center gap-2">
                                        <input data-id="{{ $appUser->id }}" class="check identify" type="checkbox"
                                            id="verify_{{ $appUser->id }}" data-toggle="toggle" data-on="Active"
                                            data-off="Inactive" data-onstyle="success" data-offstyle="danger" {{
                                $appUser->document_verify ? 'checked' : '' }}>
                                        <label for="verify_{{ $appUser->id }}" class="checktoggle mb-0">checkbox</label>
                                    </div>
                                </div>


                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $appUser->created_at
            ? $appUser->created_at->format('d M Y')
            : '-' }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $appUser->created_at
            ? $appUser->created_at->format('h:i A')
            : '' }}
                                </small>
                            </td>
                            {{-- Actions --}}
                            <td>
                                @if($appUser->firestore_id)
                                <i class="fas fa-cloud" title="{{ $appUser->firestore_id }}"
                                    style="color: #00c851;"></i>
                                @else
                                <i class="fas fa-cloud-slash" title="Not connected to Firestore"
                                    style="color: #ff4444;"></i>
                                @endif

                                @can('app_user_show')
                                <a class="btn btn-xs btn-primary" href="{{ route($routeName, $appUser->id) }}">
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
<!-- Your custom script -->
@endsection
@include('admin.common.addSteps.footer.footerJs')

@section('scripts')
@parent
@include('admin.appUsers.driver.index_footer')
@endsection