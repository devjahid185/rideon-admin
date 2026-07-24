<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\VehicleMake;
use Illuminate\Http\Request;

class MakeApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    public function index()
    {
        $rules = RentalItemRule::all();

        return response()->json($rules);
    }

    public function getMakes(Request $request)
    {

        try {

            $module = $this->getModuleIdOrDefault($request);
            $typeId = $request->input('item_type');
            $query = VehicleMake::where('status', 1)
                ->where('module', $module);

            if ($typeId) {
                $query->whereHas('makeTypeRelations', function ($q) use ($typeId) {
                    $q->where('type_id', $typeId);
                });
            }
            $query->orderBy('name', 'asc');
            // Execute the query and map results
            $vehicleMakes = $query->get()->map(function ($vehicleMake) {
                $vehicleMake->imageURL = isset($vehicleMake->image) ? $vehicleMake->image->url : null;
                unset($vehicleMake->image);
                unset($vehicleMake->media);

                return $vehicleMake;
            });

            return $this->addSuccessResponse(200, trans('global.Result_found'), ['makes' => $vehicleMakes]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.ServerError_internal_server_error'), $e->getMessage());
        }
    }

    public function getMakesModel(Request $request)
    {

        // Retrieve all makes with their associated models

        $module = $this->getModuleIdOrDefault($request);
        $typeId = $request->input('type_id');

        $query = VehicleMake::where('status', 1)
            ->where('module', 2);

        if ($typeId) {

            $query->whereHas('makeTypeRelations', function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            });
        }
        $query->orderBy('name', 'asc');

        $makesWithModels = $query->get();

        return $this->addSuccessResponse(200, trans('global.Result_found'), ['makes' => $makesWithModels]);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.ServerError_internal_server_error'), $e->getMessage());
        }
    }
}
