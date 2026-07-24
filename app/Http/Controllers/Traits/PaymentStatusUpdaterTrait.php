<?php

namespace App\Http\Controllers\Traits;

use App\Models\AppUser;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Transaction;
use Carbon\Carbon;

trait PaymentStatusUpdaterTrait
{
    use NotificationTrait, VendorWalletTrait;

    public function updateBookingStatus($orderID, $responceData)
    {

        $booking = Booking::find($orderID);
        if ($booking) {
            $transaction = new Transaction;
            $transaction->booking_id = $orderID;
            $transaction->transaction_id = $responceData->transaction_id;
            $transaction->amount = $booking->total;
            $transaction->currency_code = $booking->currency_code;
            $transaction->payment_status = $responceData->payment_status;
            $transaction->gateway_name = $responceData->gateway_name;
            $transaction->response_data = $responceData->response_data;
            $transaction->save();
            $booking->payment_status = 'paid';
            $booking->payment_method = $responceData->gateway_name;
            $booking->transaction = $transaction->id;
            $booking->status = 'Completed';
            $booking->save();

            $template_id = 14;
            $valuesArray = [];
            $valuesArray = $this->createNotificationArray($booking->userid, $booking->host_id, $booking->itemid, $orderID);
            $dataVal['message_key'] = $booking;
            $this->sendAllNotifications($valuesArray, $booking->userid, $template_id, $dataVal, $booking->host_id);

            return json_encode(['status' => 'success', 'message' => 'Payment successful']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Invalid booking ID']);
        }
    }

    public function reDirectionToNext($url)
    {

        // Output the HTML with a loader and meta refresh tag
        echo '<html><head>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'">'; // Redirect after 5 seconds
        echo '<style>';
        echo 'body {';
        echo '  display: flex;';
        echo '  align-items: center;';
        echo '  justify-content: center;';
        echo '  height: 100vh;';
        echo '  margin: 0;';
        echo '}';
        echo '.loader {';
        echo '  border: 8px solid #f3f3f3;';
        echo '  border-top: 8px solid #3498db;';
        echo '  border-radius: 50%;';
        echo '  width: 50px;';
        echo '  height: 50px;';
        echo '  animation: spin 2s linear infinite;';
        echo '}';
        echo '@keyframes spin {';
        echo '  0% { transform: rotate(0deg); }';
        echo '  100% { transform: rotate(360deg); }';
        echo '}';
        echo '</style>';
        echo '</head><body>';
        echo '<div class="loader"></div>';
        echo '</body></html>';

        exit;
    }

    public function createNotificationArray($userid = 0, $hostid = 0, $itemid = 0, $bookingid = 0)
    {

        $userData = [];
        $vendorData = [];
        $itemData = [];

        $userData = [];
        $vendorData = [];
        $vendorData = [];

        $userData = AppUser::where('id', $userid)->first();
        $vendorData = AppUser::where('id', $hostid)->first();
        $item = Item::where('id', $itemid)->first();
        $booking = Booking::find($bookingid);
        $settings = GeneralSetting::whereIn('meta_key', ['general_email', 'general_phone', 'general_default_phone_country'])
            ->get()
            ->keyBy('meta_key');

        $general_email = $settings['general_email'] ?? null;
        $general_default_phone_country = $settings['general_default_phone_country'] ?? null;
        $general_phone = $settings['general_phone'] ?? null;

        $valuesArray = [
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'bookingid' => $bookingid,
            'check_in' => $booking->check_in,
            'check_out' => $booking->check_out,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'guests' => $booking->total_guest,
            'total_days' => $booking->total_night,
            'amount' => $booking->amount_to_pay,
            'currency_code' => $booking->currency_code,
            'payment_method' => $booking->payment_method,
            'payment_status' => $booking->payment_status,
            'status' => $booking->status,
            'latitude' => $item->latitude,
            'longitude' => $item->longtitude,
            'item_name' => $item->title,
            'item_address' => $item->address,
            'vendor_name' => $vendorData->first_name.' '.$vendorData->last_name,
            'vendor_phone' => $vendorData->phone,
            'vendor_email' => $vendorData->email,
            'user_email' => $userData->email,
            'user_phone' => $userData->phone,
            'user_phone_country' => $userData->phone_country,
            'phone_country' => $vendorData->phone_country,
            'vendor_phone_country' => $userData->phone_country,
            'support_email' => $general_email->meta_value,
            'support_phone' => $general_default_phone_country->meta_value.$general_phone->meta_value,
            'current_date' => Carbon::now()->format('Y-m-d'),

        ];

        return $valuesArray;
    }
}
