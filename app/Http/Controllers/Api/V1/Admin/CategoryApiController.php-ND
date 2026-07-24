<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Models\AppUser;
use App\Models\Category;
use App\Models\Modern\Item;
use App\Models\Modern\ItemType;
use App\Models\SubCategory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, PaymentStatusUpdaterTrait, ResponseTrait, UserWalletTrait, VendorWalletTrait;

    /**
     * Check the availability of bookings for a item within a given date range.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategories(Request $request)
    {
        try {
            $module = $this->getModuleIdOrDefault($request);

            $categories = Category::where('module', $module)
                ->where('status', '1')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'image' => $category->image->url ?? '',
                    ];
                })
                ->toArray();

            return $this->addSuccessResponse(200, ['categories' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving categories.'], 500);
        }
    }

    public function getSubcategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:rental_item_category,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $categoryId = $request->input('category_id');
            $subcategories = SubCategory::where('make_id', $categoryId)
                ->where('status', '1')
                ->get()
                ->map(function ($subcategories) {
                    return [
                        'id' => $subcategories->id,
                        'name' => $subcategories->name,
                        'image' => $subcategories->image->url ?? '',
                    ];
                })
                ->toArray();

            return response()->json(['subcategories' => $subcategories]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving subcategories.'], 500);
        }
    }

    public function getCategoriesData(Request $request)
    {

        $category_id = $request->category_id ?? 0;
        $subcategory_id = $request->subcategory_id ?? 0;
        $brand_id = $request->brand_id ?? 0;
        $limit = $request->input('limit', 10) ?? 10;
        $offset = $request->input('offset', 0) ?? 0;
        $module = $this->getModuleIdOrDefault($request);

        if ($category_id == 0) {
            $categories = SubCategory::where('id', $subcategory_id)
                ->where('status', '1')
                ->first();

            if ($categories) {
                $category_id = $categories->make_id;
            }
        }

        $user = null;
        if ($request->has('token')) {
            $user = AppUser::where('token', $request->input('token'))->first();
        }

        $brands = Item::select('rental_item_types.id', 'rental_item_types.name')
            ->join('rental_item_types', 'rental_items.item_type_id', '=', 'rental_item_types.id')
            ->where('rental_items.status', 1)
            ->where('rental_items.module', $module)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('app_users')
                    ->whereColumn('rental_items.userid_id', 'app_users.id')
                    ->where('app_users.status', 1)
                    ->whereNull('app_users.deleted_at');
            })
            ->where('rental_items.category_id', $category_id)
            ->whereNull('rental_items.deleted_at')
            ->get()
            ->map(function ($brand) {
                $brandData = $brand->toArray();
                if ($brand->id) {
                    $itemType = ItemType::find($brand->id);
                    if ($itemType && $itemType->image) {
                        $brandData['brandimage'] = $itemType->image->url;
                    } else {
                        $brandData['brandimage'] = '';
                    }
                } else {
                    $brandData['brandimage'] = '';
                }
                unset($brandData['front_image']);
                unset($brandData['gallery']);
                unset($brandData['media']);

                return $brandData;
            })
            ->unique('id');

        $categories = Category::where('id', $category_id)
            ->where('status', '1')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image->url ?? '',
                ];
            })
            ->toArray();

        $otherInfo['title'] = $categories[0]['name'];

        $subcategories = SubCategory::where('make_id', $category_id)
            ->where('status', '1')
            ->get(['id', 'name'])
            ->map(function ($subcategories) {
                return [
                    'id' => $subcategories->id,
                    'name' => $subcategories->name,
                    'image' => $subcategories->image->url ?? '',
                ];
            })
            ->toArray();

        $mergedData = array_merge($categories, $subcategories);

        $items = Item::where('status', 1)
            ->where('module', $module)
            ->whereHas('userid', function ($query) {
                $query->where('status', 1)
                    ->whereNull('deleted_at');  // This ensures the user isn't soft-deleted
            })
            ->when($subcategory_id != 0, function ($query) use ($subcategory_id) {
                return $query->where('subcategory_id', $subcategory_id);
            })
            ->when($category_id != 0, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($brand_id != 0, function ($query) use ($brand_id) {
                return $query->where('item_type_id', $brand_id);
            })

            ->orderByDesc('created_at')
            ->offset($offset)
            ->take($limit)
            ->get()
            ->map(function ($item) use (&$brandData, $user) {
                $formattedItem = $this->formatItemData($item);

                if ($user) {
                    $formattedItem['is_in_wishlist'] = $user->itemWishlists()
                        ->where('item_id', $item->id)
                        ->exists();

                } else {
                    $formattedItem['is_in_wishlist'] = false;

                }

                $itemType = ItemType::find($item->item_type_id);
                $formattedItem['item_type'] = $itemType ? $itemType->name : '';
                $formattedItem['item_info'] = $this->getModuleInfoValues($item->module, $item->id);

                return $formattedItem;
            })
            ->values();

        $nextOffset = $offset + count($items);
        if (empty($items)) {
            $nextOffset = -1;
        }

        $responseData = [
            'items' => $items,
            'brands' => $brands,
            'categories' => $mergedData,
            'otherInfo' => $otherInfo,
            'offset' => $nextOffset,
            'limit' => $limit,
        ];

        return $this->addSuccessResponse(200, trans('global.Result_found'), $responseData);

    }
}
