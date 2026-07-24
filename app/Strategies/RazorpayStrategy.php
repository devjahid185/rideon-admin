<?php

namespace App\Strategies;

use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use Illuminate\Support\Facades\Log;

class RazorpayStrategy implements PaymentStrategy
{
    use MiscellaneousTrait, PaymentStatusUpdaterTrait;

    private $apiURL;

    private $apiKey;

    private $apiSecret;

    public function __construct()
    {
        $this->apiURL = 'https://api.razorpay.com/v1/orders';
        $environment = $this->getGeneralSettingValue('razorpay_options');
        $this->apiKey = $this->getGeneralSettingValue($environment === 'live' ? 'live_razorpay_key_id' : 'test_razorpay_key_id');
        $this->apiSecret = $this->getGeneralSettingValue($environment === 'live' ? 'live_razorpay_secret_key' : 'test_razorpay_secret_key');

    }

    public function process($bookingId, $bookingData, $request)
    {

        $postFields = [
            'amount' => $bookingData->amount_to_pay * 100,
            'currency' => $bookingData->currency_code,
            'payment_capture' => 1,
            'receipt' => 'order_'.$bookingId,
        ];

        $response = $this->makeCurlRequest($this->apiURL, $postFields);

        if ($response['status'] === 'error') {

            return redirect('/invalid-order')->with('error', $response['message'] ?? 'Payment initiation failed.');
        }

        $result = json_decode($response['data'], true);
        Log::info('Razorpay Response:', $result);
        if (isset($result['status']) && $result['status'] === 'created') {
            Log::info('ifRazorpay Response:', $result);

            return view('Front.razorpay-payment', [
                'bookingId' => $bookingId,
                'orderDetails' => $result,
                'apiKey' => $this->apiKey,
            ]);

        } else {
            Log::info('elseRazorpay Response:', $result);

            return redirect('/invalid-order')->with('error', $result['Message'] ?? 'Payment initiation failed.');
        }
    }

    public function return($bookingId, $request)
    {
        $razorpayResponse = $request->all();

        $paymentId = $request['razorpay_payment_id'] ?? null;

        if (! $paymentId) {
            return view('Front.Fail');
        }

        return $this->handlePaymentStatus($bookingId, $paymentId, $razorpayResponse);
    }

    public function callback($bookingId, $request)
    {

        $paymentId = $request->query('paymentId');

        return $this->handlePaymentStatus($bookingId, $paymentId);
    }

    private function handlePaymentStatus($bookingId, $paymentId, $razorpayResponse)
    {

        if (isset($razorpayResponse['razorpay_payment_id'])) {

            $transactionData = new \stdClass;
            $transactionData->response_data = json_encode($razorpayResponse);
            $transactionData->gateway_name = 'razorpay';
            $transactionData->payment_status = 'completed';
            $transactionData->transaction_id = $paymentId;

            $saveStatus = $this->updateBookingStatus($bookingId, $transactionData);

            $saveStatusData = json_decode($saveStatus, true);

            if ($saveStatusData['status'] === 'success') {

                return '/payment_success';

            } else {
                return '/payment_fail';
                // return redirect('/payment_fail')->with('error', 'Failed to update booking status.');
            }
        } else {
            // Payment failed
            return view('Front.Fail', ['error' => $errorMessage]);
        }
    }

    public function refund($bookingId, $bookingData) {}

    private function makeCurlRequest($url, $postFields)
    {
        $ch = curl_init();
        $postData = json_encode($postFields);
        if ($postData === false) {
            return [
                'status' => 'error',
                'message' => 'JSON encoding error: '.json_last_error_msg(),
            ];
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $apiKey = trim($this->apiKey);
        $apiSecret = trim($this->apiSecret);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode("{$apiKey}:{$apiSecret}"),
        ]);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'status' => 'error',
                'message' => $error,
            ];
        }

        curl_close($ch);

        if ($httpStatus >= 400) {

            return [
                'status' => 'error',
                'message' => "HTTP Error: $httpStatus, Response: $response",
            ];
        }

        return [
            'status' => 'success',
            'data' => $response,
        ];
    }

    public function paymentError()
    {
        return view('Front.Fail');
    }

    public function cancel($bookingId, $bookingData)
    {
        return '/payment_methods?booking='.$bookingId;
    }
}
