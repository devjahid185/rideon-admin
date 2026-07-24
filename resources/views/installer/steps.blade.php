<div class="step-item {{ request()->is('install/welcome') ? 'active' : '' }}">
    <div class="step-icon">1</div>
    <div class="step-title">Welcome</div>
</div>
<div class="step-item {{ request()->is('install/requirements') ? 'active' : '' }}">
    <div class="step-icon">2</div>
    <div class="step-title">Requirements</div>
</div>
<div class="step-item {{ request()->is('install/permissions') ? 'active' : '' }}">
    <div class="step-icon">3</div>
    <div class="step-title">Permissions</div>
</div>
<div class="step-item {{ request()->is('install/purchaseValidation') ? 'active' : '' }}">
    <div class="step-icon">4</div>
    <div class="step-title">Purchase Validation</div>
</div>
<div class="step-item {{ request()->is('install/database') || request()->is('install/migrate') ? 'active' : '' }}">
    <div class="step-icon">5</div>
    <div class="step-title">Database</div>
</div>
<div class="step-item {{ request()->is('install/admin') ? 'active' : '' }}">
    <div class="step-icon">6</div>
    <div class="step-title">Admin Setup</div>
</div>
<div class="step-item {{ request()->is('install/finish') ? 'active' : '' }}">
    <div class="step-icon">7</div>
    <div class="step-title">Finish</div>
</div>
