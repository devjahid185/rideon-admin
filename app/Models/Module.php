<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Module extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public $table = 'module';

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
        'default_module',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function itme()
    {
        return $this->hasMany(Item::class, 'module');
    }

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
        // $this->addMediaConversion('preview')->fit(Fit::Crop, 120, 120);

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
}
