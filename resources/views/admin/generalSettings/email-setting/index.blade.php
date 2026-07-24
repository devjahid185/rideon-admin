@extends('layouts.admin')
@section('content')

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
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('global.emailSettings_title_singular') }}</h3>
                </div>

                <div class="box-body">
                        <p style="font-size:16px;">
                            <strong>Note:</strong> SMTP settings must be configured in your <code>.env</code> file.
                        </p>

                        <p>Here are the settings you need to add or update:</p>
                        <pre style="background:#f7f7f7; padding:10px; border-radius:5p;">
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=from@example.com
                        </pre>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
