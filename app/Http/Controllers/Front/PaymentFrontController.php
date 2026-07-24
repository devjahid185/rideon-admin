<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\FoodOrder;
use App\Strategies\PayduniyaStrategy;
use App\Strategies\PaypalStrategy;
use App\Strategies\RazorpayStrategy;
use App\Strategies\StripeStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Import any other strategies as needed
class PaymentFrontController extends Controller
{
    use MiscellaneousTrait, PaymentStatusUpdaterTrait;

    public function handlePayment(Request $request)
    {
        $bookingId = $request->input('booking');
        $method = $request->input('method');
        $bookingData = Booking::find($bookingId);

        if (! $bookingData) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }
        if ($bookingData->payment_status === 'paid') {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }

        $strategy = $this->getPaymentStrategy($method);

        if (! $strategy) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }

        $returnURL = $strategy->process($bookingId, $bookingData, $request);

        return $returnURL;
    }

    public function handleReturn(Request $request)
    {

        $bookingId = $request->input('booking');
        $method = $request->input('method');

        $strategy = $this->getPaymentStrategy($method);

        if (! $strategy) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }
        // $returnURL = $strategy->return($bookingId, $request->all());
        $returnURL = $strategy->return($bookingId, $request);

        return redirect($returnURL);

    }

    public function handleCallback(Request $request)
    {
        $bookingId = $request->input('booking');
        $method = $request->input('method');
        $strategy = $this->getPaymentStrategy($method);

        if (! $strategy) {
            return response()->json(['error' => 'Invalid payment method'], 400);
        }
        $strategy->callback($bookingId, $request->all());

        return response()->json(['message' => 'Callback processed'], 200);
    }

    public function handleCancel(Request $request)
    {
        $bookingId = $request->input('booking');
        $method = $request->input('method');
        $strategy = $this->getPaymentStrategy($method);
        if (! $strategy) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }
        $returnURL = $strategy->cancel($bookingId, $request->all());

        return redirect($returnURL);
    }

    protected function getPaymentStrategy($method)
    {
        switch ($method) {
            case 'paypal':
                return new PaypalStrategy;
            case 'stripe':
                return new StripeStrategy;
            case 'payduniya':
                return new PayduniyaStrategy;
            case 'razorpay':
                return new RazorpayStrategy;

            default:
                return null;
        }
    }

    public function showPaymentPage(Request $request)
    {

        $bookingId = $request->booking;

        $keys = [
            'stripe_status',
            'paypal_status',
            'paydunya_status',
            'razorpay_status',
        ];

        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $stripe_status = $settings->get('stripe_status') ?? null;
        $paypal_status = $settings->get('paypal_status') ?? null;
        $paydunya_status = $settings->get('paydunya_status') ?? null;
        $razorpay_status = $settings->get('razorpay_status') ?? null;

        $stripeMode = $this->getGeneralSettingValue('stripe_options');
        $stripePublicKey = $stripeMode === 'test'
            ? $this->getGeneralSettingValue('test_stripe_public_key')
            : $this->getGeneralSettingValue('live_stripe_public_key');

        if ($stripe_status->meta_value == 'Active') {
            $status_stripe = true;
        } else {
            $status_stripe = false;
        }

        if ($paypal_status->meta_value == 'Active') {
            $status_paypal = true;
        } else {
            $status_paypal = false;
        }
        $paymentMethods = [
            'stripe' => [
                'active' => $status_stripe,
                'route' => '#',
                'image' => '/front/paymentLogo/Stripe.png',
                'id' => 'stripe-link',
                'public_key' => $stripePublicKey,
                'form' => false,
            ],
            'paypal' => [
                'active' => $status_paypal,
                'route' => route('payment', ['booking' => $bookingId, 'method' => 'paypal']),
                'image' => '/front/paymentLogo/Paypal.png',
                'id' => 'paypal-form',
                'form' => true,
            ],
        ];

        return view('Front.payment', compact('bookingId', 'paymentMethods'));
    }

    public function showFoodPaymentPage(Request $request)
    {
        $foodOrderId = (int) $request->input('order');
        $token = (string) $request->input('token');
        $order = FoodOrder::query()
            ->where('id', $foodOrderId)
            ->where('status', FoodOrder::STATUS_PAYMENT_PENDING)
            ->where('payment_status', 'pending')
            ->whereHas('customer', fn ($query) => $query->where('token', $token))
            ->first();

        if (! $order) {
            return redirect('/invalid-order')->with('error', 'Invalid or expired food payment');
        }

        $stripeStatus = GeneralSetting::where('meta_key', 'stripe_status')->value('meta_value');
        $stripeMode = $this->getGeneralSettingValue('stripe_options');
        $stripePublicKey = $stripeMode === 'test'
            ? $this->getGeneralSettingValue('test_stripe_public_key')
            : $this->getGeneralSettingValue('live_stripe_public_key');

        $paymentMethods = [
            'stripe' => [
                'active' => $stripeStatus === 'Active',
                'route' => '#',
                'image' => '/front/paymentLogo/Stripe.png',
                'id' => 'stripe-link',
                'public_key' => $stripePublicKey,
                'form' => false,
            ],
        ];
        $bookingId = null;

        return view('Front.payment', compact('bookingId', 'foodOrderId', 'paymentMethods'));
    }

    public function handleFoodPayment(Request $request)
    {
        $foodOrderId = (int) $request->input('order');
        $order = FoodOrder::query()
            ->where('id', $foodOrderId)
            ->where('status', FoodOrder::STATUS_PAYMENT_PENDING)
            ->where('payment_status', 'pending')
            ->first();

        if (! $order || ! $request->filled('stripeToken')) {
            return redirect()->route('food.payment_fail', ['order' => $foodOrderId]);
        }

        return (new StripeStrategy)->processFoodOrder($foodOrderId, $order, $request);
    }

    public function foodPaymentSuccess(Request $request)
    {
        $bookingId = $request->input('order');

        return view('Front.Success', compact('bookingId'));
    }

    public function foodPaymentFail(Request $request)
    {
        $bookingId = $request->input('order');

        return view('Front.Fail', compact('bookingId'));
    }

    public function paymentSuccess(Request $request)
    {
        $bookingId = $request->bookingId;

        return view('Front.Success', compact('bookingId'));
    }

    public function paymentFail(Request $request)
    {
        $bookingId = $request->bookingId;

        return view('Front.Fail', compact('bookingId'));
    }

    public function handlePaypalIPN(Request $request)
    {
        $ipnData = $request->all();
        $verify = $this->verifyPaypalIPN($ipnData);

        // $filename = 'ipn_' . date('Y-m-d_H-i-s') . '.txt';
        // $content = json_encode($ipnData, JSON_PRETTY_PRINT);
        // file_put_contents(storage_path('app/ipn/' . $filename), $content);

        if ($verify) {
            $this->processPaypalPayment($ipnData);
        } else {
            Log::error('PayPal IPN verification failed.');
        }
    }

    private function verifyPaypalIPN($ipnData)
    {

        $verifyURL = 'https://www.paypal.com/cgi-bin/webscr';
        $paypalMode = $this->getGeneralSettingValue('paypal_options');
        if ($paypalMode === 'test') {
            $verifyURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }
        $ipnData['cmd'] = '_notify-validate';
        $response = Http::asForm()->post($verifyURL, $ipnData);

        return $response->body() === 'VERIFIED';
    }

    private function processPaypalPayment($ipnData)
    {
        $transactionType = $ipnData['txn_type'];
        $paymentStatus = $ipnData['payment_status'];
        $paymentAmount = $ipnData['mc_gross'];
        $paymentCurrency = $ipnData['mc_currency'];
        $txnId = $ipnData['txn_id'];
        $receiverEmail = $ipnData['receiver_email'];
        $payerEmail = $ipnData['payer_email'];
        $bookingId = $ipnData['custom'];

        // $filename = 'ipn_' . date('Y-m-d_H-i-s') . '.txt';
        // $content = json_encode($ipnData, JSON_PRETTY_PRINT);
        // file_put_contents(storage_path('app/ipn/' . $filename), $content);
        if ($transactionType === 'web_accept') {
            if ($paymentStatus === 'Completed') {
                // Payment is completed
                $booking = Booking::findOrFail($bookingId);
                $transactionData = new \stdClass;
                $transactionData->response_data = json_encode($ipnData);
                $transactionData->gateway_name = 'paypal';
                $transactionData->payment_status = 'completed';
                $transactionData->transaction_id = $txnId;

                if ($booking->payment_status !== 'completed') {
                    $this->updateBookingStatus($bookingId, $transactionData);
                }
            } elseif ($paymentStatus === 'Pending') {
                $booking = Booking::findOrFail($bookingId);

                if ($booking->payment_status !== 'pending') {
                    $transactionData = new \stdClass;
                    $transactionData->response_data = json_encode($ipnData);
                    $transactionData->gateway_name = 'paypal';
                    $transactionData->payment_status = 'pending';
                    $transactionData->transaction_id = $txnId;
                    $this->updateBookingStatus($bookingId, $transactionData);
                }
            }
        } elseif ($transactionType === 'subscr_payment') {

        } elseif ($transactionType === 'subscr_cancel' || $transactionType === 'subscr_eot') {

        } elseif ($transactionType === 'recurring_payment') {

        }

    }
}
