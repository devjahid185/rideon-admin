<?php

namespace App\Http\Controllers\Front\WalletRecharge;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Strategies\MyFatoorahStrategy;
use App\Strategies\PayduniyaStrategy;
use App\Strategies\PaypalStrategy;
use App\Strategies\StripeStrategy;
use Illuminate\Http\Request;

// Import any other strategies as needed
class WalletRechargeController extends Controller
{
    use MiscellaneousTrait,PaymentStatusUpdaterTrait;

    public function handlePayment(Request $request)
    {
        echo $token = $request->input('token');
        exit;
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
        \Log::info('Return Method Called in handleReturn ', ['request_data' => $request->all()]);
        $bookingId = $request->input('booking');
        $method = $request->input('method');

        $strategy = $this->getPaymentStrategy($method);

        if (! $strategy) {
            return redirect('/invalid-order')->with('error', 'Invalid booking ID');
        }

        // Handle the return process
        $returnURL = $strategy->return($bookingId, $request->all());

        return redirect($returnURL);

    }

    public function handleCallback(Request $request)
    {
        $bookingId = $request->input('booking');
        $method = $request->input('method');
        // $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "/newfilesana.txt", "w") or die ("Unable to open file!");
        // $txt = "John Doe\n";
        // fwrite($myfile, $txt);
        // $txt = "Jane Doe\n";
        // fwrite($myfile, $txt);
        // fclose($myfile);
        $strategy = $this->getPaymentStrategy($method);

        if (! $strategy) {
            return response()->json(['error' => 'Invalid payment method'], 400);
        }

        // Handle the callback process
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
            case 'myfatoorah':
                return new MyFatoorahStrategy;

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
            'myfatoorah_status',
        ];

        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $stripe_status = $settings->get('stripe_status') ?? null;
        $paypal_status = $settings->get('paypal_status') ?? null;
        $paydunya_status = $settings->get('paydunya_status') ?? null;
        $myfatoorah_status = $settings->get('myfatoorah_status') ?? null;

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

        if ($paydunya_status->meta_value == 'Active') {
            $status_payduniya = true;
        } else {
            $status_payduniya = false;
        }
        if ($myfatoorah_status->meta_value == 'Active') {
            $status_myfatoorah = true;
        } else {
            $status_myfatoorah = false;
        }
        $paymentMethods = [
            'stripe' => [
                'active' => $status_stripe, // or false
                'route' => '#', // Since Stripe uses JavaScript, we use a placeholder
                'image' => '/front/paymentLogo/Stripe.png',
                'id' => 'stripe-link',
                'form' => false, // Stripe doesn't use a form
            ],
            'paypal' => [
                'active' => $status_paypal, // or false
                'route' => route('payment', ['booking' => $bookingId, 'method' => 'paypal']),
                'image' => '/front/paymentLogo/Paypal.png',
                'id' => 'paypal-form',
                'form' => true,
            ],
            'payduniya' => [
                'active' => $status_payduniya, // or false
                'route' => route('payment', ['booking' => $bookingId, 'method' => 'payduniya']),
                'image' => '/front/paymentLogo/Payduniya.png',
                'id' => 'payduniya-form',
                'form' => true,
            ],
            'myfatoorah' => [
                'active' => $status_myfatoorah, // or false
                'route' => route('payment', ['booking' => $bookingId, 'method' => 'myfatoorah']),
                'image' => '/front/paymentLogo/my-fatoorah.png',
                'id' => 'myfatoorah-form',
                'form' => true,
            ],
        ];

        return view('Front.payment', compact('bookingId', 'paymentMethods'));
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
        // Get the IPN data from the request
        $ipnData = $request->all();

        // Verify the IPN data by sending it back to PayPal
        $verify = $this->verifyPaypalIPN($ipnData);

        // $filename = 'ipn_' . date('Y-m-d_H-i-s') . '.txt';
        // $content = json_encode($ipnData, JSON_PRETTY_PRINT);
        // file_put_contents(storage_path('app/ipn/' . $filename), $content);

        if ($verify) {
            // IPN is verified, process the payment
            $this->processPaypalPayment($ipnData);
        } else {
            // IPN verification failed, log the error

        }
    }

    private function verifyPaypalIPN($ipnData)
    {
        // Set the PayPal IPN verification URL
        $verifyURL = 'https://www.paypal.com/cgi-bin/webscr';

        $paypalMode = $this->getGeneralSettingValue('paypal_options');
        if ($paypalMode === 'test') {
            $verifyURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        // Add the '_notify-validate' parameter to the IPN data
        $ipnData['cmd'] = '_notify-validate';

        // Send the IPN data back to PayPal for verification
        $response = Http::asForm()->post($verifyURL, $ipnData);

        // Check the response from PayPal
        return $response->body() === 'VERIFIED';
    }

    private function processPaypalPayment($ipnData)
    {
        // Extract the relevant information from the IPN data
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

        // Process the payment based on the transaction type and payment status
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
                // Payment is pending
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
            // Recurring payment
            // Handle recurring payment logic here
            // ...
        }

        // Handle other transaction types and payment statuses as needed
        // ...
    }
    // public function payment_payduniya(Request $request)
    // {
    //      $bookingId = $request->booking;
    //     return view('Front.payment-process',compact('bookingId'));
    // }
    // public function testing(Request $request)
    // {

    //     return view('Front.testing');
    // }

}
