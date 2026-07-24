<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SosNumber;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SOSController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sos_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sosNumbers = SosNumber::orderBy('id', 'desc')->paginate(10);

        return view('admin.sos.index', compact('sosNumbers'));
    }

    public function create()
    {
        abort_if(Gate::denies('sos_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sos.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('sos_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => 'required|string|max:255',
            'sos_number' => 'required|string|max:255',
            'status' => 'required|in:1,0',
        ]);

        SosNumber::create($request->all());

        return redirect()->route('admin.sos.index')
            ->with('success', trans('global.SOS_added_successfully'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('sos_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sos = SosNumber::findOrFail($id);

        return view('admin.sos.edit', compact('sos'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('sos_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => 'required|string|max:255',
            'sos_number' => 'required|string|max:255',
            'status' => 'required|in:1,0',
        ]);

        $sos = SosNumber::findOrFail($id);
        $sos->update($request->all());

        return redirect()->route('admin.sos.index')
            ->with('success', trans('global.SOS_updated_successfully'));
    }

    public function show($id)
    {
        abort_if(Gate::denies('sos_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sos = SosNumber::findOrFail($id);

        return view('admin.sos.show', compact('sos'));
    }

    public function updateStatus(Request $request)
    {
        abort_if(Gate::denies('sos_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $updated = SosNumber::where('id', $request->id)
            ->update(['status' => $request->status]);

        if ($updated) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => trans('global.something_went_wrong'),
            ]);
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('sos_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sos = SosNumber::find($id);

        if (! $sos) {
            return redirect()->route('admin.sos.index')->with('error', 'SOS record not found');
        }

        $sos->delete();

        return redirect()->route('admin.sos.index')
            ->with('success', trans('global.SOS_deleted_successfully'));
    }

    public function deleteAll(Request $request)
    {
        abort_if(Gate::denies('sos_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {
                SosNumber::whereIn('id', $ids)->delete();

                return response()->json(['message' => trans('global.items_deleted_successfully')], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => trans('global.something_went_wrong')], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
