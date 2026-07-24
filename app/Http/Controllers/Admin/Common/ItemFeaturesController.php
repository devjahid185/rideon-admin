<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Modern\ItemFeatures;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ItemFeaturesController extends Controller
{
    use CommonModuleItemTrait, MediaUploadingTrait;

    public function index(Request $request)
    {

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ItemFeatures::query()
                ->select(sprintf('%s.*', (new ItemFeatures)->table))
                ->where('module', $module)
                ->where(function ($query) {
                    $query->whereNull('type')
                        ->orWhere('type', '');
                });

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
                )
                );
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('icon', function ($row) {
                if ($photo = $row->icon) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? ItemFeatures::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'icon']);

            return $table->make(true);
        }
        $createRoute = 'admin.'.$realRoute.'.create';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $updateRoute = 'admin.'.$realRoute.'.update';
        $ajaxUpdate = '/admin/update-'.$realRoute.'-status';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.features.index', compact('createRoute', 'ajaxUpdate', 'title', 'indexRoute', 'permissionrealRoute'));

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

        return view('admin.common.features.create', compact('storeRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'module', 'title'));

    }

    public function store(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ItemFeatures = ItemFeatures::create($request->all());

        if ($request->input('icon', false)) {
            $ItemFeatures->addMedia(storage_path('tmp/uploads/'.basename($request->input('icon'))))->toMediaCollection('icon');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $ItemFeatures->id]);
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function edit(ItemFeatures $itemFeatures, $id)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ItemFeatures = ItemFeatures::where('id', $id)->first();
        $updateRoute = 'admin.'.$realRoute.'.update';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.features.edit', compact('ItemFeatures', 'updateRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title'));

    }

    public function destroy($id)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ItemFeatures = ItemFeatures::find($id);
        $ItemFeatures->delete();

        return back();
    }

    public function update(Request $request, $id)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        $itemFeatures = ItemFeatures::where('id', $id)->first();
        $itemFeatures->update($request->all());

        abort_if(Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->input('icon', false)) {
            if (! $itemFeatures->icon || $request->input('icon') !== $itemFeatures->icon->file_name) {
                if ($itemFeatures->icon) {
                    $itemFeatures->icon->delete();
                }
                $itemFeatures->addMedia(storage_path('tmp/uploads/'.basename($request->input('icon'))))->toMediaCollection('icon');
            }
        } elseif ($itemFeatures->icon) {
            $itemFeatures->icon->delete();
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function show($id)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $itemFeatures = ItemFeatures::where('id', $id)->first();

        $indexRoute = 'admin.'.$realRoute.'.index';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.features.show', compact('itemFeatures', 'indexRoute', 'title', 'permissionrealRoute'));

    }

    public function storeCKEditorImages(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create') && Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new ItemFeatures;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('vehicle_features_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }
        $features_status = ItemFeatures::where('id', $request->pid)->update(['status' => $request->status]);
        if ($features_status) {
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

    public function featuresDelete(Request $request)
    {
        abort_if(Gate::denies('vehicle_features_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                ItemFeatures::whereIn('id', $ids)->delete();

                return response()->json(['message' => trans('global.successfully_deleted')], 200);
            } catch (Exception $e) {
                return response()->json(['message' => trans('global.something_wrong')], 500);
            }
        }

        return response()->json(['message' => trans('global.no_entries_selected')], 400);
    }
}
