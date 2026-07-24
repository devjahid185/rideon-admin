<aside class="main-sidebar">
    <section class="sidebar" style="height: auto;">
        <ul class="sidebar-menu tree" data-widget="tree">
            <li class="{{ request()->is('admin') ? 'active' : '' }}">
                <a href="{{ route('admin.home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span> {{ trans('menu.dashboard') }} </span>
                </a>
            </li>

            @can('user_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa-fw fas fa-users"></i>
                        <span> {{ trans('menu.adminManagement') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        @can('permission_access')
                            <li
                                class="{{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.permissions.index') }}">
                                    <i class="fa-fw fas fa-unlock-alt"></i>
                                    <span>{{ trans('menu.permission_title') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('role_access')
                            <li class="{{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.roles.index') }}">
                                    <i class="fa-fw fas fa-briefcase"></i>
                                    <span>{{ trans('menu.role_title') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('user_access')
                            <li class="{{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index') }}">
                                    <i class="fa-fw fas fa-user"></i>
                                    <span>{{ trans('menu.user_title') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('vehicle_setting_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa-fw fas fa-car"></i>
                        <span>{{ trans('menu.platform_setup') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">

                        @can('vehicle_type_access')
                            <li
                                class="{{ request()->is('admin/vehicle-type') || request()->is('admin/vehicle-type/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.vehicle-type.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.vehicle_type') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('vehicle_location_access')
                            <li
                                class="{{ request()->is('admin/vehicle-location') || request()->is('admin/vehicle-location/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.vehicle-location.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.vehicle_location') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('vehicle_makes_access')
                            <li
                                class="{{ request()->is('admin/vehicle-makes') || request()->is('admin/vehicle-makes/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.vehicle-makes.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.vehicle_makes') }}</span>
                                </a>
                            </li>
                        @endcan

                        <li
                            class="{{ request()->is('admin/payout-method') || request()->is('admin/payout-method/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.payout-method.index') }}">
                                <i class="fas fa-dot-circle">

                                </i>
                                <span>{{ trans('menu.payout_method') }}</span>

                            </a>
                        </li>

                        @can('cancellation_access')
                            <li
                                class="{{ request()->is('admin/cancellation') || request()->is('admin/cancellation /*') ? 'active' : '' }}">
                                <a href="{{ route('admin.cancellation.index') }}">
                                    <i class='fas fa-dot-circle'></i>
                                    <span>{{ trans('menu.cancellationReason_title') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('item_rule')
                            <li
                                class="{{ request()->is('admin/item-rule') || request()->is('admin/item-rule/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.item-rule.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.item_rule') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

            @endcan
            @can('front_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa-fw fas fa-users"></i>
                        <span>{{ trans('menu.driverManagement') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        @can('app_user_access')
                            <li
                                class="{{ (request()->is('admin/drivers') || request()->is('admin/driver/*') || request()->is('admin/drivers/*')) && !request()->has('status') && !request()->has('host_status') ? 'active' : '' }}">
                                <a href="{{ route('admin.drivers.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.driver') }} {{ trans('menu.list') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ (request()->is('admin/drivers') || request()->is('admin/drivers/*')) && request()->input('status') === '1' ? 'active' : '' }}">
                                <a href="{{ route('admin.drivers.index', ['status' => '1']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.active') }} {{ trans('menu.drivers') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ (request()->is('admin/drivers') || request()->is('admin/drivers/*')) && request()->input('status') === '0' ? 'active' : '' }}">
                                <a href="{{ route('admin.drivers.index', ['status' => '0']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.inactive') }} {{ trans('menu.drivers') }}</span>
                                </a>
                            </li>

                            <li
                                class="{{ (request()->is('admin/drivers') || request()->is('admin/drivers/*')) && request()->input('host_status') === '2' ? 'active' : '' }}">
                                <a href="{{ route('admin.drivers.index', ['host_status' => '2']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.requested') }} {{ trans('menu.drivers') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('front_management_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa-fw fas fa-users"></i>
                        <span>{{ trans('menu.riderManagement') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        @can('app_user_access')
                            <li
                                class="{{ request()->is('admin/app-users') || request()->is('admin/app-users/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.app-users.index', ['user_type' => 'user']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.riders') }} {{ trans('menu.list') }}</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->is('admin/app-users') && request()->input('status') == '1' ? 'active' : '' }}">
                                <a href="{{ route('admin.app-users.index', ['user_type' => 'user', 'status' => '1']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.active') }} {{ trans('menu.riders') }}</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->is('admin/app-users') && request()->input('status') == '0' ? 'active' : '' }}">
                                <a href="{{ route('admin.app-users.index', ['user_type' => 'user', 'status' => '0']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.inactive') }} {{ trans('menu.riders') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('booking_access')
                <li class="treeview {{ request()->is('admin/bookings*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ trans('menu.manage_rides') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('admin/bookings') && !request()->query('status') ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index') }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_list') }}</span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/bookings') && request()->query('status') === 'accepted' ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index', ['status' => 'accepted']) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_accepted') }} </span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/bookings') && request()->query('status') === 'ongoing' ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index', ['status' => 'ongoing']) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_running') }}</span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/bookings') && request()->query('status') === 'cancelled' ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_cancelled') }}</span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/bookings') && request()->query('status') === 'rejected' ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index', ['status' => 'rejected']) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_rejected') }}</span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->is('admin/bookings') && request()->query('status') === 'completed' ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_completed') }}</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('admin.bookings.trash') ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings.trash') }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.booking_trash') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- @can('package_access')
                                    <li class="treeview">
                                        <a href="#">
                                            <i class="fa-fw fas fa-gift"></i>
                                            <span>{{ trans('menu.package_title') }}</span>
                                            <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('all_package_access')
        <li class="{{ request()->is('admin/all-packages/create') ? 'active' : '' }}">
                                                                        <a href="{{ route('admin.all-packages.create') }}">
                                                                            <i class="fas fa-dot-circle"></i>
                                                                            <span>{{ trans('menu.add') }} {{ trans('menu.allPackage_title_singular') }}</span>
                                                                        </a>
                                                                    </li>
                                                                    <li class="{{ request()->is('admin/all-packages') ? 'active' : '' }}">
                                                                        <a href="{{ route('admin.all-packages.index') }}">
                                                                            <i class="fas fa-dot-circle"></i>
                                                                            <span>{{ trans('menu.allPackage_title') }} {{ trans('menu.list') }}</span>
                                                                        </a>
                                                                    </li>
    @endcan
                                        </ul>
                                    </li>
            @endcan



            @can('contact_access')
    <li class="{{ request()->is('admin/contacts') || request()->is('admin/contacts/*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.contacts.index') }}">
                                            <i class="fa-fw far fa-calendar-check"></i>
                                            <span>{{ trans('menu.contactus_title') }}</span>
                                        </a>
                                    </li>
@endcan
 -->

            @can('coupon_access')
                <li class="treeview">
                    <a href="#">
                        <i class="fa-fw fas fa-ticket-alt"></i>
                        <span>{{ trans('menu.coupon_title') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        @can('add_coupon_access')
                            <li class="{{ request()->is('admin/add-coupons/create') ? 'active' : '' }}">
                                <a href="{{ route('admin.add-coupons.create') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.add') }} {{ trans('menu.addCoupon_title') }}</span>
                                </a>
                            </li>
                            <li class="{{ request()->is('admin/add-coupons') ? 'active' : '' }}">
                                <a href="{{ route('admin.add-coupons.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.addCoupon_title') }} {{ trans('menu.list') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('transactions_reports_access')
                <li
                    class="treeview {{ request()->is('admin/payouts*') || request()->is('admin/finance*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa-fw fas fa-users"></i>
                        <span>{{ trans('menu.transactions_reports') }}</span>
                        <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        @can('finance_access')
                            <li class="{{ request()->is('admin/finance') ? 'active' : '' }}">
                                <a href="{{ route('admin.finance') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.finance') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('payout_access')
                            <li
                                class="{{ request()->is('admin/payouts') || request()->is('admin/payouts/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.payouts.index') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.all_withdrawals') }}</span>
                                </a>
                            </li>
                            <li class="{{ request('status') === 'Success' ? 'active' : '' }}">
                                <a href="{{ route('admin.payouts.index', ['status' => 'Success']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.approved_withdrawals') }}</span>
                                </a>
                            </li>

                            <li class="{{ request('status') === 'Pending' ? 'active' : '' }}">
                                <a href="{{ route('admin.payouts.index', ['status' => 'Pending']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.pending_withdrawals') }}</span>
                                </a>
                            </li>

                            <li class="{{ request('status') === 'Rejected' ? 'active' : '' }}">
                                <a href="{{ route('admin.payouts.index', ['status' => 'Rejected']) }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.rejected_withdrawals') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('review_access')
                <li class="{{ request()->is('admin/reviews') || request()->is('admin/reviews/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.index') }}">
                        <i class="fa-fw fas fa-eye-dropper"></i>
                        <span>{{ trans('menu.review_title') }}</span>
                    </a>
                </li>
            @endcan

            <!-- Settings -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-list-alt"></i>
                    <span>{{ trans('menu.settings') }}</span>
                    <span class="pull-right-container"><i class="fa fa-fw fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    @can('all_general_setting_access')
                        @can('general_setting_access')
                            @php
                                $settingsRoutes = [
                                    'admin.settings',
                                    'admin.sms',
                                    'admin.email',
                                    'admin.pushnotification',
                                    'admin.fees',
                                    'admin.api-informations',
                                    'admin.paypal',
                                    'admin.social-logins',
                                    'admin.twillio',
                                    'admin.stripe',
                                    'admin.payment-methods',
                                ];
                                $isActive =
                                    in_array(Route::currentRouteName(), $settingsRoutes) ||
                                    request()->is('admin/general-settings/*');
                            @endphp
                            <li class="{{ $isActive ? 'active' : '' }}">
                                <a href="{{ route('admin.settings') }}">
                                    <i class="fas fa-dot-circle"></i>
                                    <span>{{ trans('menu.generalSetting_title') }}</span>
                                </a>
                            </li>
                        @endcan
                    @endcan
                    @can('sliders_access')
                        <li class="{{ request()->is('admin/sliders') ? 'active' : '' }}" style="display: none;">
                            <a href="{{ route('admin.sliders.index') }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.slider_title') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('static_page_access')
                        <li
                            class="{{ request()->is('admin/static-pages') || request()->is('admin/static-pages/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.static-pages.index') }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.staticPage_title') }}</span>
                            </a>
                        </li>
                    @endcan

                    <!-- @can('currency_access')
    <li
                                                class="{{ request()->is('admin/currency') || request()->is('admin/currency/*') ? 'active' : '' }}">
                                                <a href="{{ route('admin.currency') }}">
                                                    <i class="fas fa-dot-circle"></i>
                                                    <span>Currency</span>
                                                </a>
                                            </li>
@endcan -->

                    @can('email_access')
                        <li
                            class="{{ request()->is('user/email-templates') || request()->is('user/email-templates/*') ? 'active' : '' }}">

                            <a href="{{ route('user.email-templates', ['id' => 1]) }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.emailTemplate_title') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('sos_access')
                        <li class="{{ request()->is('admin/sos') || request()->is('admin/sos/*') ? 'active' : '' }}">

                            <a href="{{ route('admin.sos.index') }}">
                                <i class="fas fa-dot-circle"></i>
                                <span>{{ trans('menu.sos') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

            @can('support_ticket')
                <li class="{{ request()->is('admin/ticket') || request()->is('admin/ticket /*') ? 'active' : '' }}">
                    <a href="{{ route('admin.ticket.index', ['status' => 1]) }}" style="display: none;">
                        <i class="fa fa-ticket" aria-hidden="true"></i>
                        <span>{{ trans('menu.tickets_title') }}</span>
                    </a>
                </li>
            @endcan

            @can('reports_access')
                <li style="display:none"
                    class="{{ request()->is('admin/report-page') || request()->is('admin/report-page/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.report-page.index') }}">
                        <i class="fa fa-file" aria-hidden="true"></i>
                        <span>Reports</span>
                    </a>
                </li>
            @endcan

            @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                @can('profile_password_edit')
                    <li
                        class="{{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}">
                        <a href="{{ route('profile.password.edit') }}">
                            <i class="fa-fw fas fa-key"></i>
                            <span>{{ trans('menu.change_password') }}</span>
                        </a>
                    </li>
                @endcan
            @endif

            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>{{ trans('menu.logout') }}</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
