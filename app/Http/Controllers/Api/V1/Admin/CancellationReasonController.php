<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\BookingCancellationReason;
use Illuminate\Http\Request;

class CancellationReasonController extends Controller
{
    use MediaUploadingTrait,MiscellaneousTrait,ResponseTrait;

    public function getCancelReasons(Request $request)
    {
        try {
            $userType = $request->input('userType');
            $module = $this->getModuleIdOrDefault($request);
            $reasons = BookingCancellationReason::where('user_type', $userType)
                ->where('status', 1)
                ->where('module', $module)
                ->get();

            return $this->addSuccessResponse(200, trans('global.cancellation_reasons_retrieved_successfully'), ['reasons' => $reasons]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(401, trans('global.failed_to_retrieve_cancellation_reasons'));
        }
    }
}
