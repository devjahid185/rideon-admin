<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('review_create');
    }

    public function rules()
    {
        return [
            'bookingid' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'item_name' => [
                'required',
            ],
            'item_id' => [
                'required',
            ],
            'guestid' => [
                'string',
                'required',
            ],
            'guest_name' => [
                'string',
                'nullable',
            ],
            'hostid' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'host_name' => [
                'string',
                'nullable',
            ],
            'rating' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
