<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePayoutRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return Gate::allows('payout_edit');
    // }

    public function rules()
    {
        return [
            'vendorid' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'amount' => [
                'required',
            ],
            'currency' => [
                'string',
                'nullable',
            ],
            'vendor_name' => [
                'string',
                'nullable',
            ],
            'payment_method' => [
                'string',
                'nullable',
            ],
            'account_number' => [
                'string',
                'nullable',
            ],
        ];
    }
}
