<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUserOtp extends Model
{
    protected $table = 'app_user_otps';

    protected $fillable = ['phone', 'country_code', 'otp_code', 'created_at', 'expires_at'];
}
