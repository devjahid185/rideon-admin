@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3 settings_bar_gap">
                <div class="box box-info box_info">
                    <div class="">
                        <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ trans('global.manage_settings') }}
                        </h4>
                        @include('admin.generalSettings.general-setting-links.links')
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.apiCredentials_title_singular') }}</h3><span
                            class="email_status" style="display: none;">(<span class="text-green"><i class="fa fa-check"
                                    aria-hidden="true"></i>Verified</span>)</span>
                    </div>

                    <form id="api_credentials" method="post" action="{{ route('admin.apiauthenticationadd') }}"
                        class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
                        {{ csrf_field() }}

                        <div class="form-group google_client_secret">

                        </div>
                        <div class="form-group google_map_key">
                            <label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.google_mao_browser') }}
                                <span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="password" name="api_google_map_key" class="form-control" id="google_map_key"
                                    value="{{ $api_google_map_key->meta_value ?? '' }}"
                                    placeholder="Google Map Browser Key">
                                <span class="text-danger"></span>
                            </div>
                            <div class="col-sm-3">
                                <small></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="general_captcha" class="col-sm-3 control-label">{{ trans('global.captcha') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="general_captcha" id="general_captcha" class="form-control">
                                    <option value="yes"
                                        {{ $general_captcha && $general_captcha->meta_value == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no"
                                        {{ $general_captcha && $general_captcha->meta_value == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                <span class="text-danger"></span>
                            </div>
                            <div class="col-sm-3">
                                <small></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="site_key" class="col-sm-3 control-label">{{ trans('global.site_key') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="password" name="site_key" id="site_key" class="form-control"
                                    value="{{ $site_key->meta_value ?? '' }}" required>
                                <span class="text-danger"></span>
                            </div>
                            <div class="col-sm-3">
                                <small></small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="private_key" class="col-sm-3 control-label">{{ trans('global.private_key') }} <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="password" name="private_key" id="private_key" class="form-control"
                                    value="{{ $private_key->meta_value ?? '' }}" required>
                                <span class="text-danger"></span>
                            </div>
                            <div class="col-sm-3">
                                <small></small>
                            </div>
                        </div>
                        <div class="text-center" id="error-message"></div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info btn-space">{{ trans('global.save') }}</button>
                            <a class="btn btn-danger"
                                href="{{ route('admin.settings') }}">{{ trans('global.cancel') }}</a>
                        </div>

						<div class="form-group">
    <div class="col-sm-3 control-label"></div>
    <div class="col-sm-6">
        <p class="help-block" style="margin-top:10px; color:#777;">
            To create your Google reCAPTCHA v2 keys, visit:
            <a href="https://www.google.com/recaptcha/admin/create" target="_blank">
                https://www.google.com/recaptcha/admin/create
            </a>
            <br>
            After creating the reCAPTCHA, copy the Site Key and Secret Key and paste them in the fields above.
        </p>
    </div>
</div>

                </div>

                </form>
            </div>
        </div>

    </section>
@endsection
