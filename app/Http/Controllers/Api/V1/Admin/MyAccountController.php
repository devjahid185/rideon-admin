<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\OTPTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Models\AppUser;
use App\Models\AppUserMeta;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Review;
use Illuminate\Http\Request;
use Validator;

class MyAccountController extends Controller
{
    use BookingAvailableTrait, EmailTrait, MediaUploadingTrait, MiscellaneousTrait, NotificationTrait, OTPTrait, ResponseTrait, SMSTrait;

    public function editProfile(Request $request)
    {
        // try {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'first_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        $user->first_name = $request->input('first_name');
        $user->gender = $request->input('gender', null);

        $user->save();
        $user = AppUser::where('token', $request->input('token'))->first();
        if ($user->identity_image) {
            $user['identity_image'] = $user->identity_image->url;
        } else {
            $user['identity_image'] = null;
        }

        $item = Item::where('userid_id', $user->id)->first();

        if ($item) {
            $user['item_id'] = $item->id;
            $user['item_type_id'] = $item->item_type_id;
        }
        $imageFields = [
            'driving_licence_status',
            'driver_authorization_status',
            'hire_service_licence_status',
            'inspection_certificate_status',
        ];

        $metaStatuses = AppUserMeta::where('user_id', $user->id)
            ->whereIn('meta_key', $imageFields)
            ->pluck('meta_value', 'meta_key');

        $statuses = [];
        foreach ($imageFields as $field) {
            $statuses[] = $metaStatuses[$field] ?? '';
        }

        if (in_array('rejected', $statuses)) {
            $finalStatus = 'rejected';
        } elseif (count(array_filter($statuses, fn ($s) => $s != 'approved')) > 0) {
            $finalStatus = 'pending';
        } else {
            $finalStatus = 'approved';
        }

        $user['verification_document_status'] = $finalStatus;

        return $this->addSuccessResponse(200, trans('global.update_profile_success'), $user);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function checkMobileNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'phone' => 'required|min:8|max:12',
            'phone_country' => 'required',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        // try {
        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        $existingUser = AppUser::where('phone', '=', trim($request->input('phone')))
            ->where('phone_country', '=', trim($request->input('phone_country')))
            ->where('id', '!=', $user->id)
            ->withTrashed()
            ->first();

        if ($existingUser) {
            return $this->addErrorResponse(400, trans('global.mobile_number_already_exists'), '');
        }

        if ($user->phone == $request->input('phone') && $user->phone_country == $request->input('phone_country')) {
            return $this->addErrorResponse(400, trans('global.mobile_number_same_as_current'), '');
        }
        if (! empty($request->input('email'))) {
            $existingEmailUser = AppUser::where('email', $request->input('email'))
                ->where('id', '!=', $user->id)
                ->withTrashed()
                ->first();

            if ($existingEmailUser) {
                return $this->addErrorResponse(400, trans('global.email_already_exists'), '');
            }
        }

        $otp = $this->generateOtp($request->phone, $request->phone_country);
        $responseData = [
            'phone' => $request->input('phone'),
            'phone_country' => $request->input('phone_country'),
            'otp' => '',
        ];

        $valuesArray = ['OTP' => $otp, 'temp_phone' => $request->input('phone_country').$request->input('phone')];
        $this->sendAllNotifications($valuesArray, $user->id, 38);
        $responseData['otp'] = GeneralSetting::getMetaValue('auto_fill_otp') ? $otp : '';

        return $this->addSuccessResponse(200, trans('global.mobile_availabel_move_OTP_screen'), $responseData);

        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function checkEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }
        $existingUser = AppUser::where('email', $request->input('email'))
            ->where('id', '!=', $user->id)
            ->withTrashed()
            ->first();
        if ($existingUser) {
            return $this->addErrorResponse(400, trans('global.email_already_exists'), '');
        }
        if ($user->email == $request->input('email')) {
            return $this->addErrorResponse(400, trans('global.email_same_as_current'), '');
        }
        $otp = $this->generateOtp($user->phone, $user->phone_country);
        $responseData = [
            'email' => $request->input('email'),
        ];
        $valuesArray = ['OTP' => $otp, 'temp_email' => $request->input('email')];
        $this->sendAllNotifications($valuesArray, $user->id, 36);
        $responseData['otp'] = GeneralSetting::getMetaValue('auto_fill_otp') ? $otp : '';

        return $this->addSuccessResponse(200, trans('global.email_available_move_OTP_screen'), $responseData);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function changeMobileNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'phone' => 'required|min:8|max:12',
            'phone_country' => 'required',
            'otp_value' => 'required',
            'default_country' => 'nullable|string',
            'email' => 'nullable|email',

        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        if ($user->phone == $request->input('phone') && $user->phone_country == $request->input('phone_country')) {
            return $this->addErrorResponse(400, trans('global.mobile_number_same_as_current'), '');
        }

