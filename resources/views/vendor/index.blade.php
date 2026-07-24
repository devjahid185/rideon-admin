@extends('vendor.layout')
@section('content')
<div class="content">


    <!--/*seaction 1 start here*/-->
    {{--<div class="box">
        <div class="panel-body">
            <div class="nav-tabs-custom">
             @include('vendor.dashboardTabs')
                <div class="clearfix"></div>
            </div>
        </div>
    </div>--}}
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

        .dataTables_length {
            display: none;
        }

        tr.credit {
            background-color: #4CAF50 !important;
            color: white !important;
        }


        tr.warning td {
            background-color: #f15f5f !important;
            color: white !important;
        }
    </style>
    <!--/*seaction 1  end*/-->

    <!--/*seaction 4 start here*/-->

    <div class="col-lg-12">
            <div class="">
                @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h4>{{$hostspendmoney}}</h4>

                                <p> Wallet Balance</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="{{ route("vendor.payouts") }}" class="small-box-footer">{{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i></a>
                        </div>


                    </div>

                    <div class="col-md-3">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h4>{{$hostrecivemoney}}</h4>

                                <p> Total withdrawn amount</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="{{ route("vendor.payouts", ['status' => 'success']) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>


                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h4>{{$hostpendingmoney}}</h4>

                                <p> Pending Withdrawal</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="{{ route("vendor.payouts", ['status' => 'pending']) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>


                    </div>

                    <div class="col-md-3">

                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h4>{{$incoming_amount}}</h4>

                                <p>Incoming Amount </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-cash"></i>
                            </div>
                            <a href="{{ route("vendor.orders.index", ['status' => 'pending']) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a> </div>


                    </div>
                    <div class="col-md-3">

                        <div class="small-box bg-maroon">
                            <div class="inner">
                                <h4>{{$totalSales}}</h4>

                                <p>Total Sales</p>
                            </div>
                            <div class="icon">
                                <i class="ion-android-cart"></i>
                            </div>
                            <a href="{{ route("vendor.orders.index", ['status' => 'completed']) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a></div>


                    </div>
                    <div class="col-md-3">

                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h4>{{$todayOrders}}</h4>

                                <p>Today Order</p>
                            </div>
                            <div class="icon">
                                <i class="ion-android-cart"></i>
                            </div>
                            @php
                            $currentDate = date('Y-m-d');
                            @endphp

                            <a href="{{ route("vendor.orders.index", ['from' => $currentDate, 'to' => $currentDate]) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>

                        </div>


                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-maroon text-white">
                            <div class="inner">
                                <h4>{{$pendingOrders}}</h4>


                                <p>Pending Order </p>
                            </div>
                            <div class="icon">
                                <i class="ion-android-cart"></i>
                            </div>
                            <a href="{{ route("vendor.orders.index", ['status' => 'pending']) }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a> 
                        </div>


                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h4>{{$allProducts}}</h4>

                                <p>Total Product</p>
                            </div>
                            <div class="icon">
                                <i class="ion-model-s"></i>
                            </div>
                            @php
                            $currentDate = date('Y-m-d');
                            @endphp

                            <a href="{{ route("vendor.vehicles.index") }}" class="small-box-footer">
                                {{ trans('global.moreInfo') }} <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
   {{-- <div class="row g-3 coenr-capitalize">
    <!-- First Tile (Wallet Balance) -->
    <div class="col-md-3">
        <div class="caredoane cardbg-6">
            <h4 class="title">{{$hostspendmoney}}</h4>
            <div class="subtitle">Wallet Balance</div>
            <img class="sweicon" src="/public/images/icon/cash-withdrawal.png" alt="transaction">
        </div>
    </div>

    <!-- Second Tile (Total Withdrawal Amount) -->
    <div class="col-md-3">
        <div class="caredoane cardbg-3">
            <h4 class="title">{{$hostrecivemoney}}</h4>
            <div class="subtitle">Total withdrawal amount</div>
            <img class="sweicon" src="/public/images/icon/atm.png" alt="transaction">
        </div>
    </div>

    <!-- Third Tile (Pending Withdraw) -->
    <div class="col-md-3">
        <div class="caredoane cardbg-6">
            <h4 class="title">{{$hostpendingmoney}}</h4>
            <div class="subtitle">Pending Withdraw</div>
            <img class="sweicon" src="/public/images/icon/cash-withdrawal.png" alt="transaction">
        </div>
    </div>

    <!-- Fourth Tile (Total Earnings) -->
    <div class="col-md-3">
        <div class="caredoane cardbg-3">
            <h4 class="title">{{$incoming_amount}}</h4>
            <div class="subtitle">Incoming Amount</div>
            <img class="sweicon" src="/public/images/icon/atm.png" alt="transaction">
        </div>
    </div>

    <!-- Fifth Tile (Incoming Amount) -->
    <div class="col-md-3 mt-3">
        <div class="caredoane cardbg-3">
            <h4 class="title">{{$totalSales}}</h4>
            <div class="subtitle">Total Sales</div>
            <img class="sweicon" src="/public/images/icon/earning.png" alt="transaction">
        </div>
    </div>

    <!-- Sixth Tile (All Items) -->
    <div class="col-md-3 mt-3">
        <div class="caredoane cardbg-6">
            <h4 class="title">{{$todayOrders}}</h4>
            <div class="subtitle">Today Order</div>
            <i class="fa-fw fas fa-shopping-cart fa-2x sweicon" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Seventh Tile (Pending Booking) -->
    <div class="col-md-3 mt-3">
        <div class="caredoane cardbg-3">
            <h4 class="title">{{$pendingOrders}}</h4>
            <div class="subtitle">Pending Order</div>
            <i class="fa-fw fas fa-shopping-cart fa-2x sweicon" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Eighth Tile (Total Bookings) -->
    <div class="col-md-3 mt-3">
        <div class="caredoane cardbg-6">
            <h4 class="title">{{$allProducts}}</h4>
            <div class="subtitle">Total Product</div>
            <i class="fa-fw fas fa-cubes fa-2x sweicon" aria-hidden="true"></i>
        </div>
    </div>
</div>--}}



    <!--/*seaction 4  here*/-->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.vendorWallets') }}
                </div>
                <div class="panel-body">
                    <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Payout">
                        <thead>
                            <tr>

                                <th>

                                    {{ trans('global.id') }}
                                </th>

                                <th>
                                    {{ trans('global.bookingid') }}
                                </th>
                                <th>
                                    {{ trans('global.amount') }}
                                </th>
                                <th>
                                    {{ trans('global.wallet_type') }}
                                </th>
                                <th>
                                    {{ trans('global.date') }}
                                </th>
                                <th>
                                    {{ trans('global.description') }}


                                </th>
                                <th>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendor_wallets as $key => $vendor_wallet)

                            <tr data-entry-id="{{ $vendor_wallet->id }}" class="{{ $vendor_wallet->type == 'credit' ? 'credit' : 'warning' }}">

                                <td>
                                    {{ $vendor_wallet->id ?? '' }}
                                </td>

                                <td>
                                    {{ $vendor_wallet->booking_id ?? '' }}
                                </td>

                                <td>
                                    {{ ($general_default_currency->meta_value ?? '') . ' ' . ($vendor_wallet->amount ?? '') }}

                                </td>
                                <td>{{ $vendor_wallet->type ?? '' }}</td>
                                
                                <td>
                                    {{ $vendor_wallet->created_at ?? '' }}

                                </td>
                                <td>
                                    Payout ID #{{ $vendor_wallet->payout_id}}, {{ $vendor_wallet->description ?? '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <nav aria-label="...">
                        <ul class="pagination justify-content-end">
                            {{-- Previous Page Link --}}
                            @if ($vendor_wallets->currentPage() > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $vendor_wallets->previousPageUrl() }}" tabindex="-1">{{ trans('global.previous') }}</a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link">{{ trans('global.previous') }}</span>
                            </li>
                            @endif

                            {{-- Numeric Pagination Links --}}
                            @for ($i = 1; $i <= $vendor_wallets->lastPage(); $i++)
                                <li class="page-item {{ $i == $vendor_wallets->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $vendor_wallets->url($i) }}">{{ $i }}</a>
                                </li>
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($vendor_wallets->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vendor_wallets->nextPageUrl() }}">{{ trans('global.next') }}</a>
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

@endsection