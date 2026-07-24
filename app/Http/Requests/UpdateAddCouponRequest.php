<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddCouponRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('add_coupon_edit');
    }

    public function rules()
    {
        return [
            'coupon_title' => [
                'string',
                'required',
            ],
            'coupon_subtitle' => [
                'string',
                'nullable',
            ],
            'coupon_image' => [
                'string',
                'nullable',
            ],
            'coupon_expiry_date' => [
                'date_format:'.config('panel.date_format'),
                'nullable',
            ],
            'coupon_code' => [
                'string',
                'required',
            ],
            'coupon_value' => [
                'required',
            ],
        ];
    }
}
