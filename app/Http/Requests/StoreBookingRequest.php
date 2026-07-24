<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return Gate::allows('booking_create');
    // }

    public function rules()
    {
        return [
            'itemid' => [
                'string',
                'required',
            ],
            'userid' => [
                'string',
                'required',
            ],
            // 'check_in' => [
            //     'required',
            //     'date_format:',
            // ],
            // 'check_out' => [
            //     'date_format:',
            //     'nullable',
            // ],
            'status' => [
                'string',
                'required',
            ],
            'total_night' => [
                'required',
                'integer',

            ],
            'per_night' => [
                'required',
            ],
            'base_price' => [
                'required',
            ],
            'currency_code' => [
                'string',
                'nullable',
            ],
            'cancellation_reasion' => [
                'string',
                'nullable',
            ],
            'transaction' => [
                'string',
                'nullable',
            ],
            'payment_method' => [
                'string',
                'nullable',
            ],
            'prop_img' => [
                'string',
                'nullable',
            ],
            'prop_title' => [
                'string',
                'nullable',
            ],
            'rating' => [
                'numeric',
            ],
        ];
    }
}
