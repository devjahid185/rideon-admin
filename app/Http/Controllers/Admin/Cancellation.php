<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingCancellationReason;
use App\Models\Module;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cancellation extends Controller
{
    public function cancellation(Request $request)
    {
        $module = Module::where('default_module', '1')->first();
        $moduleId = $module->id;
        $moduleName = $module->name;
        $contactdata = BookingCancellationReason::with('appUser')->orderby('order_cancellation_id', 'desc')->where('module', $module->id)->get();

        return view('admin.cancellation.index', compact('contactdata', 'moduleName'));
    }

    public function cancellationcreate()
    {
        return view('admin.cancellation.create');
    }

    public function cancellationstore(Request $request)
    {
        $Booking = new BookingCancellationReason;
        $Booking->reason = $request->reason;
        $Booking->user_type = $request->user_type;
        $Booking->status = $request->status;
        $Booking->module = $request->module;
        $Booking->save();

        return redirect()->route('admin.cancellation.index');
    }

    public function cancellationedit($order_cancellation_id)
    {
        $cancellationdata = BookingCancellationReason::where('order_cancellation_id', $order_cancellation_id)->first();

        return view('admin.cancellation.edit', compact('cancellationdata'));
    }

    public function cancellationupdate(Request $request, $order_cancellation_id)
    {
        $item = BookingCancellationReason::where('order_cancellation_id', $order_cancellation_id)->first();

        if ($item) {
            $item->update([
                'reason' => $request->input('reason'),
                'status' => $request->input('status'),
                'user_type' => $request->input('user_type'),
                'module' => $request->input('module'),
            ]);

            return redirect()->route('admin.cancellation.index');
        } else {
            return redirect()->route('admin.cancellation.index');
        }
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('cancellation_reason_edit')) {
            return response()->json(['status' => 403, 'message' => "You don't have permission to perform this action."]);
        }

        $product_status = BookingCancellationReason::where('order_cancellation_id', $request->pid)->update(['status' => $request->status]);
        if ($product_status) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }

    }

    public function cancellationdestroy($order_cancellation_id)
    {

        $currentModule = Module::where('default_module', '1')->first();

        $cancellation = BookingCancellationReason::where('order_cancellation_id', $order_cancellation_id)->first();

        if ($cancellation->module == $currentModule->id) {
            $cancellation->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Cancellation reason deleted successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Cancellation reason not found.',
            ]);
        }
    }

    public function deleteCancellationAll(Request $request)
    {
        abort_if(Gate::denies('cancellation_reason_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                BookingCancellationReason::whereIn('order_cancellation_id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                \Log::error('Deletion error: '.$e->getMessage());

                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }
}
