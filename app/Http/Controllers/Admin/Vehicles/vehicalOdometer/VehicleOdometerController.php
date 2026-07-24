<?php

namespace App\Http\Controllers\Admin\Vehicles\vehicalOdometer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\VehicleOdometer;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VehicleOdometerController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {

        $VehicleOdometer = VehicleOdometer::where('module', 2)->orderBy('id', 'desc')->get();

        return view('admin.vehicles.vehicle-odometer.index', compact('VehicleOdometer'));
    }

    public function create()
    {
        $moduleId = 2;

        return view('admin.vehicles.vehicle-odometer.create', compact('moduleId'));
    }

    public function store(Request $request)
    {
        $vehicleMake = VehicleOdometer::create($request->all());

        return redirect()->route('admin.vehicle-odometer.index');
    }

    public function edit($vehicleOdometer)
    {
        // abort_if(Gate::denies('vehicle_odometer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $odometerData = VehicleOdometer::where('id', $vehicleOdometer)->first();

        return view('admin.vehicles.vehicle-odometer.edit', compact('odometerData', 'vehicleOdometer'));
    }

    public function update(Request $request, $itemType)
    {

        $itemType = VehicleOdometer::where('id', $itemType)->first();
        $itemType->update($request->all());

        return redirect()->route('admin.vehicle-odometer.index');
    }

    public function show($vehicleId)
    {
        $vehicleData = VehicleOdometer::where('id', $vehicleId)->first();

        return view('admin.vehicles.vehicle-odometer.show', compact('vehicleData'));
    }

    public function updateOdometerStatus(Request $request)
    {

        if (Gate::denies('vehicle_odometer_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }

        $product_status = VehicleOdometer::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'ststus' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'ststus' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }

    }

    public function delete($id)
    {
        abort_if(Gate::denies('vehicle_odometer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $itemType = VehicleOdometer::find($id);
        $itemType->delete();

        return back();
    }

    public function deleteAll(Request $request)
    {

        abort_if(Gate::denies('vehicle_odometer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                VehicleOdometer::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                \Log::error('Deletion error: '.$e->getMessage());

                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
