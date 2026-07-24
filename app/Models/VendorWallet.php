<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VendorWallet extends Model
{
    protected $fillable = [
        'vendor_id', 'amount', 'booking_id', 'payout_id', 'type', 'description', 'token',
    ];

    protected $casts = [
        'vendor_id' => 'string',
        'booking_id' => 'string',
        'payout_id' => 'string',
        'amount' => 'string',

    ];
    // Add any additional relationships or methods you may need

    // Disable timestamps if not needed
    public $timestamps = true;

    public function appUser()
    {
        return $this->belongsTo(AppUser::class, 'vendor_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    protected static function booted()
    {
        static::creating(function ($wallet) {
            if (empty($wallet->token)) {
                $wallet->token = self::generateUniqueToken();
            }
        });
    }

    private static function generateUniqueToken($length = 10)
    {
        do {
            $token = Str::upper(Str::random($length));
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
