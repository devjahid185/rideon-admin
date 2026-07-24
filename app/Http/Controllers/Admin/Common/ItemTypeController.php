<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\GeneralSetting;
use App\Models\Modern\ItemCityFare;
use App\Models\Modern\ItemType;
use App\Models\Modern\RentalItemServiceType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ItemTypeController extends Controller
{
    use CommonModuleItemTrait, MediaUploadingTrait;

    public function index(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');
        $itemTypeName = $request->input('name');

        $query = ItemType::where('module', $module)->with('cityFare')->orderBy('id', 'desc');

        $statusCounts = [
            'total' => ItemType::where('module', $module)->count(),
            'active' => ItemType::where('module', $module)->where('status', 1)->count(),
            'inactive' => ItemType::where('module', $module)->where('status', 0)->count(),
        ];

        $isFiltered = ($from || $to || $status || $itemTypeName);

        if ($from && $to) {
            $query->whereBetween('created_at', [
                date('Y-m-d', strtotime($from)).' 00:00:00',
                date('Y-m-d', strtotime($to)).' 23:59:59',
            ]);
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
            }
        }

        if ($itemTypeName) {
            $query->where('name', 'like', '%'.$itemTypeName.'%');
        }

        $itemTypes = $isFiltered ? $query->paginate(50) : ItemType::where('module', $module)->orderBy('id', 'desc')->paginate(50);

        $queryParameters = [];

        if ($from) {
            $queryParameters['from'] = $from;
        }
        if ($to) {
            $queryParameters['to'] = $to;
        }
        if ($status) {
            $queryParameters['status'] = $status;
        }
        if ($itemTypeName) {
            $queryParameters['name'] = $itemTypeName;
        }

        $itemTypes->appends($queryParameters);

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        $createRoute = 'admin.'.$realRoute.'.create';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $trashRoute = 'admin.'.$realRoute.'.trash';
        $ajaxUpdate = '/admin/update-'.$realRoute.'-status';

        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.type.index', compact(
            'createRoute',
            'ajaxUpdate',
            'title',
            'indexRoute',
            'trashRoute',
            'itemTypes',
            'statusCounts',
            'permissionrealRoute',
            'realRoute',
            'general_default_currency'
        ));
    }

    public function create()
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $module = $this->getTheModule($realRoute);

        $storeRoute = 'admin.'.$realRoute.'.store';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);
        $serviceTypes = RentalItemServiceType::where('status', 1)->get();

        return view('admin.common.type.create', compact('storeRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'module', 'title', 'permissionrealRoute', 'serviceTypes'));
    }

    public function store(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceTypes = array_map('strval', $request->input('service_types', []));
        $isDeliverySelected = in_array('2', $serviceTypes, true);

        Log::info('ItemType store request received', [
            'name' => $request->input('name'),
            'service_types' => $serviceTypes,
            'is_delivery_selected' => $isDeliverySelected,
            'has_max_weight' => $request->filled('max_weight'),
            'max_weight' => $request->input('max_weight'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'recommended_fare' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'admin_commission' => 'required|numeric|min:0|max:100',
            'service_types' => 'required|array',
            'max_weight' => $isDeliverySelected ? 'required|numeric|min:0.1' : 'nullable',
        ]);

        if ($validator->fails()) {
            Log::warning('ItemType store validation failed', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->except(['_token']),
            ]);
            return back()->withErrors($validator)->withInput();
        }

        // Store ItemType data
        $ItemType = ItemType::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'max_weight' => $isDeliverySelected ? $request->max_weight : null,
        ]);

        $ItemType->serviceTypes()->sync($request->service_types);
        // Store ItemCityFare data
        $ItemCityFare = ItemCityFare::create([
            'item_type_id' => $ItemType->id, // Associate with the ItemType model
            'min_fare' => $request->min_fare,
            'max_fare' => $request->max_fare,
            'recommended_fare' => $request->recommended_fare,
            'admin_commission' => $request->admin_commission,
        ]);

        if ($request->input('image', false)) {
            $ItemType->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $ItemType->id]);
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function edit($itemType)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ItemType = ItemType::where('id', $itemType)->first();
        $updateRoute = 'admin.'.$realRoute.'.update';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);
        $serviceTypes = RentalItemServiceType::where('status', 1)->get();

        return view('admin.common.type.edit', compact('ItemType', 'updateRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title', 'serviceTypes'));
    }

    public function update(Request $request, $itemType)
    {

        $request->validate([
            'recommended_fare' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'admin_commission' => 'required|numeric|min:0|max:100',
            'service_types' => 'required|array',
            'max_weight' => in_array(2, $request->service_types ?? []) ? 'required|numeric|min:0.1' : 'nullable',
        ]);

        $ItemType = ItemType::where('id', $itemType)->first();
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $ItemType->update(array_merge(
            $request->all(),
            [
                'max_weight' => in_array(2, $request->service_types ?? []) ? $request->max_weight : null,
            ]
        ));
        $ItemType->serviceTypes()->sync($request->service_types);

        if ($request->input('image', false)) {
            if (! $ItemType->image || $request->input('image') !== $ItemType->image->file_name) {
                if ($ItemType->image) {
                    $ItemType->image->delete();
                }
                $ItemType->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($ItemType->image) {
            $ItemType->image->delete();
        }
        $cityFareData = [
            'min_fare' => $request->input('min_fare'),
            'max_fare' => $request->input('max_fare'),
            'recommended_fare' => $request->input('recommended_fare'),
            'admin_commission' => $request->input('admin_commission'),
        ];

        if ($ItemType->cityFare) {
            $ItemType->cityFare->update($cityFareData);
        } else {
            $ItemType->cityFare()->create($cityFareData);
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function show($ItemType)
    {
        $itemType = ItemType::where('id', $ItemType)->first();

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $indexRoute = 'admin.'.$realRoute.'.index';

        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.type.show', compact('itemType', 'indexRoute', 'title'));
    }

    public function destroy_bkp($itemType)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $itemType = ItemType::find($itemType);
        // $ItemType->delete();
        $itemType->deleteItemType();

        return back();
    }

    public function destroy($itemType)
    {

        $itemType = ItemType::findOrFail($itemType);

        if ($itemType) {
            $itemType->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item type deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Item Type Not found',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    public function storeCKEditorImages(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        abort_if(Gate::denies($permissionrealRoute.'_create') && Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new ItemType;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('vehicle_type_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }
        $product_status = ItemType::where('id', $request->pid)->update(['status' => $request->status]);
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

    public function bulkDelete(Request $request)
    {
        abort_if(Gate::denies('vehicle_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                $deletedCount = 0;

                foreach ($ids as $id) {
                    $item = ItemType::findOrFail($id);

                    // Delete associated media (image and gallery)
                    if ($item->image) {
                        $item->image->delete();
                        $item->clearMediaCollection('image');
                    }

                    if ($item->gallery) {
                        $item->gallery->each(function (Media $media) {
                            $media->delete();
                        });
                        $item->clearMediaCollection('image');
                    }

                    // Delete the item
                    $item->forceDelete();
                    $deletedCount++;
                }

                return response()->json(['message' => trans('global.successfully_deleted', ['count' => $deletedCount])], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => trans('global.something_wrong')], 500);
            }
        }

        return response()->json(['message' => trans('global.no_entries_selected')], 400);
    }
}
