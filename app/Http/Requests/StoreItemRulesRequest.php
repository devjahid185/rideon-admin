<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRulesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rule_name' => [
                'string',
                'required',
            ],
            'status' => [
                'required',
            ],
            'module' => [
                'required',
            ],

        ];
    }
}
