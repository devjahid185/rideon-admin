<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyAddCouponRequest;
use App\Http\Requests\UpdateAddCouponRequest;
use App\Models\AddCoupon;
use App\Models\GeneralSetting;
use App\Models\Module;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class AddCouponsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('add_coupon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentModule = Module::where('default_module', '1')->first();

        $addCoupons = AddCoupon::where('module', $currentModule->id)
            ->orderByRaw('coupon_expiry_date < NOW()')
            ->orderBy('coupon_expiry_date', 'desc')
            ->get();

        $general_default_currency = cache()->remember(
            'general_default_currency',
            now()->addHours(24),
            fn () => view()->shared('general_default_currency') ?? 'USD'
        );
        $firstBookingCoupon = GeneralSetting::where('meta_key', 'first_booking_coupon')->value('meta_value');

        return view('admin.addCoupons.index', compact('addCoupons', 'general_default_currency', 'firstBookingCoupon'));
    }

    public function create()
    {
        abort_if(Gate::denies('add_coupon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.addCoupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'coupon_value' => [
                'required',
                'numeric',
                'min:0',
                'max:99',
            ],
            'coupon_type' => 'required|in:percentage',
        ]);
        $addCoupon = AddCoupon::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $addCoupon->id]);
        }
        if ($request->has('is_first_booking')) {
            GeneralSetting::updateOrCreate(
                ['meta_key' => 'first_booking_coupon'],
                ['meta_value' => $addCoupon->coupon_code]
            );
        }

        return redirect()->route('admin.add-coupons.index');
    }

    public function edit(AddCoupon $addCoupon)
    {
        abort_if(Gate::denies('add_coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $firstBookingId = GeneralSetting::where('meta_key', 'first_booking_coupon')->value('meta_value');
        $isFirstBooking = $firstBookingId == $addCoupon->coupon_code;

        return view('admin.addCoupons.edit', compact('addCoupon', 'isFirstBooking'));
    }

    public function update(UpdateAddCouponRequest $request, AddCoupon $addCoupon)
    {
        $request->validate([
            'coupon_value' => [
                'required',
                'numeric',
                'min:0',
                'max:99',
            ],
            'coupon_type' => 'required|in:percentage',
        ]);

        $addCoupon->update($request->all());
        if ($request->has('is_first_booking')) {
            GeneralSetting::updateOrCreate(
                ['meta_key' => 'first_booking_coupon'],
                ['meta_value' => $addCoupon->coupon_code]
            );
        }

        return redirect()->route('admin.add-coupons.index');
    }

    public function show(AddCoupon $addCoupon)
    {
        abort_if(Gate::denies('add_coupon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.addCoupons.show', compact('addCoupon'));
    }

    public function destroy(AddCoupon $addCoupon)
    {
        abort_if(Gate::denies('add_coupon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $addCoupon->delete();

        return back();
    }

    public function massDestroy(MassDestroyAddCouponRequest $request)
    {
        $addCoupons = AddCoupon::find(request('ids'));

        foreach ($addCoupons as $addCoupon) {
            $addCoupon->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('add_coupon_create') && Gate::denies('add_coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new AddCoupon;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateStatus(Request $request)
    {
        if (Gate::denies('add_coupon_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You haven't permission to perform this action",
            ]);
        }

        $product_status = AddCoupon::where('id', $request->pid)->update(['status' => $request->status]);
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
}
