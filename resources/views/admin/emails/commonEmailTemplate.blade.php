@php
    $brandName = trim($emailData['general_name'] ?? $emailData['sender_name'] ?? config('app.name', 'RideOn'));
    $senderEmail = trim($emailData['sender_email'] ?? config('mail.from.address'));
    $supportEmail = trim($emailData['general_email'] ?? $senderEmail);
    $supportPhone = trim(($emailData['general_default_phone_country'] ?? '').($emailData['general_phone'] ?? ''));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $brandName }}</title>
    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background: #f3f6fb;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
        }

        a {
            color: #1d56a5;
            text-decoration: none;
        }

        .email-shell {
            width: 100%;
            background: #f3f6fb;
            padding: 32px 12px;
        }

        .email-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
        }

        .brand-bar {
            background: #1d56a5;
            border-radius: 18px 18px 0 0;
            padding: 28px 32px;
            color: #ffffff;
        }

        .brand-name {
            margin: 0;
            font-size: 24px;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: 0;
        }

        .brand-subtitle {
            margin: 8px 0 0;
            color: #dbeafe;
            font-size: 14px;
            line-height: 1.5;
        }

        .email-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-top: 0;
            border-radius: 0 0 18px 18px;
            padding: 32px;
        }

        .email-content {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.7;
        }

        .email-content h1,
        .email-content h2,
        .email-content h3 {
            color: #111827;
            line-height: 1.25;
            margin: 0 0 14px;
        }

        .email-content p {
            margin: 0 0 16px;
        }

        .email-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .email-content th,
        .email-content td {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
        }

        .email-content .btn,
        .email-content a.button {
            display: inline-block;
            background: #1d56a5;
            color: #ffffff !important;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 700;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 28px 0;
        }

        .footer {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.6;
            text-align: center;
            padding: 20px 16px 0;
        }

        .footer strong {
            color: #374151;
        }

        @media screen and (max-width: 600px) {
            .email-shell {
                padding: 18px 8px;
            }

            .brand-bar,
            .email-card {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-shell">
        <div class="email-container">
            <div class="brand-bar">
                <h1 class="brand-name">{{ $brandName }}</h1>
                <p class="brand-subtitle">Reliable service updates from your {{ $brandName }} team.</p>
            </div>

            <div class="email-card">
                <div class="email-content">
                    {!! $emailData['data'] ?? '' !!}
                </div>

                <div class="divider"></div>

                <div class="footer">
                    <p>
                        <strong>Need help?</strong>
                        @if($supportEmail !== '')
                            Contact us at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
                        @endif
                        @if($supportPhone !== '')
                            {{ $supportEmail !== '' ? ' or ' : '' }}call {{ $supportPhone }}
                        @endif
                    </p>
                    <p>&copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
