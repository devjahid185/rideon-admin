<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\MakeTypeRelation;
use App\Models\Modern\ItemType;
use App\Models\VehicleMake;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ItemMakeController extends Controller
{
    use CommonModuleItemTrait, MediaUploadingTrait;

    public function index(Request $request)
    {

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;

        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $typeId = $request->input('typeId');

        if ($request->ajax()) {

            $query = VehicleMake::query()
                ->select(sprintf('%s.*', (new VehicleMake)->table))
                ->with(['makeTypeRelations.ItemType'])
                ->where('module', $module);

            if ($request->filled('typeId')) {

                $query->whereHas('makeTypeRelations', function ($query) use ($typeId) {
                    $query->where('type_id', $typeId);
                });
            }

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($permissionrealRoute, $realRoute) {
                $viewGate = $permissionrealRoute.'_show';
                $editGate = $permissionrealRoute.'_edit';
                $deleteGate = $permissionrealRoute.'_delete';
                $crudRoutePart = $realRoute;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('make_name', function ($row) {
                return $row->make_name ? $row->make_name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? VehicleMake::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('typeName', function ($row) {
                // Get all type names associated with the make
                $typeNames = $row->makeTypeRelations->pluck('ItemType.name')->toArray();

                return implode(', ', $typeNames);

            });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }
        $createRoute = 'admin.'.$realRoute.'.create';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $ajaxUpdate = '/admin/update-'.$realRoute.'-status';
        $title = $this->getTheModuleTitle($realRoute);
        $types = ItemType::where('module', $module)->get();

        return view('admin.common.make.index', compact('createRoute', 'ajaxUpdate', 'title', 'indexRoute', 'permissionrealRoute', 'types'));
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

        $itemTypes = ItemType::all();

        return view('admin.common.make.create', compact('module', 'storeRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title', 'permissionrealRoute', 'itemTypes'));
    }

    public function store(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catData = VehicleMake::create($request->all());

        if ($request->input('image', false)) {
            $catData->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $catData->id]);
        }
        $itemTypeId = $request->input('item_type');
        $makeId = $catData->id;

        $itemTypes = $request->input('item_types');
        foreach ($itemTypes as $typeId) {
            MakeTypeRelation::create([
                'make_id' => $makeId,
                'type_id' => $typeId,
            ]);
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function edit($catID)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catIData = VehicleMake::where('id', $catID)->first();
        $updateRoute = 'admin.'.$realRoute.'.update';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);

        $itemTypes = ItemType::all();
        $MakeTypeRelation = MakeTypeRelation::where('make_id', $catID)->first();

        $selectedItemTypes = MakeTypeRelation::where('make_id', $catID)->pluck('type_id')->toArray();

        return view('admin.common.make.edit', compact('catIData', 'updateRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title', 'itemTypes', 'selectedItemTypes'));
    }

    public function update(Request $request, $catID)
    {

        $makeData = VehicleMake::where('id', $catID)->first();
        $makeData->update($request->all());
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        if ($request->input('image', false)) {
            if (! $makeData->image || $request->input('image') !== $makeData->image->file_name) {
                if ($makeData->image) {
                    $makeData->image->delete();
                }
                $makeData->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($makeData->image) {
            $makeData->image->delete();
        }

        $itemTypeIds = $request->input('item_types', []);
        $currentTypeIds = MakeTypeRelation::where('make_id', $catID)->pluck('type_id')->toArray();

        // Find item types to add
        $typesToAdd = array_diff($itemTypeIds, $currentTypeIds);
        foreach ($typesToAdd as $typeId) {
            MakeTypeRelation::updateOrCreate([
                'make_id' => $catID,
                'type_id' => $typeId,
            ]);
        }

        // Find item types to remove
        $typesToRemove = array_diff($currentTypeIds, $itemTypeIds);
        MakeTypeRelation::where('make_id', $catID)->whereIn('type_id', $typesToRemove)->delete();

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function show($makeId)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $indexRoute = 'admin.'.$realRoute.'.index';
        $makeData = VehicleMake::where('id', $makeId)->first();
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.make.show', compact('makeData', 'indexRoute', 'title'));
    }

    public function storeCKEditorImages(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        abort_if(Gate::denies($permissionrealRoute.'_create') && Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new VehicleMake;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('vehicle_makes_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }

        $product_status = VehicleMake::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'ststus' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'ststus' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function destroy($catID)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $catData = VehicleMake::find($catID);

        if ($catData) {
            try {

                $catTypeRelations = $catData->MakeTypeRelations;

                if ($catTypeRelations->isNotEmpty()) {
                    foreach ($catTypeRelations as $relation) {
                        $relation->delete();
                    }
                }

                $catData->delete();

                return back()->with('message', trans('global.the_record_has_been_deleted'));
            } catch (\Exception $e) {
                return back()->with('error', trans('global.something_wrong'));
            }
        }

        return back()->with('error', trans('global.no_entries_selected'));
    }

    public function vehicleDelete(Request $request)
    {
        abort_if(Gate::denies('vehicle_makes_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                foreach ($ids as $id) {

                    MakeTypeRelation::whereIn('make_id', $ids)->delete();
                    VehicleMake::find($id)->delete();
                }

                return response()->json(['message' => trans('global.the_record_has_been_deleted')], 200);
            } catch (Exception $e) {
                return response()->json(['message' => trans('global.something_wrong')], 500);
            }
        }

        return response()->json(['message' => trans('global.no_entries_selected')], 400);
    }
}
