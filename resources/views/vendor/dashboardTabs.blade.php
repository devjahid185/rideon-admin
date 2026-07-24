<ul class="cus nav nav-tabs f-14" role="tablist">
        <li class="{{ request()->routeIs('admin.overview') ? 'active' : '' }}">
        <a href="{{ route('admin.overview', $vendorId) }}">{{ trans('global.adminOverView') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.item') ? 'active' : '' }}">
            <a href="{{ route('admin.item', $vendorId) }}">{{ trans('global.items') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <a href="{{ route('admin.orders', $vendorId) }}">{{ trans('global.Orders') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.booking') ? 'active' : '' }}">
            <a href="{{ route('admin.booking', $vendorId) }}">{{ trans('global.Bookings') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.payout') ? 'active' : '' }}">
            <a href="{{ route('admin.payout', $vendorId) }}">{{ trans('global.Payouts') }}</a>
        </li>
        <li class="{{ request()->routeIs('admin.wallet') ? 'active' : '' }}">
            <a href="{{ route('admin.wallet', $vendorId) }}">{{ trans('global.wallet') }}</a>
        </li>
        </ul>