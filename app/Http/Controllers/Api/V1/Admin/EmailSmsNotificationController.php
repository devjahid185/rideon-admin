<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Validator;

class EmailSmsNotificationController extends Controller
{
    public function emailSmsNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email_notification' => 'required',
                'sms_notification' => 'required',
                'push_notification' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $user = AppUser::where('token', $request->input('token'))->first();

            if (! $user) {
                return $this->errorResponse(401, trans('global.user_not_found'));
            }
            $data = [
                'email_notification' => $request->input('email_notification'),
                'sms_notification' => $request->input('sms_notification'),
                'push_notification' => $request->input('push_notification'),
            ];

            return $this->successResponse(200, trans('global.emailsmsnotification'), ['data' => $data]);
        } catch (\Exception $e) {
            return $this->errorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }
}
