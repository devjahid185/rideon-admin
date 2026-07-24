<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('contact_create');
    }

    public function rules()
    {
        return [
            'tittle' => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'required',
            ],
            'user' => [
                'required',
            ],
            'status' => [
                'required',
            ],

        ];
    }
}
