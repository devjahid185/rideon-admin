<?php

// app/Traits/SMSTrait.php

namespace App\Http\Controllers\Traits;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait EmailTrait
{
    private $sendgridApiKey;

    private $senderEmail;

    private $senderName;

    private $smtpConfig;

    public function sendMail($subject, $data, $recipientEmail, $attachment = null)
    {

        $settings = GeneralSetting::whereIn('meta_key', [
            'host',
            'port',
            'username',
            'password',
            'encryption',
            'from_email',
            'general_name',
            'general_email',
            'general_phone',
            'general_name',
            'general_default_phone_country',
        ])->get()->pluck('meta_value', 'meta_key')->toArray();

        $this->smtpConfig = [
            'driver' => 'smtp',
            'host' => $settings['host'] ?? 'send.smtp.com',
            'port' => $settings['port'] ?? '465',
            'username' => $settings['username'] ?? '',
            'password' => $settings['password'] ?? '',
            'encryption' => $settings['encryption'] ?? '',
            'from' => [
                'address' => $settings['from_email'] ?? '',
                'name' => $settings['general_name'] ?? '',
            ],
        ];

        try {
            // config(['mail' => $this->smtpConfig]);

            $data = html_entity_decode($data);
            $emailData = [
                'data' => $data,
                'general_email' => $settings['general_email'] ?? '',
                'general_name' => $settings['general_name'] ?? '',
                'general_phone' => $settings['general_phone'] ?? '',
                'general_default_phone_country' => $settings['general_default_phone_country'] ?? '',
            ];

            $attachmentPaths = $attachment ?? [];
            Mail::send('admin.emails.commonEmailTemplate', ['emailData' => $emailData], function ($mail) use ($recipientEmail, $subject, $attachmentPaths) {
                $mail->to($recipientEmail)
                    ->subject($subject);
                foreach ($attachmentPaths as $attachmentPath) {
                    if (file_exists($attachmentPath)) {
                        $mail->attach($attachmentPath);
                    }
                }
            });

            return 'Mail sent successfully';
        } catch (\Exception $e) {

            return;
            Log::error('Mail sending failed: '.$e->getMessage());

            return 'Mail sending failed: '.$e->getMessage();
        }
    }
}
