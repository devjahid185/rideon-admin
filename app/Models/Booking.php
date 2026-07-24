<?php

namespace App\Models;

use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Models\Modern\Item;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Booking extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MiscellaneousTrait,SoftDeletes;

    public $table = 'bookings';

    public const BOOK_FOR_SELECT = [
        'self' => 'self',
        'Other' => 'Other',
    ];

    protected $casts = [
        'host_id' => 'string',
        'amount_to_pay' => 'string',
        'vendor_commission_given' => 'string',
        'rating' => 'string',
        'module' => 'string',
    ];

    public const CANCELLED_BY_SELECT = [
        'Host' => 'Host',
        'Guest' => 'Guest',
    ];

    protected $dates = [
        'ride_date ',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'token',
        'itemid',
        'host_id',
        'userid',
        'ride_date ',
        'status',
        'price_per_km',
        'base_price',
        'iva_tax',
        'total',
        'currency_code',
        'cancellation_reasion',
        'transaction',
        'payment_method',
        'wall_amt',
        'rating',
        'cancelled_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'firebase_json',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function host()
    {
        return $this->belongsTo(AppUser::class, 'host_id');
    }

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'userid');
    }

    public function customername()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function itemgetid()
    {
        return $this->belongsTo(Item::class, 'itemid');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid');
    }

    public function getCheckInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setCheckInAttribute($value)
    {
        $this->attributes['check_in'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCheckOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->token = $booking->generateUniqueBookingToken();
        });
    }

    public function generateUniqueBookingToken()
    {
        $tokenLength = 9;
        $uniqueToken = $this->generateRandomChars($tokenLength);

        // Ensure the generated token is unique
        while (static::where('token', $uniqueToken)->exists()) {
            $uniqueToken = $this->generateRandomChars($tokenLength);
        }

        return $uniqueToken;
    }

    public function extension()
    {
        return $this->hasOne(BookingExtension::class, 'booking_id', 'id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'bookingid')->where('guest_rating', '>', 0);
    }
}
