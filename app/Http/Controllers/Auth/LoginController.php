<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $general_captcha = GeneralSetting::where('meta_key', 'general_captcha')->first();
        $site_key = GeneralSetting::where('meta_key', 'site_key')->first();
        $private_key = GeneralSetting::where('meta_key', 'private_key')->first();
        if ($general_captcha == 'yes') {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'g-recaptcha-response' => 'required',
            ]);

            $recaptchaResponse = $request->input('g-recaptcha-response');
            $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $private_key,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);

            if (! $response->json()['success']) {
                return redirect()->back()
                    ->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.'])
                    ->withInput();
            }
        }

        // Continue with the login process
    }

    public function showLoginForm()
    {
        $settings = GeneralSetting::whereIn('meta_key', [
            'general_name',
            'general_description',
            'general_logo',
            'general_favicon',
            'general_loginBackgroud',
            'general_captcha',
            'site_key',
            'private_key',
        ])->pluck('meta_value', 'meta_key'); // returns [meta_key => meta_value]

        return view('auth.login', [
            'logoUrl' => '/storage/'.($settings['general_logo'] ?? 'default_logo.png'),
            'siteName' => $settings['general_name'] ?? '',
            'tagLine' => $settings['general_description'] ?? '',
            'faviconUrl' => '/storage/'.($settings['general_favicon'] ?? 'default_favicon.png'),
            'loginBackgroud' => '/storage/'.($settings['general_loginBackgroud'] ?? 'default_bg.png'),
            'general_captcha' => $settings['general_captcha'] ?? '',
            'site_key' => $settings['site_key'] ?? '',
            'private_key' => $settings['private_key'] ?? '',
        ]);
    }
}
