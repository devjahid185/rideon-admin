@extends('layouts.admin')
@section('content')

@php
    $value = fn ($key, $fallback = '') => old($key, $settings[$key] ?? $fallback);
@endphp

<style>
    .email-settings-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    .email-settings-card .box-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 18px 20px;
    }

    .email-settings-card .box-title {
        font-weight: 700;
        color: #111827;
    }

    .email-help-panel {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        color: #4b5563;
        margin-bottom: 18px;
    }

    .email-preview-card {
        background: #f3f6fb;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        padding: 18px;
    }

    .email-preview-card strong {
        color: #111827;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin: 22px 0 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
</style>

<section class="content">
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            <div class="box box-info box_info">
                <div class="">
                    <h4 class="all_settings f-18 mt-1" style="margin-left:15px;">{{ trans('global.manage_settings') }}</h4>
                    @include('admin.generalSettings.general-setting-links.links')
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="box box-info email-settings-card">
                <div class="box-header with-border">
                    <h3 class="box-title">Professional Email Settings</h3>
                </div>

                <form action="{{ route('admin.email.update') }}" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="email-help-panel">
                            Manage the SMTP account, sender name, sender email, and support contact shown in customer,
                            vendor, and admin emails. These values are stored in General Settings and applied at send time.
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-section-title">Sender Identity</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sender Name <span class="text-danger">*</span></label>
                                    <input class="form-control" name="from_name" type="text"
                                        value="{{ $value('from_name', $settings['general_name'] ?? config('mail.from.name')) }}"
                                        placeholder="RideOn">
                                    <small class="text-muted">This is the name recipients see in their inbox.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sender Email <span class="text-danger">*</span></label>
                                    <input class="form-control" name="from_email" type="email"
                                        value="{{ $value('from_email', config('mail.from.address')) }}"
                                        placeholder="no-reply@example.com">
                                    <small class="text-muted">Use an authenticated domain email for best delivery.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title">SMTP Configuration</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Host <span class="text-danger">*</span></label>
                                    <input class="form-control" name="host" type="text"
                                        value="{{ $value('host', config('mail.mailers.smtp.host')) }}"
                                        placeholder="smtp.example.com">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Port <span class="text-danger">*</span></label>
                                    <input class="form-control" name="port" type="number" min="1" max="65535"
                                        value="{{ $value('port', config('mail.mailers.smtp.port')) }}"
                                        placeholder="587">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Encryption</label>
                                    <select class="form-control" name="encryption">
                                        <option value="">None</option>
                                        <option value="tls" {{ $value('encryption', config('mail.mailers.smtp.encryption')) === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $value('encryption', config('mail.mailers.smtp.encryption')) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Username</label>
                                    <input class="form-control" name="username" type="text"
                                        value="{{ $value('username', config('mail.mailers.smtp.username')) }}"
                                        placeholder="mailer@example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Password</label>
                                    <input class="form-control" name="password" type="password"
                                        value=""
                                        placeholder="{{ isset($settings['password']) && $settings['password'] !== '' ? 'Leave blank to keep current password' : 'SMTP password' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title">Support Contact in Email Footer</div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Support Email</label>
                                    <input class="form-control" name="general_email" type="email"
                                        value="{{ $value('general_email') }}"
                                        placeholder="support@example.com">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Phone Country</label>
                                    <input class="form-control" name="general_default_phone_country" type="text"
                                        value="{{ $value('general_default_phone_country') }}"
                                        placeholder="+1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Support Phone</label>
                                    <input class="form-control" name="general_phone" type="text"
                                        value="{{ $value('general_phone') }}"
                                        placeholder="5551234567">
                                </div>
                            </div>
                        </div>

                        <div class="email-preview-card">
                            <strong>Inbox preview:</strong>
                            {{ $value('from_name', $settings['general_name'] ?? config('mail.from.name')) }}
                            &lt;{{ $value('from_email', config('mail.from.address')) }}&gt;
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">Save Email Settings</button>
                        <span class="text-muted" style="margin-left:10px;">Run clear cache if your server caches configuration aggressively.</span>
                    </div>
                </form>
            </div>

            <div class="box box-info email-settings-card">
                <div class="box-header with-border">
                    <h3 class="box-title">Test Email Configuration</h3>
                </div>

                <form action="{{ route('admin.email.test') }}" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="email-help-panel">
                            Send a real email using the saved SMTP configuration, sender name, sender email, and the
                            same professional email wrapper used by production notifications.
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Test Recipient Email <span class="text-danger">*</span></label>
                                    <input class="form-control" name="test_email" type="email"
                                        value="{{ old('test_email', auth()->user()->email ?? '') }}"
                                        placeholder="admin@example.com">
                                    <small class="text-muted">Use an inbox you can check immediately.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-flat btn-block">
                                    Send Test Email
                                </button>
                            </div>
                        </div>

                        <div class="email-preview-card">
                            <strong>Test subject:</strong> Email Configuration Test
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
