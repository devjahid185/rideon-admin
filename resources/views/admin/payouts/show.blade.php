@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('global.payout_title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.payouts.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('global.id') }}
                                    </th>
                                    <td>
                                        {{ $payout->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.vendorid') }}
                                    </th>
                                    <td>
                                        {{ $payout->vendorid }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.amount') }}
                                    </th>
                                    <td>
                                        {{ $payout->amount }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.currency') }}
                                    </th>
                                    <td>
                                        {{ $payout->currency }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.vendor_name') }}
                                    </th>
                                    <td>
                                        {{ $payout->vendor_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.payment_method') }}
                                    </th>
                                    <td>
                                        {{ $payout->payment_method }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.account_number') }}
                                    </th>
                                    <td>
                                        {{ $payout->account_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('global.payout_status') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Payout::PAYOUT_STATUS_SELECT[$payout->payout_status] ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.payouts.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection