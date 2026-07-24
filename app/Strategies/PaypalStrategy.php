<?php

namespace App\Strategies;

use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Models\GeneralSetting;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class PaypalStrategy implements PaymentStrategy
{
    use MiscellaneousTrait,PaymentStatusUpdaterTrait;

    private $client;

    public function __construct()
    {
        $generalSettings = GeneralSetting::all();
        $paypalClientId = $this->getGeneralSettingValue('live_paypal_client_id');
        $paypalClientSecret = $this->getGeneralSettingValue('live_paypal_secret_key');

        $paypalTestClientId = $this->getGeneralSettingValue('test_paypal_client_id');
        $paypalTestClientSecret = $this->getGeneralSettingValue('test_paypal_secret_key');

        $paypalMode = $this->getGeneralSettingValue('paypal_options');
        $environment = $paypalMode === 'test'
            ? new SandboxEnvironment($paypalTestClientId, $paypalTestClientSecret)
            : new ProductionEnvironment($paypalClientId, $paypalClientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

    public function process($bookingId, $bookingData, $request)
    {
        $request = new OrdersCreateRequest;
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $bookingData->amount_to_pay,
                    ],
                    'description' => 'Payment for booking: '.$bookingId,
                ],
            ],
            'application_context' => [
                'return_url' => route('handleReturn', ['booking' => $bookingId, 'method' => 'paypal']),
                'cancel_url' => route('handleCancel', ['booking' => $bookingId, 'method' => 'paypal']),
                'notification_url' => route('paypal.ipn'),
            ],
        ];

        try {
            $response = $this->client->execute($request);
            $approvalUrl = $response->result->links[1]->href;

            return redirect($approvalUrl)->with('success', 'Please make payment');

        } catch (HttpException $ex) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }
    }

    public function cancel($bookingId, $bookingData)
    {
        return '/payment_methods?booking='.$bookingId;
    }

    public function return($bookingId, $requestData)
    {
        $request = new OrdersCaptureRequest($requestData['token']);
        $request->prefer('return=representation');

        try {
            $response = $this->client->execute($request);
            $result = $response->result;

            if ($result->status === 'COMPLETED') {
                $transactionData = new \stdClass;
                $transactionData->response_data = json_encode($result);
                $transactionData->gateway_name = 'paypal';
                $transactionData->payment_status = 'completed';
                $transactionData->transaction_id = $result->id;

                $saveStatus = $this->updateBookingStatus($bookingId, $transactionData);
                $saveStatusData = json_decode($saveStatus, true);

                if ($saveStatusData['status'] === 'success') {
                    return '/payment_success';
                } else {
                    return '/payment_fail';
                }
            } else {
                return '/payment_fail';
            }
        } catch (HttpException $ex) {
            return '/payment_fail';
        }
    }

    public function callback($bookingId, $requestData)
    {
        // PayPal doesn't require a separate callback handling
    }

    public function refund($bookingId, $bookingData) {}

    public function handleWebhook(Request $request)
    {
        echo 'yes';
        exit;
        $webhookData = $request->all();
        $eventType = $webhookData['event_type'];

        //     $filename = 'ipn_' . date('Y-m-d_H-i-s') . '.txt';
        // $content = json_encode($webhookData, JSON_PRETTY_PRINT);
        // file_put_contents(storage_path('app/ipn/' . $filename), $content);

        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCaptureCompleted($webhookData);
                break;
            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handlePaymentCaptureRefunded($webhookData);
                break;
                // Handle other event types as needed
            default:
                // Handle unknown event types
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentCaptureCompleted($webhookData)
    {
        $paymentId = $webhookData['resource']['id'];
        $bookingId = $webhookData['resource']['custom_id'];

        $transactionData = new \stdClass;
        $transactionData->response_data = json_encode($webhookData);
        $transactionData->gateway_name = 'paypal';
        $transactionData->payment_status = 'completed';
        $transactionData->transaction_id = $paymentId;

        $this->updateBookingStatus($bookingId, $transactionData);
    }

    private function handlePaymentCaptureRefunded($webhookData)
    {
        $paymentId = $webhookData['resource']['id'];
        $bookingId = $webhookData['resource']['custom_id'];

        $transactionData = new \stdClass;
        $transactionData->response_data = json_encode($webhookData);
        $transactionData->gateway_name = 'paypal';
        $transactionData->payment_status = 'refunded';
        $transactionData->transaction_id = $paymentId;

        $this->updateBookingStatus($bookingId, $transactionData);
    }
}
