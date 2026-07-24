<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AddCoupon extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public $table = 'add_coupons';

    public const STATUS_SELECT = [
        '1' => 'Active',
        '0' => 'InActive',
    ];

    protected $dates = [
        'coupon_expiry_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'coupon_title',
        'coupon_subtitle',
        'coupon_image',
        'coupon_expiry_date',
        'coupon_type',
        'coupon_code',
        'min_order_amount',
        'coupon_value',
        'coupon_description',
        'status',
        'max_uses_per_user',
        'max_uses',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 50, 50);
        $this->addMediaConversion('preview')->fit(Fit::Crop, 120, 120);
    }

    public function getCouponExpiryDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setCouponExpiryDateAttribute($value)
    {
        $this->attributes['coupon_expiry_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
