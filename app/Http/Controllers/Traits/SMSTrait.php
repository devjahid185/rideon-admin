<?php

namespace App\Http\Controllers\Traits;

use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;

trait SMSTrait
{
    public function sendSMS($subject, $message, $mobileNumber)
    {

        $autoFillOtp = GeneralSetting::getMetaValue('auto_fill_otp');
        $smsProviderName = GeneralSetting::getMetaValue('sms_provider_name');

        if ($autoFillOtp) {
            return;
        }
        switch ($smsProviderName) {
            case 'sinch':
                $this->sendViaSinch($message, $mobileNumber);
                break;

            case 'msg91':
                $this->sendViaMSG91($message, $mobileNumber);
                break;

            case 'nonage':
                $this->sendViaNonage($subject, $message, $mobileNumber);
                break;

            case 'twillio':
                $this->sendViaTwilio($message, $mobileNumber);
                break;

                // Add other SMS providers here as needed
            default:
                throw new Exception('SMS provider not supported: '.$smsProviderName);
        }
    }

    private function sendViaSinch($message, $mobileNumber)
    {

        $metaData = GeneralSetting::whereIn('meta_key', ['sinch_service_plan_id', 'sinch_api_token', 'sinch_sender_number'])
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $service_plan_id = $metaData['sinch_service_plan_id'] ?? '';
        $bearer_token = $metaData['sinch_api_token'] ?? '';
        $send_from = $metaData['sinch_sender_number'] ?? '';
        $recipient_phone_numbers = $mobileNumber;

        try {

            if (stristr($recipient_phone_numbers, ',')) {
                $recipient_phone_numbers = explode(',', $recipient_phone_numbers);
            } else {
                $recipient_phone_numbers = [$recipient_phone_numbers];
            }

            $content = [
                'to' => array_values($recipient_phone_numbers),
                'from' => $send_from,
                'body' => $message,
            ];

            $data = json_encode($content);

            $ch = curl_init("https://us.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches");

            if ($ch === false) {
                throw new Exception('Failed to initialize cURL session');
            }

            // Set cURL options
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
            curl_setopt($ch, CURLOPT_XOAUTH2_BEARER, $bearer_token);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Execute cURL request
            $result = curl_exec($ch);

            if ($result === false) {
                throw new Exception('cURL Error: '.curl_error($ch));
            }

            // Print the result
            // echo $result;

            // Close cURL session
            curl_close($ch);
        } catch (Exception $e) {
            // Handle exception
            // echo 'Error: ' . $e->getMessage();
        }
    }

    private function sendViaMSG91($message, $mobileNumber)
    {

        $metaData = GeneralSetting::whereIn('meta_key', ['msg91_auth_key', 'msg91_template_id'])
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $authkey = $metaData['msg91_auth_key'] ?? '';
        $template_id = $metaData['msg91_template_id'] ?? '';

        // msg91
        $url = 'https://control.msg91.com/api/v5/flow';

        $payload = json_encode([
            'template_id' => $template_id,
            'short_url' => '0',
            'realTimeResponse' => '',
            'recipients' => [
                [
                    'mobiles' => $mobileNumber,
                    'MESSAGE' => $message,
                ],
            ],
        ]);

        $headers = [
            'accept: application/json',
            "authkey: $authkey",
            'content-type: application/json',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:'.$err;
        } else {
            echo 'Status Code: '.$statusCode."\n";
            echo 'Response: '.$response."\n";
        }
    }

    private function sendViaNonage($subject, $message, $mobileNumber)
    {
        $metaData = GeneralSetting::whereIn('meta_key', ['messagewizard_key', 'messagewizard_secret'])
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $api_key = $metaData['messagewizard_key'] ?? '';
        $api_secret = $metaData['messagewizard_secret'] ?? '';

        // nexmo
        $currentTime = time();
        $ch = curl_init();

        $data = [
            'from' => $subject,
            'text' => $message,
            'to' => $mobileNumber,
            'api_key' => $api_key,
            'api_secret' => $api_secret,
        ];

        curl_setopt($ch, CURLOPT_URL, 'https://rest.nexmo.com/sms/json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Error: '.curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }

    public function sendViaTwilio($message, $mobileNumber)
    {
        $metaData = GeneralSetting::whereIn('meta_key', ['twillio_key', 'twillio_secret', 'twillio_number'])
            ->pluck('meta_value', 'meta_key')
            ->toArray();

        $accountSid = $metaData['twillio_key'] ?? '';
        $authToken = $metaData['twillio_secret'] ?? '';
        $from = $metaData['twillio_number'] ?? '';
        $to = '+'.$mobileNumber;

        $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json";

        $postFields = http_build_query([
            'From' => $from,
            'To' => $to,
            'Body' => $message,
        ]);

        try {
            // Log request info
            Log::info('Twilio SMS Sending Attempt', [
                'To' => $to,
                'From' => $from,
                'Message' => $message,
            ]);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                Log::error('Twilio SMS cURL Error', [
                    'Error' => $error,
                ]);
            } else {
                Log::info('Twilio SMS Response', [
                    'HTTP Code' => $httpCode,
                    'Response' => $response,
                ]);
            }

            curl_close($ch);
        } catch (Exception $e) {
            Log::error('Twilio SMS Exception', [
                'Message' => $e->getMessage(),
                'Trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
