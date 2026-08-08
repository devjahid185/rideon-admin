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
            'from_name',
            'general_name',
            'general_email',
            'general_phone',
            'general_default_phone_country',
        ])->get()->pluck('meta_value', 'meta_key')->toArray();

        $senderEmail = trim((string) ($settings['from_email'] ?? config('mail.from.address')));
        $senderName = trim((string) ($settings['from_name'] ?? $settings['general_name'] ?? config('mail.from.name')));
        $senderName = $senderName !== '' ? $senderName : config('app.name', 'RideOn');
        $replyToEmail = trim((string) ($settings['general_email'] ?? $senderEmail));

        $this->smtpConfig = [
            'transport' => 'smtp',
            'host' => $settings['host'] ?? config('mail.mailers.smtp.host'),
            'port' => (int) ($settings['port'] ?? config('mail.mailers.smtp.port')),
            'username' => $settings['username'] ?? '',
            'password' => $settings['password'] ?? '',
            'encryption' => $settings['encryption'] ?? config('mail.mailers.smtp.encryption'),
        ];

        try {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp' => array_merge(config('mail.mailers.smtp', []), $this->smtpConfig),
                'mail.from.address' => $senderEmail,
                'mail.from.name' => $senderName,
            ]);

            $data = html_entity_decode($data);
            $emailData = [
                'data' => $data,
                'general_email' => $settings['general_email'] ?? '',
                'general_name' => $settings['general_name'] ?? $senderName,
                'general_phone' => $settings['general_phone'] ?? '',
                'general_default_phone_country' => $settings['general_default_phone_country'] ?? '',
                'sender_name' => $senderName,
                'sender_email' => $senderEmail,
            ];

            $attachmentPaths = $attachment ?? [];
            Mail::send('admin.emails.commonEmailTemplate', ['emailData' => $emailData], function ($mail) use ($recipientEmail, $subject, $attachmentPaths, $senderEmail, $senderName, $replyToEmail) {
                $mail->to($recipientEmail)
                    ->from($senderEmail, $senderName)
                    ->subject($subject);
                if ($replyToEmail !== '') {
                    $mail->replyTo($replyToEmail, $senderName);
                }
                foreach ($attachmentPaths as $attachmentPath) {
                    if (file_exists($attachmentPath)) {
                        $mail->attach($attachmentPath);
                    }
                }
            });

            return 'Mail sent successfully';
        } catch (\Exception $e) {
            Log::error('Mail sending failed: '.$e->getMessage());

            return 'Mail sending failed: '.$e->getMessage();
        }
    }
}
