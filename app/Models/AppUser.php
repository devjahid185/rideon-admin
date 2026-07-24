<?php

namespace App\Models;

use App\Models\Modern\Item;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppUser extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, InteractsWithMedia, SoftDeletes;

    public $table = 'app_users';

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'profile_image',
        'identity_image',
        'driving_licence',
        'driver_authorization',
        'hire_service_licence',
        'inspection_certificate',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        '1' => 'Active',
        '0' => 'InActive',
    ];

    protected $fillable = [
        'first_name',
        'middle',
        'last_name',
        'email',
        'phone',
        'phone_country',
        'default_country',
        'password',
        'wallet',
        'token',
        'status',
        'user_type',
        'gender',
        'created_at',
        'updated_at',
        'deleted_at',
        'birthdate',
        'fcm',
        'sms_notification',
        'email_notification',
        'push_notification',
        'device_id',
        'ave_host_rate',
        'avr_guest_rate',
        'host_status',
        'firestore_id',
        'document_verify',
        'verified',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (is_int($value) || is_bool($value)) {
            return (string) $value;
        }

        return $value;
    }

    protected $casts = [
        'push_notification' => 'string',
        'email_notification' => 'string',
        'sms_notification' => 'string',
        'otp_value' => 'string',
        'reset_token' => 'string',
        'document_verify' => 'string',
        'phone_verify' => 'string',
        'email_verify' => 'string',
        'status' => 'string',
        'package_id' => 'string',
        'device_id' => 'string',
    ];

    public function getProfileImageAttribute()
    {
        $file = $this->getMedia('profile_image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getIdentityImageAttribute()
    {
        $file = $this->getMedia('identity_image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
        }

        return $file;
    }

    public function getDrivingLicenceAttribute()
    {
        $file = $this->getMedia('driving_licence')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getDriverAuthorizationAttribute()
    {
        $file = $this->getMedia('driver_authorization')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getHireServiceLicenceAttribute()
    {
        $file = $this->getMedia('hire_service_licence')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getInspectionCertificateAttribute()
    {
        $file = $this->getMedia('inspection_certificate')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 50, 50);
        $this->addMediaConversion('preview')->fit(Fit::Crop, 120, 120);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image');
        $this->addMediaCollection('identity_image');
        $this->addMediaCollection('driving_licence');
        $this->addMediaCollection('driver_authorization');
        $this->addMediaCollection('hire_service_licence');
        $this->addMediaCollection('inspection_certificate');
    }

    public function package()
    {
        return $this->belongsTo(AllPackage::class, 'package_id');
    }

    public function sender()
    {
        return $this->belongsTo(AppUser::class, 'sender_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'userid_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'userid');
    }

    public function hostBookings()
    {
        return $this->hasMany(Booking::class, 'host_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {});

        static::forceDeleting(function ($user) {

            $user->items()->each(function ($item) {
                $item->itemMeta()->forceDelete();
                $item->forceDelete();
            });

            $user->metadata()->forceDelete();

            $user->payouts()->forceDelete();

            $user->wallets()->forceDelete();

            $user->vendorWallets()->forceDelete();

        });
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'userid_id');
    }

    public function metadata()
    {
        return $this->hasMany(AppUserMeta::class, 'user_id', 'id');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'vendorid', 'id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'user_id');
    }

    public function vendorWallets()
    {
        return $this->hasMany(VendorWallet::class, 'vendor_id');
    }
}
