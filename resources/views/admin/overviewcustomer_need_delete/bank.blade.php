@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="box">
        <div class="panel-body">
            <div class="nav-tabs-custom">
                @include('admin.overviewcustomer.overviewtabs')
                <div class="clearfix"></div>
            </div>
        </div>
    </div>


    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ trans('global.bank') }} {{ trans('global.account') }} {{ trans('global.detail') }}
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable datatable-Property">
                        <thead>
                            <tr>
                                <th>{{ trans('global.id') }}</th>
                                <th>{{ trans('global.account') }} {{ trans('global.name') }}</th>
                                <th>{{ trans('global.account_number') }}</th>
                                <th>{{ trans('global.bank') }} {{ trans('global.name') }}</th>
                                <th>{{ trans('global.branch') }} {{ trans('global.name') }}</th>
                                <th>{{ trans('global.iban') }} {{ trans('global.name') }}</th>
                                <th>{{ trans('global.swift_code') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($accounts as $key => $account)
                                <tr data-entry-id="{{ $account->id }}">
                                    @can('app_user_contact_access')
                                        <td>{{ $account->id ?? '' }}</td>
                                        <td>{{ $account->account_name ?? '' }}</td>
                                        <td>{{ $account->account_number ?? '' }}</td>
                                        <td>{{ $account->bank_name ?? '' }}</td>
                                        <td>{{ $account->branch_name ?? '' }}</td>
                                        <td>{{ $account->iban ?? '' }}</td>
                                        <td>{{ $account->swift_code ?? '' }}</td>
                                    @else
                                        <td>{{ $account->id ?? '' }}</td>
                                        <td>{{ Str::mask($account->account_name ?? '', '*', 3, strlen($account->account_name) - 3) }}</td>
                                        <td>{{ Str::mask($account->account_number ?? '', '*', 4, strlen($account->account_number) - 4) }}</td>
                                        <td>{{ Str::mask($account->bank_name ?? '', '*', 3, strlen($account->bank_name) - 3) }}</td>
                                        <td>{{ Str::mask($account->branch_name ?? '', '*', 3, strlen($account->branch_name) - 3) }}</td>
                                        <td>{{ Str::mask($account->iban ?? '', '*', 4, strlen($account->iban) - 4) }}</td>
                                        <td>{{ Str::mask($account->swift_code ?? '', '*', 4, strlen($account->swift_code) - 4) }}</td>
                                    @endcan
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

