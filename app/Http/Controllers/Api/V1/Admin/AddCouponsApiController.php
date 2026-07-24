<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Requests\StoreAddCouponRequest;
use App\Http\Requests\UpdateAddCouponRequest;
use App\Http\Resources\Admin\AddCouponResource;
use App\Models\AddCoupon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AddCouponsApiController extends Controller
{
    use MediaUploadingTrait,ResponseTrait;

    public function index()
    {
        abort_if(Gate::denies('add_coupon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AddCouponResource(AddCoupon::all());
    }

    public function store(StoreAddCouponRequest $request)
    {
        $addCoupon = AddCoupon::create($request->all());

        return (new AddCouponResource($addCoupon))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AddCoupon $addCoupon)
    {
        abort_if(Gate::denies('add_coupon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AddCouponResource($addCoupon);
    }

    public function update(UpdateAddCouponRequest $request, AddCoupon $addCoupon)
    {
        $addCoupon->update($request->all());

        return (new AddCouponResource($addCoupon))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(AddCoupon $addCoupon)
    {
        abort_if(Gate::denies('add_coupon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $addCoupon->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function AddCoupon(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'coupon_title' => 'required|string|max:255',
                'coupon_subtitle' => 'nullable|string|max:255',
                'coupon_expiry_date' => 'required|date',
                'coupon_code' => 'required|unique:add_coupons,coupon_code|string|max:50',
                'min_order_amount' => 'required|numeric|min:0',
                'coupon_value' => 'required|numeric|min:0',
                'coupon_description' => 'required|string',
                'status' => 'required',
            ]);
            // print_r($request->all());
            // die;

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            if (AddCoupon::where('coupon_code', $request->coupon_code)->exists()) {

                return $this->errorResponse(401, trans('global.coupon_code_allready_exists'));
            }

            $coupon = new AddCoupon;
            $coupon->coupon_title = $request->coupon_title;
            $coupon->coupon_subtitle = $request->coupon_subtitle;
            $coupon->coupon_expiry_date = date('Y-m-d', strtotime($request->input('coupon_expiry_date'))); // Format the date
            $coupon->coupon_code = $request->coupon_code;
            $coupon->min_order_amount = $request->min_order_amount;
            $coupon->coupon_value = $request->coupon_value;
            $coupon->coupon_description = $request->coupon_description;
            $coupon->status = $request->status;
            $coupon->save();

            return $this->successResponse(200, trans('global.coupon_added_successfully'), ['coupon' => $coupon]);
        } catch (\Exception $e) {

            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function CheckCoupon(Request $request)
    {

        try {
            $checkdata = AddCoupon::where('coupon_code', $request->coupon_code)->first();
            if ($checkdata) {
                return $this->successResponse(200, trans('global.already_coupon_code_exist'), ['coupon' => $checkdata]);
            } else {
                return $this->errorResponse(401, trans('global.coupon_code_not_exist'));
            }

        } catch (\Execption $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }

    }
}
