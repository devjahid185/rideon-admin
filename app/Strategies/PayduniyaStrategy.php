<?php

namespace App\Strategies;

use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Models\GeneralSetting;

error_reporting(0);
class PayduniyaStrategy implements PaymentStrategy
{
    use PaymentStatusUpdaterTrait;

    public function process($bookingId, $bookingData, $request)
    {

        ob_start();
        $setting = GeneralSetting::where('meta_key', 'general_name')->first();
        $general_name = $setting ? $setting->meta_value : null;
        $url = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create';

        $headers = [
            'Content-Type: application/json',
            'PAYDUNYA-MASTER-KEY: DJn3I34M-mgE3-q4iR-U5ir-9d5DoDzX62R4',
            'PAYDUNYA-PRIVATE-KEY: test_private_9iUppTmxofZtzbRzG928HyaU6pB',
            'PAYDUNYA-TOKEN: SyJS67an38rtT2TrmkiG',
        ];
        // $url = "https://app.paydunya.com/api/v1/checkout-invoice/create";

        // $headers = [
        //     "Content-Type: application/json",
        //     "PAYDUNYA-MASTER-KEY: DJn3I34M-mgE3-q4iR-U5ir-9d5DoDzX62R4",
        //     "PAYDUNYA-PRIVATE-KEY: live_private_g0jzYoylWVUdVfj8E9DoHYB9zaJ",
        //     "PAYDUNYA-TOKEN: uNmCwgbcNNdC4Vqg31Yu"
        // ];

        // print_r($bookingData);
        $postData = [
            'invoice' => [
                'items' => [
                    'item_0' => [
                        'name' => $bookingData->prop_title,
                    ],
                ],
                'total_amount' => $bookingData->amount_to_pay,
                'description' => '',
            ],
            'store' => [
                'name' => $general_name,
                'tagline' => 'Découvrez votre propriété de rêve  avec Tukki',
                'postal_address' => 'ivory coast',
                'phone' => '+41793630851',
                'logo_url' => 'https://tukki-travel.com/wp-content/uploads/elementor/thumbs/footer-logo-q9n2bx7xji4ws3xuyg208g6g8wr1b0qh01vjf8wboi.png',
                'website_url' => 'https://tukki-travel.com/',
            ],
            'custom_data' => [
                'orderID' => $bookingId,
            ],
            'actions' => [
                'cancel_url' => route('handleCancel', ['booking' => $bookingId, 'method' => 'payduniya']),
                'return_url' => route('handleReturn', ['booking' => $bookingId, 'method' => 'payduniya']),
                'callback_url' => route('handleCallback', ['booking' => $bookingId, 'method' => 'payduniya']),
            ],
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $yourApiResponse = curl_exec($ch);
        $response = json_decode($yourApiResponse, true);

        //     print_r($postData);
        //     curl_close($ch);
        //     print_r($postData);
        //     print_r($response);
        //   exit;
        if (isset($response['response_code']) && $response['response_code'] === '00') {

            return redirect($response['response_text'])->with('success', 'Please make payment');
        }

        return redirect('/invalid-order')->with('error', 'Invalid booking ID');

    }

    public function cancel($bookingId, $bookingData)
    {
        return '/payment_methods?booking='.$bookingId;
    }

    public function refund($bookingId, $bookingData)
    {
        // Your PayPal-specific refund code here
    }

    public function return($bookingId, $requestData)
    {
        $invoiceId = $_GET['token'];
        $url = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/confirm/'.$invoiceId;
        $headers = [
            'Content-Type: application/json',
            'PAYDUNYA-MASTER-KEY: DJn3I34M-mgE3-q4iR-U5ir-9d5DoDzX62R4',
            'PAYDUNYA-PRIVATE-KEY: test_private_9iUppTmxofZtzbRzG928HyaU6pB',
            'PAYDUNYA-TOKEN: SyJS67an38rtT2TrmkiG',
        ];
        // $url = "https://app.paydunya.com/api/v1/checkout-invoice/confirm/" . $invoiceId;

        // $headers = [
        //     "Content-Type: application/json",
        //     "PAYDUNYA-MASTER-KEY: DJn3I34M-mgE3-q4iR-U5ir-9d5DoDzX62R4",
        //     "PAYDUNYA-PRIVATE-KEY: live_private_g0jzYoylWVUdVfj8E9DoHYB9zaJ",
        //     "PAYDUNYA-TOKEN: uNmCwgbcNNdC4Vqg31Yu"
        // ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);
        curl_close($ch);
        if ($responseData['response_code'] == '00' && $responseData['status'] == 'completed') {
            $orderID = $responseData['custom_data']['orderID'];
            $tranactonData = new \stdClass;
            $tranactonData->response_data = $response;
            $tranactonData->gateway_name = 'payduniya';
            $tranactonData->payment_status = $responseData['status'];
            $tranactonData->transaction_id = $responseData['invoice']['token'];
            $saveStatus = $this->updateBookingStatus($orderID, $tranactonData);
            $saveStatusData = json_decode($saveStatus, true);
            if ($saveStatusData['status'] === 'success') {
                // $this->reDirectionToNext('/payment_success');
                return '/payment_success';
            } else {
                //  $this->reDirectionToNext('/payment_fail');
                return '/payment_fail';
            }
        } else {
            // $this->reDirectionToNext('/payment_fail');
            return '/payment_fail';
        }

    }

    public function callback($bookingId, $requestData)
    {
        try {

            // file_put_contents('newfile.txt', file_get_contents('php://input'));
            $masterKey = 'DJn3I34M-mgE3-q4iR-U5ir-9d5DoDzX62R4';
            // Take your MasterKey, hash it and compare the result to the received hash by IPN
            if ($_POST['data']['hash'] === hash('sha512', $masterKey)) {

                if ($_POST['data']['status'] == 'completed') {
                    // Do your backoffice treatments here ...

                    $myfile = fopen($_SERVER['DOCUMENT_ROOT'].'/newfile.txt', 'w') or exit('Unable to open file!');
                    $txt = "John Doe\n";
                    fwrite($myfile, $txt);
                    $txt = "Jane Doe\n";
                    fwrite($myfile, $txt);
                    fclose($myfile);
                }

            } else {
                exit('This request was not issued by PayDunya');
            }
        } catch (Exception $e) {
            exit();
        }
    }
}
