<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CitiesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = City::query()->select(sprintf('%s.*', (new City)->table))->where('module', 1);
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'city_show';
                $editGate = 'city_edit';
                $deleteGate = 'city_delete';
                $crudRoutePart = 'cities';

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
            $table->editColumn('city_name', function ($row) {
                return $row->city_name ? $row->city_name : '';
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
            $table->editColumn('status', function ($row) {
                return $row->status ? City::STATUS_SELECT[$row->status] : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }
        $createRoute = 'admin.cities.create';
        $indexRoute = 'admin.cities.index';
        $updateRoute = 'admin.cities.update';
        $ajaxUpdate = '/admin/update-cities-status';
        $title = trans('global.city_title_singular');

        return view('admin.common.location.index', compact('createRoute', 'ajaxUpdate', 'title', 'indexRoute', 'updateRoute'));
    }

    public function create()
    {
        abort_if(Gate::denies('city_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $module = 1;
        $storeRoute = 'admin.cities.store';
        $storeMediaRoute = 'admin.cities.storeMedia';
        $storeCKEditorImageRoute = 'admin.cities.storeCKEditorImages';

        return view('admin.common.location.create', compact('storeRoute', 'storeMediaRoute', 'storeCKEditorImageRoute', 'module'));
    }

    public function store(StoreCityRequest $request)
    {

        $city = City::create($request->all());

        if ($request->input('image', false)) {
            $city->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $city->id]);
        }

        return redirect()->route('admin.cities.index');
    }

    public function edit(City $city)
    {
        abort_if(Gate::denies('city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // echo '<pre>';
        // print_r($city);
        $updateRoute = 'admin.cities.update';
        $storeMediaRoute = 'admin.cities.storeMedia';
        $storeCKEditorImageRoute = 'admin.cities.storeCKEditorImages';

        return view('admin.common.location.edit', compact('city', 'updateRoute', 'storeMediaRoute', 'storeCKEditorImageRoute'));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->all());

        if ($request->input('image', false)) {
            if (! $city->image || $request->input('image') !== $city->image->file_name) {
                if ($city->image) {
                    $city->image->delete();
                }
                $city->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($city->image) {
            $city->image->delete();
        }

        return redirect()->route('admin.cities.index');
    }

    public function show(City $city)
    {
        abort_if(Gate::denies('city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $city->load('placeItems');
        $indexRoute = 'admin.cities.index';
        $title = trans('global.city_title_singular');

        return view('admin.common.location.show', compact('city', 'indexRoute', 'title'));
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('city_create') && Gate::denies('city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new City;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {

        $product_status = City::where('id', $request->pid)->update(['status' => $request->status]);
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

    public function destroy(City $city)
    {
        abort_if(Gate::denies('city_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $city->delete();

        return back();
    }
}
