<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class TokenController extends Controller
{
    use ResponseTrait;

    public function issueSanctumToken(Request $request)
    {
        $sanctumSecret = '49382716504938271650493827165049';

        $validator = Validator::make($request->all(), [
            'secret' => 'required|string',
            'user_token' => 'nullable|string',
            'user_id' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'phone_country' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if ($request->secret !== $sanctumSecret) {
            return $this->addSuccessResponse(498, trans('front.Unauthorized'), $request->all());

        }
        $isRealUser = false;
        if ($request->filled('user_token')) {

            $user = AppUser::where('token', $request->input('user_token'))->first();
            if ($user) {
                $isRealUser = true;
            } else {
                $user = $this->resolveUserForTokenRepair($request);
                $isRealUser = (bool) $user;
            }
        } else {
            $user = $this->resolveUserForTokenRepair($request);
            $isRealUser = (bool) $user;
        }

        if (! $user) {
            $user = AppUser::firstOrCreate(
                ['email' => 'guest@unibooker.app'],// Never delete this user
                ['first_name' => 'Guest User', 'user_type' => 'guest', 'password' => bcrypt('f07c02db6c1c42289f58')]
            );
        }

        if ($isRealUser && empty($user->token)) {
            $user->token = Str::random(120);
            $user->save();
        }

        $tokenInstance = $user->createToken('api-access');
        $token = $tokenInstance->plainTextToken;
        $expiration = now()->addYears(5);
        $tokenInstance->accessToken->expires_at = $expiration;
        $tokenInstance->accessToken->called_ip = $request->ip();
        $tokenInstance->accessToken->save();

        return $this->addSuccessResponse(200, trans('front.authorized'), [
            'token' => $token,
            'type' => $request->type,
            'expires_at' => $expiration,
            'user_token_valid' => $isRealUser,
            'app_user_token' => $isRealUser ? $user->token : '',
        ]);

    }

    private function resolveUserForTokenRepair(Request $request)
    {
        $query = AppUser::query()
            ->where('status', 1)
            ->where('user_type', '<>', 'guest');

        if ($request->filled('user_id')) {
            $query->where('id', $request->input('user_id'));
        } elseif ($request->filled('email')) {
            $query->where('email', strtolower($request->input('email')));
        } elseif ($request->filled('phone') && $request->filled('phone_country')) {
            $query->where('phone', $request->input('phone'))
                ->where('phone_country', $request->input('phone_country'));
        } else {
            return null;
        }

        $user = $query->first();

        if (! $user) {
            return null;
        }

        if ($request->filled('email') && strtolower($request->input('email')) !== strtolower((string) $user->email)) {
            return null;
        }

        if ($request->filled('phone') && (string) $request->input('phone') !== (string) $user->phone) {
            return null;
        }

        if ($request->filled('phone_country') && (string) $request->input('phone_country') !== (string) $user->phone_country) {
            return null;
        }

        return $user;
    }
}
