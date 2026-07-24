<ul class="cus nav nav-tabs f-14" role="tablist">
    @php
        $hostStatus = request()->input('host_status');
    @endphp


    @if($hostStatus != '0') 
    
    <li class="{{ request()->routeIs('admin.overview') ? 'active' : '' }}">
        <a href="{{ route('admin.overview', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.adminOverView') }}</a>
    </li>
        <li class="{{ request()->routeIs('admin.item') ? 'active' : '' }}">
            <a href="{{ route('admin.item', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.items') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <a href="{{ route('admin.orders', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.Orders') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.payout') ? 'active' : '' }}">
            <a href="{{ route('admin.payout', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.Payouts') }}</a>
        </li>
    @endif

    @if($hostStatus == '0')
    <li class="{{ request()->routeIs('admin.wallet') ? 'active' : '' }}">
            <a href="{{ route('admin.wallet', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.wallet') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.booking') ? 'active' : '' }}">
            <a href="{{ route('admin.booking', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.Bookings') }}</a>
        </li>
       
       
    @endif
    <li class="{{ request()->routeIs('admin.bankAccount') ? 'active' : '' }}">
            <a href="{{ route('admin.bankAccount', $booking) }}?host_status={{ $hostStatus }}">{{ trans('global.bank') }} {{ trans('global.account') }}</a>
        </li>
</ul>
