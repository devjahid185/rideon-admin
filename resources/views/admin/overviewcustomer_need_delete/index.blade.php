@extends('layouts.admin')
@section('content')
<div class="content">


<!--/*seaction 1 start here*/-->
  <div class="box">
    <div class="panel-body">
      <div class="nav-tabs-custom">
      @include('admin.overviewcustomer.overviewtabs')
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  <style>
        .dataTables_info{
            display: none;
        }
        .paging_simple_numbers{
            display: none;
        }
        .pagination.justify-content-end{
            float: right;
        }
        
.dataTables_length{
    display:none;
}
tr.credit {
    background-color: green !important;
    color: white !important; 
}


tr.warning td{
    background-color: orangered !important;
    color: black !important; 
}

    </style>
    <!--/*seaction 1  end*/-->
  
      <!--/*seaction 4 start here*/-->
  
  <div class="row g-3 coenr-capitalize">
<div class="col-md-4">
<div class="cardbg-1">
<div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
<h5 class="">

{{ trans('global.walletBalance') }}
</h5>
<div class="d-flex align-items-center justify-content-center mt-3">
<div class="">
<img class="sweicon" src="{{ asset('public/images/icon/cash-new.png') }}" alt="transaction">
</div>
<h2 class="cash--title text-white">{{$hostspendmoney}}</h2>
</div>
</div>
<div class="">
<button class="btn" id="collect_cash" type="button">{{ trans('global.payout_title') }}
</button>
</div>
</div>
</div>
<div class="col-md-8">
<div class="row g-3">
<div class="col-sm-6">
<div class="caredoane cardbg-2">
<h4 class="title">{{ $hostpendingmoney}}</h4>
<div class="subtitle">{{ trans('global.pendingWithdraw') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/cash-withdrawal.png') }}" alt="transaction">
</div>
</div>
<div class="col-sm-6">
<div class="caredoane  cardbg-3">
<h4 class="title">{{ $hostrecivemoney}}</h4>
<div class="subtitle">{{ trans('global.Total_withdrawal_amount') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/atm.png') }}" alt="transaction">
</div>
</div>
<div class="col-sm-6 mt-3 mt-3">
<div class="caredoane cardbg-4">
<h4 class="title">{{$totalmoney}}</h4>
<div class="subtitle">{{ trans('global.totalEarning') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/atm.png') }}" alt="transaction">
</div>
</div>
<div class="col-sm-6 mt-3">
<div class="caredoane cardbg-6 bg-danger" >
<h4 class="title">  {{ $refunded }} </h4>
<div class="subtitle">{{ trans('global.totalRefund') }}</div>
<img class="sweicon" src="{{ asset('public/images/icon/earning.png') }}" alt="transaction">
</div>
</div>
</div>
</div>
</div>

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
                               {{ trans('global.vendor_name') }}
                                   
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
                                            {{ $vendor_wallet->appUser->first_name ?? '' }} {{ $vendor_wallet->appUser->last_name}}
                                       
                                        </td>
                                        <td>
                                            {{ $vendor_wallet->booking_id ?? '' }}
                                        </td>
                                        
                                        <td>
                                        {{ ($general_default_currency->meta_value ?? '') . ' ' . ($vendor_wallet->amount ?? '') }}
                                            
                                        </td>
                                        <td>{{ $vendor_wallet->type ?? '' }}</td>
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