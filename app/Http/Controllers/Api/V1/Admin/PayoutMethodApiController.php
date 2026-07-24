<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\OTPTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Models\AppUser;
use App\Models\AppUserMeta;
use App\Models\Payout;
use App\Models\PayoutMethod;
use Illuminate\Http\Request;
use Validator;

class PayoutMethodApiController extends Controller
{
    use EmailTrait, MediaUploadingTrait, MiscellaneousTrait, NotificationTrait, OTPTrait, PushNotificationTrait, ResponseTrait, SMSTrait, UserWalletTrait, VendorWalletTrait;

    public function getPayoutTypes(Request $request)
    {

        $payoutMethods = PayoutMethod::select('id', 'name')
            ->where('status', 1)
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => strtolower($method->name),
                ];
            });

        return $this->addSuccessResponse(
            200,
            trans('front.payment_methods_retrieved_successfully'),
            ['payout_methods' => $payoutMethods]
        );
    }

    public function getPayoutMethods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'token' => 'required|exists:app_users,token',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(404, trans('front.user_not_found'), '');
        }

        $payoutMethods = collect();

        $user->metadata()->get()->each(function ($meta) use ($payoutMethods) {
            $payoutMethod = PayoutMethod::where('name', $meta->meta_key)->first();
            if ($payoutMethod) {
                $decoded = json_decode($meta->meta_value, true);
                $payoutMethods->push([
                    'id' => $payoutMethod->id,
                    'payout_method' => $payoutMethod->name,
                    'details' => $decoded,
                    // 'is_active' => isset($decoded['is_active']) ? (int)$decoded['is_active'] : 0,
                ]);
            }
        });

        return $this->addSuccessResponse(
            200,
            trans('front.payment_methods_retrieved_successfully'),
            [
                'payout_methods' => $payoutMethods,
            ]
        );
    }

    public function updatePayoutMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            // 'token' => 'required|exists:app_users,token',
            'payout_methods' => 'required|array',
            'payout_methods.*.payout_method_id' => 'required|exists:payout_method,id',
            'payout_methods.*.is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(404, trans('front.user_not_found'), '');
        }

        $responseData = [];

        foreach ($request->input('payout_methods') as $methodData) {
            $payoutMethodId = $methodData['payout_method_id'];
            $payoutMethod = PayoutMethod::find($payoutMethodId);
            if (! $payoutMethod) {
                continue;
            }

            $type = strtolower($payoutMethod->name);

            // Validation rules
            $rules = $type === 'bank account'
                ? [
                    'account_name' => 'required|string',
                    'bank_name' => 'required|string',
                    'branch_name' => 'nullable|string',
                    'account_number' => 'required|string',
                    'iban' => 'nullable|string',
                    'swift_code' => 'nullable|string',
                ]
                : [
                    'email' => 'required',
                    'note' => 'nullable|string',
                ];

            $validator = Validator::make($methodData, $rules);
            if ($validator->fails()) {
                continue;
            }

            $validatedData = $validator->validated();
            $validatedData['id'] = $payoutMethod->id;
            $validatedData['is_active'] = isset($methodData['is_active']) ? (int) $methodData['is_active'] : 0;

            // Save all payout methods (including bank accounts) in AppUserMeta only
            AppUserMeta::updateOrCreate(
                ['user_id' => $user->id, 'meta_key' => $type],
                ['meta_value' => json_encode($validatedData)]
            );

            $responseData[] = [
                'id' => $payoutMethod->id,
                'payout_method' => $type,
                'details' => $validatedData,
                // 'is_active' => $validatedData['is_active'],
            ];
        }

        return $this->addSuccessResponse(
            200,
            trans('front.payment_method_saved_successfully'),
            [
                'payout_methods' => $responseData,
            ]
        );
    }
}
