<?php

namespace App\Models\Modern;

use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Models\AppUser;
use App\Models\Booking;
use App\Models\CancellationPolicy;
use App\Models\City;
use App\Models\Module;
use App\Models\Review;
use App\Models\VehicleMake;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Item extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, MiscellaneousTrait, SoftDeletes;

    public $table = 'rental_items';

    protected $casts = [
        'person_allowed' => 'string',
        'status' => 'string',
        'accommodates' => 'string',
        'city' => 'string',
        'is_verified' => 'string',
        'is_featured' => 'string',
        'weekly_discount' => 'string',
        'monthly_discount' => 'string',
        'userid_id' => 'string',
        'item_type_id' => 'string',
        'features_id' => 'string',
        'place_id' => 'string',
        'booking_policies_id' => 'integer',
        'views_count' => 'string',
        'item_rating' => 'string',
        'model_id' => 'string',
        'size' => 'string',
        'order_column' => 'string',
        'latitude' => 'string',
        'longitude' => 'string',
        'step_progress' => 'decimal:2',
        'steps_completed' => 'json',
        'make' => 'string',
        'model' => 'string',
        'registration_number' => 'string',

    ];

    protected $appends = [
        'front_image',
        'gallery',
        'vehicle_registration_doc',
        'vehicle_insurance_doc',
        'driving_licence',
        'driver_authorization',
        'hire_service_licence',
        'inspection_certificate',
    ];

    public const IS_VERIFIED_SELECT = [
        '1' => 'Yes',
        '0' => 'No',
    ];

    public const IS_FEATURED_SELECT = [
        '0' => 'No',
        '1' => 'Yes',
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

    public const WEEKLY_DISCOUNT_TYPE_SELECT = [
        'fixed' => 'Fixed',
        'percent' => 'Percent',
    ];

    public const MONTHLY_DISCOUNT_TYPE_SELECT = [
        'fixed' => 'Fixed',
        'percent' => 'Percent',
    ];

    protected $fillable = [
        'token',
        'title',
        'description',
        'userid_id',
        'average_speed_kmph',
        'country',
        'city',
        'item_type_id',
        'features_id',
        'item_rating',
        'mobile',
        'status',
        'person_allowed',
        'accommodates',
        'price',
        'address',
        'place_id',
        'state_region',
        'city_name',
        'zip_postal_code',
        'latitude',
        'longitude',
        'is_verified',
        'is_featured',
        'weekly_discount',
        'service_type',
        'weekly_discount_type',
        'monthly_discount',
        'monthly_discount_type',
        'booking_policies_id',
        'category_id',
        'subcategory_id',
        'module',
        'step_progress',
        'steps_completed',
        'created_at',
        'updated_at',
        'deleted_at',
        'make',
        'model',
        'registration_number',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->sharpen(10);
        $this->addMediaConversion('preview')
            ->width(400)
            ->sharpen(20);
    }

    public function userid()
    {
        return $this->belongsTo(AppUser::class, 'userid_id');
    }

    public function moduleName()
    {
        return $this->belongsTo(Module::class, 'module');
    }

    public function host()
    {
        return $this->belongsTo(AppUser::class, 'host_id');
    }

    public function features()
    {
        return $this->belongsTo(ItemFeatures::class, 'features_id');
    }

    public function getFrontImageAttribute()
    {
        $file = $this->getMedia('front_image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getGalleryAttribute()
    {
        $files = $this->getMedia('gallery');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview = $item->getUrl('preview');
        });

        return $files;
    }

    public function getChartImageAttribute()
    {
        $file = $this->getMedia('chart_image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getVehicleRegistrationDocAttribute()
    {
        $file = $this->getMedia('vehicle_registration_doc')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function getVehicleInsuranceDocAttribute()
    {
        $file = $this->getMedia('vehicle_insurance_doc')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
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

    public function place()
    {
        return $this->belongsTo(City::class, 'place_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'itemid', 'id');
    }

    public function bookingPolicy()
    {
        return $this->belongsTo(CancellationPolicy::class, 'booking_policies_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->token = $item->generateUniqueToken();
        });

        static::deleting(function ($item) {
            // Check if the model is being soft deleted
            if (! $item->isForceDeleting()) {
                // Perform actions for soft deletes if needed
            }
        });

        static::forceDeleting(function ($item) {
            // Delete related models when the item is force deleted
            $item->itemMeta()->delete();
            $item->itemVehicle()->delete();
            $item->reviews()->delete();
        });
    }

    public function itemMeta()
    {
        return $this->hasMany(ItemMeta::class, 'item_id');
    }

    public static function generateFullTextSearch($item)
    {
        $searchableFields = [
            $item->title,
            $item->description,

        ];

        $metaKeys = ['fits', 'sizes', 'colors'];
        $metaValues = ItemMeta::whereIn('item_id', [$item->id])
            ->whereIn('meta_key', $metaKeys)
            ->pluck('meta_value')
            ->toArray();

        $featureIds = array_merge(
            explode(',', $item->features_id),
            explode(',', implode(',', $metaValues))
        );

        $features = ItemFeatures::whereIn('id', $featureIds)
            ->pluck('name')
            ->toArray();

        $searchableFields = array_merge($searchableFields, $features);

        $searchableFieldsString = implode(' ', array_filter($searchableFields));
        // $filename = base_path('searchable_fields.txt');
        // file_put_contents($filename, $searchableFieldsString);

        return implode(' ', array_filter($searchableFields));
    }

    public function appUser()
    {
        return $this->belongsTo(AppUser::class, 'userid_id');
    }

    public function item_Type()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id', 'id');
    }

    public function itemVehicle()
    {
        return $this->hasOne(ItemVehicle::class, 'item_id', 'id');
    }

    public function vehicleMake()
    {
        return $this->belongsTo(VehicleMake::class, 'make');
    }
}
