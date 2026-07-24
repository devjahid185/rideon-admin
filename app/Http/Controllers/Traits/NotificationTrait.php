<?php

namespace App\Http\Controllers\Traits;

use App\Jobs\SendAllNotificationsJob;
use App\Models\AppUser;
use App\Models\EmailSmsNotification;
use App\Models\GeneralSetting;

trait NotificationTrait
{
    use EmailTrait, MiscellaneousTrait, PushNotificationTrait, SMSTrait;

    public function sendAllNotifications($valuesArray, $user_id, $template_id, $data = ['key' => 'value'], $vendor_id = 0)
    {
        // Just dispatch the job
        SendAllNotificationsJob::dispatch($valuesArray, $user_id, $template_id, $data, $vendor_id);

        // $this->_sendAllNotificationsNow($valuesArray, $user_id, $template_id, $data, $vendor_id);
    }

    public function _sendAllNotificationsNow($valuesArray, $user_id, $template_id, $data = ['key' => 'value'], $vendor_id = 0)
    {

        $settings = GeneralSetting::whereIn('meta_key', [
            'push_notification_status',
            'general_name',
            'general_email',
        ])->get()->pluck('meta_value', 'meta_key')->toArray();

        $valuesArray['AppName'] = $settings['general_name'];
        $valuesArray['website_name'] = $settings['general_name'];
        $adminemail = $settings['general_email'];

        $user = AppUser::with('metadata')->where('id', $user_id)->get();
        $vendorData = '';
        if ($vendor_id > 0) {
            $vendorData = AppUser::with('metadata')->where('id', $vendor_id)->get();
        }
        $template = EmailSmsNotification::find($template_id);
        if ($template->status == 0) {
            return;
        }
        $subject = $this->replaceTemplatePlaceholders($template->subject, $valuesArray);
        $testing = '';

        $message = $this->replaceTemplatePlaceholders($template->body, $valuesArray).$testing;
        $smsMessage = $this->replaceTemplatePlaceholders($template->sms, $valuesArray).$testing;
        $pushMessage = $this->replaceTemplatePlaceholders($template->push_notification, $valuesArray).$testing;

        $adminmessage = $this->replaceTemplatePlaceholders($template->adminbody, $valuesArray).$testing;
        $adminsmsMessage = $this->replaceTemplatePlaceholders($template->adminsms, $valuesArray).$testing;
        $adminpushMessage = $this->replaceTemplatePlaceholders($template->adminpush_notification, $valuesArray).$testing;

        $vendormessage = $this->replaceTemplatePlaceholders($template->vendorbody, $valuesArray).$testing;
        $vendorsmsMessage = $this->replaceTemplatePlaceholders($template->vendorsms, $valuesArray).$testing;
        $vendorpushMessage = $this->replaceTemplatePlaceholders($template->vendorpush_notification, $valuesArray).$testing;

        if ($template->adminsubject != 'null') {
            $adminsubject = $this->replaceTemplatePlaceholders($template->adminsubject, $valuesArray).$testing;
        }
        if ($template->vendorsubject != 'null') {
            $vendorsubject = $this->replaceTemplatePlaceholders($template->vendorsubject, $valuesArray).$testing;
        }

        if ($template->smssent == 1) {

            if (isset($valuesArray['temp_phone']) && ! empty($valuesArray['temp_phone'])) {
                $phone = str_replace('+', '', $valuesArray['temp_phone']);
                $this->sendSMS($subject, $smsMessage, $valuesArray['temp_phone']);
            } elseif ($user->isNotEmpty()) {
                $phone = str_replace('+', '', $user->first()->phone_country).$user->first()->phone;
                $this->sendSMS($subject, $smsMessage, $phone);
            }

        }
        if ($template->emailsent == 1) {
            if (isset($valuesArray['temp_email']) && ! empty($valuesArray['temp_email'])) {
                $this->sendMail($subject, $message, $valuesArray['temp_email']);
            } elseif ($user->isNotEmpty()) {
                $this->sendMail($subject, $message, $user->first()->email);
            }
        }
        if ($template->pushsent == 1) {
            if ($user->isNotEmpty() && ! empty($user->first()->fcm)) {
                $this->sendFcmMessage($user->first()->fcm, $subject, $pushMessage, $data);
            }
        }

        // For Admin
        if ($template->adminsmssent == 1) {

            $adminphone = $this->getGeneralSettingValue('general_phone');
            $this->sendSMS($adminsubject, $adminsmsMessage, $adminphone);
        }
        if ($template->adminemailsent == 1) {
            $this->sendMail($adminsubject, $adminmessage, $adminemail);
        }

        // For vendor
        if ($vendorData) {
            if ($template->vendorsmssent == 1) {
                $vendorphone = str_replace('+', '', $vendorData->first()->phone_country).$vendorData->first()->phone;
                $this->sendSMS($vendorsubject, $vendorsmsMessage, $vendorphone);
            }
            if ($template->vendoremailsent == 1) {
                $vendoremail = $vendorData->first()->email;
                $this->sendMail($vendorsubject, $vendormessage, $vendoremail);
            }

            if ($template->vendorpushsent == 1) {
                $vendorNotification = 1;
                if ($vendorData->isNotEmpty() && ! empty($vendorData->first()->fcm)) {
                    $this->sendFcmMessage($vendorData->first()->fcm, $vendorsubject, $vendorpushMessage, $data, $vendorNotification, 'driver');
                }
            }
        }

    }

    private function replaceTemplatePlaceholders($templateString, $valuesArray)
    {
        foreach ($valuesArray as $key => $value) {
            $templateString = str_replace('{{'.$key.'}}', $value, $templateString);
        }

        return $templateString;
    }
}
