<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCancellationpolicyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('cancellation_policies');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'description' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
            ],
            'value' => [
                'required',
            ],
            'status' => [
                'required',
            ],
            'cancellation_time' => [
                'required',
            ],

        ];
    }
}
