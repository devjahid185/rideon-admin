<?php

namespace App\Http\Controllers\VendorAuth;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Validator;

class VendorLoginController extends Controller
{
    public function loginForm()
    {
        // if (Auth::guard('appUser')->check()) {
        //     return redirect()->route('vendor.dashboard');
        // }

        $general_name = GeneralSetting::where('meta_key', 'general_name')->first('meta_value');
        $general_description = GeneralSetting::where('meta_key', 'general_description')->first('meta_value');
        $logoUrl = GeneralSetting::where('meta_key', 'general_logo')->first('meta_value');
        $faviconUrl = GeneralSetting::where('meta_key', 'general_favicon')->first('meta_value');
        $general_loginBackgroud = GeneralSetting::where('meta_key', 'general_loginBackgroud')->first('meta_value');
        $general_captcha = GeneralSetting::where('meta_key', 'general_captcha')->first();
        $site_key = GeneralSetting::where('meta_key', 'site_key')->first();
        $private_key = GeneralSetting::where('meta_key', 'private_key')->first();

        return view('vendor.login', [
            'logoUrl' => '/storage/'.$logoUrl->meta_value,
            'siteName' => $general_name->meta_value,
            'tagLine' => $general_description->meta_value,
            'faviconUrl' => '/storage/'.$faviconUrl->meta_value,
            'loginBackgroud' => '/storage/'.$general_loginBackgroud->meta_value,
            'general_captcha' => $general_captcha->meta_value,
            'site_key' => $site_key->meta_value,
            'private_key' => $private_key->meta_value,
        ]);
    }

    public function registerForm()
    {
        // if (Auth::guard('appUser')->check()) {
        //     return redirect()->route('vendor.dashboard');
        // }

        $general_name = GeneralSetting::where('meta_key', 'general_name')->first('meta_value');
        $general_description = GeneralSetting::where('meta_key', 'general_description')->first('meta_value');
        $logoUrl = GeneralSetting::where('meta_key', 'general_logo')->first('meta_value');
        $faviconUrl = GeneralSetting::where('meta_key', 'general_favicon')->first('meta_value');
        $general_loginBackgroud = GeneralSetting::where('meta_key', 'general_loginBackgroud')->first('meta_value');
        $general_captcha = GeneralSetting::where('meta_key', 'general_captcha')->first();
        $site_key = GeneralSetting::where('meta_key', 'site_key')->first();
        $private_key = GeneralSetting::where('meta_key', 'private_key')->first();

        return view('vendor.register', [
            'logoUrl' => '/storage/'.$logoUrl->meta_value,
            'siteName' => $general_name->meta_value,
            'tagLine' => $general_description->meta_value,
            'faviconUrl' => '/storage/'.$faviconUrl->meta_value,
            'loginBackgroud' => '/storage/'.$general_loginBackgroud->meta_value,
            'general_captcha' => $general_captcha->meta_value,
            'site_key' => $site_key->meta_value,
            'private_key' => $private_key->meta_value,
        ]);
    }

