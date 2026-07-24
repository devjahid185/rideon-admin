<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyModule;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Models\Module;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        $module = Module::All();

        return view('admin.module.index', compact('module'));
    }

    public function create()
    {
        return view('admin.module.create');
    }

    public function store(StoreModuleRequest $request)
    {
        $module = Module::create($request->all());
        if ($request->input('front_image', false)) {
            if (! $module->front_image || $request->input('front_image') !== $module->front_image->file_name) {
                if ($module->front_image) {
                    $module->front_image->delete();
                }
                $module->addMedia(storage_path('tmp/uploads/'.basename($request->input('front_image'))))->toMediaCollection('front_image');
            }
        } elseif ($module->front_image) {
            $module->front_image->delete();
        }

        return redirect()->route('admin.module.index');
    }

    public function edit(Module $module)
    {

        return view('admin.module.edit', compact('module'));
    }

    public function update(UpdateModuleRequest $request, Module $module)
    {
        $module->update($request->all());
        if ($request->input('front_image', false)) {
            if (! $module->front_image || $request->input('front_image') !== $module->front_image->file_name) {
                if ($module->front_image) {
                    $module->front_image->delete();
                }
                $module->addMedia(storage_path('tmp/uploads/'.basename($request->input('front_image'))))->toMediaCollection('front_image');
            }
        } elseif ($module->front_image) {
            $module->front_image->delete();
        }

        return redirect()->route('admin.module.index');
    }

    public function show(Module $module)
    {
        return view('admin.module.show', compact('module'));
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return back();
    }

    public function massDestroy(MassDestroyModule $request)
    {
        $module = Module::find(request('ids'));

        foreach ($module as $addCoupon) {
            $addCoupon->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updateStatus(Request $request)
    {

        $module = Module::find($request->pid);

        if (! $module) {
            return response()->json([
                'status' => 404,
                'message' => 'Module not found.',
            ]);
        }
        if ($request->status == 1 && $request->type == 'default_module') {
            Module::where('id', '!=', $request->pid)->update(['default_module' => 0]);
        }

        $module->update([$request->type => $request->status]);

        return response()->json([
            'status' => 200,
            'message' => trans('global.'.($request->type == 'default_module' ? 'default_updated_successfully' : 'status_updated_successfully')),
        ]);

    }
}
