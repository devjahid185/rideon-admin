<?php

namespace App\Http\Controllers\Admin\Common\addSteps;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Models\AppUser;
use App\Models\Modern\Item;
use App\Models\Modern\ItemMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class TitleDescriptionController extends Controller
{
    use BookingAvailableTrait, CommonModuleItemTrait, MediaUploadingTrait, MiscellaneousTrait;

    public function titleDescription(Request $request, $id)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        $slug = $this->getTheModuleTitle($realRoute);

        $itemData = Item::with('appUser')->where('id', $id)->first();
        $userids = AppUser::pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $backButtonRoute = 'admin.'.$realRoute.'.description';
        $updateLocationRoute = '';
        $nextButton = '/admin/'.$realRoute.'/location/';
        $leftSideMenu = $this->getLeftSideMenu($module);

        return view('admin.common.description', compact('id', 'itemData', 'permissionrealRoute', 'userids', 'backButtonRoute', 'updateLocationRoute', 'nextButton', 'leftSideMenu'));
    }

    public function updateTitleDescription(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'name' => 'required|string',
            'summary' => 'required|string',
            'style_note' => 'nullable|string',
            'chart_image' => 'nullable|string',

        ]);

        $id = $request->id;
        $itemData = Item::findOrFail($id);

        $itemData->update([
            'title' => $request->name,
            'description' => $request->summary,
            'userid_id' => $request->userid_id,
        ]);
        $itemMetaInfo = $this->getModuleInfoValues('', $id);

        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($id, $data);

        // If style_note is provided, save it to Item Meta
        if ($request->has('style_note')) {
            ItemMeta::updateOrCreate(
                ['rental_item_id' => $id, 'meta_key' => 'style_note'],
                ['meta_value' => $request->style_note]
            );
        }

        // If chart_image is provided, save it to ItemMeta
        if ($request->has('chart_image')) {
            ItemMeta::updateOrCreate(
                ['item_id' => $id, 'meta_key' => 'chart_image'],
                ['meta_value' => $request->chart_image]
            );
        }
        $this->updateStepCompleted($id, 'title', true);

        return response()->json(['success' => true]);
    }
}
