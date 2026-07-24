<?php

namespace App\Strategies;

use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Http\Controllers\Api\V1\Admin\FoodDeliveryApiController;
use App\Models\AppUser;
use App\Models\Modern\FoodOrder;
use App\Models\Modern\FoodOrderStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeStrategy implements PaymentStrategy
{
    use MiscellaneousTrait, PaymentStatusUpdaterTrait,UserWalletTrait, VendorWalletTrait;

    public function __construct()
    {
        $stripeMode = $this->getGeneralSettingValue('stripe_options');
        $stripeSecretKey = $stripeMode === 'test'
        ? $this->getGeneralSettingValue('test_stripe_secret_key')
        : $this->getGeneralSettingValue('live_stripe_secret_key');
        Stripe::setApiKey($stripeSecretKey);
    }

    public function rechargeWallet($userID, $Amount, $currency, $request)
    {

        $token = $request->input('stripeToken');
        $userToken = $request->input('userToken');
        $userData = AppUser::find($userID);

        $customerName = $userData->first_name.''.$userData->last_name;
        $billingAddress = [
            'line1' => '123 Main St',
            'postal_code' => '90001',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'country' => 'US',
        ];

        $customer = Customer::create([
            'name' => $customerName,
            'address' => $billingAddress,
            'email' => $userData->email,
            'source' => $token,
        ]);

        // Set the charge description
        $chargeDescription = 'Payment for Wallet recharge: '.$userID;

        // Create the charge
        $charge = Charge::create([
            'amount' => $Amount * 100, // Amount in cents
            'currency' => $currency,
            'customer' => $customer->id,
            'description' => $chargeDescription,
        ]);

        if ($charge->status === 'succeeded') {

            $transactionData = new \stdClass;
            $transactionData->response_data = json_encode($charge);
            $transactionData->gateway_name = 'stripe';
            $transactionData->payment_status = 'completed';
            $transactionData->transaction_id = $charge->id;
            $type = 'credit';

            if ($userData->user_type == 'user') {
                $this->addToWallet($userID, $Amount, $type, $chargeDescription, $currency);
            } else {
                $this->addToVendorWallet($userID, $Amount, null, null, $chargeDescription);
            }

            return redirect()->route('wallet_recharge_success', ['userToken' => $userToken]);
        } else {
            return redirect()->route('wallet_recharge_fail', ['userToken' => $userToken]);
        }
        try {
        } catch (ApiErrorException $e) {
            return redirect()->route('wallet_recharge_fail', ['bookingId' => $userToken]);
        }
    }

    public function process($bookingId, $bookingData, $request)
    {

        $token = $request->input('stripeToken');
        $orderId = $request->input('order_id');
        $userData = AppUser::find($bookingData->userid);
        // Retrieve the customer name and billing address from the request
        $customerName = $userData->first_name.' '.$userData->last_name;
        $billingAddress = [
            'line1' => '123 Main St',
            'postal_code' => '90001',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'country' => 'US',
        ];

        $customer = Customer::create([
            'name' => $customerName,
            'address' => $billingAddress,
            'email' => $userData->email,
            'source' => $token,
        ]);

        // Set the charge description
        $chargeDescription = 'Payment for booking: '.$bookingId;

        // Create the charge
        $charge = Charge::create([
            'amount' => $bookingData->amount_to_pay * 100, // Amount in cents
            'currency' => $bookingData->currency_code,
            'customer' => $customer->id,
            'description' => $chargeDescription,
        ]);

        if ($charge->status === 'succeeded') {
            $transactionData = new \stdClass;
            $transactionData->response_data = json_encode($charge);
            $transactionData->gateway_name = 'stripe';
            $transactionData->payment_status = 'completed';
            $transactionData->transaction_id = $charge->id;

            $this->updateBookingStatus($bookingId, $transactionData);

            return redirect()->route('payment_success', ['bookingId' => $bookingId]);
        } else {
            return redirect()->route('payment_fail', ['bookingId' => $bookingId]);
        }
        try {
        } catch (ApiErrorException $e) {
            return redirect()->route('payment_fail', ['bookingId' => $bookingId]);
        }
    }

    public function processFoodOrder(int $orderId, FoodOrder $orderData, $request)
    {
        try {
            $token = $request->input('stripeToken');
            $userData = AppUser::find($orderData->user_id);
            if (! $userData) {
                return redirect()->route('food.payment_fail', ['order' => $orderId]);
            }

            $customer = Customer::create([
                'name' => trim($userData->first_name.' '.$userData->last_name),
                'email' => $userData->email,
                'source' => $token,
            ]);

            $currency = strtolower($this->getGeneralSettingValue('general_default_currency') ?: 'usd');
            $charge = Charge::create([
                'amount' => (int) round(((float) $orderData->total_amount) * 100),
                'currency' => $currency,
                'customer' => $customer->id,
                'description' => 'Payment for food order: '.$orderData->order_number,
                'metadata' => [
                    'food_order_id' => (string) $orderId,
                    'food_order_number' => (string) $orderData->order_number,
                ],
            ]);

            if ($charge->status !== 'succeeded') {
                return redirect()->route('food.payment_fail', ['order' => $orderId]);
            }

            $paidOrder = DB::transaction(function () use ($orderId, $charge) {
                $order = FoodOrder::where('id', $orderId)->lockForUpdate()->firstOrFail();
                if ($order->payment_status === 'paid') {
                    return $order;
                }
                if ($order->status !== FoodOrder::STATUS_PAYMENT_PENDING) {
                    throw new \RuntimeException('Food order is not awaiting payment.');
                }

                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'payment_reference' => $charge->id,
                    'status' => FoodOrder::STATUS_PLACED,
                    'placed_at' => now(),
                ]);

                FoodOrderStatusLog::create([
                    'food_order_id' => $order->id,
                    'from_status' => FoodOrder::STATUS_PAYMENT_PENDING,
                    'to_status' => FoodOrder::STATUS_PLACED,
                    'changed_by_user_id' => $order->user_id,
                    'note' => 'Stripe payment completed; food order placed',
                ]);

                return $order->fresh(['items.foodItem', 'items.variant', 'restaurant', 'branch']);
            });

            (new FoodDeliveryApiController)->notifyFoodOrderPlaced($paidOrder);

            return redirect()->route('food.payment_success', ['order' => $orderId]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe food order payment failed', [
                'food_order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('food.payment_fail', ['order' => $orderId]);
        } catch (\Throwable $e) {
            Log::error('Food order payment finalization failed', [
                'food_order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('food.payment_fail', ['order' => $orderId]);
        }
    }

    public function cancel($bookingId, $bookingData)
    {
        return '/payment_methods?booking='.$bookingId;
    }

    public function return($bookingId, $requestData)
    {
        // Stripe doesn't require a separate return handling
    }

    public function callback($bookingId, $requestData)
    {
        // Stripe doesn't require a separate callback handling
    }

    public function refund($bookingId, $bookingData)
    {
        // Implement the refund logic for Stripe
    }
}
