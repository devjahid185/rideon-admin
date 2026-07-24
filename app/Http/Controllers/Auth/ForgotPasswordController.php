<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {

        $settingsKeys = [
            'emailwizard_driver',
            'host',
            'port',
            'from_email',
            'encryption',
            'username',
            'password',
        ];

        $settings = GeneralSetting::whereIn('meta_key', $settingsKeys)->get()->keyBy('meta_key');

        $smtpConfig = [
            'transport' => $settings->get('emailwizard_driver')->meta_value ?? 'smtp',
            'host' => $settings->get('host')->meta_value ?? 'test',
            'port' => $settings->get('port')->meta_value ?? '',
            'encryption' => $settings->get('encryption')->meta_value ?? '',
            'username' => $settings->get('username')->meta_value ?? '',
            'password' => $settings->get('password')->meta_value ?? '',
            'timeout' => null,
            'auth_mode' => null,
        ];

        // if (empty($smtpConfig['host']) || $smtpConfig['host'] == 'test' || empty($smtpConfig['username']) || empty($smtpConfig['password'])) {

        //     redirect()
        //         ->route('login')
        //         ->with('error', 'Email configuration is missing or incorrect. Please update SMTP settings from General Settings')
        //         ->send();
        // }

        Config::set('mail.mailers.smtp', $smtpConfig);

        Config::set('mail.from', [
            'address' => $smtpConfig['username'],
            'name' => config('app.name', 'Vehicle Unibooker'),
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {

        $request->validate(['email' => 'required|email']);
        try {

            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);
        } catch (\Exception $e) {

            return redirect()->route('password.request')->with('error', $e->getMessage());
        }
    }

    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    public function broker()
    {
        return Password::broker();
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {

        return back()->withErrors(
            ['email' => trans($response)]
        );
    }

    public function sendResetLinkResponse(Request $request, $response)
    {

        return redirect()->route('password.request')->with('status', trans($response));
    }
}
