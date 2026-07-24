<?php

namespace App\Http\Controllers\Traits;

use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\RentalItemRule;
use Illuminate\Http\Request;

trait CommonModuleItemTrait
{
    use MiscellaneousTrait;

    public function getItemRule($module)
    {
        $itemRules = RentalItemRule::where('module', $module)->get();

        return $itemRules;
    }

    public function commonnlocationUpdate(Request $request)
    {

        $request->validate([
            'id' => 'required|numeric',
            'state' => 'required|string',
            'address_line_1' => 'required|string',
        ]);
        $id = $request->id;
        $item = Item::findOrFail($id);

        $item->update([
            'country' => $request->country,
            'place_id' => $request->place_id,
            'address' => $request->address_line_1,
            'city' => $request->location,
            'state_region' => $request->state,
            'city_name' => $request->city,
            'zip_postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        $itemMetaInfo = $this->getModuleInfoValues('', $id);

        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($id, $data);

        $this->updateStepCompleted($id, 'location', true);
    }

    public function CommonFeaturesUpdate(Request $request)
    {
        $request->validate([
            'features' => 'required|array|min:1',
        ]);
        $id = $request->id;
        $selectedFeatures = implode(',', $request->input('features', []));
        $item = Item::findOrFail($id);
        if ($item->module == 5) {
            $sizes = implode(',', $request->input('sizes', []));
            $colors = implode(',', $request->input('colors', []));
            $fits = implode(',', $request->input('fits', []));
            $data = [
                'sizes' => $sizes,
                'colors' => $colors,
                'fits' => $fits,
            ];

            $this->addOrUpdateItemMeta($id, $data);
        }

        $item = Item::findOrFail($id);

        $item->update([
            'features_id' => $selectedFeatures,
        ]);
        $itemMetaInfo = $this->getModuleInfoValues('', $id);

        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($id, $data);

        $this->updateStepCompleted($id, 'features', true);
    }

    public function CommonPhotosUpdate(Request $request)
    {

        $id = $request->id;
        $item = Item::findOrFail($id);
        if ($request->input('front_image', false)) {
            $this->updateStepCompleted($item->id, 'photos', true);
            if (! $item->front_image || $request->input('front_image') !== $item->front_image->file_name) {
                if ($item->front_image) {
                    $item->front_image->delete();
                }
                $item->addMedia(storage_path('tmp/uploads/'.basename($request->input('front_image'))))->toMediaCollection('front_image');
            }
        } elseif ($item->front_image) {

            $item->front_image->delete();
            $this->updateStepCompleted($item->id, 'photos', false);
        }
        if ($request->input('front_image_doc', false)) {
            $this->updateStepCompleted($item->id, 'document', true);
            if (! $item->front_image_doc || $request->input('front_image_doc') !== $item->front_image_doc->file_name) {
                if ($item->front_image_doc) {
                    $item->front_image_doc->delete();
                }
                $item->addMedia(storage_path('tmp/uploads/'.basename($request->input('front_image_doc'))))->toMediaCollection('front_image_doc');
            }
        } elseif ($item->front_image_doc) {
            $item->front_image_doc->delete();
            $this->updateStepCompleted($item->id, 'document', false);
        }

        if (count($item->gallery) > 0) {
            foreach ($item->gallery as $media) {
                if (! in_array($media->file_name, $request->input('gallery', []))) {
                    $media->delete();
                }
            }
        }
        $media = $item->gallery->pluck('file_name')->toArray();
        foreach ($request->input('gallery', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $item->addMedia(storage_path('tmp/uploads/'.basename($file)))->toMediaCollection('gallery');
            }
        }
        $itemMetaInfo = $this->getModuleInfoValues('', $id);

        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($id, $data);
    }

    public function CommonCancellationPoliciesUpdate(Request $request)
    {
        $request->validate([
            'rules' => 'required|array|min:1',
        ]);

        $id = $request->input('id');
        // $policy = $request->input('policy');
        $policy = 1;
        $item = Item::findOrFail($id);
        $item->update([
            'booking_policies_id' => $policy,
        ]);
        $selectedRules = implode(',', $request->input('rules', []));
        $itemMetaInfo = $this->getModuleInfoValues('', $id);

        $data = [
            'rules' => $selectedRules,
        ];
        $this->addOrUpdateItemMeta($id, $data);

        $data = [
            'itemMetaInfo' => $itemMetaInfo ?? null,
        ];
        $this->addOrUpdateItemMeta($id, $data);
        $this->updateStepCompleted($id, 'policies', true);
    }

    public function CommanAddConfigurationWizard(Request $request)
    {
        $formData = $request->except('_token', 'general_logo', 'general_favicon');

        if ($request->hasFile('item_setting_image')) {
            $file = $request->file('item_setting_image');
            $fileName = rand(10, 1000000).'.'.$file->getClientOriginalName();
            $destinationPath = 'public/uploads/logo';
            $path = $file->storeAs('logo', $fileName, 'public');
            $formData['item_setting_image'] = $path;
        }

        $module = $request->input('module');

        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['module' => $module, 'meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }
    }

    public function commonCalanderUpdate(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);

        $dateInterval = new \DateInterval('P1D');
        $endDateObj->modify('+1 day');

        $datePeriod = new \DatePeriod($startDateObj, $dateInterval, $endDateObj);
        $module = Item::select('module')->findOrFail($request->item_id)->module;
        $existingRecordMap = $existingRecords->keyBy('date');

        foreach ($datePeriod as $date) {
            $formattedDate = $date->format('Y-m-d');

            if ($existingRecordMap->has($formattedDate)) {

                $existingRecord = $existingRecordMap->get($formattedDate);

                if ($existingRecord->booking_id == 0 || $existingRecord->booking_id == null) {

                    $existingRecord->update([
                        'price' => $request->price,
                        'min_stay' => $request->min_stay,
                        'status' => $request->status,
                        'module' => $module,
                    ]);
                }
            } else {

            }
        }
        $this->updateStepCompleted($request->item_id, 'calendar', true);

        return response()->json(['message' => 'Records updated and added.']);
    }

    public function getTheModule($realRoute)
    {
        $module = collect([
            ['item-location', 1],
            ['item-types', 1],
            ['features', 1],
            ['items-setting', 1],
            ['add-item', 1],
            ['item', 1],
            ['items', 1],

            ['vehicle-features', 2],
            ['vehicle-location', 2],
            ['vehicle-type', 2],
            ['vehicle-setting', 2],
            ['vehicle-makes', 2],
            ['vehicle-model', 2],
            ['vehicles', 2],

        ])->filter(function ($item) use ($realRoute) {
            return $item[0] === $realRoute;
        })->map(function ($item) {
            return $item[1];
        })->first();

        return $module;
    }

    public function getTheModuleTitle($realRoute)
    {
        $module = collect([
            ['item-types', trans('global.itemType_title_singular')],
            ['features',  trans('global.feature_title_singular')],
            ['item-setting', trans('global.item_setting')],
            ['add-item', 'item'],
            ['item', 'item'],
            ['items', 'items'],

            ['vehicle-type', trans('global.vehicle_type')],
            ['vehicle-features', trans('global.vehicle_features')],
            ['vehicle-setting',  trans('global.vehicle_setting')],
            ['vehicle-makes',  trans('global.vehicle_makes')],
            ['vehicle-model', trans('global.vehicle_model')],
            ['vehicles', 'vehicles'],

        ])->filter(function ($item) use ($realRoute) {
            return $item[0] === $realRoute;
        })->map(function ($item) {
            return $item[1];
        })->first();

        return $module;
    }

    public function getLeftSideMenu($module)
    {
        $leftSideMenu = '';
        switch ($module) {
            case 2:
                $leftSideMenu = 'admin.vehicles.addVehicle.vehicle_left_menu';
                break;
        }

        return $leftSideMenu;
    }

    public function getLeftSideMenuVendor($module)
    {
        $leftSideMenu = '';
        switch ($module) {
            case 2:
                $leftSideMenu = 'vendor.vehicles.vehicle_left_menu';
                break;
        }

        return $leftSideMenu;
    }

    public function updateStepCompleted_bkp($itemId, $stepName, $value)
    {
        // Find the vehicle by its ID
        $item = Item::find($itemId);

        if ($item->step_progress === 100) {
            return false; // If step progress is already 100%, do nothing
        }

        // Decode the current JSON value from the steps_completed field
        $steps = json_decode($item->steps_completed, true);

        // Check if the current JSON value is null, initialize structure if necessary
        if (is_null($steps)) {
            $steps = [
                'basic' => false,
                'title' => false,
                'location' => false,
                'features' => false,
                'price' => false,
                'policies' => false,
                'photos' => false,
                'document' => false,
                'calendar' => false,
            ];
        }

        if (isset($steps[$stepName]) && $steps[$stepName] === $value) {
            return false;
        }

        $steps[$stepName] = $value;

        $item->steps_completed = json_encode($steps);

        // Define the increment values
        $incrementForPhotos = 11.11;
        $incrementForDocument = 11.11;

        if ($stepName === 'photos' && $value === true) {

            if ($item->step_progress < 100) {
                $item->step_progress += $incrementForPhotos;
            }
        } elseif ($stepName === 'document' && $value === true) {

            if ($item->step_progress < 100) {
                $item->step_progress += $incrementForDocument;
            }
        } else {

            $totalSteps = count($steps);
            $completedSteps = count(array_filter($steps)); // Count of steps that are true
            $completionPercentage = ($totalSteps > 0) ? ($completedSteps / $totalSteps) * 100 : 0;

            $item->step_progress = $completionPercentage;
        }

        $item->save();

        return true; // Or return any success indication if needed
    }

    public function updateStepCompleted($itemId, $stepName, $value)
    {

        $item = Item::find($itemId);

        if (! $item) {
            return false;
        }

        $steps = json_decode($item->steps_completed, true);

        if (is_null($steps)) {
            $steps = [
                'basic' => false,
                'title' => false,
                'location' => false,
                'features' => false,
                'price' => false,
                'policies' => false,
                'photos' => false,
                'document' => false,
                'calendar' => false,
            ];
        }

        if (isset($steps[$stepName]) && $steps[$stepName] === $value) {
            return false;
        }

        $steps[$stepName] = $value;

        $incrementForPhotos = 11.11;
        $incrementForDocument = 11.11;
        $decrementForPhotos = -11.11;
        $decrementForDocument = -11.11;

        $totalProgressChange = 0;

        if ($steps['photos'] === true) {
            if ($value === false && $stepName === 'photos') {
                $totalProgressChange += $decrementForPhotos;
            } elseif ($value === true && $stepName === 'photos') {
                $totalProgressChange += $incrementForPhotos;
            }
        } elseif ($steps['photos'] === false && $stepName === 'photos') {
            if ($value === true) {
                $totalProgressChange += $incrementForPhotos;
            } elseif ($value === false) {
                $totalProgressChange += $decrementForPhotos;
            }
        }

        if ($steps['document'] === true) {
            if ($value === false && $stepName === 'document') {
                $totalProgressChange += $decrementForDocument;
            } elseif ($value === true && $stepName === 'document') {
                $totalProgressChange += $incrementForDocument;
            }
        } elseif ($steps['document'] === false && $stepName === 'document') {
            if ($value === true) {
                $totalProgressChange += $incrementForDocument;
            } elseif ($value === false) {
                $totalProgressChange += $decrementForDocument;
            }
        }

        $item->step_progress += $totalProgressChange;

        $item->step_progress = min(max($item->step_progress, 0), 100);

        if ($totalProgressChange === 0) {
            $totalSteps = count($steps);
            $completedSteps = count(array_filter($steps)); // Count of steps that are true
            $item->step_progress = ($totalSteps > 0) ? ($completedSteps / $totalSteps) * 100 : 0;
        }

        $item->steps_completed = json_encode($steps);
        $item->save();

        return true;
    }
}