    public function register(Request $request)
    {

        $data = [
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone_country' => $request->input('phone_country'),
            'password' => $request->input('password'),
            'default_country' => $request->input('default_country'),
        ];

        try {

            $response = Http::post(url('api/v1/userRegister'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => $responseData['status'],
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'error' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'error' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function otpVerification(Request $request)
    {

        $data = [
            'phone' => $request->input('phone'),
            'otp_value' => $request->input('otp_value'),
            'phone_country' => $request->input('phone_country'),
        ];

        try {

            $response = Http::get(url('api/v1/otpVerification'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => $responseData['status'],
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'error' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'error' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $data = [
            'phone' => $request->input('phone'),
            'phone_country' => $request->input('phone_country'),
            'token' => $request->input('token'),
        ];

        try {

            $response = Http::get(url('api/v1/ResendOtp'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => $responseData['status'],
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'error' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'error' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $data = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::guard('appUser')->attempt($data)) {
                $user = Auth::guard('appUser')->user();

                $token = Str::random(120);

                $user->update(['token' => $token]);

                return redirect()->intended(route('vendor.dashboard'));
            }

            return redirect()->back()->withErrors(['email' => trans('auth.failed')]);
        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['generic' => 'An error occurred. Please try again.']);
        }
    }

    public function logout()
    {
        Auth::guard('appUser')->logout();

        session()->flush();

        session()->regenerate();

        return redirect()->route('vendor.login');
    }

    public function hostRequestForm(Request $request)
    {
        $user = Auth::guard('appUser')->user();

        if (! $user) {
            return redirect()->route('vendor.login');
        }
        $general_name = GeneralSetting::where('meta_key', 'general_name')->first('meta_value');
        $general_description = GeneralSetting::where('meta_key', 'general_description')->first('meta_value');
        $logoUrl = GeneralSetting::where('meta_key', 'general_logo')->first('meta_value');
        $faviconUrl = GeneralSetting::where('meta_key', 'general_favicon')->first('meta_value');
        $general_loginBackgroud = GeneralSetting::where('meta_key', 'general_loginBackgroud')->first('meta_value');
        $general_captcha = GeneralSetting::where('meta_key', 'general_captcha')->first();
        $site_key = GeneralSetting::where('meta_key', 'site_key')->first();
        $api_google_map_key = GeneralSetting::where('meta_key', 'api_google_map_key')->first();
        $private_key = GeneralSetting::where('meta_key', 'private_key')->first();

        return view('vendor.hostRequest', [
            'logoUrl' => '/storage/'.$logoUrl->meta_value,
            'siteName' => $general_name->meta_value,
            'tagLine' => $general_description->meta_value,
            'faviconUrl' => '/storage/'.$faviconUrl->meta_value,
            'loginBackgroud' => '/storage/'.$general_loginBackgroud->meta_value,
            'general_captcha' => $general_captcha->meta_value,
            'site_key' => $site_key->meta_value,
            'private_key' => $private_key->meta_value,
            'user' => $user,
            'api_google_map_key' => $api_google_map_key,
        ]);
    }

    public function putHostRequest(Request $request)
    {

        $user = Auth::guard('appUser')->user();

        if (! $user) {
            return response()->json([
                'status' => 401,
                'error' => trans('global.unauthenticated'),
            ], 401);
        }

        $userToken = $user->token;

        $data = [
            'host_status' => '2',
            'token' => $userToken,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'company_name' => $request->input('company_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country_code' => $request->input('phone_country'),
            'residency_type' => $request->input('nationality_residency'),
            'full_address' => $request->input('address'),
            'identity_type' => $request->input('identity_type'),
        ];

        if ($request->hasFile('identity_photo')) {
            $identityPhoto = $request->file('identity_photo');
            $imageBase64 = 'data:image/jpeg;base64,'.base64_encode(file_get_contents($identityPhoto->getRealPath()));

            $data['identity_image'] = $imageBase64;
        }

        try {
            $response = Http::post(url('api/v1/puthostRequest'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => $responseData['status'],
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'error' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
        ];

        try {
            $response = Http::get(url('api/v1/forgotPassword'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => $responseData['status'],
                    'data' => $responseData['data'],
                ]);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'error' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
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
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $data = [
                'email' => $request->email,
                'reset_token' => $request->reset_token,
            ];

            $response = Http::get(url('api/v1/verifyResetToken'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => 200,
                    'message' => trans('global.RESET_OTP_Found_YOU_CAN_PROCEED'),
                    'data' => $responseData['data'],
                ], 200);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'message' => $response->json()['message'] ?? trans('global.RESET_OTP_ERROR'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function resendTokenForgotPassword(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $data = [
                'email' => $request->email,
            ];

            $response = Http::get(url('api/v1/ResendToken'), $data);

            \Log::info('Response from API: ', [$response->body()]);
            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => 200,
                    'message' => trans('global.OTP_resent_succesfully'),
                    'data' => $responseData['data'],
                ], 200);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'message' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
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
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $data = [
                'email' => $request->email,
                'reset_token' => $request->reset_token,
                'password' => $request->password,
                'confirm_password' => $request->confirm_password,
            ];

            $response = Http::post(url('api/v1/resetPassword'), $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'status' => 200,
                    'message' => trans('global.Password_changed_successfully'),
                    'data' => $responseData['data'],
                ], 200);
            } else {
                return response()->json([
                    'status' => $response->status(),
                    'message' => $response->json()['message'] ?? trans('global.something_wrong'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => trans('global.something_wrong'),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