        $resultOtp = $this->validateOtpFromDB($request->phone, $request->phone_country, $request->otp_value);
        if ($resultOtp['status'] === 'success') {
            $user->phone = $request->input('phone');
            $user->phone_country = $request->input('phone_country');
            $user->default_country = $request->input('default_country');
            $user->phone_verify = 1;

            if (! empty($request->input('email'))) {
                $user->email = $request->input('email');
            }
            if (! empty($request->input('first_name'))) {
                $user->first_name = $request->input('first_name');
            }
            if (! empty($request->input('last_name'))) {
                $user->last_name = $request->input('last_name');
            }

            $user->save();
            $userdata = AppUser::where('token', $request->input('token'))->first();

            return $this->addSuccessResponse(200, trans('global.mobile_number_updated_successfully'), $userdata);
        } else {
            return $this->errorResponse(401, trans('global.Wrong_OTP'));
        }
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function changeEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|unique:users,email',
            'otp_value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        if ($user->email == $request->input('new_email')) {
            return $this->addErrorResponse(400, trans('global.email_same_as_current'), '');
        }
        $resultOtp = $this->validateOtpFromDB($user->phone, $user->phone_country, $request->otp_value);
        if ($resultOtp['status'] === 'success') {
            $user->email = $request->input('email');
            $user->email_verify = 1;
            $user->save();
            $userdata = AppUser::where('token', $request->input('token'))->first();

            return $this->addSuccessResponse(200, trans('global.email_updated_successfully'), $userdata);
        } else {
            return $this->errorResponse(401, trans('global.Wrong_OTP'));
        }
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    protected function validateBase64Image($attribute, $value, $parameters, $validator)
    {
        $decoded = base64_decode($value);
        $data = getimagesizefromstring($decoded);

        return $data !== false && in_array($data[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF]);
    }

    public function uploadProfileImage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'profile_image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        if ($request->has('profile_image')) {
            $profileImage = $request->input('profile_image');

            $frontImageUrl = $this->serveBase64Image($profileImage, 'app/profile_images/');
            if ($frontImageUrl) {
                if ($user->profile_image) {
                    $user->profile_image->delete();
                }
                $user->addMedia($frontImageUrl)->toMediaCollection('profile_image');
                $user = AppUser::where('token', $request->input('token'))->first();

                return $this->addSuccessResponse(200, trans('global.profile_image_successfully'), ['profile_image_url' => $user->profile_image->url]);
            } else {
                return $this->addErrorResponse(500, trans('global.Failed_to_upload_image'), '');
            }
        } else {
            return $this->addErrorResponse(500, trans('global.No_image_found_in_the_request'), '');
        }
    }

    public function getDriverDashboardStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $module = $this->getModuleIdOrDefault($request);
        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        $baseConditions = [
            ['host_id', '=', $user->id],
            ['module', '=', $module],
        ];

        $bookingStats = Booking::selectRaw("
            COUNT(CASE WHEN status = 'Completed' THEN 1 END) as total_orders,
            SUM(CASE WHEN payment_status = 'Paid' THEN vendor_commission ELSE 0 END) as total_earnings
        ")
            ->where($baseConditions)
            ->first();

        $averageGuestRating = Review::where([
            ['hostid', '=', $user->id],
            ['module', '=', $module],
        ])->avg('guest_rating');

        $dashboardStats = [
            'total_orders' => $bookingStats->total_orders ?? 0,
            'total_earnings' => $bookingStats->total_earnings ?? 0,
            'average_rating' => round($averageGuestRating ?? 0, 2),
        ];

        return $this->addSuccessResponse(200, trans('global.dashboard_stats_retrieved_successfully'), $dashboardStats);

    }

    public function uploadItemDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        $imageFields = [
            'item_driving_licence',
            'item_driver_authorization',
            'item_hire_service_licence',
            'item_inspection_certificate',
        ];

        $uploadedImages = [];

        foreach ($imageFields as $field) {
            if ($request->has($field) && ! empty($request->input($field))) {
                $imageData = $request->input($field);
                $userItemFolder = 'app/item_documents_uploads/'.$user->id.'/';
                $imageUrl = $this->serveBase64Image($imageData, $userItemFolder);

                \Log::info("Saving Image for {$field}: ", ['imageUrl' => $imageUrl]);

                if ($imageUrl) {
                    if ($user->getMedia($field)->isNotEmpty()) {
                        $user->getMedia($field)->last()->delete();
                    }

                    $media = $user->addMedia($imageUrl)->toMediaCollection($field);
                    $uploadedImages[$field] = $media->getUrl();

                    AppUserMeta::updateOrCreate(
                        ['user_id' => $user->id, 'meta_key' => $field.'_status'],
                        ['meta_value' => 'pending']
                    );
                }
            }
        }

        if (empty($uploadedImages)) {
            return $this->addErrorResponse(500, trans('global.No_image_found_in_the_request'), '');
        }

        return $this->addSuccessResponse(200, 'Item Documents Added Successfully', '');
    }
}
