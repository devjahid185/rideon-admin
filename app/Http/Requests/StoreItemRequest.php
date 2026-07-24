<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'gallery' => [
                'array',
            ],
            'person_allowed' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'place_id' => [
                'required',
                'integer',
            ],
            'latitude' => [
                'string',
                'nullable',
            ],
            'longtitude' => [
                'string',
                'nullable',
            ],
            'state_region' => [
                'string',
                'nullable',
            ],
            'zip_postal_code' => [
                'string',
                'nullable',
            ],
        ];
    }
}
