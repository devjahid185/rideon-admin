<?php

namespace App\Http\Controllers\Admin\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CommonModuleItemTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Category;
use App\Models\SubCategory;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ItemSubCategoryController extends Controller
{
    use CommonModuleItemTrait, MediaUploadingTrait;

    public function index(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $module = $this->getTheModule($realRoute);
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        abort_if(Gate::denies($permissionrealRoute.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SubCategory::query()->select(sprintf('%s.*', (new SubCategory)->table))->with('make')->where('module', $module);

            // Filter by module_id if present in the request
            if ($request->filled('Category')) {
                $query->where('make_id', $request->input('Category'));
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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('make_name', function ($row) {
                return $row->make ? $row->make->make_name : 'null';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? SubCategory::STATUS_SELECT[$row->status] : '';
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

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }

        $createRoute = 'admin.'.$realRoute.'.create';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $ajaxUpdate = '/admin/update-'.$realRoute.'-status';
        $title = $this->getTheModuleTitle($realRoute);

        // Fetch categories based on the module
        $categories = Category::where('module', $module)->get();

        return view('admin.common.subCategory.index', compact('createRoute', 'ajaxUpdate', 'title', 'indexRoute', 'permissionrealRoute', 'categories', 'realRoute'));
    }

    public function create()
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $module = $this->getTheModule($realRoute);
        $mainCategory = Category::where('module', $module)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $storeRoute = 'admin.'.$realRoute.'.store';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.subCategory.create', compact('module', 'mainCategory', 'storeRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title', 'permissionrealRoute'));
    }

    public function store(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subCategoryData = SubCategory::create($request->all());

        if ($request->input('image', false)) {
            $subCategoryData->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $subCategoryData->id]);
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function edit($subCatID)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $module = $this->getTheModule($realRoute);
        $mainCategory = Category::where('module', $module)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $subCatData = SubCategory::where('id', $subCatID)->where('status', 1)->first();
        $updateRoute = 'admin.'.$realRoute.'.update';
        $storeMediaRoute = 'admin.'.$realRoute.'.storeMedia';
        $storeCKEditorImageRoute = 'admin.'.$realRoute.'.storeCKEditorImages';
        $title = $this->getTheModuleTitle($realRoute);

        return view('admin.common.subCategory.edit', compact('subCatData', 'mainCategory', 'updateRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'title'));
    }

    public function update(Request $request, $subCatID)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $SubCategoryData = SubCategory::where('id', $subCatID)->first();
        $SubCategoryData->update($request->all());

        if ($request->input('image', false)) {
            if (! $SubCategoryData->image || $request->input('image') !== $SubCategoryData->image->file_name) {
                if ($SubCategoryData->image) {
                    $SubCategoryData->image->delete();
                }
                $SubCategoryData->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($SubCategoryData->image) {
            $SubCategoryData->image->delete();
        }

        return redirect()->route('admin.'.$realRoute.'.index');
    }

    public function show($subCatID)
    {

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $indexRoute = 'admin.'.$realRoute.'.index';
        $title = $this->getTheModuleTitle($realRoute);
        $categoryData = SubCategory::where('id', $subCatID)->with('make')->first();

        return view('admin.common.subCategory.show', compact('categoryData', 'indexRoute', 'title'));
    }

    public function storeCKEditorImages(Request $request)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        abort_if(Gate::denies($permissionrealRoute.'_create') && Gate::denies($permissionrealRoute.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sunCatData = new SubCategory;
        $sunCatData->id = $request->input('crud_id', 0);
        $sunCatData->exists = true;
        $media = $sunCatData->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('vehicle_model_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }

        $product_status = SubCategory::where('id', $request->pid)->update(['status' => $request->status]);
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

    public function destroy($subCatID)
    {
        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);
        abort_if(Gate::denies($permissionrealRoute.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $VehicleMake = SubCategory::find($subCatID);

        $VehicleMake->delete();

        return back();
    }

    public function vehicleModelDelete(Request $request)
    {
        abort_if(Gate::denies('vehicle_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                // SubCategory::whereIn('id', $ids)->delete();
                $subCategory = SubCategory::whereIn('id', $ids)->get();

                foreach ($subCategory as $category) {

                    if ($category->image) {
                        $category->image->delete();
                    }

                    $category->clearMediaCollection('image');

                    $category->forceDelete();
                }

                return response()->json(['message' => trans('global.successfully_deleted')], Response::HTTP_OK);
            } catch (\Exception $e) {
                return response()->json(['message' => trans('global.something_wrong')], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response()->json(['message' => trans('global.no_entries_selected')], Response::HTTP_BAD_REQUEST);
    }
}
