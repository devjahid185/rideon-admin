<?php

namespace App\Http\Controllers\Admin\Common\addSteps;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\City;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CommonVehicleLocationController extends Controller
{
    use BookingAvailableTrait,CommonModuleItemTrait,MediaUploadingTrait;

    public function location(Request $request, $id)
    {
        $itemData = Item::where('id', $id)->first();
        $cityData = City::where('module', $itemData->module)->where('status', '1')->get();
        $api_google_map_key = GeneralSetting::where('meta_key', 'api_google_map_key')->first();

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        $slug = $this->getTheModuleTitle($realRoute);

        $backButtonRoute = 'admin.'.$realRoute.'.description';
        $updateLocationRoute = 'admin.location-Update';
        $nextButton = '/admin/'.$realRoute.'/features/';
        $leftSideMenu = $this->getLeftSideMenu($module);

        return view('admin.common.addSteps.location.location', compact('id', 'api_google_map_key', 'itemData', 'cityData', 'backButtonRoute', 'updateLocationRoute', 'nextButton', 'leftSideMenu'));

    }

    public function locationUpdate(Request $request)
    {

        $this->commonnlocationUpdate($request);

    }
}
