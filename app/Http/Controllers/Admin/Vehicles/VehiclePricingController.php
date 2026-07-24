<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Modern\ItemMeta;
use Illuminate\Http\Request;

class VehiclePricingController extends Controller
{
    use BookingAvailableTrait,CommonModuleItemTrait, MediaUploadingTrait;

    public function pricing(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $itemMetas = $item->itemMeta->pluck('meta_value', 'meta_key');

        $night_price = $item->price;
        $serviceType = $item->service_type;

        $weeklyDiscount = $itemMetas->get('weekly_discount', 0);
        $monthlyDiscount = $itemMetas->get('monthly_discount', 0);
        $weeklyDiscountType = $itemMetas->get('weekly_discount_type', 'percent');
        $monthlyDiscountType = $itemMetas->get('monthly_discount_type', 'percent');

        $doorStep = ItemMeta::where('rental_item_id', $id)->where('meta_key', 'doorStep_price')->first();
        $securityFee = ItemMeta::where('rental_item_id', $id)->where('meta_key', 'security_fee')->first();

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.vehicles.addVehicle.pricing', compact('id', 'general_default_currency', 'weeklyDiscount', 'monthlyDiscount', 'serviceType', 'doorStep', 'night_price', 'securityFee', 'item', 'weeklyDiscountType', 'monthlyDiscountType'));
    }

    public function pricesUpdate(Request $request)
    {
        $request->validate([
            'night_price' => 'required|string',
            'service_type' => 'required|string',
        ]);
        $id = $request->input('id');

        $itemData = Item::findOrFail($id);
        $weeklydiscountType = $request->input('weekly_discount_type') ?: 'percent';
        $monthlyDiscountType = $request->input('monthly_discount_type') ?: 'percent';

        $itemData->update([
            'price' => $request->input('night_price'),
            'service_type' => $request->input('service_type'),

        ]);
        $data = [
            'doorStep_price' => $request->input('doorstep_delivery_price'),
            'security_fee' => $request->input('security_fee'),
            'weekly_discount' => $request->input('weekly_discount'),
            'monthly_discount' => $request->input('monthly_discount'),
            'monthly_discount_type' => $monthlyDiscountType,
            'weekly_discount_type' => $weeklydiscountType,
        ];

        $this->addOrUpdateItemMeta($id, $data);

        $itemMetaInfo = $this->getModuleInfoValues($itemData->module, $itemData->id);
        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($itemData->id, $data);

        $this->updateStepCompleted($id, 'price', true);

    }
}
