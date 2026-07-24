<?php

namespace App\Http\Controllers\Traits;

use App\Models\Payout;

trait PayoutsTrait
{
    public function applyPayouts($userid)
    {
        $payoutStatus = 'Pending';
        $totalPayoutMoney = Payout::where('vendorid', $userid)->where('payout_status', $payoutStatus)->sum('amount');
        $vendorWalletMoney = $this->getVendorWalletBalance($userid);

    }
}
