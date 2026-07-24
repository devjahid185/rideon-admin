<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverFinanceApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait, UserWalletTrait;

    public function getDriverEarings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'offset' => 'nullable|numeric|min:0',
            'limit' => 'nullable|numeric|min:1',
            'startDate' => 'nullable|date',
            'endDate' => 'sometimes|nullable|date|after_or_equal:startDate',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(500, trans('global.token_not_match'), '');
        }
        $hostBookingsQuery = $user->hostBookings()->where('status', 'completed');

        if ($startDate) {
            $hostBookingsQuery->whereDate('ride_date', '>=', $startDate);
        }
        if ($endDate) {
            $hostBookingsQuery->whereDate('ride_date', '<=', $endDate);
        }

        $statsQuery = clone $hostBookingsQuery;
        $totalCommission = $statsQuery->sum('vendor_commission');
        $totalBookings = $hostBookingsQuery->count();
        $hostBookings = $hostBookingsQuery
            ->select('id', 'token', 'ride_date', 'status', 'vendor_commission', 'total')
            ->orderByDesc('ride_date')
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return $this->addSuccessResponse(200, trans('global.vendor_Wallet_amount'), [
            'driverRides' => $hostBookings,
            'offset' => $offset + $hostBookings->count(),
            'totalRides' => $totalBookings,
            'totalEarnings' => $totalCommission,
        ]);
    }
}
