@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}">
@endsection

@section('content')
    <div class="content container-fluid">
        @include('admin.appUsers.driver.menu')

        <!-- Wallet Section -->
        <div class="driver-profile-page">
            <div class="profile-container">
                <div class="row g-3 coenr-capitalize">
                    <div class="col-md-4">
                        <div class="cardbg-1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                <h5>{{ trans('global.walletBalance') }}</h5>
                                <div class="d-flex align-items-center justify-content-center mt-3">
                                    <img class="sweicon" src="{{ asset('images/icon/cash-new.png') }}" alt="transaction">
                                    <h2 class="cash--title text-white">{{ $hostspendmoney }}</h2>
                                </div>
                            </div>
                            <div>
                                <button class="btn" id="collect_cash"
                                    type="button">{{ trans('global.payout_title') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row g-3">
                            @php
                                $currency = $general_default_currency->meta_value ?? '';
                            @endphp

                            <div class="col-sm-6">
                                <div class="caredoane cardbg-2">
                                    <h4 class="title">{{ $hostpendingmoney }}</h4>
                                    <div class="subtitle">{{ trans('global.pendingWithdraw') }}</div>
                                    <img class="sweicon" src="{{ asset('images/icon/cash-withdrawal.png') }}"
                                        alt="transaction">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="caredoane cardbg-3">
                                    <h4 class="title">{{ $hostrecivemoney }}</h4>
                                    <div class="subtitle">{{ trans('global.Total_withdrawal_amount') }}</div>
                                    <img class="sweicon" src="{{ asset('images/icon/atm.png') }}" alt="transaction">
                                </div>
                            </div>

                            <div class="col-sm-6 mt-3">
                                <div class="caredoane cardbg-4">
                                    <h4 class="title">{{ $hostrecivemoney }}</h4>
                                    <div class="subtitle">{{ trans('global.totalEarning') }}</div>
                                    <img class="sweicon" src="{{ asset('images/icon/atm.png') }}" alt="transaction">
                                </div>
                            </div>

                            <div class="col-sm-6 mt-3">
                                <div class="caredoane cardbg-6 bg-danger">
                                    <h4 class="title">{{ number_format($refunded) }}</h4>
                                    <div class="subtitle">{{ trans('global.totalRefund') }}</div>
                                    <img class="sweicon" src="{{ asset('images/icon/earning.png') }}" alt="transaction">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Wallet Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default shadow-sm">
                    <div class="panel-heading bg-primary p-3 text-white">
                        <h4 class="panel-title m-0">{{ trans('user.driver_wallet_transaction') }}</h4>
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table
                                class="table-bordered table-striped table-hover datatable ajaxTable datatable-Payout mb-0 table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('global.bookingid') }}</th>
                                        <th class="text-right">{{ trans('global.credit') }}</th>
                                        <th class="text-right">{{ trans('global.debit') }}</th>
                                        <th>{{ trans('global.wallet_type') }}</th>
                                        <th>{{ trans('global.description') }}</th>
                                        <th class="text-center">{{ trans('global.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vendor_wallets as $vendor_wallet)
                                        <tr
                                            class="{{ $vendor_wallet->type === 'credit' ? 'table-success' : 'table-warning' }}">
                                            <td>{{ $vendor_wallet->id }}</td>
                                            <td>
                                                @if ($vendor_wallet->type === 'credit')
                                                    @if ($vendor_wallet->type === 'credit' && $vendor_wallet->booking_id)
                                                        <a target="_blank" class="badge badge-pill badge-primary live-badge"
                                                            href="{{ route('admin.bookings.show', $vendor_wallet->booking_id) }}">
                                                            <i class="fas fa-database table-icon"></i>
                                                            {{ $vendor_wallet->booking->token ?? '-' }}
                                                        </a>
                                                    @else
                                                        <span class="badge badge-pill badge-warning live-badge">
                                                            <i class="fas fa-money-bill-wave table-icon"></i> Payout
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-pill badge-warning live-badge">
                                                        <i class="fas fa-money-bill-wave table-icon"></i> Payout
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Credit column --}}
                                            <td class="text-right">
                                                @if ($vendor_wallet->type === 'credit')
                                                    <strong>{{ $currency . ' ' . number_format($vendor_wallet->amount, 2) }}</strong>
                                                @endif
                                            </td>

                                            {{-- Debit column --}}
                                            <td class="text-right">
                                                @if ($vendor_wallet->type !== 'credit')
                                                    <strong>{{ $currency . ' ' . number_format($vendor_wallet->amount, 2) }}</strong>
                                                @endif
                                            </td>

                                            <td class="text-capitalize">{{ $vendor_wallet->type ?? '-' }}</td>
                                            <td>
                                                @if ($vendor_wallet->payout_id > 0)
                                                    <strong>Payout ID #{{ $vendor_wallet->payout_id }}</strong><br>
                                                @endif
                                                <small class="text-muted">{{ $vendor_wallet->description ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-muted">
                                                    {{ $vendor_wallet->created_at->format('d M Y - h:i A') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-muted text-center">
                                                {{ trans('global.no_data_available') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end mt-3">
                            <ul class="pagination">
                                {{-- Previous --}}
                                <li class="page-item {{ $vendor_wallets->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vendor_wallets->previousPageUrl() }}">
                                        {{ trans('global.previous') }}
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @for ($i = 1; $i <= $vendor_wallets->lastPage(); $i++)
                                    <li class="page-item {{ $vendor_wallets->currentPage() == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $vendor_wallets->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                {{-- Next --}}
                                <li class="page-item {{ !$vendor_wallets->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vendor_wallets->nextPageUrl() }}">
                                        {{ trans('global.next') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
