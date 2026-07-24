<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('booking_edit');
    }

    public function rules()
    {
        return [
            'userid' => [
                'string',
                'required',
            ],
            'check_in' => [
                'required',
                'date_format:'.config('panel.date_format'),
            ],
            'check_out' => [
                'date_format:'.config('panel.date_format'),
                'nullable',
            ],
            'status' => [
                'string',
                'required',
            ],
            'total_night' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
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
            'rating' => [
                'numeric',
            ],
        ];
    }
}
