<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Payout extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $table = 'payouts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'vendorid' => 'string',
        'amount' => 'string',
    ];

    public const PAYOUT_STATUS_SELECT = [
        'Pending' => 'Pending',
        'Success' => 'Success',
    ];

    protected $fillable = [
        'vendorid',
        'amount',
        'currency',
        'request_by',
        'vendor_name',
        'payment_method',
        'account_number',
        'payout_status',
        'note',
        'module',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function vendorid()
    {
        return $this->belongsTo(AppUser::class, 'vendorid');
    }

    public function vendor()
    {
        return $this->belongsTo(AppUser::class, 'vendorid');
    }

    /**
     * Register the media collection for payout proof file/image.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payout_proof')
            ->singleFile(); // Optional: restrict to 1 proof per payout
    }

    /**
     * Optional: Define media conversions if needed
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10);
    }

    public function getPayoutProofAttribute()
    {

        $file = $this->getMedia('payout_proof')->last();
        if ($file) {
            $file->url = $file->getUrl();

        }

        return $file;
    }
}
