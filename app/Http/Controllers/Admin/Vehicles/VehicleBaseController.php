<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Models\Modern\Item;
use App\Models\Modern\ItemType;
use App\Models\Modern\ItemVehicle;
use App\Models\SubCategory;
use App\Models\VehicleMake;
use App\Models\VehicleOdometer;
use Illuminate\Http\Request;

class VehicleBaseController extends Controller
{
    use BookingAvailableTrait, CommonModuleItemTrait, MediaUploadingTrait, MiscellaneousTrait;

    public function base(Request $request, $id)
    {

        $itemVehicle = ItemVehicle::where('item_id', $id)->first();
        $YoulistingData = '';

        $YearData = $itemVehicle->year ?? null;
        $TransmissionData = $itemVehicle->transmission ?? null;
        $OdometerData = $itemVehicle->odometer ?? null;

        $vehicleTypeData = ItemType::where('module', 2)->get();
        $vehicleData = Item::where('id', $id)->first();
        $vehicleMakeData = VehicleMake::where('module', 2)->get();

        $MakeData = $vehicleData->category_id;
        $ModelData = $vehicleData->subcategory_id;

        $vehicleOdoMeterData = VehicleOdometer::all();
        if ($YoulistingData) {

        } else {
            $YoulistingData = '';
        }

        return view('admin.vehicles.addVehicle.base', compact('id', 'vehicleMakeData', 'vehicleData', 'vehicleOdoMeterData', 'YoulistingData', 'MakeData', 'ModelData', 'YearData', 'TransmissionData', 'OdometerData', 'vehicleTypeData'));
    }

    public function baseUpdate(Request $request)
    {
        $request->validate([
            'car_type' => 'required|numeric',
            'make' => 'required|numeric',
            'model' => 'required',
            'year' => 'required|numeric',
            'registration_number' => 'required',
        ]);

        $id = $request->input('id');
        $car_type = $request->input('car_type');
        $itemData = Item::findOrFail($id);
        $itemData->update([
            'item_type_id' => $car_type,
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'registration_number' => $request->input('registration_number'),
        ]);

        $data = [
            'year' => $request->input('year'),
        ];

        $identifier = [
            'item_id' => $id,
        ];

        $itemVehicle = ItemVehicle::updateOrCreate($identifier, $data);

        if ($request->input('front_image', false)) {

            if (! $itemData->front_image || $request->input('front_image') !== $itemData->front_image->file_name) {
                if ($itemData->front_image) {
                    $itemData->front_image->delete();
                }
                $itemData->addMedia(storage_path('tmp/uploads/'.basename($request->input('front_image'))))->toMediaCollection('front_image');
            }
        } elseif ($itemData->front_image) {

            $itemData->front_image->delete();

        }
        if ($request->input('vehicle_registration_doc', false)) {

            if (! $itemData->vehicle_registration_doc || $request->input('vehicle_registration_doc') !== $itemData->vehicle_registration_doc->file_name) {
                if ($itemData->vehicle_registration_doc) {
                    $itemData->vehicle_registration_doc->delete();
                }
                $itemData->addMedia(storage_path('tmp/uploads/'.basename($request->input('vehicle_registration_doc'))))->toMediaCollection('vehicle_registration_doc');
            }
        } elseif ($itemData->vehicle_registration_doc) {
            $itemData->vehicle_registration_doc->delete();

        }
        if ($request->input('vehicle_insurance_doc', false)) {

            if (! $itemData->vehicle_insurance_doc || $request->input('vehicle_insurance_doc') !== $itemData->vehicle_insurance_doc->file_name) {
                if ($itemData->vehicle_insurance_doc) {
                    $itemData->vehicle_insurance_doc->delete();
                }
                $itemData->addMedia(storage_path('tmp/uploads/'.basename($request->input('vehicle_insurance_doc'))))->toMediaCollection('vehicle_insurance_doc');
            }
        } elseif ($itemData->vehicle_insurance_doc) {
            $itemData->vehicle_insurance_doc->delete();

        }
    }

    public function getVehicleType(Request $request)
    {
        return;
        $vehicleModelData = SubCategory::where('make_id', $request->make)->get();

        return response()->json($vehicleModelData);

    }

    public function getVehicleMake(Request $request)
    {
        $typeId = $request->input('typeId');

        $query = VehicleMake::where('module', 2);

        if ($typeId) {
            $query->whereHas('makeTypeRelations', function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            });
        }

        $vehicleMakeDataAll = $query->get();

        return response()->json($vehicleMakeDataAll);

    }
}
