<?php

namespace App\Http\Controllers\Traits;

use App\Models\AppUser;
use App\Models\Wallet;
use DB;

trait UserWalletTrait
{
    use VendorWalletTrait;

    public function addToWallet($user_id, $amount, $type, $description, $currency = 'USD')
    {
        Wallet::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'status' => 1,
            'currency' => $currency,
        ]);

        $this->sendNotificationOnWalletTransaction($user_id, $amount, $type);
    }

    public function deductFromWallet($user_id, $amount, $type, $description, $currency = 'USD')
    {

        Wallet::create([
            'user_id' => $user_id,
            'amount' => -$amount,
            'type' => $type,
            'description' => $description,
            'status' => 1,
            'currency' => $currency,
        ]);

        $this->sendNotificationOnWalletTransaction($user_id, $amount, $type);
    }

    public function getUserWalletBalance($user_id)
    {
        $balance = (string) Wallet::where('user_id', $user_id)->sum('amount');

        return $balance;
    }

    public function addWalletTransaction($user_id, $amount, $type, $description, $currency = 'USD')
    {
        DB::beginTransaction();

        try {
            $user = AppUser::findOrFail($user_id);

            if ($type === 'credit') {
                $user->wallet += $amount;
            } elseif ($type === 'debit') {
                $user->wallet -= $amount;
            }

            // Save the user
            $user->save();

            if ($type === 'credit') {
                $this->addToWallet($user_id, $amount, $type, $description, $currency);
            } elseif ($type === 'debit') {
                $this->deductFromWallet($user_id, $amount, $type, $description, $currency);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getUserWalletTransactionsDetails($user_id)
    {

        $transactions = Wallet::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $transactions;
    }
}
