<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRulesRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('item_rule_edit');
    }

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
