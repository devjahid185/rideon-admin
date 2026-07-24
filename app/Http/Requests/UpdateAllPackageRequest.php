<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAllPackageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('all_package_edit');
    }

    public function rules()
    {
        return [
            'package_name' => [
                'string',
                'required',
            ],
            'package_total_day' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'package_price' => [
                'required',
            ],
            'status' => [
                'required',
            ],
            'max_item' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
