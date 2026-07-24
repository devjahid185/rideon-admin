@section('styles')
<style>
    .d-flex {
        display: flex;
    }
    .justify-content-between {
        justify-content: space-between;
    }
    .sms-provider-name {
        /* Ensure this has enough space to be aligned right */
        margin-left: auto; /* This will push the element to the right */
        font-weight: bold;
        color: #333;
    }
.autoFillOtp-toggle {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 22px;
    margin-right: 10px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 15px;
    width: 15px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.toggle-label {
    font-size: 14px;
}
</style>
@endsection
   <div class="col-md-9">
   <div class="d-flex justify-content-between align-items-center mb-3">
   <span class="toggle-label">Auto Fill OTP</span>
   <div class="autoFillOtp">
   
    <label class="switch">
            <input type="checkbox" id="autofillotp" data-offstyle="danger"
            data-toggle="toggle" data-on="Active" data-off="InActive"
            {{ $auto_fill_otp && $auto_fill_otp->meta_value == 1 ? 'checked' : '' }}> 
                <span class="slider round"></span>
            </label>
           
        </div>
        <div class="ms-auto sms-provider-name">
        @php
            $url = '';
            if ($sms_provider_name->meta_value == 'nonage') {
                $url = route('admin.smssetting');
            } elseif ($sms_provider_name->meta_value == 'twillio') {
                $url = route('admin.twilliosetting');
            }
            elseif ($sms_provider_name->meta_value == 'sinch') {
                $url = route('admin.sinchSetting');
            }
            elseif ($sms_provider_name->meta_value == 'msg91') {
                $url = route('admin.msg91');
            }
        @endphp
        Active :: <a href="{{ $url }}" class="sms-provider-link">
            {{ $sms_provider_name->meta_value }}
        </a>
        </div>
        
    </div>
          <div class="nav-tabs-custom">
          <ul class="nav nav-tabs" style="display: inline-block;">             
    <li class="{{ Request::route()->getName() === 'admin.smssetting' ? 'active' : '' }}">
        <a href="{{ route('admin.smssetting') }}" id="smssetting">{{ trans('global.smssettings_title_singular') }}</a>
    </li>

    <li class="{{ Request::route()->getName() === 'admin.twilliosetting' ? 'active' : '' }}">
        <a href="{{ route('admin.twilliosetting') }}" id="twilliosetting">{{ trans('global.twillio') }}</a>
    </li>

    <li class="{{ Request::route()->getName() === 'admin.sinchSetting' ? 'active' : '' }}">
        <a href="{{ route('admin.sinchSetting') }}" id="sinchSetting" >Sinch</a>
    </li>
     <li class="{{ Request::route()->getName() === 'admin.msg91' ? 'active' : '' }}">
        <a href="{{ route('admin.msg91') }}" >{{ trans('global.msg91') }}</a>
    </li>
   <!-- <li class="{{ Request::route()->getName() === 'admin.twilliosetting' ? 'active' : '' }}">
        <a href="{{ route('admin.twilliosetting') }}" >{{ trans('global.twillio') }}</a>
    </li>
    <li class="{{ Request::route()->getName() === 'admin.nexmosetting' ? 'active' : '' }}">
        <a href="{{ route('admin.nexmosetting') }}" >{{ trans('global.nexmo') }}</a>
    </li>
    <li class="{{ Request::route()->getName() === 'admin.twofactor' ? 'active' : '' }}">
        <a href="{{ route('admin.twofactor') }}" >{{ trans('global.2_factor') }}</a>
    </li> -->
</ul>
</div>

<div class="box box-muted">

