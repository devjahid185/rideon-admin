<?php

namespace App\Http\Controllers\Admin\Common\addSteps;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Modern\Item;
use App\Models\Modern\ItemFeatures;
use App\Models\Modern\ItemMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CommonFeaturesController extends Controller
{
    use BookingAvailableTrait, CommonModuleItemTrait, MediaUploadingTrait;

    public function features(Request $request, $id)
    {
        $itemData = Item::where('id', $id)->first();
        $features_ids = explode(',', $itemData->features_id);
        $fits_ids = explode(',', ItemMeta::getMetaValue($id, 'fits'));
        $sizes_ids = explode(',', ItemMeta::getMetaValue($id, 'sizes'));
        $colors_ids = explode(',', ItemMeta::getMetaValue($id, 'colors'));

        $features = ItemFeatures::where('module', $itemData->module)
            ->where(function ($query) {
                $query->whereNull('type')
                    ->orWhere('type', '');
            })
            ->get();

        $fits = ItemFeatures::where('module', $itemData->module)
            ->where('type', 'fit')
            ->get();

        $sizes = ItemFeatures::where('module', $itemData->module)
            ->where('type', 'size')
            ->get();

        $colors = ItemFeatures::where('module', $itemData->module)
            ->where('type', 'color')
            ->get();

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;

        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        $slug = $this->getTheModuleTitle($realRoute);

        $backButtonRoute = 'admin.'.$realRoute.'.location';
        $updateLocationFeature = 'admin.features-Update';
        $nextButton = '/admin/'.$realRoute.'/photos/';
        $leftSideMenu = $this->getLeftSideMenu($module);

        return view('admin.common.addSteps.features.features', compact('id', 'itemData', 'features', 'features_ids', 'backButtonRoute', 'updateLocationFeature', 'nextButton', 'leftSideMenu', 'fits', 'sizes', 'colors', 'fits_ids', 'sizes_ids', 'colors_ids'));
    }

    public function featuresUpdate(Request $request)
    {

        $this->CommonFeaturesUpdate($request);
    }
}
