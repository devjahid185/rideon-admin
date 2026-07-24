@php
    $paymentMethods = [
        'stripe' => 'Stripe',
        'paypal' => 'Paypal',
        'upi' => 'UPI',
        'bank' => 'Bank Account',
    ];
@endphp

<ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
    @foreach($paymentMethods as $key => $label)
        <li class="{{ request()->routeIs('admin.driver.' . $key) ? 'active' : '' }}">
            <a href="{{ route('admin.driver.' . $key, $appUser->id) }}" data-group="payout">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>


{{--<ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
    <li class="{{ request()->routeIs('admin.driver.stripe') ? 'active' : '' }}">
    <a href="{{ route('admin.driver.stripe', $appUser->id) }}" data-group="payout">Stripe</a>
   </li>
    <li class="{{ request()->routeIs('admin.driver.paypal') ? 'active' : '' }}">
        <a href="{{ route('admin.driver.paypal', $appUser->id) }}" data-group="payout">Paypal</a>
    </li>
    <li class="{{ request()->routeIs('admin.driver.upi') ? 'active' : '' }}">
        <a href="{{ route('admin.driver.upi', $appUser->id) }}" data-group="payout">UPI</a>
    </li>
    <li class="{{ request()->routeIs('admin.vendor.bank') ? 'active' : '' }}">
        <a href="{{ route('admin.driver.bank', $appUser->id) }}" data-group="payout">Bank Account</a>
    </li>
</ul>--}}
