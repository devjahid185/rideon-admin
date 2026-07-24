<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\SosNumber;

class SOSController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $activeSosNumbers = SosNumber::where('status', '1')
                ->latest()
                ->get();

            return $this->addSuccessResponse(
                200,
                trans('global.SOS_numbers_fetched_successfully'),
                ['sos' => $activeSosNumbers]
            );
        } catch (\Exception $e) {
            return $this->addErrorResponse(
                500,
                trans('global.something_went_wrong'),
                ['error' => $e->getMessage()]
            );
        }
    }
}
