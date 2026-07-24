@extends('layouts.admin')
@section('content')

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="box">
                <div class="box-body no-padding d-block">
                    <ul class="nav nav-pills nav-stacked d-flex flex-column">

                        @php
                        $currentRoute = request()->path();
                        @endphp



                        @foreach($AllEmailRecord as $data)
                        @php

                        $userRoute = 'user/email-templates/'.$data->id;
                        $vendorRoute = 'vendor/email-templates/'.$data->id;
                        $adminRoute = 'admin/email-templates/'.$data->id;
                        $cls = ($currentRoute === $userRoute || $currentRoute === $vendorRoute || $currentRoute ===
                        $adminRoute ) ? 'active' : '';
                        @endphp



                        <li class=" {{ $cls }} ">
                            <a href="{{ route('user.email-templates', ['id' => $data->id]) }}">{{ trans('global.' . strtolower(str_replace(' ', '_', $data->temp_name ?? 'default_value'))) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>



        <div class="col-md-9">
            @php
            $roles = ['user', 'vendor', 'admin'];
            $userRoles = explode('#', $emaildata->role); $userRoles = isset($emaildata) && !is_null($emaildata->role) ? explode('#', $emaildata->role) : [];
            @endphp

            @foreach($userRoles as $index => $userRole)
            @if(isset($roles[$index]))
            @if($userRole == 'user')
            <a href="{{ route('user.email-templates', ['id' => $emaildata->id]) }}" class="btn btn-primary formtag"
                id="2"> {{ trans('global.user') }}</a>
            @elseif($userRole == 'vendor')
            <a href="{{ route('vendor.email-templates', ['id' => $emaildata->id]) }}" class="btn btn-primary formtag"
                id="3"> {{ trans('global.vendor') }}</a>
            @elseif($userRole == 'admin')
            <a href="{{ route('admin.email-templates', ['id' => $emaildata->id]) }}" class="btn btn-primary formtag"
                id="1" active> {{ trans('global.admin') }}</a>
            @endif
            @endif
            @endforeach


            <div class="box" style=" margin-top: 4px; ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        {{ trans('global.emailTemplate_title_singular') }}

                    </h3>

                    <button class="pull-right btn btn-success" id="available">
                        {{ trans('global.availableVariable') }}</button>
                </div>
                <div class="box-header d-none" id="variable" style="display: none;">
                    <div class="row ">
                        <div class="col-md-6">
                          
                        @if(in_array($emaildata->id, [1, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 18, 22, 26, 34, 35, 37, 38, 39, 40, 41, 42, 43, 44, 45]))
                            <p>{{ trans('global.first_name') }} : @{{ first_name }}</p>
                            <p>{{ trans('global.last_name') }} : @{{ last_name }}</p>
                            @endif

                            @if(in_array($emaildata->id, [1, 34, 35, 38, 39, 40, 41, 42]))
                            <p>{{ trans('global.email') }} : @{{ email }}</p>
                            <p>{{ trans('global.phone') }} : @{{ phone }}</p>
                            @endif

                            @if(in_array($emaildata->id, [10]))
                            <p>{{ trans('global.guests') }} : @{{ guests }}</p>
                            <p>{{ trans('global.beds') }} : @{{ beds }}</p>
                            <p>{{ trans('global.payment_method') }} : @{{ payment_method }}</p>
                            <p>{{ trans('global.payment_status') }} : @{{ payment_status }}</p>
                            <p>{{ trans('global.phone_country') }} : @{{ phone_country }}</p>
                            <p>{{ trans('global.vendor_phone') }} : @{{ vendor_phone }}</p>
                            <p>{{ trans('global.vendor_email') }} : @{{ vendor_email }}</p>
                            @endif

                            @if(in_array($emaildata->id, [22, 26]))
                            <p>{{ trans('global.support_phone') }} : @{{support_phone}}</p>
                            @endif

                            @if(in_array($emaildata->id, [14]))
                            <p>{{ trans('global.payment_status') }} : @{{ payment_status }}</p>
                            <p>{{ trans('global.phone_country') }} : @{{ phone_country }}</p>
                            <p>{{ trans('global.vendor_phone') }} : @{{ vendor_phone }}</p>
                            <p>{{ trans('global.vendor_email') }} : @{{ vendor_email }}</p>
                            @endif

                            @if(in_array($emaildata->id, [2, 3, 36, 37, 38]))
                            <p>{{ trans('global.otp') }} : @{{ OTP }}</p>
                            @endif

                            @if(in_array($emaildata->id, [7]))
                            <p>{{ trans('global.transaction_type') }} : @{{transaction_type}}</p>
                            @endif

                            @if(in_array($emaildata->id, [1, 14, 22, 34, 35]))
                            <p>{{ trans('global.support_email') }} : @{{ support_email }}</p>
                            @endif

                            @if(in_array($emaildata->id, [7, 6]))
                            <p>{{ trans('global.payout_amount') }} : @{{ payout_amount }}</p>
                            <p>{{ trans('global.payout_date') }} : @{{ payout_date }}</p>
                            <p>{{ trans('global.currency_code') }} : @{{ currency_code }}</p>
                            
                            @endif

                            @if(in_array($emaildata->id, [4]))
                            <p>{{ trans('global.payout_amount') }} : @{{ payout_amount }}</p>
                            <p>{{ trans('global.payout_bank') }} : @{{ payout_bank }}</p>
                            <p>{{ trans('global.payout_date') }} : @{{ payout_date }}</p>
                            <p>{{ trans('global.support_email') }} : @{{ support_email }}</p>
                            <p>{{ trans('global.currency_code') }} : @{{ currency_code }}</p>
                            @endif

                            @if(in_array($emaildata->id, [5, 9, 10, 11, 12, 13]))
                            <p>{{ trans('global.booking_id') }} : @{{ booking_id }}</p>
                            <p>{{ trans('global.item_name') }} : @{{ item_name }}</p>
                            <p>{{ trans('global.check_in') }} : @{{ check_in }}</p>
                            <p>{{ trans('global.check_out') }} : @{{ check_out }}</p>
                            <p>{{ trans('global.currency_code') }} : @{{ currency_code }}</p>
                            <p>{{ trans('global.amount') }} : @{{ amount }}</p>
                            @endif

                            @if(in_array($emaildata->id, [14, 18, 22, 26]))
                            <p>{{ trans('global.booking_id') }} : @{{ bookingid }}</p>
                            <p>{{ trans('global.item_name') }} : @{{ item_name }}</p>
                            <p>{{ trans('global.check_in') }} : @{{ check_in }}</p>
                            <p>{{ trans('global.start_time') }} : @{{start_time}}</p>
                            <p>{{ trans('global.check_out') }} : @{{ check_out }}</p>
                            <p>{{ trans('global.end_time') }} : @{{end_time}}</p>
                            <p>{{ trans('global.currency_code') }} : @{{ currency_code }}</p>
                            <p>{{ trans('global.amount') }} : @{{ amount }}</p>
                            @endif

                            @if(in_array($emaildata->id, [12, 45]))
                            <p>{{ trans('global.vendor_name') }} : @{{vendor_name}}</p>
                            @endif

                            @if(in_array($emaildata->id, [1, 2, 3, 7, 14, 18, 22, 26, 34, 37, 40, 41, 42, 44]))
                            <p>{{ trans('global.website_name') }} : @{{ website_name }}</p>
                            @endif

                            @if(in_array($emaildata->id, [39, 40]))
                            <p>{{ trans('global.title') }} : @{{title}}</p>
                            @endif

                            @if(in_array($emaildata->id, [41, 42]))
                            <p>{{ trans('global.ticket_id') }} : @{{ticket_id}}</p>
                            <p>{{ trans('global.subject') }} : @{{subject}}</p>
                            <p>{{ trans('global.update_date') }} : @{{update_date}}</p>
                            @endif

                            @if(in_array($emaildata->id, [43, 44, 45]))
                            <p>{{ trans('global.booking_id') }} : @{{ bookingid }}</p>
                            <p>{{ trans('global.item_name') }} : @{{ item_name }}</p>
                            <p>{{ trans('global.current_date') }} : @{{current_date}}</p>
                            @endif

                            @if(in_array($emaildata->id, [4, 5, 9, 10, 11, 12, 13, 35, 36, 38, 39, 43, 45]))
                            <p>{{ trans('global.website_name') }} : @{{ website_name }}</p>
                            @endif



                        </div>
                    </div>
                </div>
            </div>


            @if(request()->is("user/email-templates/*"))

            <form action="{{ route('user.email-template.create',['id' => $emaildata->id]) }}" method="POST"
                class="myform" data-id="2" id="user-form">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.subject') }}</label>
                        <input class="form-control f-14" name="subject" type="text"
                            value="{{ $emaildata->subject ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.email_enable') }} </label>
                        <input type="checkbox" name="emailsent" value="1"
                            {{ $emaildata && $emaildata->emailsent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <textarea name="body" class="editor">{{ $emaildata->body}}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.sms_enable') }} </label>
                        <input type="checkbox" name="smssent" value="1"
                            {{ $emaildata && $emaildata->smssent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.message') }}</label>
                        <textarea class="form-control" name="sms" id="message">{{ $emaildata->sms ?? '' }}</textarea>

                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2"
                            for="exampleInputEmail1">{{ trans('global.push_notification') }} {{ trans('global.enable') }}</label>
                        <input type="checkbox" name="pushsent" value="1"
                            {{ $emaildata && $emaildata->pushsent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.push_notification') }}</label>
                        <textarea class="form-control" name="push_notification"
                            id="message">{{ $emaildata->push_notification ?? '' }}</textarea>

                    </div>
                    <div class="pull-right">
                    {{-- @can('email_update')    --}}
                    <button type="submit"
                            class="btn btn-primary btn-flat f-14">{{ trans('global.update') }}</button>
                    {{-- @endcan --}}
                    </div>

                </div>
            </form>
            @elseif(request()->is("vendor/email-templates/*"))

            <form action="{{ route('vendor.email-template.create',['id' => $emaildata->id]) }}" method="POST"
                class="myform" data-id="3" id="vendor-form">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.vendor') }}
                            {{ trans('global.subject') }}</label>
                        <input type="hidden" name="type" value="vendor">
                        <input class="form-control f-14" name="vendorsubject" type="text"
                            value="{{ $emaildata->vendorsubject ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.email_enable') }}</label>
                        <input type="checkbox" name="vendoremailsent" value="1"
                            {{ $emaildata && $emaildata->vendoremailsent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <textarea name="vendorbody" id="editor2">{{ $emaildata->vendorbody}}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.vendor') }}
                            {{ trans('global.sms_enable') }}</label>
                        <input type="checkbox" name="vendorsmssent" value="1"
                            {{ $emaildata && $emaildata->vendorsmssent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.vendor') }}
                            {{ trans('global.message') }}</label>
                        <textarea class="form-control" name="vendorsms"
                            id="message">{{ $emaildata->vendorsms ?? '' }}</textarea>

                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.vendor') }}
                            {{ trans('global.push_notification') }} {{ trans('global.enable') }}</label>
                        <input type="checkbox" name="vendorpushsent" value="1"
                            {{ $emaildata && $emaildata->vendorpushsent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.vendor') }}
                            {{ trans('global.push_notification') }} </label>
                        <textarea class="form-control" name="vendorpush_notification"
                            id="message">{{ $emaildata->vendorpush_notification ?? '' }}</textarea>

                    </div>
                    <div class="pull-right">
                    {{-- @can('email_update')    --}}
                    <button type="submit"
                            class="btn btn-primary btn-flat f-14">{{ trans('global.update') }}</button>
                    </div>
                    {{-- @endcan --}}
                </div>
            </form>
            @else
            <form action="{{ route('admin.email-template.create',['id' => $emaildata->id]) }}" method="POST"
                class="myform" data-id="1" id="admin-form">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <input type="hidden" name="type" value="admin">
                        <label class="fw-bold mb-2" for="exampleInputEmail1">{{ trans('global.admin') }}
                            {{ trans('global.subject') }}</label>
                        <input class="form-control f-14" name="adminsubject" type="text"
                            value="{{ $emaildata->adminsubject ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="fw-bold mb-2" for="exampleInputEmail1"> {{ trans('global.email_enable') }}</label>
                        <input type="checkbox" name="adminemailsent" value="1"
                            {{ $emaildata && $emaildata->adminemailsent == 1 ? ' checked' : '' }}>
                    </div>
                    <div class="form-group">
                        <textarea name="adminbody" id="editor3">{{ $emaildata->adminbody}}</textarea>
                    </div>

                    <div class="pull-right">
                    {{-- @can('email_update')      --}}
                    <button type="submit"
                            class="btn btn-primary btn-flat f-14">{{ trans('global.update') }}</button>
                    </div>
                    {{-- @endcan --}}

                </div>
            </form>
            @endif
        </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Correct CKEditor script URL -->
<script src="https://cdn.ckeditor.com/ckeditor5/45.2.0/classic/ckeditor.js"></script>
<script>
$(document).ready(function() {
    $('#available').click(function() {
        $('#variable').toggle();
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    ClassicEditor
        .create(document.querySelector('.editor'))

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    ClassicEditor
        .create(document.querySelector('#editor2'))

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    ClassicEditor
        .create(document.querySelector('#editor3'))
   
});
</script>



@endsection