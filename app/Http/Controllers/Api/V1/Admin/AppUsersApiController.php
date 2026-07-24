<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\FirestoreTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\OTPTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Http\Requests\StoreAppUserRequest;
use App\Http\Requests\UpdateAppUserRequest;
use App\Http\Resources\Admin\AppUserResource;
use App\Models\AppUser;
use App\Models\AppUserMeta;
use App\Models\GeneralSetting;
use App\Models\Media;
use App\Models\Modern\Item;
use App\Models\Payout;
use App\Models\PayoutMethod;
use App\Models\Wallet;
use Auth;
use Carbon\Carbon;
use Gate;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class AppUsersApiController extends Controller
{
    use EmailTrait, FirestoreTrait, MediaUploadingTrait, MiscellaneousTrait, NotificationTrait, OTPTrait, PushNotificationTrait, ResponseTrait, SMSTrait, UserWalletTrait, VendorWalletTrait;

    public function index()
    {
        abort_if(Gate::denies('app_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppUserResource(AppUser::with(['package'])->get());
    }

    public function store(StoreAppUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $appUser = AppUser::create($data);

        if ($request->input('profile_image', false)) {
            $appUser->addMedia(storage_path('tmp/uploads/'.basename($request->input('profile_image'))))->toMediaCollection('profile_image');
        }

        return (new AppUserResource($appUser))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(AppUser $appUser)
    {
        abort_if(Gate::denies('app_user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppUserResource($appUser->load(['package']));
    }

    public function update(UpdateAppUserRequest $request, AppUser $appUser)
    {
        $data = $request->all();
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }
        $appUser->update($data);

        if ($request->input('profile_image', false)) {
            if (! $appUser->profile_image || $request->input('profile_image') !== $appUser->profile_image->file_name) {
                if ($appUser->profile_image) {
                    $appUser->profile_image->delete();
                }
                $appUser->addMedia(storage_path('tmp/uploads/'.basename($request->input('profile_image'))))->toMediaCollection('profile_image');
            }
        } elseif ($appUser->profile_image) {
            $appUser->profile_image->delete();
        }

        return (new AppUserResource($appUser))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
    // //////////// API ////////////

    public function userRegister(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'phone' => ['required', 'numeric', 'min:9'],
                'email' => ['required', 'email'],
                'first_name' => ['required'],
                'phone_country' => ['required'],
                'user_type' => ['required'],
            ]);

            if ($validator->fails()) {

                return $this->errorComputing($validator);
            }
            $email = strtolower($request->email);
            if (AppUser::withTrashed()->where('phone', $request->phone)->where('phone_country', $request->phone_country)->exists() || AppUser::withTrashed()->where('email', $email)->exists()) {
                return $this->errorResponse(409, trans('global.user_alredy_exist'));
            } else {
                $token = Str::random(120);
                $customerData = [
                    'phone' => $request->phone,
                    'email' => $email,
                    'first_name' => $request->first_name,
                    'phone_country' => $request->phone_country,
                    'fcm' => $request->fcm,
                    'status' => 1,
                    'default_country' => $request->default_country,
                    'user_type' => $request->user_type,
                    'token' => $token,
                ];
                $customer = AppUser::create($customerData);

                if ($request->user_type == 'driver') {

                    $firestoreData = $this->generateDriverFirestoreData($customer);
                    $firestoreDoc = $this->storeDriverInFirestore($firestoreData);
                    // $firestoreDocId = $firestoreDoc->id(); for GRPC
                    $firestoreDocId = basename($firestoreDoc);
                    $customer->update(['firestore_id' => $firestoreDocId]);
                    $customer['firestore_id'] = $firestoreDocId;
                }

                $otp = $this->generateOtp($request->phone, $request->phone_country);
                $this->sendAllNotifications(['OTP' => $otp], $customer->id, 2);
                $valuesArray = $customer->only(['first_name', 'last_name', 'email']);
                $valuesArray['phone'] = $customer->phone_country.$customer->phone;
                $settings = GeneralSetting::whereIn('meta_key', ['general_email'])->get()->keyBy('meta_key');
                $general_email = $settings['general_email']->meta_value ?? null;
                $valuesArray['support_email'] = $general_email;
                $this->sendAllNotifications($valuesArray, $customer->id, 1);
                $customer->update(['otp_value' => $otp]);
                $customer['otp_value'] = GeneralSetting::getMetaValue('auto_fill_otp') ? $otp : '';

                return $this->successResponse(200, trans('global.user_created_successfully'), $customer);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function generateOtp($phoneNumber, $countryCode)
    {

        DB::table('app_user_otps')
            ->where('phone', $phoneNumber)
            ->where('country_code', $countryCode)
            ->delete();

        $otp = $this->createOTP();

        $expiresAt = Carbon::now()->addMinutes(10);

        DB::table('app_user_otps')->insert([
            'phone' => $phoneNumber,
            'country_code' => $countryCode,
            'otp_code' => $otp,
            'created_at' => Carbon::now(),
            'expires_at' => $expiresAt,
        ]);

        return $otp;
    }

    public function validateOtpFromDB($phoneNumber, $countryCode, $inputOtp)
    {

        $otpRecord = DB::table('app_user_otps')
            ->where('phone', $phoneNumber)
            ->where('country_code', $countryCode)
            ->orderByDesc('created_at')
            ->first();

        if (! $otpRecord) {
            return [
                'status' => trans('global.failed'),
                'message' => trans('global.noOTP_recordFound'),
            ];
        }

        $currentTime = Carbon::now();
        $expiresAt = Carbon::parse($otpRecord->expires_at);

        if ($currentTime->greaterThanOrEqualTo($expiresAt)) {
            return [
                'status' => trans('global.failed'),
                'message' => trans('global.OTPhas_expired'),
            ];
        }

        if ($otpRecord->otp_code === $inputOtp) {

            DB::table('app_user_otps')
                ->where('id', $otpRecord->id)
                ->delete();

            return [
                'status' => trans('global.success'),
                'message' => trans('global.OTP_varified'),
            ];
        } else {
            return [
                'status' => trans('global.failed'),
                'message' => trans('global.Incorrect_OTP'),
            ];
        }
    }

    public function otpVerification(Request $request)
    {
        // try {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'numeric', 'min:9'],
            'otp_value' => ['required'],
            'phone_country' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if (AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->exists()) {
            $resultOtp = $this->validateOtpFromDB($request->phone, $request->phone_country, $request->otp_value);
            if ($resultOtp['status'] === 'success') {

                $token = Str::random(120);
                $customer = AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->first();
                $customer->update(['otp_value' => '0', 'email_verify' => '1', 'phone_verify' => '1', 'status' => '1', 'verified' => '1']);
                $module = $this->getModuleIdOrDefault($request);
                $item = Item::where('userid_id', $customer->id)->first();

                if (! $item) {

                    $item = Item::create([
                        'userid_id' => $customer->id,
                    ]);
                }

                $customer['item_id'] = $item->id;

                $remainingItems = $this->checkRemainingItems($customer->id, $module);

                if ($remainingItems) {
                    $customer['remaining_items'] = $remainingItems;
                } else {
                    $customer['remaining_items'] = 0;
                }

                return $this->successResponse(200, trans('global.Login_Sucessfully'), $customer);
            } else {
                return $this->errorResponse(401, trans('global.Wrong_OTP'));
            }
        } else {
            return $this->errorResponse(404, trans('global.User_not_register'));
        }
        try {
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function userLogout(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
                'id' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }
            if (AppUser::where('token', $request->token)->exists()) {
                AppUser::where('token', $request->token)->update(['token' => '']);

                return $this->successResponse(200, trans('global.Logout_Sucessfully'));
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function userLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => ['required', 'numeric', 'min:9'],
                'password' => ['required'],
                'phone_country' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }
            $data = [
                'phone' => $request->phone,
                'password' => $request->password,
                'phone_country' => $request->phone_country,

            ];

            if (Auth::guard('appUser')->attempt($data)) {
                $otp = $this->createOTP();
                AppUser::where('phone', $request->phone)->update(['otp_value' => $otp, 'token' => '']);
                $customer = AppUser::where('phone', $request->phone)->first();
                unset($customer['token']);

                return $this->successResponse(200, trans('global.Login_Sucessfully'), $customer);
            } else {
                return $this->errorResponse(401, trans('global.something_wrong'));
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function userEmailLogin(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $data = [
                'email' => strtolower($request->email),
                'password' => $request->password,
            ];

            if (Auth::guard('appUser')->attempt($data)) {
                $customer = AppUser::where('email', $request->email)->first();

                if ($customer->status != 1) {
                    $otp = $this->generateOtp($customer->phone, $customer->phone_country);
                    $this->sendAllNotifications(['OTP' => $otp], $customer->id, 2);
                    $customer['reset_token'] = '';
                    if (GeneralSetting::getMetaValue('auto_fill_otp')) {
                        $customer['reset_token'] = $otp;
                    }

                    return $this->successResponse(200, trans('global.account_inactive'), $customer);
                }

                $token = Str::random(120);
                $customer->update(['token' => $token]);

                $mediaItem = Media::where('model_id', $customer->id)
                    ->where('model_type', 'App\Models\AppUser')
                    ->where('collection_name', 'identity_image')
                    ->first();

                $domain = env('APP_URL');
                $imageUrl = $mediaItem ? asset($domain.'/storage/app/public/'.$mediaItem->id.'/'.$mediaItem->file_name) : '';
                $customer['identity_image'] = $imageUrl;

                $module = $this->getModuleIdOrDefault($request);
                $remainingItems = $this->checkRemainingItems($customer->id, $module);

                if ($remainingItems) {
                    $customer['remaining_items'] = $remainingItems;
                } else {
                    $customer['remaining_items'] = 0;
                }

                $firebaseMeta = $customer->metadata->where('meta_key', 'firebase_auth')->first();
                if (! $firebaseMeta) {

                    $firebasePassword = Str::random(16);
                    $apiKey = 'AIzaSyBrI9JUsS-TMmEx1Fnnq-yDlKIiH9WTWA0';

                    $userFirebase = $this->createFirebaseUser($request->email, $firebasePassword, $apiKey);

                    if (isset($userFirebase['success']) && ! $userFirebase['success']) {

                        $result = AppUserMeta::updateOrCreate(
                            [
                                'meta_key' => 'firebase_auth',
                                'user_id' => $customer->id,
                            ],
                            [
                                'meta_value' => 0,
                            ]
                        );
                        $customer['firebase_auth'] = 0;
                    }

                    if (isset($userFirebase['success']) && $userFirebase['success']) {
                        $result = AppUserMeta::updateOrCreate(
                            [
                                'meta_key' => 'firebase_auth',
                                'user_id' => $customer->id,
                            ],
                            [
                                'meta_value' => 1,
                            ]
                        );
                        $customer['firebase_auth'] = 1;
                    }
                } else {
                    $customer['firebase_auth'] = $firebaseMeta->meta_value;
                }

                return $this->successResponse(200, trans('global.Login_Sucessfully'), $customer);
            } else {
                return $this->errorResponse(401, trans('global.user_not_exist'));
            }
            // try {
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function userMobileLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'numeric', 'min:9'],
            'otp_value' => ['required'],
            'phone_country' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if (AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->exists()) {
            $resultOtp = $this->validateOtpFromDB($request->phone, $request->phone_country, $request->otp_value);
            if ($resultOtp['status'] === 'success') {

                $token = Str::random(120);
                $customer = AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->first();
                if ($customer->status != 1) {
                    return $this->successResponse(200, trans('global.account_inactive'), $customer);
                }

                $customer->update(['token' => $token]);
                if ($request->user_type == 'driver') {
                    if ($customer->user_type == 'user') {
                        $customer->update([
                            'user_type' => 'driver',
                            'host_status' => 2,
                        ]);
                    }

                    $firestoreDocId = $customer->firestore_id;
                    $firestoreDocExists = false;
                    if ($firestoreDocId) {
                        $docData = $this->getDocument('drivers', $firestoreDocId);
                        $firestoreDocExists = $docData !== null;
                    }

                    if (! $firestoreDocId || ! $firestoreDocExists) {
                        $firestoreData = $this->generateDriverFirestoreData($customer);
                        $firestoreDoc = $this->storeDriverInFirestore($firestoreData);
                        $firestoreDocId = basename((string) $firestoreDoc);
                        if (! empty($firestoreDocId)) {
                            $customer->update(['firestore_id' => $firestoreDocId]);
                            $customer['firestore_id'] = $firestoreDocId;
                        }
                    } else {
                        $customer['firestore_id'] = $firestoreDocId;
                    }
                }

                $module = $this->getModuleIdOrDefault($request);
                $remainingItems = $this->checkRemainingItems($customer->id, $module);

                $customer['remaining_items'] = $remainingItems ?? 0;

                if ($request->user_type == 'driver') {
                    $item = Item::where('userid_id', $customer->id)->first();

                    if (! $item) {

                        $item = Item::create([
                            'userid_id' => $customer->id,
                        ]);
                        echo 'here';
                    }

                    if ($item) {
                        $customer['item_id'] = $item->id;
                        $customer['item_type_id'] = $item->item_type_id;
                    }
                }

                $docunmentsFields = [
                    'driving_licence_front_status',
                    'driving_licence_back_status',
                    'driver_id_front_status',
                    'driver_id_back_status',
                ];

                $metaStatuses = AppUserMeta::where('user_id', $customer->id)
                    ->whereIn('meta_key', $docunmentsFields)
                    ->pluck('meta_value', 'meta_key');

                $statuses = [];
                foreach ($docunmentsFields as $field) {
                    $statuses[] = $metaStatuses[$field] ?? '';
                }

                if (in_array('rejected', $statuses)) {
                    $finalStatus = 'rejected';
                } elseif (count(array_filter($statuses, fn ($s) => $s != 'approved')) > 0) {
                    $finalStatus = 'pending';
                } else {
                    $finalStatus = 'approved';
                }

                //   $customer['verification_document_status'] = $customer->document_verify == 1 ? 'approved' : 'pending';
                $customer['verification_document_status'] = $finalStatus;

                if ($request->user_type == 'driver' && ! empty($customer->firestore_id)) {
                    $this->updateDocument('drivers', $customer->firestore_id, [
                        'driverId' => (int) $customer->id,
                        'driverNumber' => (string) ($customer->phone_country.$customer->phone),
                        'driverName' => (string) ($customer->first_name ?? ''),
                        'docApprovedStatus' => (string) $finalStatus,
                    ]);
                }

                return $this->successResponse(200, trans('global.Login_Sucessfully'), $customer);
            } else {
                return $this->errorResponse(401, trans('global.Wrong_OTP'));
            }
        } else {
            return $this->errorResponse(404, trans('global.User_not_register'));
        }
    }

    public function sendMobileLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'phone_country' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('phone', $request->input('phone'))->where('phone_country', $request->phone_country)->first();

        if (! $user) {
            return $this->addErrorResponse(400, trans('global.User_not_found'), '');
        }

        $otp = $this->generateOtp($user->phone, $user->phone_country);

        $valuesArray = ['OTP' => $otp, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
        $template_id = 2;
        $this->sendAllNotifications($valuesArray, $user->id, $template_id);

        $user->update([
            'reset_token' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $responseData = [];
        $responseData['reset_token'] = '';
        $user['reset_token'] = '';
        if (GeneralSetting::getMetaValue('auto_fill_otp')) {
            $user['reset_token'] = $otp;
        }
        $filteredUser = $user->only([
            'phone',
            'phone_country',
            'reset_token',
            'token',
        ]);

        return $this->successResponse(200, trans('global.OTP_sent_successfully'), $user);
    }

    public function sendOnlyEmailLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        //    try {

        $user = AppUser::where('email', $request->input('email'))->first();

        if (! $user) {
            return $this->addErrorResponse(400, trans('global.User_not_found'), '');
        }

        $otp = $this->generateOtp($user->phone, $user->phone_country);
        $valuesArray = ['OTP' => $otp, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
        $template_id = 3;
        $this->sendAllNotifications($valuesArray, $user->id, $template_id);

        AppUser::where('email', $request->email)->update(['reset_token' => $otp, 'token' => '']);
        $responseData = [];
        $responseData['reset_token'] = '';
        if (GeneralSetting::getMetaValue('auto_fill_otp')) {
            $responseData['reset_token'] = $otp;
        }

        return $this->successResponse(200, trans('global.Password_reset_OTP'), $responseData);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.password_Set_error'), $e->getMessage());
        }
    }

    public function userOnlyEmailLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'otp_value' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('email', $request->email)->first();
        if (! $user) {
            return $this->addErrorResponse(400, trans('global.User_not_found'), '');
        }

        $resultOtp = $this->validateOtpFromDB($user->phone, $user->phone_country, $request->otp_value);
        if ($resultOtp['status'] === 'success') {
            $token = Str::random(120);

            if ($user->status != 1) {
                return $this->successResponse(200, trans('global.account_inactive'), $user);
            }

            $user->update(['token' => $token]);

            $module = $this->getModuleIdOrDefault($request);
            $remainingItems = $this->checkRemainingItems($user->id, $module);

            $user['remaining_items'] = $remainingItems ?? 0;

            return $this->successResponse(200, trans('global.Login_Sucessfully'), $user);
        } else {
            return $this->errorResponse(401, trans('global.Wrong_OTP'));
        }
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'displayName' => 'nullable|string',
            'email' => 'nullable|email',
            'id' => 'required|string',
            'login_type' => 'required|in:google,apple',
            'profile_image' => 'nullable|string',
        ]);

        if (empty($request->input('email'))) {
            $temporaryEmailDomain = '@rideon.unibooker.app';
            $email = $request->input('id').$temporaryEmailDomain;
        } else {
            $email = strtolower($request->input('email'));
        }

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        try {
            $displayName = trim((string) $request->input('displayName', ''));
            if ($displayName === '') {
                $displayName = 'User';
            }
            $names = preg_split('/\s+/', $displayName);
            $firstName = $names[0] ?? 'User';
            $lastName = isset($names[1]) ? implode(' ', array_slice($names, 1)) : '';

            $socialId = (string) $request->input('id');
            $loginType = $request->input('login_type');
            $user = null;

            if ($request->filled('email')) {
                $user = AppUser::where('email', $email)->withTrashed()->first();
                if (! is_null($user) && $user->trashed()) {
                    return $this->addErrorResponse(400, trans('User has been block'), '');
                }
            } else {
                $user = AppUser::where('social_id', $socialId)->first();
            }

            DB::beginTransaction();

            if ($user) {
                $customer = $this->generateAccessToken($user->email);
                $userIdForRemainingItems = $user->id;
            } else {
                $newUser = AppUser::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ]);

                $imagePath = null;
                if ($request->filled('profile_image')) {
                    $imageData = @file_get_contents($request->input('profile_image'));
                    if ($imageData !== false) {
                        $imageName = Str::random(40).'.jpg';
                        $imagePath = 'profile_images/'.$imageName;
                        try {
                            Storage::put($imagePath, $imageData);
                        } catch (\Exception $e) {
                            Log::warning('socialLogin image processing failed', ['error' => $e->getMessage()]);
                            $imagePath = null;
                        }
                    }
                }

                if ($imagePath && file_exists(storage_path('app/'.$imagePath))) {
                    $newUser->addMedia(storage_path('app/'.$imagePath))->toMediaCollection('profile_image');
                }

                $newUser->social_id = $socialId;
                $newUser->login_type = $loginType;
                $newUser->save();

                $userIdForRemainingItems = $newUser->id;
                $customer = $this->generateAccessToken($email);
            }

            DB::commit();

            $module = $this->getModuleIdOrDefault($request);
            $remainingItems = $this->checkRemainingItems($userIdForRemainingItems, $module);
            $customer['remaining_items'] = $remainingItems ?? 0;

            return $this->successResponse(200, trans('global.Login_Sucessfully'), $customer);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('socialLogin failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->except(['identityToken', 'authorizationCode']),
            ]);

            return $this->addErrorResponse(500, trans('global.ServerError_internal_server_error'), $e->getMessage());
        }
    }

    private function generateAccessToken($email)
    {
        $token = Str::random(120);
        AppUser::where('email', $email)->update([
            'otp_value' => '0',
            'token' => $token,
            'verified' => '1',
        ]);
        $customer = AppUser::where('email', $email)->first();

        return $customer;
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        //    try {

        $user = AppUser::where('email', $request->input('email'))->first();

        if (! $user) {
            return $this->addErrorResponse(400, trans('global.User_not_found'), '');
        }

        $otp = $this->generateOtp($user->phone, $user->phone_country);
        $valuesArray = ['OTP' => $otp, 'first_name' => $user->first_name, 'last_name' => $user->last_name];
        $template_id = 3;
        $this->sendAllNotifications($valuesArray, $user->id, $template_id);

        AppUser::where('email', $request->email)->update(['reset_token' => $otp, 'token' => '']);
        $responseData = [];
        $responseData['reset_token'] = '';
        if (GeneralSetting::getMetaValue('auto_fill_otp')) {
            $responseData['reset_token'] = $otp;
        }

        return $this->successResponse(200, trans('global.Password_reset_OTP'), $responseData);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.password_Set_error'), $e->getMessage());
        }
    }

    public function verifyResetToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'reset_token' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $user = AppUser::where('email', $request->email)->first();
            if (! $user) {
                return $this->addErrorResponse(400, trans('global.User_not_found'), '');
            }

            if ($user) {
                $resultOtp = $this->validateOtpFromDB($user->phone, $user->phone_country, $request->reset_token);
                if ($resultOtp['status'] === 'success') {
                    return $this->successResponse(200, trans('global.RESET_OTP_Found_YOU_CAN_PROCEED'), [
                        'email' => $request->email,
                        'reset_token' => $request->reset_token,
                    ]);
                } else {
                    return $this->errorResponse(401, trans('global.RESET_OTP_ERROR'));
                }
            } else {
                return $this->errorResponse(404, trans('global.User_not_register'));
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'reset_token' => ['required'],
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            if (AppUser::where('email', $request->email)->exists()) {
                if (AppUser::where('email', $request->email)->where('reset_token', $request->reset_token)->exists()) {

                    AppUser::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

                    return $this->successResponse(200, trans('global.Password_changed_successfully.'), [
                        'email' => $request->email,
                        'reset_token' => $request->reset_token,
                    ]);
                } else {
                    return $this->errorResponse(401, trans('global.RESET_OTP_ERROR'));
                }
            } else {
                return $this->errorResponse(404, trans('global.User_not_register'));
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function emailcheck(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if (AppUser::where('email', $request->email)->exists()) {

            return $this->successResponse(200, trans('global.email_already_exists'), [
                'email' => $request->email,
            ]);
        } else {

            return $this->errorResponse(401, trans('global.email_is_not_exists'));
        }
    }

    public function mobilecheck(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'numeric', 'digits_between:9,10'],
            'phone_country' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if (AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->exists()) {
            return $this->successResponse(200, trans('global.Phone_number_is_avilable'), ['phone' => $request->phone]);
        } else {

            return $this->errorResponse(401, trans('global.phone_number_not_exists.'));
        }
    }

    public function ResendOtp(Request $request)
    {

        // $myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "/resend_otp.txt", "w") or die("Unable to open file!");

        // $txt = "phone = " . $request->input('phone') . "\n";
        // fwrite($myfile, $txt);
        // $txt = "phone_country = " . $request->input('phone_country') . "\n";
        // fwrite($myfile, $txt);
        // fclose($myfile);

        //   try{

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'numeric'],
            'phone_country' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $checkdata = AppUser::where('phone', $request->phone)->where('phone_country', $request->phone_country)->first();
        if (empty($checkdata)) {
            $checkdata = DB::table('app_user_otps')->where('phone', $request->phone)->where('country_code', $request->phone_country)->first();
        }

        $first_name = '';
        $last_name = '';

        $user = null;
        if ($request->has('token')) {
            $user = AppUser::where('token', $request->input('token'))->first();
        }
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        } else {

            $first_name = $user->first_name;
            $last_name = $user->last_name;
        }

        if ($checkdata) {
            $otp = $this->generateOtp($request->phone, $request->phone_country);
            if (isset($checkdata->first_name)) {
                $first_name = $checkdata->first_name;
            }

            if (isset($checkdata->last_name)) {
                $last_name = $checkdata->last_name;
            }

            $valuesArray = ['OTP' => $otp, 'first_name' => $first_name, 'last_name' => $last_name];
            $template_id = 37;
            $this->sendAllNotifications($valuesArray, $checkdata->id, $template_id);
            $responseData = [];
            $responseData['otp_value'] = '';
            if (GeneralSetting::getMetaValue('auto_fill_otp')) {
                $responseData['otp_value'] = $otp;
            }

            return $this->successResponse(200, trans('global.OTP_sent_successfully'), $responseData);
        } else {
            return $this->errorResponse(409, trans('global.user_record_not_match_44'));
        }
        try {
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function ResendToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $checkdata = AppUser::where('email', $request->email)->first();
        if ($checkdata) {
            $otp = $this->generateOtp($checkdata->phone, $checkdata->phone_country);
            $valuesArray = ['OTP' => $otp, 'first_name' => $checkdata->first_name, 'last_name' => $checkdata->last_name];
            $template_id = 37;
            $this->sendAllNotifications($valuesArray, $checkdata->id, $template_id);
            $update_otp = AppUser::where('email', $request->email)->update(['reset_token' => $otp]);
            $responseData = [];
            $responseData['reset_token'] = '';
            if (GeneralSetting::getMetaValue('auto_fill_otp')) {
                $responseData['reset_token'] = $otp;
            }

            return $this->successResponse(200, trans('global.OTP_resent_succesfully'), $responseData);
        } else {
            return $this->errorResponse(409, trans('global.user_record_not_match'));
        }
        try {
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function ResendTokenEmailChange(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $checkdata = AppUser::where('token', $request->input('token'))->first();
        if ($checkdata) {
            $otp = $this->generateOtp($checkdata->phone, $checkdata->phone_country);

            $valuesArray = ['OTP' => $otp, 'first_name' => $checkdata->first_name, 'last_name' => $checkdata->last_name];
            if ($request->input('type') === 'email_reset') {
                $valuesArray['temp_email'] = $request->input('email');
            }
            $template_id = 37;
            $this->sendAllNotifications($valuesArray, $checkdata->id, $template_id);
            $update_otp = AppUser::where('email', $request->email)->update(['reset_token' => $otp]);
            $responseData = [];
            $responseData['reset_token'] = '';
            if (GeneralSetting::getMetaValue('auto_fill_otp')) {
                $responseData['reset_token'] = $otp;
            }

            return $this->successResponse(200, trans('global.OTP_resent_succesfully'), $responseData);
        } else {
            return $this->errorResponse(409, trans('global.user_record_not_match'));
        }
        try {
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function userValidate(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }
            if (AppUser::where('token', $request->token)->exists()) {

                return $this->successResponse(200, trans('global.user_exist'));
            } else {
                return $this->errorResponse(401, trans('global.user_not_exist'));
            }
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
                'old_password' => ['required'],
                'new_password' => ['required'],
                'conf_new_password' => ['required', 'same:new_password'],
            ]);
            if ($request) {

                if ($validator->fails()) {
                    return $this->errorComputing($validator);
                }
            }

            $user = AppUser::where('token', $request->input('token'))->first();

            if (! $user) {
                return $this->addErrorResponse(419, trans('global.token_not_match'), '');
            }

            if (! Hash::check($request->input('old_password'), $user->password)) {
                return $this->addErrorResponse(500, trans('global.password_does_not_match'), '');
            }

            if (Hash::check($request->input('new_password'), $user->password)) {
                return $this->addErrorResponse(400, trans('global.new_password_same_as_old'), '');
            }
            $user->update([
                'password' => Hash::make($request->input('new_password')),
            ]);

            return $this->addSuccessResponse(200, trans('global.password_updated_successfully'), $user);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), '');
        }
    }

    public function getUserWallet(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        try {
            $walletBalance = $this->getUserWalletBalance($user->id);

            return $this->addSuccessResponse(200, trans('global.Wallet_amount'), ['wallet_balance' => $walletBalance]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.user_not_found'), '');
        }
    }

    public function getUserWalletTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'offset' => 'nullable|numeric|min:0',
            'limit' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        // Fetch pagination parameters
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        try {
            $user = AppUser::where('token', $request->input('token'))->first();
            if (! $user) {
                return $this->addErrorResponse(419, trans('global.token_not_match'), '');
            }

            $WalletTransactionsDetails = Wallet::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->toArray(); // Convert to array
            foreach ($WalletTransactionsDetails as &$transaction1) {
                $transaction1['created_at'] = Carbon::parse($transaction1['created_at'])->format('j M Y');
                $transaction1['updated_at'] = Carbon::parse($transaction1['updated_at'])->format('j M Y');
            }

            $WalletTransactionsDetails = collect($WalletTransactionsDetails);

            $nextOffset = $request->input('offset', 0) + count($WalletTransactionsDetails);
            if (empty($WalletTransactionsDetails)) {
                $nextOffset = -1;
            }

            return $this->addSuccessResponse(200, trans('global.Wallet_amount'), [
                'WalletTransactionsDetails' => $WalletTransactionsDetails,
                'offset' => $nextOffset,
            ]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.user_not_found'), '');
        }
    }

    public function getVendorWallet(Request $request)
    {
        // \DB::enableQueryLog();
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        // try {

        $summary = $this->getVendorWalletSummary($user->id);
        $walletBalance = $summary['walletBalance'];
        $pendingToWithdrawl = $summary['pendingToWithdrawl'];
        $totalWithdrawled = $summary['totalWithdrawled'];
        $totalEarning = $summary['totalEarning'];
        $refunded = $summary['refunded'];
        $incoming_amount = $summary['incoming_amount'];
        $pendingPayout = $summary['pendingPayout'];

        // $queries = \DB::getQueryLog(); // Get all queries
        // dd(count($queries), $queries);

        return $this->addSuccessResponse(200, trans('global.vendor_Wallet_amount'), ['walletBalance' => $walletBalance, 'pendingToWithdrawl' => $pendingToWithdrawl, 'totalWithdrawled' => $totalWithdrawled, 'totalEarning' => $totalEarning, 'refunded' => $refunded, 'incoming_amount' => $incoming_amount, 'pendingPayout' => $pendingPayout]);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), '');
        }
    }

    public function getVendorWalletTransactions(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'offset' => 'nullable|numeric|min:0',
        ]);
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        try {
            $WalletTransactionsDetails = $this->getVendorWalletTransactionsDetails($user->id, $offset, $limit);

            return $this->addSuccessResponse(200, trans('global.vendor_Wallet_amount'), ['WalletTransactionsDetails' => $WalletTransactionsDetails['transactions'], 'offset' => $WalletTransactionsDetails['offset']]);
            // try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), '');
        }
    }

    // fcmUpdate
    public function fcmUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
                'fcm' => ['nullable', 'string'],
                'device_id' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $user = AppUser::where('token', $request->input('token'))->first();
            if (! $user) {
                return $this->addErrorResponse(404, trans('global.user_not_found'), '');
            }

            Log::info('fcmUpdate request', [
                'user_id' => $user->id,
                'has_fcm' => $request->filled('fcm'),
                'fcm_preview' => $request->filled('fcm') ? substr($request->input('fcm'), 0, 24).'...' : null,
                'device_id' => $request->input('device_id'),
            ]);

            $user->update([
                'fcm' => $request->input('fcm'),
                'device_id' => $request->input('device_id'),
            ]);

            Log::info('fcmUpdate stored', [
                'user_id' => $user->id,
                'fcm_preview' => ! empty($user->fcm) ? substr($user->fcm, 0, 24).'...' : null,
                'device_id' => $user->device_id,
            ]);

            return $this->addSuccessResponse(200, trans('global.fcm_updated_successfully'), $user);
        } catch (\Exception $e) {
            Log::error('fcmUpdate failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->addErrorResponse(500, trans('global.something_wrong'), '');
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            // Find the user by token
            $user = AppUser::where('token', $request->token)->first();

            if (! $user) {
                return $this->errorResponse(404, trans('global.User_not_found'));
            }
            $token = Str::random(120);
            $user->token = $token;
            $user->save();

            // Delete the user
            $user->forceDelete();

            return $this->successResponse(200, trans('global.user_deleted_successfully'));
        } catch (\Exception $e) {
            return $this->errorResponse(401, trans('global.something_wrong'));
        }
    }

    public function insertPayout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();

        $payoutMethodId = $request->input('active_payout_method_id');

        $payoutMethodDetails = PayoutMethod::find($payoutMethodId);
        $payoutMethod = strtolower($payoutMethodDetails->name);

        if (! $user) {
            return $this->errorResponse(404, trans('global.User_not_found'));
        }

        try {
            $payoutStatus = 'Pending';
            $totalPayoutMoney = Payout::where('vendorid', $user->id)->where('payout_status', $payoutStatus)->sum('amount');
            $vendorWalletMoney = $this->getVendorWalletBalance($user->id);

            $withdrawalAmount = $request->input('amount');

            $withdrawalAmount = $withdrawalAmount + $totalPayoutMoney;

            if ($withdrawalAmount > $vendorWalletMoney) {
                return $this->errorResponse(404, trans('global.did_not_have_sufficient_balance'));
            } else {

                $payout = new Payout;
                $payout->vendorid = $user->id;
                $payout->amount = $request->input('amount');
                $payout->currency = $request->input('currency');
                if ($request->has('module_id')) {
                    $payout->module = $request->input('module_id');
                }
                // $payout->payment_method = '';
                $payout->payment_method = $payoutMethod;
                $payout->payout_status = 'Pending';
                $payout->save();

                $settings = GeneralSetting::whereIn('meta_key', ['general_email', 'general_default_currency'])
                    ->get()
                    ->keyBy('meta_key');

                $general_email = $settings['general_email'] ?? null;
                $general_default_currency = $settings['general_default_currency'] ?? null;

                $template_id = 4;
                $valuesArray = $user->toArray();
                $valuesArray = $user->only(['first_name', 'last_name', 'email', 'phone_country', 'phone']);
                $valuesArray['phone'] = $valuesArray['phone_country'].$valuesArray['phone'];
                $valuesArray['payout_amount'] = $request->input('amount');
                $valuesArray['payout_bank'] = $payout->payment_method;
                $valuesArray['support_email'] = $general_email->meta_value;
                $valuesArray['currency_code'] = $general_default_currency->meta_value;
                $valuesArray['payout_date'] = now()->format('Y-m-d');
                $this->sendAllNotifications($valuesArray, $user->id, $template_id);

                return $this->successResponse(200, trans('payout requested successfully'), ['payout' => $payout]);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('global.something_wrong').': '.$e->getMessage());
        }
    }

    public function getPayoutTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'offset' => 'nullable|numeric|min:0',
            'limit' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, trans('global.something_wrong'));
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        try {
            $user = AppUser::where('token', $request->input('token'))->first();

            if (! $user) {
                return $this->errorResponse(404, trans('global.User_not_found'));
            }

            $payoutTransactions = Payout::where('vendorid', $user->id)
                ->orderByDesc('created_at')
                ->offset($offset)
                ->take($limit)
                ->get()
                ->toArray(); // Convert to array

            foreach ($payoutTransactions as &$transaction) {
                $transaction['created_at'] = Carbon::parse($transaction['created_at'])->format('j M Y');
                $transaction['updated_at'] = Carbon::parse($transaction['updated_at'])->format('j M Y');
            }

            $payoutTransactions = collect($payoutTransactions);

            $nextOffset = $request->input('offset', 0) + count($payoutTransactions);

            if ($payoutTransactions->isEmpty()) {
                $nextOffset = -1;
            }

            return $this->successResponse(200, trans('global.Result_found'), [
                'payout_transactions' => $payoutTransactions,
                'offset' => $nextOffset,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('global.something_wrong').': '.$e->getMessage());
        }
    }

    public function emailSmsNotification(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'type' => 'required',
            'value' => 'required',
        ]);

        $type = $request->type;
        $value = $request->value;

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->errorResponse(401, trans('global.user_not_found'));
        }

        if ($type == 'email') {
            $user->update(['email_notification' => $value]);

            return $this->successResponse(200, trans('global.emailNotification'), ['emailNotification' => $user]);
        }
        if ($type == 'push') {
            $user->update(['push_notification' => $value]);

            return $this->successResponse(200, trans('global.pushNotification'), ['emailsmsnotification' => $user]);
        }
        if ($type == 'sms') {
            $user->update(['sms_notification' => $value]);

            return $this->successResponse(200, trans('global.smsNotification'), ['emailsmsnotification' => $user]);
        }
    }

    public function puthostRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'host_status' => 'required',
            'token' => 'required',
            'license_number' => 'required|string',
            'license_expire_date' => 'required|date',
            'driving_experience' => 'required|integer|min:0',
        ]);

        $host_status = $request->host_status;
        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->errorResponse(401, trans('global.user_not_found'));
        }
        $hostFormData = $request->only([
            'host_status',
            'first_name',
            'last_name',
            'email',
            'phone',
            'country_code',
            'license_number',
            'license_expire_date',
            'driving_experience',
        ]);

        $userUpdated = $user->update(['host_status' => $host_status]);

        if ($userUpdated) {

            $imagePath = null;

            // if (!empty($request->input('identity_image'))) {
            //     $identityImage = $request->input('identity_image');
            //     $identityImageURL = $this->serveBase64Image($identityImage);
            //     $user->addMedia($identityImageURL)->toMediaCollection('identity_image');
            // }

            if (! empty($request->input('license_image'))) {
                $identityImage = $request->input('license_image');
                $identityImageURL = $this->serveBase64Image($identityImage);
                $user->addMedia($identityImageURL)->toMediaCollection('license_image');
            }

            AppUserMeta::updateOrCreate(
                ['user_id' => $user->id, 'meta_key' => 'host_form_data'],
                ['meta_value' => json_encode($hostFormData)]
            );

            // $template_id = 34;
            // $valuesArray = $user->toArray();
            // $valuesArray = $user->only(['first_name', 'last_name', 'email', 'phone_country', 'phone']);
            // $valuesArray['phone'] = $valuesArray['phone_country'] . $valuesArray['phone'];
            //  $settings = GeneralSetting::whereIn('meta_key', ['general_email'])->get()->keyBy('meta_key');

            // // Get the general email value safely
            // $general_email = $settings['general_email']->meta_value ?? null;

            // // Add support email to the values array
            // $valuesArray['support_email'] = $general_email;

            // $this->sendAllNotifications($valuesArray, $user->id, $template_id);
        }

        return $this->successResponse(200, trans('global.hostRequest'), ['host_status' => $host_status]);
    }

    public function gethostStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->errorResponse(401, trans('global.user_not_found'));
        }

        return $this->successResponse(200, trans('global.hostRequest'), ['host_status' => $user->host_status]);
    }

    public function addEditVerificationDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:app_users,token',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user_id = $this->checkUserByToken($request->token);
        if (! $user_id) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        try {
            $user = AppUser::find($user_id);
            $imageFields = [
                'driving_licence_front',
                'driving_licence_back',
                'driver_id_front',
                'driver_id_back',
                'hire_service_licence',
                'inspection_certificate',
            ];
            $uploadedImages = [];
            $hasImageUploaded = false;

            foreach ($imageFields as $field) {
                if ($request->has($field) && ! empty($request->input($field))) {
                    if ($user->hasMedia($field)) {
                        $user->getFirstMedia($field)->delete();
                    }

                    $imageData = $request->input($field);
                    $imageUrl = $this->serveBase64Image($imageData);
                    $user->addMedia($imageUrl)->toMediaCollection($field);
                    $uploadedImages[$field] = $imageUrl;

                    // Save or update meta as "pending" status for each document
                    AppUserMeta::updateOrCreate(
                        ['user_id' => $user->id, 'meta_key' => $field.'_status'],
                        ['meta_value' => 'pending']
                    );
                    $hasImageUploaded = true;
                }
            }

            if (empty($uploadedImages)) {
                return $this->addErrorResponse(500, trans('global.No_image_found_in_the_request'), '');
            }
            if ($hasImageUploaded) {
                $user->host_status = '2';
                $user->save();
            }

            $userMeta = AppUserMeta::where('user_id', $user->id)->get();
            $user->meta_data = $userMeta;

            return $this->addSuccessResponse(200, trans('global.images_added_successfully'), $user);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, $e->getMessage(), $e->getMessage());
        }
    }

    public function getVerificationDocuments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|exists:app_users,token',
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $user_id = $this->checkUserByToken($request->token);
            if (! $user_id) {
                return $this->addErrorResponse(419, trans('global.token_not_match'), '');
            }

            $user = AppUser::with(['media', 'metadata'])->find($user_id);

            if (! $user) {
                return $this->addErrorResponse(404, trans('global.user_not_found'), '');
            }

            $documentKeys = [
                'driving_licence_front',
                'driving_licence_back',
                'driver_id_front',
                'driver_id_back',
                'hire_service_licence',
                'inspection_certificate',
            ];

            $documentData = [];
            $metaData = $user->metadata->pluck('meta_value', 'meta_key');

            foreach ($documentKeys as $key) {
                $file = $user->getMedia($key)->last();
                $imageUrl = $file ? $file->getUrl() : null;
                $metaKey = "{$key}_status";
                $metaStatus = $metaData[$metaKey] ?? '';
                $documentData[$key] = [
                    "{$key}_image" => $imageUrl,
                    "{$key}_status" => $metaStatus,
                ];
            }

            return $this->addSuccessResponse(200, trans('global.user_documents_fetched_successfully'), $documentData);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, $e->getMessage(), $e->getMessage());
        }
    }
}
