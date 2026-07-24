<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use App\Models\Modern\Item;
use App\Models\Modern\ItemType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemTypeApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    /**
     * Get all item categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllCategories(Request $request)
    {
        try {
            $module = $this->getModuleIdOrDefault($request);
            $itemTypes = ItemType::where('status', '1')
                ->where('module', $module)
                ->get()
                ->map(function ($itemTypes) {
                    $imageUrl = isset($itemTypes->image) ? $itemTypes->image->url : null;

                    $name = strtolower($itemTypes->name);
                    $mode = 'driving';

                    if (str_contains($name, 'bike')) {
                        $mode = 'driving';
                    } elseif (str_contains($name, 'bus')) {
                        $mode = 'transit';
                    } elseif (str_contains($name, 'walk')) {
                        $mode = 'walking';
                    }

                    return [
                        'id' => $itemTypes->id,
                        'name' => $itemTypes->name,
                        'description' => $itemTypes->description,
                        'status' => $itemTypes->status,
                        'image' => $itemTypes->image->url ?? null,
                        'fare_per_km' => $itemTypes->cityFare->recommended_fare,
                        'min_fare' => $itemTypes->cityFare->min_fare,
                        'mode' => $itemTypes->mode,
                    ];
                });

            return $this->addSuccessResponse(200, trans('global.Result_found'), ['itemTypes' => $itemTypes]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    /**
     * Get items by ItemType.
     *
     * @return \Illuminate\Http\Response
     */
    public function getItemsByItemType(Request $request)
    {
        try {

            $itemTypeId = $request->item_type_id ?? 0;
            $limit = $request->input('limit', 10) ?? 10;
            $offset = $request->input('offset', 0) ?? 0;
            $module = $this->getModuleIdOrDefault($request);

            // if () {
            if ($module == 4) {
                $user = null;
                if ($request->has('token')) {
                    $user = AppUser::where('token', $request->input('token'))->first();
                }
                $items = Item::where('status', 1)
                    ->where('module', $module)
                    ->whereHas('userid', function ($query) {
                        $query->where('status', 1)
                            ->whereNull('deleted_at');  // This ensures the user isn't soft-deleted
                    })
                    ->when($itemTypeId != 0, function ($query) use ($itemTypeId) {
                        return $query->where('item_type_id', $itemTypeId);
                    })
                    ->orderByDesc('created_at')
                    ->offset($offset)
                    ->take($limit)
                    ->get()
                    ->map(function ($item) use ($user, $request) {
                        $formattedItem = $this->formatItemData($item);
                        $distance = '0';
                        if ($request->has('latitude') && $request->has('longitude') && $item->latitude && $item->latitude) {

                            $distance = $this->calculateDistance($request->input('latitude'), $request->input('longitude'), $item->latitude, $item->latitude);
                            $formattedItem['distance'] = $distance;
                        }

                        if ($user) {
                            $formattedItem['is_in_wishlist'] = $user->itemWishlists()
                                ->where('item_id', $item->id)
                                ->exists();
                        } else {
                            $formattedItem['is_in_wishlist'] = false;
                        }
                        $itemType = ItemType::find($item->item_type_id);
                        $formattedItem['item_type'] = $itemType ? $itemType->name : '';
                        $itemInfoData = json_decode($this->getModuleInfoValues($item->module, $item->id), true);
                        $itemInfoData['distance'] = $distance;
                        $formattedItem['item_info'] = json_encode($itemInfoData);

                        return $formattedItem;
                    })->sortBy('distance', SORT_REGULAR, false)
                    ->values();

                $nextOffset = $offset + count($items);
                if (empty($items)) {
                    $nextOffset = -1;
                }

                $responseData = [
                    'items' => $items,
                    'offset' => $nextOffset,
                    'limit' => $limit,
                ];
            } else {

                $items = Item::where('status', '1')
                    ->where('module', $module)
                    ->whereHas('userid', function ($query) {
                        $query->where('status', 1)
                            ->whereNull('deleted_at');  // This ensures the user isn't soft-deleted
                    })
                    ->when($itemTypeId != 0, function ($query) use ($itemTypeId) {
                        return $query->where('item_type_id', $itemTypeId);
                    })
                    ->skip($offset)
                    ->take($limit)
                    ->get()
                    ->map(function ($item) {
                        return $this->formatItemData($item);
                        if ($user) {
                            $formattedItem['is_in_wishlist'] = $user->itemWishlists()
                                ->where('item_id', $item->id)
                                ->exists();
                        } else {
                            $formattedItem['is_in_wishlist'] = false;
                        }
                    });

                $nextOffset = $offset + count($items);
                if (empty($items)) {
                    $nextOffset = -1;
                }
                // Prepare the response data
                $responseData = [
                    'items' => $items,
                    'offset' => $nextOffset,
                    'limit' => $limit,
                ];
            }

            // try {
            return $this->addSuccessResponse(200, trans('global.Result_found'), $responseData);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function getServiceTypes(Request $request)
    {
        try {
            $module = $this->getModuleIdOrDefault($request);

            $serviceTypes = ItemType::where('status', 1)
                ->where('module', $module)
                ->with('serviceTypes')
                ->get()
                ->pluck('serviceTypes')
                ->flatten()
                ->unique('id')
                ->values()
                ->map(function ($serviceType) {
                    return [
                        'id' => $serviceType->id,
                        'name' => $serviceType->name,
                        'status' => $serviceType->status,
                    ];
                });

            return $this->addSuccessResponse(
                200,
                trans('global.Result_found'),
                ['service_types' => $serviceTypes]
            );
        } catch (\Exception $e) {
            return $this->addErrorResponse(
                500,
                trans('global.something_wrong'),
                $e->getMessage()
            );
        }
    }

    public function getItemTypesByServiceType(Request $request)
    {
        try {
            $request->validate([
                'service_type_id' => 'required|integer|exists:rental_item_service_types,id',
            ]);

            $module = $this->getModuleIdOrDefault($request);
            $serviceTypeId = $request->service_type_id;

            $itemTypes = ItemType::where('status', 1)
                ->where('module', $module)
                ->whereHas('serviceTypes', function ($q) use ($serviceTypeId) {
                    $q->where('rental_item_service_types.id', $serviceTypeId);
                })
                ->with(['cityFare'])
                ->get()
                ->map(function ($itemType) {
                    return [
                        'id' => $itemType->id,
                        'name' => $itemType->name,
                        'description' => $itemType->description,
                        'image' => $itemType->image->url ?? null,
                        'fare_per_km' => $itemType->cityFare->recommended_fare ?? 0,
                        'min_fare' => $itemType->cityFare->min_fare ?? 0,
                        'max_weight' => $itemType->max_weight ?? 0,
                        'mode' => $itemTypes->mode ?? 'driving',
                    ];
                });

            return $this->addSuccessResponse(
                200,
                trans('global.Result_found'),
                ['item_types' => $itemTypes]
            );
        } catch (\Exception $e) {
            return $this->addErrorResponse(
                500,
                trans('global.something_wrong'),
                $e->getMessage()
            );
        }
    }
}
