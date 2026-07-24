<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreAllPackageRequest;
use App\Http\Requests\UpdateAllPackageRequest;
use App\Http\Resources\Admin\AllPackageResource;
use App\Models\AllPackage;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class AllPackagesApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('all_package_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AllPackageResource(AllPackage::all());
    }

    public function store(StoreAllPackageRequest $request)
    {
        $allPackage = AllPackage::create($request->all());

        if ($request->input('package_image', false)) {
            $allPackage->addMedia(storage_path('tmp/uploads/'.basename($request->input('package_image'))))->toMediaCollection('package_image');
        }

        return (new AllPackageResource($allPackage))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AllPackage $allPackage)
    {
        abort_if(Gate::denies('all_package_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AllPackageResource($allPackage);
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

        return (new AllPackageResource($allPackage))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
