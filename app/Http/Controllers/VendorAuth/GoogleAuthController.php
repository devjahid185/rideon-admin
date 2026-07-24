<?php

namespace App\Http\Controllers\VendorAuth;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client_id = '951328556833-6bgch0ffsohs22g0lt53bmkc836c3m7c.apps.googleusercontent.com';
        $redirect_uri = route('login.google.callback');
        $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=$client_id&redirect_uri=$redirect_uri&response_type=code&scope=email profile";

        return redirect($auth_url); // Redirect the user to Google
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        $access_token = $this->getAccessTokenFromGoogle($code);

        if (! $access_token) {
            return redirect()->route('vendor.login')->with('error', 'Failed to get Google access token.');
        }

        $user_info = $this->getGoogleUserInfo($access_token);

        if (! $user_info) {
            return redirect()->route('vendor.login')->with('error', 'Failed to get user information from Google.');
        }

        $data = [
            'displayName' => $user_info['name'],
            'email' => $user_info['email'],
            'id' => $user_info['sub'],
            'login_type' => 'google',
            'profile_image' => $user_info['picture'] ?? null,
        ];

        try {
            $response = Http::post(url('api/v1/socialLogin'), $data);
            if ($response->successful()) {
                $response_data = $response->json();

                if (isset($response_data['data']['email']) && isset($response_data['data']['social_id'])) {
                    $userEmail = $response_data['data']['email'];
                    $socialId = $response_data['data']['social_id'];

                    $user = AppUser::where('email', $userEmail)->orWhere('social_id', $socialId)->first();

                    if ($user) {
                        Auth::guard('appUser')->login($user);

                        return redirect()->intended(route('vendor.dashboard'));
                    } else {
                        return redirect()->back()->withErrors(['email' => 'No matching user found.']);
                    }
                } else {
                    return redirect()->back()->withErrors(['email' => 'No email or social_id found from social login.']);
                }
            } else {
                return redirect()->route('vendor.login')->with('error', 'Social login failed. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->route('vendor.login')->with('error', 'An error occurred while logging in. Please try again.');
        }
    }

    private function getAccessTokenFromGoogle($code)
    {
        $url = 'https://oauth2.googleapis.com/token';
        $client_id = '951328556833-6bgch0ffsohs22g0lt53bmkc836c3m7c.apps.googleusercontent.com';
        $client_secret = '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';
        $redirect_uri = route('login.google.callback');

        $data = [
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return null;
        }

        curl_close($ch);

        $response_data = json_decode($response, true);

        return $response_data['access_token'] ?? null;
    }

    private function getGoogleUserInfo($access_token)
    {
        $url = 'https://www.googleapis.com/oauth2/v3/userinfo';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$access_token,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return null;
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
