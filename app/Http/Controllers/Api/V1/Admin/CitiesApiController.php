<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\Admin\CityResource;
use App\Models\City;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CitiesApiController extends Controller
{
    use MediaUploadingTrait,MiscellaneousTrait,ResponseTrait;

    public function index()
    {
        abort_if(Gate::denies('city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CityResource(City::all());
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->all());

        if ($request->input('image', false)) {
            $city->addMedia(storage_path('tmp/uploads/'.basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new CityResource($city))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(City $city)
    {
        abort_if(Gate::denies('city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CityResource($city);
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

        return (new CityResource($city))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    // ///////// API /////////////

    public function yourLocations(Request $request)
    {
        $module = $this->getModuleIdOrDefault($request);

        $cities = City::where('status', '1')->where('module', $module)->get()->map(function ($city) {

            return [
                'id' => $city->id,
                'city_name' => $city->city_name,
                'description' => $city->description,
                'image' => $city->image->url ?? '',
                'latitude' => $city->latitude,
                'country_code' => $city->country_code,
                'longitude' => $city->longtitude,
            ];
        })->toArray();

        return $this->addSuccessResponse(200, trans('global.yourLocations_found'), [
            'Locations' => $cities,
        ]);

    }

    public function searchCities(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $limit = $request->input('limit', 10);

        $query = City::where('status', '1');

        if ($searchTerm) {
            $query->where('city_name', 'like', '%'.$searchTerm.'%');
        }

        $cities = $query->take($limit)->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'city_name' => $city->city_name.', Ivory Coast',
                'description' => $city->description,
                'image' => $city->image->url ?? '',
                'country_code' => $city->country_code,
                'latitude' => $city->latitude,
                'longitude' => $city->longtitude,
            ];
        })->toArray();

        return $this->addSuccessResponse(200, trans('global.locations_found'), [
            'Locations' => $cities,
        ]);
    }
}
