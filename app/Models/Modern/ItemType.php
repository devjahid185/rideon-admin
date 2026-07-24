<?php

namespace App\Models\Modern;

use App\Models\CategoryTypeRelation;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ItemType extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public bool $queueable = false;

    public $table = 'rental_item_types';

    protected $appends = [
        'image',
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
        'name',
        'description',
        'status',
        'module',
        'max_weight',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'status' => 'string',
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

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();
        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'item_type_id', 'id');
    }

    public function categoryTypeRelations()
    {
        return $this->hasMany(CategoryTypeRelation::class, 'type_id');
    }

    public function cityFare()
    {
        return $this->hasOne(ItemCityFare::class, 'item_type_id');
    }

    public function deleteItemType()
    {
        DB::transaction(function () {
            $categoryTypeRelations = $this->categoryTypeRelations;

            foreach ($categoryTypeRelations as $relation) {
                $relation->delete();
            }

            $this->items()->each(function ($item) {
                $item->delete();
            });

            $this->delete();
        });
    }

    public function serviceTypes()
    {
        return $this->belongsToMany(
            RentalItemServiceType::class,
            'rental_item_type_service_type',
            'rental_item_type_id',
            'rental_item_service_type_id'
        );
    }
}
