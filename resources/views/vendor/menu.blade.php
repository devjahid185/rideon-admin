<aside class="main-sidebar">
    <section class="sidebar" style="height: auto;">
        <ul class="sidebar-menu tree" data-widget="tree">
            <li>
                <a href="{{ route("vendor.dashboard") }}">
                    <i class="fas fa-fw fa-tachometer-alt">

                    </i>
                    {{ trans('global.dashboard') }}
                </a>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fas fa-user-circle">

                    </i>
                    <span>{{ trans('global.profile') }} {{ trans('global.setting') }}</span>
                    <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is("vendor/profile/") || request()->is("vendor/profile") ? "active" : "" }}">
                                <a href="{{ route("vendor.profile") }}">
                                    <i class="fas fa-user-circle">

                                    </i>
                                    <span>{{ trans('global.profile') }}</span>

                                </a>
                    </li>

                    

                </ul>
            </li>
            <!--  -->
            <li class="treeview">
                <a href="#">
                    <i class="fa-fw fas fa-car">

                    </i>
                    <span>{{ trans('global.vehicle_setting') }}</span>
                    <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">

                    <li class="{{ request()->is("vendor/vehicles/create") || request()->is("vendor/vehicles/create") ? "active" : "" }}">
                                <a href="{{ route("vendor.vehicles.create") }}">
                                    <i class="fa-fw fas fa-plus">

                                    </i>
                                    <span>{{trans('global.add')}} {{trans('global.vehicle') }}</span>

                                </a>
                            </li>


                    <li class="{{ request()->is("vendor/vehicles") || request()->is("vendor/vehicles/*") ? "active" : "" }}">
                                <a href="{{ route("vendor.vehicles.index") }}">
                                    <i class="fa fa-list-ul">

                                    </i>
                                    <span>{{ trans('global.vehicle') }} {{ trans('global.list')}}</span>

                                </a>
                            </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa-fw fas fa-list-alt">

                    </i>
                    <span>{{ trans('global.orders') }} {{ trans('global.setting') }}</span>
                    <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">

                <li class="{{ request()->is("vendor/orders") || request()->is("vendor/orders/*") ? "active" : "" }}">
                    <a href="{{ route("vendor.orders.index") }}">
                        <i class="fa-fw far fa-calendar-check">

                        </i>
                        <span>{{ trans('global.orders') }}</span>

                    </a>
                </li>


                            <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'pending' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'pending']) }}">
                            <i class="far fa-clock"></i>
                            <span>{{ trans('global.pending') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'confirmed' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'confirmed']) }}">
                            <i class="far fa-check-circle"></i>
                            <span>{{ trans('global.confirmed') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'cancelled' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'cancelled']) }}">
                            <i class="far fa-times-circle"></i>
                            <span>{{ trans('global.cancelled') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'declined' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'declined']) }}">
                            <i class="far fa-thumbs-down"></i>
                            <span>{{ trans('global.declined') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'completed' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'completed']) }}">
                            <i class="far fa-check-square"></i>
                            <span>{{ trans('global.completed') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('vendor/orders') && request()->query('status') === 'refunded' ? 'active' : '' }}">
                        <a href="{{ route('vendor.orders.index', ['status' => 'refunded']) }}">
                            <i class="far fa-money-bill-alt"></i>
                            <span>{{ trans('global.refunded') }} {{ trans('global.orders')}}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li
                class="treeview {{ request()->is("admin/payouts*") || request()->is("admin/finance*") ? "active" : "" }}">
                <a href="#">
                    <i class="fa fa-table"></i>
                    <span>{{ trans('global.transactions_reports') }}</span>
                    <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                 
                <li class="{{ request()->is("vendor/bankaccount/") || request()->is("vendor/bankaccount") ? "active" : "" }}">
                                <a href="{{ route("vendor.bankaccount") }}">
                                    <i class="fa fa-bank">

                                    </i>
                                    <span>{{ trans('global.bank') }} {{ trans('global.account') }}</span>

                                </a>
                    </li>
                    
                    <li class="{{ request()->is("vendor/finance") ? "active" : "" }}">
                        <a href="{{ route("vendor.finance") }}">
                            <i class="fas fa-coins"></i>
                            <span>{{ trans('global.finance') }}</span>
                        </a>
                    </li>

                  <li
                        class="{{ request()->is("vendor/payouts") || request()->is("vendor/payouts/*") ? "active" : "" }}">
                        <a href="{{ route("vendor.payouts") }}">
                            <i class="fa-fw fab fa-paypal"></i>
                            <span>{{ trans('global.payout_title') }}</span>
                        </a>
                    </li>

                    
                    

                </ul>
            </li>
            <li class="{{ request()->is("vendor/chat") ? "active" : "" }}">
                <a href="{{ route("vendor.chatPage") }}">
                <i class="fas fa-comment"></i>
                    {{ trans('global.chat') }}
                </a>
            </li>
            <li>
                <a href="{{ route("vendor.logout") }}">
                    <i class="fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>
    </section>
</aside>