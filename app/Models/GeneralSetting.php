<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'general_settings';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'meta_key',
        'meta_value',
        'module',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function getMetaValue($key)
    {
        $setting = self::where('meta_key', $key)->first();

        return $setting ? $setting->meta_value : null;
    }

    protected static function booted()
    {
        $refreshCache = function () {
            Cache::forget('general_settings');
            Cache::rememberForever('general_settings', function () {
                return self::pluck('meta_value', 'meta_key')->toArray();
            });
        };

        static::saved(function ($setting) use ($refreshCache) {
            $refreshCache();
        });

        static::deleted(function ($setting) use ($refreshCache) {
            $refreshCache();
        });
    }
}
