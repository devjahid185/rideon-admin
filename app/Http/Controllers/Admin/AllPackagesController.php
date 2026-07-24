<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreAllPackageRequest;
use App\Http\Requests\UpdateAllPackageRequest;
use App\Models\AllPackage;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AllPackagesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('all_package_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AllPackage::query()->select(sprintf('%s.*', (new AllPackage)->table));

            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'all_package_show';
                $editGate = 'all_package_edit';
                $deleteGate = 'all_package_delete';
                $crudRoutePart = 'all-packages';

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
            $table->editColumn('package_name', function ($row) {
                return $row->package_name ? $row->package_name : '';
            });
            $table->editColumn('package_total_day', function ($row) {
                return $row->package_total_day ? $row->package_total_day : '';
            });
            $table->editColumn('package_price', function ($row) {
                return $row->package_price ? $row->package_price : '';
            });
            $table->editColumn('package_image', function ($row) {
                if ($photo = $row->package_image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? AllPackage::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('max_item', function ($row) {
                return $row->max_item ? $row->max_item : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'package_image']);

            return $table->make(true);
        }

        return view('admin.allPackages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('all_package_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.allPackages.create');
    }

    public function store(StoreAllPackageRequest $request)
    {
        $allPackage = AllPackage::create($request->all());

        if ($request->input('package_image', false)) {
            $allPackage->addMedia(storage_path('tmp/uploads/'.basename($request->input('package_image'))))->toMediaCollection('package_image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $allPackage->id]);
        }

        return redirect()->route('admin.all-packages.index');
    }

    public function edit(AllPackage $allPackage)
    {
        abort_if(Gate::denies('all_package_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.allPackages.edit', compact('allPackage'));
    }

    public function update(UpdateAllPackageRequest $request, AllPackage $allPackage)
    {
        $allPackage->update($request->all());

        if ($request->input('package_image', false)) {
            if (! $allPackage->package_image || $request->input('package_image') !== $allPackage->package_image->file_name) {
                if ($allPackage->package_image) {
                    $allPackage->package_image->delete();
                }
                $allPackage->addMedia(storage_path('tmp/uploads/'.basename($request->input('package_image'))))->toMediaCollection('package_image');
            }
        } elseif ($allPackage->package_image) {
            $allPackage->package_image->delete();
        }

        return redirect()->route('admin.all-packages.index');
    }

    public function show(AllPackage $allPackage)
    {
        abort_if(Gate::denies('all_package_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.allPackages.show', compact('allPackage'));
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('all_package_create') && Gate::denies('all_package_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new AllPackage;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('all_package_edit')) {
            return response()->json(['status' => 403, 'message' => "You don't have permission to perform this action."]);
        }

        $product_status = AllPackage::where('id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'status' => 200,
                'message' => 'status updated successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }

    }
}
