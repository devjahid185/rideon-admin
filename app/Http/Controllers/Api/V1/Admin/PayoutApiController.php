<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Models\AppUser;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayoutApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait,UserWalletTrait;

    public function getTotalPayoutAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:app_users,token',
        ]);
        if ($validator->fails()) {
            return $this->addErrorResponse(419, trans('global.invalid_token'), $validator->errors());
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(401, trans('global.user_not_found'), 'User not found');
        }
        $payoutStatus = 'Pending';
        $totalPayoutMoney = Payout::where('vendorid', $user->id)->where('payout_status', $payoutStatus)->sum('amount');

        return $this->addSuccessResponse(200, trans('global.Result_found'), ['total_payout_amount' => $totalPayoutAmount]);
    }
}
