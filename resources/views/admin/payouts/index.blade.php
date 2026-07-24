@extends('layouts.admin')
@section('content')
        <div class="content">
            @can('payout_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('admin.payouts.create') }}">
                            {{ trans('global.add') }} {{ trans('global.payout_title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            @php
            $currency = Config::get('general.general_default_currency');
        @endphp
            <div class="box">
                <div class="box-body">
                    <form class="form-horizontal" enctype="multipart/form-data" action="" method="GET" accept-charset="UTF-8"
                        id="filterForm">

                        <div class="col-md-12 d-none">
                            <input class="form-control" type="hidden" id="startDate" name="from" value="">
                            <input class="form-control" type="hidden" id="endDate" name="to" value="">
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label> {{ trans('global.date_range') }}</label>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control" id="daterange-btn">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label> {{ trans('global.status') }} </label>
                                    <select class="form-control" name="status" id="status">
                            <option value="">{{ trans('global.all') }}</option>
                           @foreach(['Success' => 'Success', 'Pending' => 'Requested', 'Rejected' => 'Rejected'] as $key => $label)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                        </select>
                                </div>
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label>{{ trans('global.vendor_name') }}</label>
                                    <select class="form-control select2" name="vendor" data-vendor-id="{{ $vendorId }}" data-vendor-name="{{ $vendorName }}" id="payoutDriver">
                                        <option value="">{{ $vendorName }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-2 col-xs-4 mt-5">
                                    <br>
                                    <button type="submit" name="btn"
                                        class="btn btn-primary btn-flat">{{ trans('global.filter') }}</button>
                                    <button type="button" id='resetBtn'
                                        class="btn btn-primary btn-flat">{{ trans('global.reset') }}</button>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
             <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h4>{{ trans('payout.total_payouts') }}</h4>
                                <span class="text-20">{{ $summary['total_payouts'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h4>{{ trans('payout.total_amount') }}</h4>
                                <span class="text-20">{{ formatCurrency($summary['total_amount']) . ' ' . $currency}}
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h4>{{ trans('payout.pending_amount') }}</h4>
                                <span
                                    class="text-20">{{ formatCurrency($summary['pending_amount']) . ' ' . $currency }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary">
                            <div class="panel-body text-center">
                                <h4>{{ trans('payout.success_amount') }}</h4>
                                <span class="text-20">{{ formatCurrency($summary['success_amount']) . ' ' . $currency }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <div style="margin-bottom: 6px;" class="row">
            <div class="col-lg-12">
                @php $statuses = ['' => 'all', 'Success' => 'Success', 'Pending' => 'Requested', 'Rejected' => 'Rejected']; @endphp
                @foreach($statuses as $value => $label)
                    <a class="btn {{ request('status') === $value || ($value === '' && !request()->has('status')) ? 'btn-primary' : 'btn-inactive' }}"
                        href="{{ route('admin.payouts.index', array_merge(request()->except('btn', 'page'), ['status' => $value ?: null])) }}">
                        {{ trans("payout." . strtolower($label)) }}
                    </a>
                @endforeach
            </div>
        </div>
          <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans('payout.payout_title_singular') }} {{ trans('payout.list') }}
                    </div>
                    <div class="panel-body">
                        <table
                            class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Payout table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ trans('payout.id') }}</th>
                                    <th>{{ trans('payout.vendor_name') }}</th>
                                    <th>{{ trans('payout.amount') }}</th>
                                    <th>{{ trans('payout.payment_method') }}</th>
                                    <th>Source</th>
                                    <th>{{ trans('payout.payout_status') }}</th>
                                    <th>{{ trans('payout.request_status') }}</th>
                                    <th>{{ trans('payout.proof') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payouts as $payout)
                                        <tr data-entry-id="{{ $payout->id }}">
                                            <td>{{ $payout->id }}</td>
                                            <td>
                                                @if($payout->vendor)
                                                    @if(($payout->vendor->user_type ?? '') === 'driver')
                                                        <a href="{{ route('admin.driver.profile', $payout->vendor->id) }}" target="_blank">
                                                            {{ $payout->vendor->first_name }} {{ $payout->vendor->last_name }}
                                                        </a>
                                                    @else
                                                        <strong>{{ $payout->vendor->first_name }} {{ $payout->vendor->last_name }}</strong>
                                                        <span class="label label-info">Restaurant Owner</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ formatCurrency($payout->amount) }} {{  $currency }} </td>
                                            <td>
                                                @if (!empty($payout->payment_method))
                                                    <span class="badge badge-info">
                                                        {{ $payout->payment_method }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        {{ trans('payout.manual_payment') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if((int) $payout->module === 3 || $payout->request_by === 'restaurant_owner')
                                                    <span class="label label-primary">Restaurant Owner</span>
                                                @else
                                                    <span class="label label-default">Driver</span>
                                                @endif
                                            </td>


                                            <td>
                                    @php
                                        $status = $payout->payout_status;
                                        $badgeClass = match ($status) {
                                            'Pending' => 'label-danger',
                                            'Rejected' => 'label-rejected',
                                            'Success' => 'label-success',
                                            default => 'label-default',
                                        };

                                        $icon = match ($status) {
                                            'Pending' => 'fa-clock',      // pending
                                            'Rejected' => 'fa-times-circle', // rejected
                                            'Success' => 'fa-check-circle',  // success
                                            default => 'fa-info-circle',
                                        };
                                    @endphp

                                                <span class="badge badge-pill {{ $badgeClass }}">
                                                    <i class="fa {{ $icon }}"></i> {{ $status }}
                                                </span>
                                            </td>


                                            <td>
                                                @if($payout->payout_status === 'Pending')
                                                    <div class="mb-1">
                                                        <a class="badge badge-pill label-success open-payout-modal animate__animated animate__pulse animate__infinite animate__slow d-inline-block w-100"
                                                            href="#" data-payout-id="{{ $payout->id }}"
                                                            data-amount="{{ $payout->amount }}"
                                                            data-vendor="{{ $payout->vendor ? trim($payout->vendor->first_name.' '.$payout->vendor->last_name) : 'Vendor' }}">
                                                            <i class="fas fa-check"></i> {{ trans('payout.approve') }}
                                                        </a>
                                                        &nbsp; &nbsp;
                                                        <a class="badge badge-pill label-rejected payout-reject animate__animated animate__pulse animate__infinite animate__slow d-inline-block w-100"
                                                            href="#" data-payout-id="{{ $payout->id }}"
                                                            data-amount="{{ $payout->amount }}"
                                                            data-vendor="{{ $payout->vendor ? trim($payout->vendor->first_name.' '.$payout->vendor->last_name) : 'Vendor' }}">
                                                            <i class="fas fa-times"></i> {{ trans('payout.reject') }}
                                                        </a>
                                                    </div>
                                                @elseif($payout->payout_status === 'Success')
                                                    <span class="badge badge-pill label-success disabled-span d-inline-block w-100">
                                                        <i class="fas fa-check-circle"></i> {{ trans('payout.success') }}
                                                    </span>
                                                @elseif($payout->payout_status === 'Rejected')
                                                    <span class="badge badge-pill label-rejected disabled-span d-inline-block w-100">
                                                        <i class="fas fa-times-circle"></i> {{ trans('payout.rejected') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-pill label-default disabled-span d-inline-block w-100">
                                                        <i class="fas fa-info-circle"></i> {{ trans('global.done') }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($payout->payout_proof)
                                                    <a href="{{ $payout->payout_proof->url }}" target="_blank">
                                                        <i class="fas fa-file-alt text-success"></i>
                                                    </a>
                                                @else
                                                    <i class="fas fa-times-circle text-danger" title="No Proof"></i>
                                                @endif
                                            </td>

                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end">
                                @if ($payouts->currentPage() > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $payouts->previousPageUrl() }}"
                                            tabindex="-1">{{ trans('payout.previous') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ trans('payout.previous') }}</span>
                                    </li>
                                @endif
                                @for ($i = 1; $i <= $payouts->lastPage(); $i++)
                                    <li class="page-item {{ $i == $payouts->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $payouts->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                @if ($payouts->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $payouts->nextPageUrl() }}">{{ trans('payout.next') }}</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">{{ trans('payout.next') }}</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        </div><!-- Payout Modal -->
        <div class="modal fade" id="payoutModal" tabindex="-1" role="dialog" aria-labelledby="payoutModalLabel">
            <div class="modal-dialog" role="document">
                <form id="payoutForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payout_id" id="modalPayoutId">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="payoutModalLabel">Release Funds</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label><strong>Vendor:</strong></label>
                                <p class="form-control-static" id="modalVendor"></p>
                            </div>

                            <div class="form-group">
                                <label><strong>Amount:</strong></label>
                                <p class="form-control-static" id="modalAmount"></p>
                            </div>

                            <div class="form-group">
                                <label for="payoutProof">Upload Payout Proof <span class="text-danger">*</span></label>
                                <input type="file" name="payout_proof" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="payoutNote">Notes</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit & Release</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
@section('scripts')
    @parent
  <script>
      var payoutVendorSearchUrl = "{{ route('admin.payoutVendorSearch') }}";
       var payoutUpdateStatus = "{{ route("admin.payouts.updateStatus", ":payoutId") }}";
       var payoutRejectUrl = "{{ route('admin.payout.reject') }}";
      
    </script>
@endsection
