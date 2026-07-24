<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\RentalItemRule;
use Illuminate\Http\Request;

class RentalItemRuleApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    public function index()
    {
        $rules = RentalItemRule::all();

        return response()->json($rules);
    }

    public function getItemRules(Request $request)
    {
        try {
            $module = $this->getModuleIdOrDefault($request);
            $cancellationPolicies = RentalItemRule::where('status', 1)
                ->where('module', $module)
                ->get();

            return $this->addSuccessResponse(200, trans('global.Result_found'), ['booking_rules' => $cancellationPolicies]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.ServerError_internal_server_error'), $e->getMessage());
        }
    }
}
