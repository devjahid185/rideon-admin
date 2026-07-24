<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\ItemControlTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Requests\StoreItemRequest;
use App\Models\AppUser;
use App\Models\City;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Modern\ItemFeatures;
use App\Models\Modern\ItemMeta;
use App\Models\Modern\ItemType;
use App\Models\Module;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    use CommonModuleItemTrait, ItemControlTrait, MediaUploadingTrait, NotificationTrait;

    public function index()
    {

        abort_if(Gate::denies('item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);

        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');
        $item_title = request()->input('title');
        $vendor = request()->input('vendor');
        $typeId = request()->input('type');
        $stepProgressRange = request()->input('step_progress_range');

        $query = Item::where('module', $module)->orderBy('id', 'desc')->with(['userid', 'item_type', 'features', 'place', 'media']);

        $statusCounts = [
            'live' => Item::where('module', $module)->count(),
            'active' => Item::where('module', $module)->where('status', 1)->count(),
            'inactive' => Item::where('module', $module)->where('status', 0)->count(),
            'verified' => Item::where('module', $module)->where('is_verified', 1)->count(),
            'featured' => Item::where('module', $module)->where('is_featured', 1)->count(),
            'trash' => Item::onlyTrashed()->where('module', $module)->count(),
        ];

        $isFiltered = ($from || $to || $status || $item_title || $vendor || $typeId || $stepProgressRange);

        if ($from && $to) {
            $query->whereBetween('created_at', [date('Y-m-d', strtotime($from)).' 00:00:00', date('Y-m-d', strtotime($to)).' 23:59:59']);
        } elseif ($from) {
            $query->where('created_at', '>=', date('Y-m-d', strtotime($from)).' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($to)).' 23:59:59');
        }

        if ($status !== null) {
            if ($status === 'active') {
                $query->where('status', 1);
            } elseif ($status === 'inactive') {
                $query->where('status', 0);
            } elseif ($status === 'verified') {
                $query->where('is_verified', 1);
            } elseif ($status === 'featured') {
                $query->where('is_featured', 1);
            }
        }

        if ($item_title) {
            $query->where('title', 'like', '%'.$item_title.'%');
        }
        if ($vendor) {
            $query->where('userid_id', $vendor);
        }
        if ($typeId) {
            $query->whereHas('item_type', function ($q) use ($typeId) {
                $q->where('id', $typeId);
            });
        }

        if ($stepProgressRange) {
            [$start, $end] = explode('-', $stepProgressRange);
            $query->whereBetween('step_progress', [(float) $start, (float) $end]);
        }

        $items = $isFiltered ? $query->paginate(50) : Item::where('module', $module)->orderBy('id', 'desc')->with(['userid', 'item_type', 'features', 'place', 'media'])->paginate(50);

        $queryParameters = [];

        if ($from != null) {
            $queryParameters['from'] = $from;
        }
        if ($to != null) {
            $queryParameters['to'] = $to;
        }
        if ($status != null) {
            $queryParameters['status'] = $status;
        }

        if ($item_title != null) {
            $queryParameters['item_title'] = $item_title;
        }
        if ($vendor != null) {
            $queryParameters['vendor'] = $vendor;
        }

        if ($from != null && $to != null) {
            $queryParameters['from'] = $from;
            $queryParameters['to'] = $to;
        }
        if ($stepProgressRange != null) {
            $queryParameters['step_progress_range'] = $stepProgressRange;
        }

        if ($item_title != null && $vendor != null && $status != '' && $from != '' && $to != '' && $stepProgressRange != '') {
            $queryParameters['item_title'] = $item_title;
            $queryParameters['vendor'] = $vendor;
            $queryParameters['status'] = $status;
            $queryParameters['to'] = $to;
            $queryParameters['from'] = $from;
            $queryParameters['step_progress_range'] = $stepProgressRange;
        }
        $items->appends($queryParameters);

        $fielddata = request()->input('item_title');
        $fieldname = Item::find($fielddata);
        if ($fieldname) {
            $searchfield = $fieldname->title;
        } else {
            $searchfield = 'All';
        }
        // user
        $fieldname = AppUser::find($vendor);

        if ($fieldname) {
            $vendorname = $fieldname->first_name.' '.$fieldname->last_name.'('.$fieldname->phone.')';
            $vendorId = $fieldname ? $fieldname->id : '';
        } else {

            $vendorname = 'All';
            $vendorId = '';
        }

        $typeNameData = ItemType::find($typeId);

        if ($typeNameData) {
            $typeName = $typeNameData->name;
            $typeId = $typeNameData ? $typeNameData->id : '';
        } else {

            $typeName = 'All';
            $typeId = '';
        }

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        $permissionrealRoute = str_replace('-', '_', $realRoute);
        $trashRoute = 'admin.'.$realRoute.'.trash';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.index', compact('title', 'realRoute', 'statusCounts', 'trashRoute', 'indexRoute', 'items', 'searchfield', 'general_default_currency', 'item_title', 'vendorId', 'vendorname', 'typeName', 'typeId'));
    }

    public function create()
    {

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $title = $this->getTheModuleTitle($realRoute);

        $module = $this->getTheModule($realRoute);

        $userids = AppUser::pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $item_types = ItemType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $features = ItemFeatures::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $places = City::where('module', $module)->where('status', '1')->pluck('city_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.common.create', compact('title', 'realRoute', 'features', 'places', 'item_types', 'userids', 'module'));
    }

    public function store(StoreItemRequest $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $title = $this->getTheModuleTitle($realRoute);

        $module = $this->getTheModule($realRoute);
        $selectedfeatures = implode(',', $request->input('features_id', []));
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
        $stepJson = json_encode($steps);

        $item = Item::create([
            'title' => $request->title,
            'description' => $request->description,
            'userid_id' => $request->userid_id,
            'place_id' => $request->place_id,
            'features' => $selectedfeatures,
            'steps_completed' => $stepJson,
            'module' => $module, // Ensure the module is stored correctly
        ]);
        $newitemId = $item->id;

        $this->updateStepCompleted($item->id, 'title', true);

        return redirect()->to(url('admin/'.$realRoute.'/base').'/'.$newitemId);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden'); //

        $item = Item::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }

    public function trashListings(Request $request)
    {

        abort_if(Gate::denies('item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');
        $items_title = request()->input('title');
        $customer = request()->input('customer');
        $query = Item::onlyTrashed()->where('module', $module)->orderBy('id', 'desc')->with(['userid', 'item_Type', 'features', 'place', 'media']);

        $statusCounts = [
            'live' => Item::where('module', $module)->count(),
            'active' => Item::where('module', $module)->where('status', 1)->count(),
            'inactive' => Item::where('module', $module)->where('status', 0)->count(),
            'verified' => Item::where('module', $module)->where('is_verified', 1)->count(),
            'featured' => Item::where('module', $module)->where('is_featured', 1)->count(),
            'trash' => Item::onlyTrashed()->where('module', $module)->count(),
        ];

        $isFiltered = ($from || $to || $status || $items_title || $customer);

        if ($from && $to) {
            $query->whereBetween('created_at', [date('Y-m-d', strtotime($from)).' 00:00:00', date('Y-m-d', strtotime($to)).' 23:59:59']);
        } elseif ($from) {
            $query->where('created_at', '>=', date('Y-m-d', strtotime($from)).' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($to)).' 23:59:59');
        }

        if ($status !== null) {
            if ($status === 'active') {
                $query->where('status', 1);
            } elseif ($status === 'inactive') {
                $query->where('status', 0);
            } elseif ($status === 'verified') {
                $query->where('is_verified', 1);
            } elseif ($status === 'featured') {
                $query->where('is_featured', 1);
            }
        }

        if ($items_title) {
            $query->where('title', 'like', '%'.$items_title.'%');
        }
        if ($customer) {
            $query->where('userid_id', $customer);
        }

        $items = $isFiltered ? $query->paginate(50) : Item::onlyTrashed()->where('module', $module)->orderBy('id', 'desc')->with(['userid', 'item_Type', 'features', 'place', 'media'])->paginate(50);

        $queryParameters = [];

        if ($from != null) {
            $queryParameters['from'] = $from;
        }
        if ($to != null) {
            $queryParameters['to'] = $to;
        }
        if ($status != null) {
            $queryParameters['status'] = $status;
        }

        if ($items_title != null) {
            $queryParameters['items_title'] = $items_title;
        }
        if ($customer != null) {
            $queryParameters['customer'] = $customer;
        }

        $items->appends($queryParameters);

        $fielddata = request()->input('items_title');
        $fieldname = Item::find($fielddata);
        if ($fieldname) {
            $searchfield = $fieldname->title;
        } else {
            $searchfield = 'All';
        }

        $fieldname = AppUser::find($customer);
        if ($fieldname) {
            $customername = $fieldname->first_name.' '.$fieldname->last_name.'('.$fieldname->phone.')';
        } else {
            $customername = 'All';
        }

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        $permissionrealRoute = str_replace('-', '_', $realRoute);
        $trashRoute = 'admin.'.$realRoute.'.trash';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.trash.trash', compact('title', 'trashRoute', 'realRoute', 'indexRoute', 'items', 'searchfield', 'statusCounts', 'general_default_currency', 'items_title', 'customername', 'module'));
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();

    }

    public function permanentDelete($id)
    {
        $item = Item::onlyTrashed()->find($id);

        if (! $item) {
            return;
        }

        $module_id = $item->module;

        $moduleName = strtolower(Module::where('id', $module_id)->value('name'));
        abort_if(Gate::denies('item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($item->front_image) {
            $item->front_image->delete();
            $item->clearMediaCollection('front_image');
        }
        if ($item->gallery) {
            $item->gallery->each(function (Media $media) {
                $media->delete();
            });
            $item->clearMediaCollection('gallery');
        }
        ItemMeta::where('rental_item_id', $item->id)->delete();

        $item->forceDelete();

        if (! $moduleName) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        return response()->json(['module_name' => $moduleName], 200);
    }

    public function permanentDeleteAll(Request $request)
    {
        $module_id = $request->input('module');

        abort_if(Gate::denies('item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (! $module_id) {
            return response()->json(['error' => 'Module ID is required'], 400);
        }

        $trashedItems = Item::onlyTrashed()->where('module', $module_id)->get();

        foreach ($trashedItems as $item) {

            ItemMeta::where('rental_item_id', $item->id)->delete();
            if ($item->front_image) {
                $item->front_image->delete();
                $item->clearMediaCollection('front_image');
            }

            if ($item->gallery) {
                $item->gallery->each(function (Media $media) {
                    $media->delete();
                });
                $item->clearMediaCollection('gallery');
            }
            $item->forceDelete();
        }

        return response()->json(['success' => 'Selected items permanently deleted']);
    }

    public function deleteRows(Request $request)
    {
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                Item::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }

    public function trashDeleteRows(Request $request)
    {
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                $trashedItems = Item::onlyTrashed()->whereIn('id', $ids)->get();
                foreach ($trashedItems as $item) {
                    ItemMeta::where('rental_item_id', $item->id)->delete();
                    if ($item->front_image) {
                        $item->front_image->delete();
                        $item->clearMediaCollection('front_image');
                    }

                    if ($item->gallery) {
                        $item->gallery->each(function (Media $media) {
                            $media->delete();
                        });
                        $item->clearMediaCollection('gallery');
                    }
                    $item->forceDelete();
                }

                return response()->json(['message' => 'Items deleted from trash successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }

    public function searchItem(Request $request)
    {
        // Get the search term entered by the user
        $searchTerm = $request->input('q');
        $currentModule = Module::where('default_module', '1')->first();

        $item = Item::where('title', 'like', '%'.$searchTerm.'%')->where('module', $currentModule->id)->get();

        $data = [];
        foreach ($item as $item) {

            $data[] = [
                'id' => $item->id,
                'name' => $item->title,
            ];
        }

        return response()->json($data);
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('vehicle_edit')) {
            return response()->json([
                'message' => "You don't have permission to perform this action.",
            ]);
        }
        $item = Item::find($request->pid);
        $product_status = Item::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function updateFeatured(Request $request)
    {
        if (Gate::denies('vehicle_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }
        $product_featured = Item::where('id', $request->pid)->update(['is_featured' => $request->featured]);
        if ($product_featured) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.featured_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function updateVerified(Request $request)
    {
        if (Gate::denies('vehicle_edit')) {
            return response()->json([
                'message' => "You don't have permission to perform this action.",
            ]);
        }

        $product_verified = Item::where('id', $request->pid)->update(['is_verified' => $request->isverified]);
        if ($product_verified) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.verified_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    // In your controller
    public function getIncompleteSteps(Request $request)
    {
        $item = Item::find($request->pid);

        if (! $item) {
            return response()->json([
                'status' => 404,
                'message' => 'Item not found.',
            ]);
        }

        $steps = json_decode($item->steps_completed, true);

        if ($steps !== null && is_array($steps)) {
            // Find incomplete steps
            $incompleteSteps = array_keys(array_filter($steps, function ($step) {
                return ! $step;
            }));

            if (! empty($incompleteSteps)) {
                return response()->json([
                    'status' => 200,
                    'incomplete_steps' => $incompleteSteps,
                ]);
            } else {
                return response()->json([
                    'status' => 204,
                    'incomplete_steps' => [],
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid steps data.',
            ]);
        }
    }

    public function getItemDocuments(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'item_id' => 'required|exists:rental_items,id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => 'Invalid request.', 'errors' => $validator->errors()], 422);
        // }

        try {
            $item = Item::find($request->item_id);

            if (! $item) {
                return response()->json(['status' => false, 'message' => 'Item not found.'], 404);
            }

            $documentFields = [
                'item_driving_licence',
                'item_driver_authorization',
                'item_hire_service_licence',
                'item_inspection_certificate',
            ];

            $documents = [];

            foreach ($documentFields as $field) {
                $image = $item->hasMedia($field) ? $item->getFirstMediaUrl($field) : null;
                $status = ItemMeta::where('rental_item_id', $item->id)
                    ->where('meta_key', $field.'_status')
                    ->value('meta_value') ?? 'pending';

                $documents[$field] = [
                    'image' => $image,
                    'status' => $status,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Item documents retrieved successfully.',
                'data' => [
                    'item_id' => $item->id,
                    'documents' => $documents,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateItemDocumentStatus(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'item_id' => 'required|exists:rental_items,id',
        //     'meta_key' => 'required|string',
        //     'status' => 'required|in:approved,rejected,pending',
        //     'token' => 'required|exists:app_users,token',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        // }

        // $user_id = $this->checkUserByToken($request->token);
        // if (!$user_id) {
        //     return response()->json(['success' => false, 'message' => trans('global.token_not_match')], 401);
        // }

        try {

            // Fetch the item
            $item = Item::where('id', $request->item_id)->first();

            if (! $item) {
                return response()->json(['success' => false, 'message' => trans('global.item_not_found')], 404);
            }

            // Update the item document status in the metadata
            ItemMeta::updateOrCreate(
                ['rental_item_id' => $item->id, 'meta_key' => $request->meta_key.'_status'],
                ['meta_value' => $request->status]
            );

            return response()->json([
                'success' => true,
                'message' => trans('global.status_updated_successfully'),
                'status' => $request->status,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
