@extends('layouts.admin')
@section('content')
    <section class="content">
        <div class="row gap-2">
            <div class="col-md-3 settings_bar_gap">
                <div class="box box-info box_info">
                    <div class="">
                        <h4 class="all_settings f-18 mt-1" style="margin-left:15px;"> {{ trans('global.manage_settings') }}
                        </h4>
                        @include('admin.generalSettings.general-setting-links.links')
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('global.general_title_singular') }}</h3><span class="email_status"
                            style="display: none;">(<span class="text-green"><i class="fa fa-check"
                                    aria-hidden="true"></i>Verified</span>)</span>
                    </div>
                    <form id="general_form" method="post" action="{{ route('admin.add_configuration_wizard') }}"
                        class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group name">
                                <label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.name') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="general_name" class="form-control" id="name"
                                        value=" {{ $general_name->meta_value ?? '' }}" placeholder="Name">
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="form-group name">
                                <label for="inputEmail3"
                                    class="col-sm-3 control-label">{{ trans('global.site_desciption') }} <span
                                        class="text-danger"></span></label>
                                <div class="col-sm-6">
                                    <input type="text" name="general_description" class="form-control"
                                        id="general_description" value=" {{ $general_description->meta_value ?? '' }}"
                                        placeholder="general_description">
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="form-group email">
                                <label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.email') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="email" name="general_email" class="form-control" id="email"
                                        value=" {{ $general_email->meta_value ?? '' }}" placeholder="Email">
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div
                                class="form-group phone row {{ $errors->has('general_default_phone_country') || $errors->has('general_phone') ? 'has-error' : '' }}">
                                <label for="phone_country"
                                    class="col-sm-3 control-label">{{ trans('global.phone_country') }}<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="general_default_phone_country"
                                        id="general_default_phone_country" onchange="updateDefaultCountry()">
                                        @foreach (config('countries') as $country)
                                            <option value="{{ $country['dial_code'] }}"
                                                data-country-code="{{ $country['code'] }}"
                                                {{ old('general_default_phone_country', $general_default_phone_country->meta_value ?? '') == $country['dial_code'] ? 'selected' : '' }}>
                                                {{ $country['name'] }} ({{ $country['dial_code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('general_default_phone_country'))
                                        <span
                                            class="help-block text-danger">{{ $errors->first('general_default_phone_country') }}</span>
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" type="text" name="general_phone" id="phone"
                                        value="{{ $general_phone->meta_value ?? '' }}" required>
                                    @if ($errors->has('general_phone'))
                                        <span class="help-block text-danger">{{ $errors->first('general_phone') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group" style="display: none;">
                                <label for="default_country">{{ trans('global.default_country') }}</label>
                                <input class="form-control" type="text" name="general_default_country_code"
                                    id="general_default_country_code"
                                    value="{{ old('general_default_country_code', '') }}">
                                @if ($errors->has('general_default_country_code'))
                                    <span
                                        class="help-block text-danger">{{ $errors->first('general_default_country_code') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">{{ trans('global.logo') }}</label>
                                <div class="col-sm-6">
                                    <input type="file" name="general_logo" class="form-control" id="photos[logo]"
                                        value="" placeholder="Logo">
                                    <span class="text-danger"></span>
                                    @if (!empty($general_logo->meta_value))
                                        <br><img class="file-img" src="{{ '/storage/' . $general_logo->meta_value }}"
                                            width="150" alt="Logo">
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3"
                                    class="col-sm-3 control-label">{{ trans('global.favicon') }}</label>
                                <div class="col-sm-6">
                                    <input type="file" name="general_favicon" class="form-control validate_field"
                                        id="photos[favicon]" value="" placeholder="Favicon">
                                    <span class="text-danger"></span>
                                    @if (!empty($general_favicon->meta_value))
                                        <br>
                                        <img class="file-img" src="{{ '/storage/' . $general_favicon->meta_value }}"
                                            height="25" alt="Favicon">
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="form-group default_currency" style="display: none;">
                                <label for="inputEmail3"
                                    class="col-sm-3 control-label">{{ trans('global.default_currency') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control validate_field" id="default_currency"
                                        name="general_default_currency">
                                        @foreach ($allcurrency as $currency)
                                            <option value="{{ $currency->currency_code }}"
                                                @if (($general_default_currency->meta_value ?? null) == $currency->currency_code) selected @endif>
                                                {{ $currency->currency_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="form-group default_language">
                                <label for="inputEmail3"
                                    class="col-sm-3 control-label">{{ trans('global.default_language') }}</label>
                                <div class="col-sm-6">
                                    <select class="form-control validate_field" id="default_language"
                                        name="general_default_language">
                                        @foreach ($languagedata as $language)
                                            <option value="{{ $language->short_name }}"
                                                @if ($language->short_name == $general_default_language->meta_value) selected @endif>{{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-sm-3">
                                    <small></small>
                                </div>
                            </div>
                            <div class="text-center" id="error-message"></div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info btn-space"> {{ trans('global.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('admin.settings') }}">
                                {{ trans('global.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        function updateDefaultCountry() {
            const phoneCountryDropdown = document.getElementById('general_default_phone_country');
            const defaultCountryField = document.getElementById('general_default_country_code');
            const selectedDialCode = phoneCountryDropdown.value;
            const selectedOption = phoneCountryDropdown.querySelector(`option[value="${selectedDialCode}"]`);
            const countryCode = selectedOption ? selectedOption.getAttribute('data-country-code') : '';
            defaultCountryField.value = countryCode;
        }
        document.addEventListener("DOMContentLoaded", function() {
            updateDefaultCountry();
        });
    </script>
@endsection
