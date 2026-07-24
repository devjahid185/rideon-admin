<ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
    <li class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <a href="{{ route('admin.settings') }}" data-group="profile">{{ trans('global.general_title') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.project_setup') ? 'active' : '' }}">
        <a href="{{ route('admin.project_setup') }}" data-group="profile">{{ trans('global.project_setup') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.preferences') ? 'active' : '' }}" style=" display: none ">
        <a href="{{ route('admin.preferences') }}" data-group="profile">{{ trans('global.preferences_title') }}</a>
    </li>
    <li
        class="{{ request()->routeIs('admin.smssetting') || request()->routeIs('admin.msg91') || request()->routeIs('admin.twilliosetting') || request()->routeIs('admin.nexmosetting') || request()->routeIs('admin.twofactor') ? 'active' : '' }}">
        <a href="{{ route('admin.smssetting') }}" data-group="sms">{{ trans('global.smssettings_title') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.settings.app.show') ? 'active' : '' }}">
        <a href="{{ route('admin.settings.app.show') }}" data-group="sms">{{ trans('global.app_settings') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.email') ? 'active' : '' }}">
        <a href="{{ route('admin.email') }}" data-group="sms">{{ trans('global.emailSettings_title') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.currencySetting') ? 'active' : '' }}">
        <a href="{{ route('admin.currencySetting') }}" data-group="sms">Currency Settings</a>
    </li>
    <li class="{{ request()->routeIs('admin.pushnotification') ? 'active' : '' }}">
        <a href="{{ route('admin.pushnotification') }}"
            data-group="sms">{{ trans('global.push_notification_setting') }}</a>
    </li>
    <li class="{{ request()->routeIs('admin.api-informations') ? 'active' : '' }}">
        <a href="{{ route('admin.api-informations') }}" data-group="sms">{{ trans('global.apiCredentials_title') }}
        </a>
    </li>
    <li class="{{ request()->routeIs('admin.payment_methods.index') ? 'active' : '' }}">
        <a href="{{ route('admin.payment_methods.index', 'paypal') }}" data-group="sms">
            {{ trans('global.paymentMethods_title') }}
        </a>
    </li>
</ul>
