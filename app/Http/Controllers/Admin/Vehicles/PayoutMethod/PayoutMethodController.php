<?php

namespace App\Http\Controllers\Admin\Vehicles\PayoutMethod;

use App\Http\Controllers\Controller;
use App\Models\PayoutMethod;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class PayoutMethodController extends Controller
{
    public function index(Request $request)
    {

        $realRoute = explode('.', Route::currentRouteName())[1] ?? null;
        $permissionrealRoute = str_replace('-', '_', $realRoute);

        abort_if(Gate::denies($permissionrealRoute.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PayoutMethod::orderBy('id', 'desc');
            $table = DataTables::of($query)
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) use ($permissionrealRoute, $realRoute) {
                $viewGate = '';
                $editGate = null;
                $deleteGate = null;
                $crudRoutePart = $realRoute;

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn ($row) => $row->id ?: '');
            $table->editColumn('name', fn ($row) => $row->name ?: '');
            $table->editColumn('created_at', fn ($row) => $row->created_at ? $row->created_at->format('d-m-Y H:i') : '');
            $table->editColumn('status', fn ($row) => $row->status ? PayoutMethod::STATUS_SELECT[$row->status] : '');
            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        $createRoute = 'admin.'.$realRoute.'.create';
        $indexRoute = 'admin.'.$realRoute.'.index';
        $ajaxUpdate = '/admin/update-payout-method-status';
        $title = 'Payout Method';

        return view('admin.vehicles.payout-method.index', compact(
            'createRoute',
            'ajaxUpdate',
            'title',
            'indexRoute',
            'permissionrealRoute'
        ));
    }

    public function create()
    {
        $moduleId = 2;

        return view('admin.vehicles.payout-method.create', compact('moduleId'));
    }

    public function store(Request $request)
    {

        $vehicleFuelType = PayoutMethod::create($request->all());

        return redirect()->route('admin.payout-method.index');
    }

    public function edit($vehicleFuelType)
    {
        $payoutData = PayoutMethod::where('id', $vehicleFuelType)->first();

        return view('admin.vehicles.payout-method.edit', compact('payoutData', 'vehicleFuelType'));
    }

    public function update(Request $request, $payoutData)
    {

        $payoutMethod = PayoutMethod::where('id', $payoutData)->first();
        $payoutMethod->update($request->all());

        return redirect()->route('admin.payout-method.index');
    }

    public function show($fuelTypeId)
    {

        // $fuelTypeData = PayoutMethod::where('id', $fuelTypeId)->first();
        // return view('admin.vehicles.vehicle-fuel-type.show', compact('fuelTypeData'));
    }

    public function updatePayoutMethodStatus(Request $request)
    {
        if (Gate::denies('payout_method_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }

        $statusUpdate = PayoutMethod::where('id', $request->pid)->update(['status' => $request->status]);
        if ($statusUpdate) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function destroy($id)
    {
        $payoutMethod = PayoutMethod::findOrFail($id);
        $payoutMethod->delete();

        return redirect()->back()->with('success', 'Payout Method deleted successfully.');
    }

    public function deleteAll(Request $request)
    {
        abort_if(Gate::denies('payout_method_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {
                PayoutMethod::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Payout Method deleted successfully'], 200);
            } catch (\Exception $e) {
                \Log::error('Deletion error: '.$e->getMessage());

                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
